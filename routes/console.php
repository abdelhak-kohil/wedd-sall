<?php

use App\Models\Reservation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reservations:expire-holds', function () {
    $count = Reservation::where('status', Reservation::STATUS_HOLD)
        ->whereNotNull('hold_expires_at')
        ->where('hold_expires_at', '<=', now())
        ->update(['status' => Reservation::STATUS_EXPIRED]);

    $this->info("Expired {$count} temporary hold(s).");
})->purpose('Expire reservations with past TTL');

Schedule::command('reservations:expire-holds')->hourly();
