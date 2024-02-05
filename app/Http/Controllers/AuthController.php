<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Http;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function goToLineLogin(Request $request)
    {
        $lineAuthUrl = 'https://access.line.me/oauth2/v2.1/authorize';
        $queryParams = [
            'response_type' => 'code',
            'client_id' => env('LINE_CLIENT_ID'),
            'redirect_uri' => env('LINE_REDIRECT_URI'),
            'state' => csrf_token(),
            'scope' => 'profile openid',
        ];

        $redirectUrl = $lineAuthUrl . '?' . http_build_query($queryParams);

        return redirect($redirectUrl);
    }

    public function lineCallback(Request $request)
    {
        if (!hash_equals(csrf_token(), $request->input('state'))) {
            abort(403, 'Unauthorized action.');
        }

        $response = Http::asForm()
            ->post('https://api.line.me/oauth2/v2.1/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->input('code'),
                'redirect_uri' => 'https://621b-60-251-132-249.ngrok-free.app/lineCallback',
                'client_id' => env('LINE_CLIENT_ID'),
                'client_secret' => env('LINE_CLIENT_SECRET'),
            ]);

        $lineAccessToken = $response->json()['access_token'];
        $lineInfo = Http::withHeaders([
            'Authorization' => 'Bearer ' . $lineAccessToken,
        ])->get('https://api.line.me/v2/profile')->json();

        $user = User::firstOrCreate(
            ['line_id' => $lineInfo['userId']],
            ['name' => $lineInfo['displayName']]
        );

        Auth::login($user);

        session(['lineInfo' => $lineInfo]);

        return redirect(RouteServiceProvider::HOME);
    }
}
