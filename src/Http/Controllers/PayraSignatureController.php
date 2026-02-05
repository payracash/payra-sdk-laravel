<?php
namespace Payra\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Payra\Facades\Payra;

class PayraSignatureController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'network' => 'required|string',
            'token_address' => 'required|string',
            'order_id' => 'required|string',
            'amount_wei' => 'required',
            'timestamp' => 'required|integer',
            'payer_address' => 'required|string',
        ]);

        try {
            $signature = Payra::generate($request->all());
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
