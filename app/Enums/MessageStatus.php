<?php

namespace App\Enums;

enum MessageStatus: string
{
    case Queued       = 'queued';
    case Sent         = 'sent';
    case Delivered    = 'delivered';
    case Failed       = 'failed';
    case Undelivered  = 'undelivered';
    case Expired      = 'expired';
    case Rejected     = 'rejected';
    case Blacklisted  = 'blacklisted';
    case Unknown      = 'unknown';
}
