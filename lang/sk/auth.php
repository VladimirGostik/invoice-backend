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

    'failed' => 'Tieto údaje sa nezhodujú s našimi záznamami.',
    'password' => 'Zadané heslo je nesprávne.',
    'throttle' => 'Príliš veľa pokusov o prihlásenie. Skúste znovu za :seconds sekúnd.',

    // Custom messages pre AuthController
    'user_not_found' => 'Používateľ s týmto emailom neexistuje.',
    'wrong_password' => 'Zadané heslo je nesprávne.',
    'invalid_credentials' => 'Neplatné prihlasovacie údaje.',
    'unauthorized_access' => 'Neautorizovaný prístup.',

    // Password reset messages
    'reset' => 'Vaše heslo bolo obnovené.',
    'sent' => 'Poslali sme vám odkaz na obnovenie hesla.',
    'throttled' => 'Počkajte pred ďalším pokusom.',
    'token' => 'Token na obnovenie hesla je neplatný.',
    'user' => 'Nenašli sme používateľa s týmto emailom.',
];
