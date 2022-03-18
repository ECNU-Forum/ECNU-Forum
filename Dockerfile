# alpine based image
FROM mondedie/flarum:stable AS base

LABEL version="1.0"
LABEL description="flarum custom image"
LABEL maintainer="oceanlvr"

# RUN apk add git
WORKDIR /flarum/app
COPY . .
RUN composer install