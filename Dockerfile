# alpine based image
FROM mondedie/flarum:1.2.0 AS base

LABEL version="1.0"
LABEL description="flarum custom image"
LABEL maintainer="oceanlvr"

RUN apk --no-cache add git

WORKDIR /flarum/app
COPY . .
RUN git submodule update --init --recursive

# install git submodule
# RUN composer install --ignore-platform-req=ext-simplexml
RUN composer install

# RUN php flarum migrate
EXPOSE 8888