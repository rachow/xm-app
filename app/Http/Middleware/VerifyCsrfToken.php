<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Auth;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * $rachow - added as additional for checks and control
     * of the except array.
     *
     * @param Illuminate\Http\Request
     * @param \Closure
     */
    public function handle(Request $request, Closure $next)
    {
        // is the route logout
        if ($request->route()->named('logout')) {
            // verify user is guest, did the user get logged back in via 'RememberMe'
            // cookie
            if (!Auth::check() || Auth::guard()->viaRemember()) {
                $this->except[] = route('logout');
            }
        }
        return parent::handle($request, $next);
    }
}
