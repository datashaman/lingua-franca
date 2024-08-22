<?php

namespace App\Observers;

use App\Interfaces\Threadable;
use OpenAI\Client;

class CreateThread
{
    public function __construct(protected Client $openai)
    {
    }

    public function creating(Threadable $threadable)
    {
        $threadable->thread_id = $this->openai->threads()->create([])->id;
    }
}
