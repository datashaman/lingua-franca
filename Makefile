default:

db-reset:
	php artisan migrate:fresh --seed

db-reset-empty:
	php artisan migrate:fresh --seed --seeder=EmptySeeder

dev:
	yarn dev

reverb:
	php artisan reverb:start

worker:
	php artisan queue:work

ziggy:
	php artisan ziggy:generate
