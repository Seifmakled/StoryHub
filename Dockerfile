FROM php:8.2-apache

# Explicitly disable other MPMs (important)
RUN a2dismod mpm_event mpm_worker || true

# Enable rewrite (safe)
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Railway port
ENV PORT=8080
EXPOSE 8080

# Apache listen on Railway port
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
 && sed -i "s/80/${PORT}/g" /etc/apache2/sites-enabled/000-default.conf
