<?php

namespace Payra;

use Payra\PayraSignatureGenerator;

class Payra
{
    public function sign(array $payload): string
    {
        $generator = new PayraSignatureGenerator();

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
