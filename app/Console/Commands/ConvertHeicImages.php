<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertHeicImages extends Command
{
    protected $signature = 'images:convert-heic {--quality=90}';
    protected $description = 'Convert already-uploaded HEIC/HEIF product images to JPEG so browsers can display them';

    public function handle(): int
    {
        $quality = (int) $this->option('quality');
        $disk = Storage::disk('public');

        $images = ProductImage::where(function ($q) {
            $q->where('url', 'like', '%.heic')
              ->orWhere('url', 'like', '%.heif')
              ->orWhere('url', 'like', '%.HEIC')
              ->orWhere('url', 'like', '%.HEIF');
        })->get();

        if ($images->isEmpty()) {
            $this->info('No HEIC/HEIF images found. Nothing to convert.');
            return self::SUCCESS;
        }

        $this->info("Found {$images->count()} HEIC/HEIF image(s).");

        $converter = $this->detectConverter();
        if (!$converter) {
            $this->error('No HEIC converter available on this server (need Imagick with HEIC, or one of: heif-convert, magick, convert, sips).');
            $this->warn('Tip: delete and re-upload these images from the dashboard — the browser will convert them automatically.');
            return self::FAILURE;
        }
        $this->line("Using converter: <info>{$converter}</info>");

        $converted = 0;
        $failed = 0;

        foreach ($images as $image) {
            $oldPath = $image->url;
            if (!$disk->exists($oldPath)) {
                $this->warn("Missing file, skipping: {$oldPath}");
                $failed++;
                continue;
            }

            $absOld = $disk->path($oldPath);
            $newRel = preg_replace('/\.(heic|heif)$/i', '.jpg', $oldPath);
            $absNew = $disk->path($newRel);

            $ok = $this->convert($converter, $absOld, $absNew, $quality);

            if ($ok && file_exists($absNew)) {
                $image->update(['url' => $newRel]);
                $disk->delete($oldPath);
                $converted++;
                $this->line("Converted: {$oldPath} -> {$newRel}");
            } else {
                $failed++;
                $this->error("Failed: {$oldPath}");
            }
        }

        $this->newLine();
        $this->info("Done. Converted: {$converted}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function detectConverter(): ?string
    {
        if (extension_loaded('imagick') && !empty(\Imagick::queryFormats('HEIC'))) {
            return 'imagick';
        }
        foreach (['heif-convert', 'magick', 'convert', 'sips'] as $bin) {
            if ($this->binaryExists($bin)) {
                return $bin;
            }
        }
        return null;
    }

    private function binaryExists(string $bin): bool
    {
        $which = @shell_exec('command -v ' . escapeshellarg($bin) . ' 2>/dev/null');
        return !empty(trim((string) $which));
    }

    private function convert(string $converter, string $src, string $dst, int $quality): bool
    {
        try {
            if ($converter === 'imagick') {
                $im = new \Imagick($src);
                $im->setImageFormat('jpeg');
                $im->setImageCompressionQuality($quality);
                $im->writeImage($dst);
                $im->clear();
                return true;
            }

            $q = (string) $quality;
            $cmd = match ($converter) {
                'heif-convert' => sprintf('heif-convert -q %s %s %s', $q, escapeshellarg($src), escapeshellarg($dst)),
                'magick'       => sprintf('magick %s -quality %s %s', escapeshellarg($src), $q, escapeshellarg($dst)),
                'convert'      => sprintf('convert %s -quality %s %s', escapeshellarg($src), $q, escapeshellarg($dst)),
                'sips'         => sprintf('sips -s format jpeg %s --out %s', escapeshellarg($src), escapeshellarg($dst)),
                default        => null,
            };

            if (!$cmd) return false;

            @shell_exec($cmd . ' 2>&1');
            return file_exists($dst);
        } catch (\Throwable $e) {
            $this->error('  ' . $e->getMessage());
            return false;
        }
    }
}
