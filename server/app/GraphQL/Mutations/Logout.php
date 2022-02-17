<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

class Logout
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        if (!Auth::user()) {
            throw new AuthenticationException("You have to be logged in to log out");
        }
        Auth::user()->currentAccessToken()->delete();
        return "You have been logged out";
    }
}
