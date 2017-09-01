<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Display a page with different login providers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('layout')->with([
            'title' => 'Login',
            'component' => 'login',
            'props' => [
                'auth-error' => session('auth_error')
            ]
        ]);
    }

    /**
     * Redirects the user to the login provider.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from a login provider.
     *
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($provider)
    {
        try {
            $oAuthUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return $this->goBackWithError('Unable to get user data');
        }

        if (! $oAuthUser->getEmail()) {
            return $this->goBackWithError('Email is not available');
        }

        $this->authenticate($oAuthUser, $provider);

        return redirect('/');
    }

    /**
     * Redirect the user to the login page with an error message.
     *
     * @param  string $error
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function goBackWithError($error)
    {
        return redirect()->route('login')->with(['auth_error' => $error]);
    }

    /**
     * Authenticates a app user using information from an OAuthUser.
     *
     * @param  string $provider
     * @param  \Laravel\Socialite\Contracts\User $oAuthUser
     */
    protected function authenticate($oAuthUser, $provider)
    {
        Auth::login(User::firstOrCreate([
            'email' => $oAuthUser->getEmail(),
            'provider' => $provider,
        ]));
    }
}
