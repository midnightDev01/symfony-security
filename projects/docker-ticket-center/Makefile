CONFIG = .env
include ${CONFIG}

build:
	rm -rf ./nginx/sites/default.conf && \
    cp ./nginx/sites/laravel.conf.dist ./nginx/sites/default.conf && \
    sed -i "s/__SERVER_NAME__/${SERVER_NAME}/g"   ./nginx/sites/default.conf && \
    sed -i "s/__APP_NAME__/${APP_NAME}/g"          ./nginx/sites/default.conf && \
    sed -i "s/__APP_ENV__/${APP_ENV}/g"           ./nginx/sites/default.conf && \
    sed -i "s/__APP_SECRET__/${APP_SECRET}/g"     ./nginx/sites/default.conf && \
    sudo ./add_host.sh && \
	docker network create elk_external || echo "elk_external already exists.." && \
	docker-compose up --build -d && \
	docker-compose ps;
up:
	docker network create elk_external || echo "elk_external already exists..";
	docker-compose up -d
stop:
	docker-compose stop
restart:
	docker-compose restart
status:
	docker-compose ps
down:
	docker-compose down --volumes --remove-orphans
	docker-compose down --volumes --remove-orphans
login:
	docker-compose exec --user=1000:1000 "php-fpm" bash
logs:
	docker-compose logs -f "php-fpm"
flush:
	docker-compose exec --user=1000:1000 "php-fpm" "bash" "-c" "cachetool opcache:reset --fcgi";
	docker-compose exec --user=1000:1000 "php-fpm" "bash" "-c" "php artisan cache:clear";
	docker-compose exec --user=1000:1000 "php-fpm" "bash" "-c" "chown -R 1000:www-data /var/www/${APP_NAME}/laravel/storage /var/www/${APP_NAME}/laravel/bootstrap/cache";
	docker-compose exec --user=1000:1000 "php-fpm" "bash" "-c" "chmod -R 775  /var/www/${APP_NAME}/laravel/storage /var/www/${APP_NAME}/laravel/bootstrap/cache";

test:
	docker-compose exec --user=1000:1000 "php-fpm" "bash" "-c" "ls -al /${APP_NAME}"
	docker-compose exec --user=1000:1000 "nginx" "sh" "-c" "ls -al /"
	docker-compose exec --user=1000:1000 "mongo" "bash" "-c" "ls -al /"
	curl "http://${SERVER_NAME}:${DOCKER_NGINX_PORT}"

.PHONY:build
