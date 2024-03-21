FROM ghcr.io/ophelios-studio/docker-pulsar-php82:latest

# Crontab
COPY cronjobs /etc/cron.d/cron

# Copy specific php.ini directives
COPY custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir /var/www/html/temp
RUN chown -R www-data:www-data /var/www/html/temp
RUN chmod -R 744 /var/www/html/temp