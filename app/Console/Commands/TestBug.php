<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Client;

class TestBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-bug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Client $client)
    {
        $thread = $client->threads()->create([]);

        $message = $client->threads()->messages()->create($thread->id, [
            'role' => 'user',
            'content' => 'Explain deep learning to a 5 year old.',
        ]);

        $response = $client->threads()->messages()->modify(
            threadId: $thread->id,
            messageId: $message->id,
            parameters: [
                'metadata' => [
                    'key' => 'value',
                ],
            ]
        );

        dd($response);
    }
}
