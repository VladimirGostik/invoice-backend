<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Custom messages for AuthController
    'user_not_found' => 'User with this email does not exist.',
    'wrong_password' => 'The provided password is incorrect.',
    'invalid_credentials' => 'Invalid credentials.',
    'unauthorized_access' => 'Unauthorized access.',

    // Password reset messages
    'reset' => 'Your password has been reset.',
    'sent' => 'We have emailed your password reset link.',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => 'We can\'t find a user with that email address.',
];
