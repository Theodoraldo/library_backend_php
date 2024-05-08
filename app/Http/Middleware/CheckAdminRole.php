<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->role == 'Admin') {
            return $next($request);
        }

        return response()->json(['status' => Response::HTTP_FORBIDDEN, 'message' => 'Sorry you are not permitted to undertake this action. Contact the Admin on theodoraldo@gmail.com !!!'], Response::HTTP_FORBIDDEN);
    }
}
