<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocsController extends Controller
{
    private static string $routeLogin = 'docs.login';

    public function login()
    {
        return view(static::$routeLogin);
    }

    public function logout(Request $request)
    {
        if ($request->user('web')) {
            Auth::guard('web')->logout();
        }

        return redirect()->route(static::$routeLogin);
    }

    public function auth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|email',
            'password' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route(static::$routeLogin)
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = [
            'email' => $request->input('login'),
            'password' => $request->input('password')
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            /** @var User $user */
            $user = $request->user('web');
            if ($user->hasPermissionTo('docs.view', 'web')) {
                return redirect()->route('docs.index');
            }
            Auth::guard('web')->logout();
        }

        return $this->redirectFailure();
    }

    protected function redirectFailure(): RedirectResponse
    {
        return redirect()
            ->route(static::$routeLogin)
            ->withInput()
            ->withErrors(['failure' => 'Логин или пароль не корректны.']);
    }
}
