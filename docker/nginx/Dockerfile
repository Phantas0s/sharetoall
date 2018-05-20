FROM nginx:stable-alpine

ARG HTTP_AUTH_LOGIN
ARG HTTP_AUTH_PASSWORD

ENV HTTP_AUTH_LOGIN=$HTTP_AUTH_LOGIN
ENV HTTP_AUTH_PASSWORD=$HTTP_AUTH_PASSWORD

RUN apk update \
    && apk add --virtual build-deps \
    openssl 

COPY ./dhparam-2048.pem /etc/ssl/certs/dhparam-2048.pem
COPY ./docker/nginx/site-http.conf /etc/nginx/conf.d/default.conf
