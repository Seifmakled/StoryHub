FROM php:8.2-apache

RUN a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

ENV PORT=8080
EXPOSE 8080

RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf
