default:

db-reset:
	php artisan migrate:fresh --seed

db-reset-empty:
	php artisan migrate:fresh --seed --seeder=EmptySeeder
