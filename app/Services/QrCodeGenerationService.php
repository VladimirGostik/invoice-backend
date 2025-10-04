<?php

namespace App\Services;

use rikudou\SkQrPayment\QrPayment;
use Rikudou\Iban\Iban\IBAN;
use rikudou\SkQrPayment\Payment\QrPaymentOptions;

class QrCodeGenerationService
{
    /**
     * Generuje QR kód ako base64 reťazec pre platbu.
     *
     * @param string $iban IBAN účtu príjemcu
     * @param float $amount Suma platby
     * @param string $variableSymbol Variabilný symbol
     * @param string|null $dueDate Dátum splatnosti (YYYY-MM-DD)
     * @param string|null $swift SWIFT kód (voliteľné)
     * @param string $payeeName Názov príjemcu
     * @return string Base64 reťazec (data:image/png;base64,...)
     */
    public function generate(
        string $iban,
        float $amount,
        string $variableSymbol,
        ?string $dueDate = null,
        ?string $payeeName = null,
    ): string {
        $payment = new QrPayment(new IBAN($iban));

        $options = [
            QrPaymentOptions::AMOUNT => $amount,
            QrPaymentOptions::CURRENCY => 'EUR',
            QrPaymentOptions::VARIABLE_SYMBOL => $variableSymbol,
            QrPaymentOptions::PAYEE_NAME => $payeeName,
        ];

        if ($dueDate) {
            $options[QrPaymentOptions::DUE_DATE] = new \DateTime($dueDate);
        }
        $payment->setOptions($options);

        $qrCode = $payment->getQrCode();
        return $qrCode->getDataUri(); 
    }
}