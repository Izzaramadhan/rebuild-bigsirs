<?php

namespace App\Enums;

enum GuarantorType: string
{
    case Umum = 'UMUM';
    case Bpjs = 'BPJS';
    case Private = 'PRIVATE';
}
