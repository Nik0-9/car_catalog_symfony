<?php

namespace App\Enum;

enum CarStatus: string
{
    case AVAILABLE = 'available';
    case SOLD = 'sold';
}