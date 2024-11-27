<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;
use App\Services\VoucherService;

class ProcessExistingVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-existing-vouchers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract and save additional fields from xml_content in vouchers';

    /**
     * Execute the console command.
     */
    public function __construct(private readonly VoucherService $voucherService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Processing existing vouchers...');

        // Fetch vouchers with missing fields
        $vouchers = Voucher::whereNull('series')
            ->orWhereNull('number')
            ->orWhereNull('voucher_type')
            ->orWhereNull('currency')
            ->get();

        foreach ($vouchers as $voucher) {
            try {
                // Process each voucher using the service
                $this->voucherService->processExistingVoucher($voucher);
                $this->info("Voucher ID {$voucher->id} processed successfully.");
            } catch (\Exception $e) {
                $this->error("Failed to process Voucher ID {$voucher->id}: " . $e->getMessage());
            }
        }

        $this->info('Processing completed.');
    }
}
