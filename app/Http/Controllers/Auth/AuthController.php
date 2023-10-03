<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthUserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePassword as ChangePasswordRequest;
use App\Http\Requests\Auth\Forgot as ForgotRequest;
use App\Mail\Forgot as ForgotMail;
use App\Repositories\User\User as UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Passport\RefreshTokenRepository;


class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function forgot(ForgotRequest $request): Response
    {
        $user = $this->userRepository->byLogin($request->email);
        if (!$user || $user->external) {
            throw new AuthUserNotFoundException();
        }

        $user->update(['confirm_token' => Str::random(40)]);
        if (!is_null($user->mainData->email)) {
            Mail::to($user->mainData->email)->send(new ForgotMail($user->confirm_token));
        }

        return response(['success' => true]);
    }

    /**
     * @throws AuthUserNotFoundException
     */
    public function changePassword(ChangePasswordRequest $request): Response
    {
        $user = $this->userRepository->byConfirmToken($request->key);
        if (!$user) {
            throw new AuthUserNotFoundException();
        }

        $user->setPassword($request->password);
        $user->clearConfirmToken();

        return response(['success' => true]);
    }

    public function logout(RefreshTokenRepository $refreshTokenRepository, Request $request): Response
    {
        $token = $request->user()->token();
        $token->revoke();
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

        return response(['success' => true]);
    }
}
