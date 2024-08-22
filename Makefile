om:
	overmind s

db-reset:
	php artisan migrate:fresh --seed

db-reset-empty:
	php artisan migrate:fresh --seed --seeder=EmptySeeder

dev:
	yarn dev

reverb:
	php artisan reverb:start --debug

logs:
	tail -f storage/logs/laravel.log | jq

worker:
	php artisan queue:work

ziggy:
	php artisan ziggy:generate
