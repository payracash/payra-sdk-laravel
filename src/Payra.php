<?php
namespace Payra;

use Payra\PayraSignature;
use Payra\PayraOrderService;
use Payra\PayraUtils;

class Payra
{
    public function generate(array $payload): string
    {
        return (new PayraSignature)->generate(
            $payload['network'],
            $payload['token_address'],
            $payload['order_id'],
            $payload['amount_wei'],
            (int)$payload['timestamp'],
            $payload['payer_address']
        );
    }

    public function getDetails(array $payload): array
    {
        return (new PayraOrderService)->getDetails(
            $payload['network'],
            $payload['order_id']
        );
    }

    public function isPaid(array $payload): array
    {
        return (new PayraOrderService)->isPaid(
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
