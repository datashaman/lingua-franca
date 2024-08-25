<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Events\TranslationSent;
use App\Models\Translation;
use Datashaman\LaravelTranslators\Facades\Translator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateTranslations
{
    public function handle(MessageSent $event): void
    {
        $event
            ->conversation
            ->users()
            ->where('translate', true)
            ->distinct('locale')
            ->pluck('locale')
            ->each(fn (string $locale) => $this->translate($event, $locale));
    }

    protected function translate(MessageSent $event, string $locale): void
    {
        $message = $event->message;

        $translatedContent = Translator::translate([$message->content[0]->text->value], $locale)[0];

        $translation = Translation::query()
            ->create([
                'message_id' => $message->id,
                'locale' => $locale,
                'content' => $translatedContent,
            ]);

        $message->content[0]->text->value = $translatedContent;

        TranslationSent::dispatch(
            $event->conversation,
            $message,
            $locale
        );
    }
}
