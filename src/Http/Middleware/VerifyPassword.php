<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerifyPassword
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|JsonResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $request->validate([
            'currentPassword' => 'required',
        ]);

        $user = Auth::user();
        if ($user == null) {
            throw new Exception('User not found');
        }
        $password = $request->input('currentPassword');

        // Perform password verification against the user's actual stored password
        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'currentPassword' => ['The provided password does not match your current password.'],
            ]);
        }
        return $next($request);
    }
}
