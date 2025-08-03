<?php

namespace App\Services;

use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Models\OneTimeInvoice;
use App\Models\Company;
use App\Models\Invoice;

class InvoiceService
{
    protected $invoiceRepo;
    public function __construct(InvoiceRepositoryInterface $invoiceRepo)
    {
        $this->invoiceRepo = $invoiceRepo;
    }

   public function generateInvoiceNumber(int $company_id, int $billing_year): string
    {
        $lastInvoice = OneTimeInvoice::where('company_id', $company_id)
            ->where('billing_year', $billing_year)
            ->orderBy('invoice_number', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -5) + 1 : 1;
        return sprintf('%s%05d', date('Y'), $nextNumber); // ex. "202500001"
    }

    /**
     * Vráti posledné číslo faktúry pre firmu.
     */
    public function getLastInvoiceNumber(int $company_id, int $billing_year): ?string
    {
        $lastInvoice = OneTimeInvoice::where('company_id', $company_id)
            ->where('billing_year', $billing_year)
            ->orderBy('invoice_number', 'desc')
            ->first();

        return $lastInvoice ? $lastInvoice->invoice_number : null;
    }

    /**
     * generateVariableSymbol
     */
    public function generateVariableSymbol(string $invoiceNumber): string
    {
        // Extract the last 4 digits from the invoice number
        $numberPart = substr($invoiceNumber, -4);
        // Combine with the year or another identifier (max. 10 digits)
        return sprintf('%s%s', date('Y'), str_pad($numberPart, 6, '0', STR_PAD_LEFT)); // ex. "2025000001"
    }

    public function createInvoice($data){
        $company = Company::findOrFail($data['company_id']);
        $residentialCompany = Company::findOrFail($data['residential_company_id']);
        $companyCustomization = $company->companyCustomization; 

        $data['invoice_number'] = $this->generateInvoiceNumber($data['company_id'], $data['billing_year']);
        $data['variable_symbol'] = $this->generateVariableSymbol($data['invoice_number']);
        $data['residential_company_name'] = $residentialCompany->company_name;
        $data = array_merge(
            $data,
            $companyCustomization ? $companyCustomization->snapshot() : [],
            $company->snapshot()
        );
        return $this->invoiceRepo->createOneTime($data);
    }
}