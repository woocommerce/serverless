FROM php:8.2-fpm AS base_server

RUN apt-get update && apt-get install nginx -y

# Utils
RUN apt-get install vim subversion git zsh curl -y

RUN mkdir -p /usr/src/public_html

ENV RUNNING_IN_DOCKER true

RUN pecl install redis && docker-php-ext-enable redis

COPY nginx.conf /etc/nginx/sites-enabled/default
ADD start.sh /
RUN chmod +x /start.sh

WORKDIR /usr/src/public_html/src

# Start nginx.
CMD ["/start.sh"]
