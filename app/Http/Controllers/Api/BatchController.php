<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Bus;

class BatchController extends Controller
{
    public function show(string $id)
    {
        $batch = Bus::findBatch($id);

        if (!$batch) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        return response()->json([
            'id' => $batch->id,
            'total_jobs' => $batch->totalJobs,
            'pending_jobs' => $batch->pendingJobs,
            'processed_jobs' => $batch->processedJobs(),
            'failed_jobs' => $batch->failedJobs,
            'progress' => $batch->progress(),
            'finished' => $batch->finished(),
        ]);
    }
}
