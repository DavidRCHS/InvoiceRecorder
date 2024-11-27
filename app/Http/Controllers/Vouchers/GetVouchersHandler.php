<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): AnonymousResourceCollection
    {
        $filters = [
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
            'series' => $request->query('series'),
            'number' => $request->query('number'),
            'voucher_type' => $request->query('voucher_type'),
            'currency' => $request->query('currency'),
        ];

        $vouchers = $this->voucherService->getVouchers(
            (int) $request->query('page'),
            (int) $request->query('paginate'),
            $filters
        );

        return VoucherResource::collection($vouchers);
    }
}
