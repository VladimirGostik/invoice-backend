<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Observers\InvoiceObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Http\Request;
use Knuckles\Scribe\Scribe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Scribe callback pre autentifikáciu pri generovaní dokumentácie
        if ($this->app->runningInConsole() && class_exists(\Knuckles\Scribe\Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request) {
                // ✅ Odstránený typehint pre $endpointData - nechajte PHP aby to samo zistilo
                try {
                    // Skontrolujeme či existuje tabuľka users a obsahuje dáta
                    if (Schema::hasTable('users')) {
                        $user = User::first();

                        if ($user) {
                            // Použijeme JWT guard namiesto session guard
                            $token = auth('api')->login($user);
                            $request->headers->set('Authorization', 'Bearer ' . $token);
                        }
                    }
                } catch (\Exception $e) {
                    // Ticho ignorujeme chyby pri generovaní dokumentácie
                    logger()->warning('Scribe auth setup failed: ' . $e->getMessage());
                }
            });
        }

        // Observer pre faktúry
        Invoice::observe(InvoiceObserver::class);
    }
}
