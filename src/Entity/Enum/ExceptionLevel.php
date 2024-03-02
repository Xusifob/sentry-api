<?php

namespace App\Entity\Enum;

enum ExceptionLevel: string
{
    case  DEBUG = 'debug';

    case  INFO = 'info';

    case  WARNING = 'warning';

    case  ERROR = 'error';
    case  FATAL = 'fatal';

    case  CRITICAL = 'critical';
}
