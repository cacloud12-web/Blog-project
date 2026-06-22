<?php

use Illuminate\Foundation\Inspiring;//display inspiring quote
use Illuminate\Support\Facades\Artisan;//define artisan 

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');//purpose of the comamnd
