<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthUserNotFoundException;
use App\Exceptions\AuthUserNotVerifiedException;
use App\Factories\User as UserFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePassword as ChangePasswordRequest;
use App\Http\Requests\Auth\CheckEmail as CheckEmailRequest;
use App\Http\Requests\Auth\Confirm as ConfirmRequest;
use App\Http\Requests\Auth\Forgot as ForgotRequest;
use App\Http\Requests\Auth\Registration as RegistrationRequest;
use App\Http\Requests\Auth\RetryActivate as RetryActivateRequest;
use App\Mail\Forgot as ForgotMail;
use App\Mail\Registration as RegistrationMail;
use App\Mail\RetryActivate as RetryActivateMail;
use App\Repositories\User\User as UserRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registration(RegistrationRequest $request): Response
    {
        $user = UserFactory::fromRegistrationRequest($request);
        Mail::to($user)->send(new RegistrationMail($user->confirm_token));
        return response(['success' => true]);
    }

    public function confirm(ConfirmRequest $request): Response
    {
        $user = $this->userRepository->byConfirmToken($request->key);
        if (!$user) {
            throw new AuthUserNotFoundException();
        }

        $user->update(['email_verified_at' => now()]);
        $user->clearConfirmToken();

        return response(['success' => true]);
    }

    public function retryActivate(RetryActivateRequest $request): Response
    {
        $user = $this->userRepository->byEmail($request->email);
        if (!$user) {
            throw new AuthUserNotFoundException();
        }

        if ($user->hasVerifiedEmail()) {
            throw new UnprocessableEntityHttpException(__('auth.user_is_verified', ['user' => $request->email]));
        }

        Mail::to($user)->send(new RetryActivateMail($user->confirm_token));

        return response(['success' => true]);
    }

    public function forgot(ForgotRequest $request): Response
    {
        $user = $this->userRepository->byEmail($request->email);
        if (!$user) {
            throw new AuthUserNotFoundException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new AuthUserNotVerifiedException($request->email);
        }

        $user->update(['confirm_token' => Str::random(40)]);
        Mail::to($user)->send(new ForgotMail($user->confirm_token));

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

    public function checkEmail(CheckEmailRequest $request): Response
    {
        return response(['success' => boolval($this->userRepository->byEmail($request->email))]);
    }
}
