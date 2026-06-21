<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('owner_id')) {
            return redirect()->route('owner.login');
        }

        return $next($request);
    }
}
