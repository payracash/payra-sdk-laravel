<?php

namespace Payra\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Payra\Facades\Payra;

class SignatureController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'network' => 'required|string',
            'tokenAddress' => 'required|string',
            'orderId' => 'required|string',
            'amount' => 'required|string',
            'timestamp' => 'required|integer',
            'payerAddress' => 'required|string',
        ]);

        try {
            $signature = Payra::sign($request->all());

            return response()->json([
                'status' => 'success',
                'signature' => $signature,
                'message' => 'Signature generated successfully.',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate signature.',
            ], 500);
        }
    }
}
