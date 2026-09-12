<?php

namespace App\Http\Controllers;

use App\Enums\ServicesEnum;
use App\Models\Service;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Masmerise\Toaster\Toaster;

class OAuthController extends Controller
{
    public function redirect(string $provider, string $service)
    {
        if ($provider === 'google') {
            $state = base64_encode(json_encode([
                'provider' => $provider,
                'service' => $service,
            ]));

            return Socialite::driver($provider)
                ->scopes(['https://www.googleapis.com/auth/'.$service])
                ->with([
                    'access_type' => 'offline',
                    'prompt' => 'consent',
                    'state' => $state,
                ])
                ->redirect();
        }

        return redirect()->back()->withErrors(['error' => 'Unsupported provider']);
    }

    public function callback(Request $request)
    {
        $rawState = $request->input('state');

        if (! $rawState) {
            abort(400, 'State parameter missing from OAuth response.');
        }

        $stateData = json_decode(base64_decode($rawState), true);

        if (empty($stateData['provider']) || empty($stateData['service'])) {
            abort(400, 'Invalid state data from OAuth response.');
        }

        $serviceUser = Socialite::driver($stateData['provider'])
            ->stateless()
            ->user();

        $accessToken = $serviceUser->token;
        $refreshToken = $serviceUser->refreshToken;

        $service = Service::query()->updateOrCreate(
            [
                'user_id' => auth()->id(),
                'name' => ServicesEnum::fromProvider($stateData['provider'], $stateData['service']),
            ],
            [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken ?? Service::where('user_id', auth()->id())
                    ->where('name', $stateData['service'])
                    ->value('refresh_token'),
                'is_available' => true,
                'is_active' => true,
            ]
        );

        Toaster::success("{$service->name->label()} connected successfully.");

        return redirect()->route('chat')->with('service-connected', true);
    }
}
