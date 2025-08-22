<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification;

class CustomOneTimePasswordNotification extends OneTimePasswordNotification implements ShouldQueue
{
}
