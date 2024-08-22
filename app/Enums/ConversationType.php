<?php

namespace App\Enums;

enum ConversationType: string {
    case DirectMessage = 'direct-message';
    case GroupMessage = 'group-message';
    case PublicChannel = 'public-channel';
    case PrivateChannel = 'private-channel';
}
