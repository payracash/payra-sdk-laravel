<?php
namespace Payra\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Payra\Facades\Payra;

class PayraOrderServiceController extends Controller
{
    protected function validateBase(Request $request)
    {
        return $request->validate([
            'network' => 'required|string',
            'order_id' => 'required|string',
        ]);
    }

    public function getDetails(Request $request)
    {
        $data = $this->validateBase($request);

        try {
            $result = Payra::getDetails($data);

            return response()->json([
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get order status.',
            ], 500);
        }
    }

    public function isPaid(Request $request)
    {
        $data = $this->validateBase($request);

        try {
            $result = Payra::isPaid($data);

            return response()->json([
                'result' => $result,
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
