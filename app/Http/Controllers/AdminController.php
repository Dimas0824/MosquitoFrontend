<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Image;
use App\Models\InferenceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'device_count' => Device::count(),
            'inference_count' => InferenceResult::count(),
            'gallery_count' => Image::count(),
        ];

        $devices = Device::latest()->get();

        $inferenceResults = InferenceResult::with('device')
            ->latest('inference_at')
            ->take(10)
            ->get()
            ->map(function (InferenceResult $result) {
                $confidence = $result->avg_confidence;
                $score = $confidence === null ? null : ($confidence <= 1 ? $confidence * 100 : $confidence);

                return [
                    'id' => $result->id,
                    'timestamp' => optional($result->inference_at)->format('M d, H:i:s') ?? '-',
                    'device_code' => $result->device_code ?? optional($result->device)->device_code ?? '-',
                    'label' => $result->status ?? 'N/A',
                    'score' => $score ? number_format($score, 1) : null,
                    'total_jentik' => $result->total_jentik,
                    'raw_score' => $confidence,
                ];
            });

        $galleryImages = Image::with(['device', 'inferenceResult'])
            ->where('image_type', 'preprocessed')
            ->latest('captured_at')
            ->take(12)
            ->get()
            ->map(function (Image $image) {
                $confidence = optional($image->inferenceResult)->avg_confidence;
                $score = $confidence === null ? null : ($confidence <= 1 ? $confidence * 100 : $confidence);
                $normalizedPath = $this->normalizeImagePath($image->image_path);

                $imageUrl = null;
                if (!empty($image->image_blob)) {
                    $imageUrl = 'data:image/jpeg;base64,' . base64_encode($image->image_blob);
                } elseif ($normalizedPath) {
                    $imageUrl = Storage::url($normalizedPath);
                }

                return [
                    'id' => $image->id,
                    'device_code' => $image->device_code ?? optional($image->device)->device_code ?? '-',
                    'captured_at' => optional($image->captured_at)->format('d M Y H:i'),
                    'image_url' => $imageUrl,
                    'label' => optional($image->inferenceResult)->status ?? 'Deteksi',
                    'score' => $score ? number_format($score, 1) : null,
                ];
            });

        return view('admin.admin', [
            'admin_email' => session('admin_email'),
            'stats' => $stats,
            'devices' => $devices,
            'inferenceResults' => $inferenceResults,
            'galleryImages' => $galleryImages,
        ]);
    }

    private function normalizeImagePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, './');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $normalized = ltrim($normalized, '/');

        return $normalized !== '' ? $normalized : null;
    }
}
