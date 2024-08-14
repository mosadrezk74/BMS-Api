<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');
        if ($user && $user->status == 1) {
            return Response()->json(
                [
                    'message' => 'Forbidden',
                    'errors' => [
                        'access' => "Forbidden. You must be an administrator."
                    ]
                ], 403
            );
        }
        return $next($request);
    }
}
