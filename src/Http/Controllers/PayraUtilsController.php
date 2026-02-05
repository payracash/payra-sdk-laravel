<?php
namespace Payra\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Payra\Facades\Payra;

class PayraUtilsController extends Controller
{
    public function convertToUSD(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'from_currency' => 'required|string',
        ]);

        try {
            $amountInUSD = Payra::convertToUSD($request->all());
            return response()->json([
                'result' => $amountInUSD,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to convert currency.',
            ], 500);
        }
    }
}
