<?php

namespace Payra\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Payra\Facades\Payra;

class PayraVerificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'network' => 'required|string',
            'order_id' => 'required|string',
        ]);

        try {
            $verify = Payra::orderVerification($request->all());
            return response()->json([
                'result' => $verify,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify order.',
            ], 500);
        }
    }
}
