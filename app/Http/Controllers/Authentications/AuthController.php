<?php

namespace App\Http\Controllers\Authentications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

// https://medium.com/@hendriks96/api-authenticate-how-to-authenticate-api-using-laravel-sanctum-c4eeaa99b472
// Laravel Sanctum for Authorization: Bearer <token>
// Login via Web using LDAP
class AuthController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * @unauthenticated
     * Handle an incoming authentication request
     */
    public function login(LoginRequest $request)
    {
        // use LDAP
        $credentials = [
            'mail' => $request->input('email'), // LDAP use key 'mail'
            'password' => $request->input('password'),
        ];
        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) { // try LDAP login
            $user = Auth::guard('web')->user();
        } else if (Auth::guard('local')->attempt($request->only('email', 'password'), $remember)) { // fallback to local login
            $user = Auth::guard('local')->user();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($request->is('api/**')) {
            // For API: return token
            $token = $user->createToken('API Token')->plainTextToken; // for Sanctum Bearer token
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'token' => $token,
            ]);
        } else {
            // For web: session-based
            $request->session()->regenerate();
            Auth::login($user, $remember); // set session for CSRF + cookie mode

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
            ]);
        }
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $data['is_local'] = true;
        $user = $this->userRepository->createWithRoles($data, 'viewer');

        event(new Registered($user));
        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'Register successful!',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->is('api/**')) {
            // API: revoke current token
            Auth::user()->currentAccessToken()?->delete();
            return response()->json([
                'status' => true,
                'message' => 'Logged out!',
            ]);
        } else {
            // Web: session logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Logged out!',
            ]);
        }
    }

    public function loginView()
    {
        return view('authentications.login');
    }

    public function registerView()
    {
        return view('authentications.register');
    }
}
