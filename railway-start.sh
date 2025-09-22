#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views

php artisan config:cache
php artisan route:cache
php artisan storage:link || true

php artisan migrate --force

