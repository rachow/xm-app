<?php
/**
 *  @author: $rachow
 *  @copyright: XM App 2023
 *
 *  Attach unique request Id to logging.
 *
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AssignUniqueRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $requestId = (string) Str::uuid();
        Log::withContext([
            'request-id' => $requestId,
            'request-time' => Carbon::now()
        ]);

        return $next($request)->header('request-id', $requestId);
    }
}
