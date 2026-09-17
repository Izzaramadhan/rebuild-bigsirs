<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Draft = 'draft';
    case Registered = 'registered';
    case Cancelled = 'cancelled';
}
