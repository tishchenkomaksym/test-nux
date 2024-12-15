install:
	composer install;

deploy:
	php artisan key:generate; \
    php artisan migrate; \
    php artisan optimize;
