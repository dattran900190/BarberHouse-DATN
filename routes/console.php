<?php

use Illuminate\Foundation\Inspiring;
use App\Jobs\CleanExpiredAppointments;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CleanExpiredAppointments)->everyFiveMinutes();