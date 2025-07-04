<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChooseScribeUserType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:choose-scribe-user-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // if app local , ask, if no , log in as first user
        // login superadmin for scribe documentation

        $user = auth()->login(User::find(1));
        dd($user);
    }
}