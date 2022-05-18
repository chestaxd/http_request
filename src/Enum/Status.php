<?php

namespace App\Enum;

enum Status: int
{
    case PENDING = 0;
    case ERROR = -1;
    case DONE = 1;
}