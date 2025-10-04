<?php

namespace App\Enums;

enum AuditLogSeverityEnum: int
{
    case INFO = 0;
    case WARNING = 1;
    case ERROR = 2;
    case CRITICAL = 3;
}
