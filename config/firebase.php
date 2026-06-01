<?php

return [
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),
    'project_id' => env('FIREBASE_PROJECT_ID', 'on-track-1dae9'),

    // Web SDK config (used in frontend JS)
    'web_config' => [
        'apiKey' => env('FIREBASE_API_KEY', ''),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN', 'on-track-1dae9.firebaseapp.com'),
        'projectId' => env('FIREBASE_PROJECT_ID', 'on-track-1dae9'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET', 'on-track-1dae9.firebasestorage.app'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID', ''),
        'appId' => env('FIREBASE_APP_ID', ''),
        'vapidKey' => env('FIREBASE_VAPID_KEY', ''),
    ],
];
