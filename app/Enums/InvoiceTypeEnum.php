<?php

namespace App\Enums;

enum InvoiceTypeEnum: string
{
    case MONTHLY = 'monthly';
    case ONE_TIME = 'one_time';
}
