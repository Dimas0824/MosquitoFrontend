<?php

namespace App\Services;

use App\Models\InferenceResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HistoryService
{
    public function fetchHistoryPage(string $deviceId, int $perPage, int $page): array
    {
        $query = $this->buildBaseQuery($deviceId);
        $total = (clone $query)->count();

        $records = (clone $query)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(fn(InferenceResult $row) => $this->formatRecord($row));

        return [
            'total' => $total,
            'records' => $records,
        ];
    }

    public function getRecentRecords(string $deviceId, int $limit): Collection
    {
        return $this->buildBaseQuery($deviceId)
            ->limit($limit)
            ->get()
            ->map(fn(InferenceResult $row) => $this->formatRecord($row));
    }

    public function getAllRecords(string $deviceId): Collection
    {
        return $this->buildBaseQuery($deviceId)
            ->get()
            ->map(fn(InferenceResult $row) => $this->formatRecord($row));
    }

    private function formatRecord(InferenceResult $row): array
    {
        $capturedAt = $row->image?->captured_at ?? $row->inference_at;
        $time = $capturedAt?->timezone(config('app.timezone'))->format('H:i') . ' WIB';
        $date = $capturedAt?->isToday()
            ? 'Hari Ini'
            : ($capturedAt?->isYesterday() ? 'Kemarin' : $capturedAt?->format('d M Y'));
        $count = $row->total_jentik ?? 0;

        return [
            'id' => $row->id,
            'time' => $time,
            'date' => $date,
            'count' => $count,
            'status' => $this->determineStatus($count),
            'captured_at' => $capturedAt?->toIso8601String(),
            'image_path' => $row->image?->image_path,
        ];
    }

    private function buildBaseQuery(string $deviceId): Builder
    {
        return InferenceResult::with('image:id,device_code,image_path,captured_at')
            ->where('device_id', $deviceId)
            ->orderByDesc('inference_at');
    }

    private function determineStatus(int $count): string
    {
        if ($count > 5) {
            return 'Bahaya';
        }

        if ($count > 0) {
            return 'Waspada';
        }

        return 'Aman';
    }
}
