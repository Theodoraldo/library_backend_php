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

        return response()->json(['status' => Response::HTTP_FORBIDDEN, 'message' => 'Ooooooop!!! you are not permitted or authorized to undertake this action. Sorry contact the Admin on theodoraldo@gmail.com !!!'], Response::HTTP_FORBIDDEN);
    }
}
