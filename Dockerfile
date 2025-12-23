FROM php:8.2-apache

# Disable all MPMs first (important!)
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork rewrite

# Copy project files
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Railway port
ENV PORT=8080
EXPOSE 8080

# Make Apache listen on Railway port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf
