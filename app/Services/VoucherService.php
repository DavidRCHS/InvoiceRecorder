<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate, array $filters): LengthAwarePaginator
    {
        $query = Voucher::with(['lines', 'user'])
            ->where('user_id', auth()->id())
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);

        if (!empty($filters['series'])) {
            $query->where('series', $filters['series']);
        }

        if (!empty($filters['number'])) {
            $query->where('number', $filters['number']);
        }

        if (!empty($filters['voucher_type'])) {
            $query->where('voucher_type', $filters['voucher_type']);
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        return $query->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $id = (string) $xml->xpath('//cbc:ID')[0];
        [$series, $number] = explode('-', $id);
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode ')[0] ?? null;
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? null;

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
            'series' => $series,
            'number' => $number,
            'voucher_type' => $voucherType,
            'currency' => $currency,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    /**
     * Process existing voucher to extract and save additional fields from xml_content.
     *
     * @param Voucher $voucher
     * @return void
     */
    public function processExistingVoucher(Voucher $voucher): void
    {
        $xml = new SimpleXMLElement($voucher->xml_content);

        // Extract additional fields
        $id = (string) $xml->xpath('//cbc:ID')[0];
        [$series, $number] = explode('-', $id);
        $voucherType = (string) $xml->xpath('//cbc:InvoiceTypeCode ')[0] ?? null;
        $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? null;

        // Update voucher with additional fields
        $voucher->update([
            'series' => $series,
            'number' => $number,
            'voucher_type' => $voucherType,
            'currency' => $currency,
        ]);
    }
}
