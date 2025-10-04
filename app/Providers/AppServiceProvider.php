<?php

namespace App\Providers;

use App\Models\OneTimeInvoice;
use App\Observers\InvoiceObserver;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
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
        if (class_exists(\Knuckles\Scribe\Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request, ExtractedEndpointData $endpointData) {
                $token = auth()->login(User::first());
                $request->headers->add(["Authorization" => "Bearer $token"]);
            });
        }
        OneTimeInvoice::observe(InvoiceObserver::class);
    }
}
