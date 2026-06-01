<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    protected ?array $credentials = null;
    protected ?string $projectId = null;

    public function __construct()
    {
        $credPath = config('firebase.credentials');
        $this->projectId = config('firebase.project_id');

        if ($credPath && file_exists($credPath)) {
            $this->credentials = json_decode(file_get_contents($credPath), true);
        }
    }

    public function isConfigured(): bool
    {
        return $this->credentials !== null && $this->projectId !== null;
    }

    protected function getAccessToken(): ?string
    {
        if (!$this->credentials) return null;

        try {
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $this->credentials
            );
            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Firebase: Failed to get access token: ' . $e->getMessage());
            return null;
        }
    }

    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Firebase not configured');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        try {
            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => $data['url'] ?? url('/'),
                        ],
                    ],
                    'data' => array_map('strval', $data),
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message);

            if ($response->failed()) {
                Log::warning('Firebase send failed: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToTokens(array $fcmTokens, string $title, string $body, array $data = []): int
    {
        $sent = 0;
        foreach ($fcmTokens as $token) {
            if ($token && $this->sendToToken($token, $title, $body, $data)) {
                $sent++;
            }
        }
        return $sent;
    }
}
