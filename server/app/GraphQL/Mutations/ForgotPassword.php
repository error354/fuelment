<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;
use Nuwave\Lighthouse\Exceptions\GenericException;

class ForgotPassword
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        function setResetPasswordUrl(string $url): void
        {
            ResetPassword::createUrlUsing(function (CanResetPassword $notifiable, string $token) use ($url) {
                return transformUrl($notifiable, $token, $url);
            });
        }

        function transformUrl(CanResetPassword $notifiable, string $token, string $url): string
        {
            return str_replace([
                '__EMAIL__',
                '__TOKEN__',
            ], [
                $notifiable->getEmailForPasswordReset(),
                $token,
            ], $url);
        }

        setResetPasswordUrl($args['url']);
        $email = $args['email'];
        $status = Password::sendResetLink(['email' => $email]);
        switch ($status) {
            case "passwords.user":
                $response = "There is no account with this email address.";
                break;
            case "passwords.throttled":
                $response = "Too many attempts. Try again after some time.";
                break;
            case "passwords.sent":
                $response = "An email with password reset link has been sent.";
                break;
            default:
                throw new GenericException("Unexpected error occured during sending an email with password reset link");
        }

        return $response;
    }
}
