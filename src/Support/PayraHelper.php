<?php

namespace Payra\Support;

class PayraHelper
{
    public static function rpcUrls(string $network): array
    {
        $network = strtoupper($network);
        $urls = [];

        for ($i = 1; $i <= 1000; $i++) {
            $value = env("PAYRA_{$network}_RPC_URL_{$i}");
            if (!$value) break;
            $urls[] = trim($value);
        }

        return $urls;
    }
}
