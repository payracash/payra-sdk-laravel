<?php

namespace Payra;

use Payra\PayraSignatureGenerator;
use Payra\PayraOrderVerification;
use Payra\PayraUtils;

class Payra
{
    public function generateSignature(array $payload): string
    {
        return (new PayraSignatureGenerator)->generateSignature(
            $payload['network'],
            $payload['token_address'],
            $payload['order_id'],
            $payload['amount_wei'],
            (int)$payload['timestamp'],
            $payload['payer_address']
        );
    }

    public function orderVerification(array $payload): array
    {
        return (new PayraOrderVerification)->isOrderPaid(
            $payload['network'],
            $payload['order_id']
        );
    }

    public function convertToUSD(array $payload): float
    {
        return (new PayraUtils)->convertToUSD(
            $payload['amount'],
            $payload['from_currency']
        );
    }

}
