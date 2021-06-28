FROM nginx:1.21 AS build
WORKDIR /composer
RUN set -x \
  && apt-get update \
  && apt-get install -y git unzip php7.3 php7.3-xml php7.3-mbstring \
  && curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer require symfony/dotenv:^3.4

FROM nginx:1.21 AS execute
COPY --from=build /composer/ /composer/
COPY deploy/common/nginx/ /etc/nginx/
COPY deploy/common/docker-entrypoint.d/ /docker-entrypoint.d/
RUN set -x \
  && usermod --append --groups www-data nginx \
  && chmod -R 755 /docker-entrypoint.d \
  && apt-get update \
  && apt-get dist-upgrade --no-install-recommends --no-install-suggests -y \
  && apt-get install --no-install-recommends --no-install-suggests -y \
    php7.3-mysql php7.3-fpm php7.3-xml php7.3-mbstring \
  && apt-get remove --purge --auto-remove -y && rm -rf /var/lib/apt/lists/*
