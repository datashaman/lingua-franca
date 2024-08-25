<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use OpenAI;
use OpenAI\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Client::class,
            function ($app) {
                $factory = OpenAI::factory()
                    ->withApiKey(config('services.openai.api_key'))
                    ->withHttpHeader('OpenAI-Beta', 'assistants=v2');

                if ($organization = config('services.openai.organization')) {
                    $factory->withOrganization($organization);
                }

                if ($project = config('services.openai.project')) {
                    $factory->withProject($project);
                }

                return $factory->make();
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'bot' => 'App\Models\Bot',
            'conversation' => 'App\Models\Conversation',
            'user' => 'App\Models\User',
        ]);
    }
}
