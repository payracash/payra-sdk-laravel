<?php

namespace Payra;

use Payra\SignatureGenerator;

class Payra
{
    public function sign(array $payload): string
    {
        $generator = new SignatureGenerator();

        return $generator->generateSignature(
            $payload['network'],
            $payload['tokenAddress'],
            $payload['orderId'],
            $payload['amount'],
            (int)$payload['timestamp'],
            $payload['payerAddress']
        );
    }
}
