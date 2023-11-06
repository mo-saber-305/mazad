<?php

namespace App\Http\Middleware;

use Closure;

class CheckStatusMerchantApi
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth('api_merchant')->user();

        if ($user->status && $user->ev && $user->sv && $user->tv) {
            return $next($request);
        } else {
            return redirect()->route('api.merchant.authorization');
        }

    }
}
