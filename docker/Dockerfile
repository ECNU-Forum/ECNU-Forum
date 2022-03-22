# alpine based image
FROM mondedie/flarum:1.2.0 AS base

LABEL version="1.0"
LABEL description="flarum custom image"
LABEL maintainer="oceanlvr"

WORKDIR /flarum/app

COPY .nginx.conf .nginx.conf
COPY extend.php extend.php
COPY flarum flarum
COPY site.php site.php

# composer file
COPY extensions extensions
COPY patches patches
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN apk add --no-cache patch
RUN sed -i '/cd \/flarum\/app/a\COMPOSER_CACHE_DIR=${CACHE_DIR} su-exec ${UID}:${GID} composer install' /usr/local/bin/startup
EXPOSE 8888
