<?php

use App\Models\Image;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('photos:prune-old {--days=30 : Delete photos older than this number of days} {--dry-run : Only preview and do not delete}', function () {
    $days = max(1, (int) $this->option('days'));
    $dryRun = (bool) $this->option('dry-run');
    $cutoff = now()->subDays($days);

    $baseQuery = Image::query()
        ->where(function ($query) use ($cutoff) {
            $query
                ->where(function ($innerQuery) use ($cutoff) {
                    $innerQuery
                        ->whereNotNull('captured_at')
                        ->where('captured_at', '<', $cutoff);
                })
                ->orWhere(function ($innerQuery) use ($cutoff) {
                    $innerQuery
                        ->whereNull('captured_at')
                        ->where('uploaded_at', '<', $cutoff);
                });
        });

    $total = (clone $baseQuery)->count();

    if ($total === 0) {
        $this->info("Tidak ada foto yang lebih lama dari {$days} hari.");

        return 0;
    }

    $this->info(($dryRun ? '[Dry Run] ' : '')."Memproses {$total} foto (cutoff: {$cutoff->toDateTimeString()})...");

    $deleted = 0;
    $fileDeleted = 0;
    $errors = 0;

    $normalizePath = static function (?string $path): ?string {
        if (empty($path)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);

        if (preg_match('/^https?:\/\//i', $normalized) === 1) {
            return null;
        }

        $normalized = ltrim($normalized, './');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }

        $normalized = ltrim($normalized, '/');

        return $normalized !== '' ? $normalized : null;
    };

    $baseQuery
        ->select(['id', 'image_path'])
        ->orderBy('id')
        ->chunkById(200, function ($images) use (
            &$deleted,
            &$fileDeleted,
            &$errors,
            $dryRun,
            $normalizePath
        ) {
            foreach ($images as $image) {
                try {
                    $normalizedPath = $normalizePath($image->image_path);

                    if (! $dryRun && $normalizedPath !== null) {
                        foreach (array_unique([(string) config('filesystems.default'), 'public']) as $diskName) {
                            if ($diskName === '') {
                                continue;
                            }

                            $disk = Storage::disk($diskName);

                            if ($disk->exists($normalizedPath)) {
                                $disk->delete($normalizedPath);
                                $fileDeleted++;
                                break;
                            }
                        }
                    }

                    if (! $dryRun) {
                        $image->delete();
                    }

                    $deleted++;
                } catch (\Throwable $exception) {
                    $errors++;

                    Log::warning('Failed pruning old photo.', [
                        'image_id' => $image->id ?? null,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        });

    $verb = $dryRun ? 'ditemukan' : 'dihapus';
    $this->info("Selesai: {$deleted} foto {$verb}, {$fileDeleted} file storage dihapus, {$errors} error.");

    return 0;
})->purpose('Delete old photos from database and storage');

Schedule::command('photos:prune-old --days=30')
    ->dailyAt('02:00')
    ->withoutOverlapping();
