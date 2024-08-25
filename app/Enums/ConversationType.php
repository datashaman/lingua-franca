<?php

namespace App\Enums;

enum ConversationType: string
{
    case DirectMessage = 'direct-message';
    case GroupMessage = 'group-message';
    case PrivateChannel = 'private-channel';
    case PublicChannel = 'public-channel';
}
