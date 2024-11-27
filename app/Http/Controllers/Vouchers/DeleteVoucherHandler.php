<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteVoucherHandler
{
    public function __invoke(Request $request, $id)
    {
        $user = auth()->user();

        $voucher = Voucher::where('id', $id)->where('user_id', $user->id)->first();

        if (!$voucher) {
            return response()->json([
                'message' => 'Voucher not found or you are not authorized to delete it.'
            ], Response::HTTP_NOT_FOUND);
        }

        $voucher->delete();

        return response()->json([
            'message' => 'Voucher deleted successfully.'
        ], Response::HTTP_OK);
    }
}
