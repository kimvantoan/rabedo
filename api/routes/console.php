<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Chức năng chạy Định Kì: Tự động lên một bài báo Du lịch mỗi ngày lúc 8:00 Sáng
use Illuminate\Support\Facades\Schedule;

Schedule::command('ai:generate-article')->dailyAt('08:00');
