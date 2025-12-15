<?php
namespace Payra\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PayraAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = config('payra.api_key');
        $header = $request->header('X-Payra-Key');

        if ($key && $header === $key) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
