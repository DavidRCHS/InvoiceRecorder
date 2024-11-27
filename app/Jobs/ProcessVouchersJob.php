<?php

namespace App\Jobs;

use App\Services\VoucherService;
use App\Models\User;
use App\Events\Vouchers\VouchersCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $xmlContents;
    private User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $voucherService = app(VoucherService::class);

        $successfulVouchers = [];
        $failedVouchers = [];

        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $successfulVouchers[] = $voucher;
            } catch (Exception $e) {
                $failedVouchers[] = [
                    'xml' => $xmlContent,
                    'error' => $e->getMessage(),
                ];
            }
        }

        event(new VouchersCreated($successfulVouchers, $failedVouchers, $this->user));
    }
}
