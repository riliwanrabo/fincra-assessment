<?php

namespace App\Enums;

enum Status: string
{
    case SUCCESSFUL = 'successful';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
}
