<?php

namespace App\Enums;

enum MessageRole: string
{
    case Assistant = 'assistant';
    case User = 'user';
}
