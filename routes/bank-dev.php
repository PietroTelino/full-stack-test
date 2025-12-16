<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Fake Bank Development API
 * Simulates a bank API for generating and managing bank billets
 */

/**
 * Create a new bank billet
 * POST /bank-dev/api/billets
 */
Route::post('/bank-dev/api/billets', function () {
    $validated = request()->validate([
        'name' => 'required|string',
        'document' => 'required|string',
        'amount' => 'required|numeric|min:0.01',
        'due_date' => 'required|date',
    ]);

    // Generate a unique billet code (similar to real bank formats)
    $code = 'BLT' . now()->format('Ymd') . strtoupper(Str::random(8));
    sleep(rand(1, 4));

    $expiresAt = \Carbon\Carbon::parse($validated['due_date'])->endOfDay();

    // Create billet data
    $billetData = [
        'code' => $code,
        'name' => $validated['name'],
        'document' => $validated['document'],
        'amount' => $validated['amount'],
        'due_date' => $validated['due_date'],
        'expires_at' => $expiresAt->toIso8601String(),
        'status' => 'pending',
        'created_at' => now()->toIso8601String(),
        'paid_at' => null,
        'bank' => 'Development Bank',
        'bank_code' => '341',
        'agency' => '0001',
        'account' => '12345-6',
    ];

    // Store in cache (TTL: 90 days)
    Cache::put("bank_billet:{$code}", $billetData, now()->addDays(90));

    // Return response
    return response()->json($billetData, 201);
});

/**
 * Get bank billet details
 * GET /bank-dev/api/billets/{code}
 */
Route::get('/bank-dev/api/billets/{code}', function (string $code) {
    $billetData = Cache::get("bank_billet:{$code}");

    if (!$billetData) {
        return response()->json([
            'error' => 'Billet not found',
            'message' => "Bank billet with code {$code} does not exist",
        ], 404);
    }

    // Generate a barcode (fake format: 47 digits)
    $barcode = sprintf(
        '34191%s%s%s%s%s',
        str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT),
        str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
        str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
        str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT),
        str_pad((int)($billetData['amount'] * 100), 10, '0', STR_PAD_LEFT)
    );

    $billetData['barcode'] = $barcode;
    Cache::put("bank_billet:{$code}", $billetData, now()->addDays(90));

    // Simulate random payment status for demo purposes
    // In a real scenario, this would be updated by the bank when payment is received
    if ($billetData['status'] === 'pending' && rand(1, 10) > 8) {
        $billetData['status'] = 'paid';
        $billetData['paid_at'] = now()->toIso8601String();
        Cache::put("bank_billet:{$code}", $billetData, now()->addDays(90));
    }

    return response()->json($billetData, 200);
});

/**
 * Cancel a bank billet (soft delete)
 * DELETE /bank-dev/api/billets/{code}
 */
Route::delete('/bank-dev/api/billets/{code}', function (string $code) {
    $billetData = Cache::get("bank_billet:{$code}");

    if (!$billetData) {
        return response()->json([
            'error' => 'Billet not found',
            'message' => "Bank billet with code {$code} does not exist",
        ], 404);
    }

    if ($billetData['status'] === 'paid') {
        return response()->json([
            'error' => 'Cannot cancel paid billet',
            'message' => 'This billet has already been paid and cannot be cancelled',
        ], 400);
    }

    $billetData['status'] = 'cancelled';
    $billetData['cancelled_at'] = now()->toIso8601String();
    Cache::put("bank_billet:{$code}", $billetData, now()->addDays(90));

    return response()->json([
        'message' => 'Billet cancelled successfully',
        'data' => $billetData,
    ], 200);
});

/**
 * Manually mark a billet as paid (for testing)
 * POST /bank-dev/api/billets/{code}/pay
 */
Route::post('/bank-dev/api/billets/{code}/pay', function (string $code) {
    $billetData = Cache::get("bank_billet:{$code}");

    if (!$billetData) {
        return response()->json([
            'error' => 'Billet not found',
            'message' => "Bank billet with code {$code} does not exist",
        ], 404);
    }

    if ($billetData['status'] === 'paid') {
        return response()->json([
            'error' => 'Already paid',
            'message' => 'This billet has already been paid',
        ], 400);
    }

    if ($billetData['status'] === 'cancelled') {
        return response()->json([
            'error' => 'Cannot pay cancelled billet',
            'message' => 'This billet has been cancelled',
        ], 400);
    }

    $billetData['status'] = 'paid';
    $billetData['paid_at'] = now()->toIso8601String();
    Cache::put("bank_billet:{$code}", $billetData, now()->addDays(90));

    return response()->json([
        'message' => 'Billet marked as paid successfully',
        'data' => $billetData,
    ], 200);
});

/**
 * List all billets (for debugging)
 * GET /bank-dev/api/billets
 */
Route::get('/bank-dev/api/billets', function () {
    $billets = [];
    $pattern = config('cache.prefix') . ':bank_billet:*';

    // Get all keys matching the pattern
    $redis = Redis::connection();
    $keys = [];

    // Use SCAN to iterate through keys
    $cursor = 0;
    do {
        $result = $redis->scan($cursor, ['match' => $pattern, 'count' => 100]);
        $cursor = $result[0];
        $keys = array_merge($keys, $result[1] ?? []);
    } while ($cursor !== 0);

    // Retrieve each billet
    foreach ($keys as $key) {
        // Extract the code from the key (remove prefix)
        $code = str_replace(config('cache.prefix') . ':bank_billet:', '', $key);
        $billetData = Cache::get("bank_billet:{$code}");

        if ($billetData) {
            $billets[] = $billetData;
        }
    }

    return response()->json([
        'total' => count($billets),
        'billets' => $billets,
    ]);
});

