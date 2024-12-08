<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\DeleteOldPosts;
use App\Jobs\RequestUserData;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(DeleteOldPosts::class)->daily();

Schedule::job(RequestUserData::class)->everySixHours();