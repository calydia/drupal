FROM strathos/php-nginx-supervisord:latest

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --chown=www-data:www-data src/ /var/www/html/

RUN composer install --no-dev --no-interaction && \
  rm /var/www/html/.git* && \
  chown -R www-data:www-data /var/www/html
