<?php

namespace App\Providers;

use App\Models\OneTimeInvoice;
use App\Observers\InvoiceObserver;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Scribe;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('token', function () {
            $token = request()->bearerToken();
            if ($token) {
                $payload = JWTAuth::parseToken()->getPayload($token)->toArray();
                return (object)$payload;
            }

            return null;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Scribe konfigurácia s bezpečným handling
        if (class_exists(\Knuckles\Scribe\Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request, ExtractedEndpointData $endpointData) {
                try {
                    // Skontrolujeme či existuje tabuľka users a obsahuje dáta
                    if (Schema::hasTable('users')) {
                        $user = User::first();

                        if ($user) {
                            // Použijeme JWT guard namiesto session guard
                            $token = auth('api')->login($user);
                            if ($token) {
                                $request->headers->add(["Authorization" => "Bearer $token"]);
                            }
                        } else {
                            // Ak neexistujú používatelia, vytvoríme dočasného pre dokumentáciu
                            $this->createTempUserForScribe($request);
                        }
                    }
                } catch (\Exception $e) {
                    // Ignorujeme chyby pri generovaní dokumentácie
                    logger()->warning('Scribe authentication failed: ' . $e->getMessage());
                }
            });
        }

        // Observer pre faktúry
        OneTimeInvoice::observe(InvoiceObserver::class);
    }

    /**
     * Vytvorí dočasného používateľa pre Scribe dokumentáciu
     */
    private function createTempUserForScribe(Request $request): void
    {
        try {
            // Vytvoríme dočasného používateľa len pre generovanie dokumentácie
            $tempUser = User::make([
                'id' => 1,
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'state' => \App\Enums\UserStateEnum::ACTIVE,
                'email_verified_at' => now(),
            ]);

            // Vygenerujeme fake JWT token
            $payload = [
                'iss' => config('app.url'),
                'iat' => time(),
                'exp' => time() + 3600,
                'nbf' => time(),
                'jti' => 'fake-jwt-for-scribe',
                'sub' => '1',
                'prv' => 'fake'
            ];

            $fakeToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0IiwiaWF0IjoxNjk1NzEyMDUxLCJleHAiOjE2OTU3MTU2NTEsIm5iZiI6MTY5NTcxMjA1MSwianRpIjoiZmFrZS1qd3QtZm9yLXNjcmliZSIsInN1YiI6IjEiLCJwcnYiOiJmYWtlIn0.fake-signature-for-documentation';

            $request->headers->add(["Authorization" => "Bearer $fakeToken"]);

        } catch (\Exception $e) {
            logger()->warning('Failed to create temp user for Scribe: ' . $e->getMessage());
        }
    }
}
