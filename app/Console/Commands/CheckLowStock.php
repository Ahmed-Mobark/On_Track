<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check {--threshold=5}';
    protected $description = 'Check for low stock items and notify admins';

    public function handle(NotificationService $service): int
    {
        $threshold = (int) $this->option('threshold');
        $lowStock = $service->checkLowStock($threshold);

        $this->info("Found {$lowStock->count()} low stock items (threshold: {$threshold})");

        return 0;
    }
}
