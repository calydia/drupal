FROM strathos/php-nginx-supervisord:latest

COPY --chown=www-data:www-data src/ /var/www/html/

RUN composer install --no-dev --no-interaction && \
  rm /var/www/html/.editorconfig && \
  rm /var/www/html/.git* && \
  chown -R www-data:www-data /var/www/html
