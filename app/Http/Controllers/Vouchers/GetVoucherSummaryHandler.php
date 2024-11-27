<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetVoucherSummaryHandler extends Controller
{
    public function __construct()
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $totals = Voucher::where('user_id', $user->id)
            ->selectRaw('currency, SUM(total_amount) as total')
            ->groupBy('currency')
            ->get();

        $response = [
            'soles' => $totals->where('currency', 'PEN')->first()->total ?? 0,
            'dolares' => $totals->where('currency', 'USD')->first()->total ?? 0,
        ];

        return response()->json($response, 200);
    }
}
