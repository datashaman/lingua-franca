<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Arr;
use OpenAI\Client;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

use function React\Promise\resolve;

class BotService
{
    public function __construct(protected Client $openai) {}

    public function createBot(
        User $user,
        string $name,
        string $handle,
        string $instructions,
        string $description = '',
        ?string $model = null,
        ?string $locale = null
    ): Bot {
        $model ??= config('services.openai.model');
        $locale ??= config('app.locale');

        return $user->bots()->create([
            'name' => $name,
            'handle' => $handle,
            'instructions' => $instructions,
            'description' => $description,
            'model' => $model,
            'locale' => $locale,
        ]);
    }

    public function generateResponse(
        Bot $bot,
        Conversation $conversation
    ): ThreadMessageResponse {
        logger()->debug('Generating response for conversation', [
            'conversation' => $conversation->id,
        ]);

        $stream = $this->openai->threads()->runs()->createStreamed(
            threadId: $conversation->thread_id,
            parameters: [
                'assistant_id' => $bot->assistant_id,
            ]
        );

        $message = null;

        do {
            foreach ($stream as $response) {
                switch ($response->event) {
                    case 'thread.message.completed':
                        $message = $response->response;
                        break;
                    case 'thread.run.created':
                    case 'thread.run.queued':
                    case 'thread.run.completed':
                    case 'thread.run.cancelling':
                        $run = $response->response;
                        break;
                    case 'thread.run.expired':
                    case 'thread.run.cancelled':
                    case 'thread.run.failed':
                        $run = $response->response;
                        break 3;
                    case 'thread.run.requires_action':
                        $toolOutputs = [];

                        foreach (
                            $response->respoonse->requiredAction
                                ->submitToolOutputs->toolCalls as $toolCall
                        ) {
                            $definition = config(
                                "tools.{$toolCall->function->name}"
                            );

                            $toolClass = Arr::pull($definition, 'toolClass');
                            $toolObject = resolve($toolClass);

                            $arguments = json_decode(
                                $toolCall->function->arguments,
                                true
                            );

                            $toolOutputs[] = [
                                'tool_call_id' => $toolCall->id,
                                'output' => $toolObject->handle(
                                    $conversation,
                                    $arguments
                                ),
                            ];
                        }

                        // Something here

                        // Overwrite the stream with the new stream started by submitting the tool outputs
                        $stream = $this->openai
                            ->threads()
                            ->runs()
                            ->submitToolOutputsStreamed(
                                threadId: $run->threadId,
                                runId: $run->id,
                                parameters: [
                                    'tool_outputs' => $toolOutputs,
                                ]
                            );
                        break;
                }
            }
        } while ($run->status !== 'completed');

        $message = $this->openai->threads()->messages()->modify(
            threadId: $message->threadId,
            messageId: $message->id,
            parameters: [
                'metadata' => [
                    'conversation_id' => (string) $conversation->id,
                    'sender' => "bot:{$bot->getRouteKey()}",
                ],
            ]
        );

        MessageSent::dispatch(
            $bot,
            $conversation,
            $message
        );

        return $message;
    }
}
