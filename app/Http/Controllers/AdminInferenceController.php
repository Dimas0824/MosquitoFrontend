<?php

namespace App\Http\Controllers;

use App\Models\InferenceResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminInferenceController extends Controller
{
    public function update(Request $request, InferenceResult $inference): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:50',
            'total_jentik' => 'required|integer|min:0',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);

        $score = $validated['score'] ?? null;
        if ($score !== null && $score > 1) {
            $score = $score / 100; // allow percent input
        }

        $inference->update([
            'status' => $validated['status'],
            'total_jentik' => $validated['total_jentik'],
            'avg_confidence' => $score,
        ]);

        return Redirect::route('admin.dashboard')
            ->with('success', 'Data inferensi berhasil diperbarui.');
    }
}
