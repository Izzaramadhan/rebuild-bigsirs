<?php

namespace App\Enums;

enum AdmissionStatus: string
{
    case Waiting = 'waiting';
    case Admitted = 'admitted';
    case InService = 'in_service';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
