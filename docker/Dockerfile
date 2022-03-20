# alpine based image
FROM mondedie/flarum:1.2.0 AS base

LABEL version="1.0"
LABEL description="flarum custom image"
LABEL maintainer="oceanlvr"

RUN apk --no-cache add git

WORKDIR /flarum/app

COPY .nginx.conf .nginx.conf
COPY extend.php extend.php
COPY flarum flarum
COPY site.php site.php

# install git submodule
COPY .git .git
COPY .gitmodules .gitmodules
RUN git submodule update --init --recursive

# composer file
COPY composer.json composer.json
# COPY composer.lock composer.lock

RUN composer install --ignore-platform-req=ext-simplexml

EXPOSE 8888
