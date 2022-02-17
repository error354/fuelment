<?php

namespace App\GraphQL\Mutations;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Exceptions\GenericException;

class ResetPassword
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $status = Password::reset(
            $args, 
            function ($user, $password) {
                $user->forceFill(['password' => $password])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
        switch ($status) {
            case "passwords.user":
                $response = "There is no account with this email address.";
                break;
            case "passwords.throttled":
                $response = "Too many attempts. Try again after some time.";
                break;
            case "passwords.reset":
                $response = "The new password has been set. Now you can use it to log in.";
                break;
            case "passwords.token":
                $response = "Reset password token is invalid. Generete a new reset password link.";
                break;
            default:
                throw new GenericException("Unexpected error occured during sending an email with password reset link");
        }
        return $response;
    }
}
