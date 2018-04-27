FROM nginx:stable-alpine

ARG HTTP_AUTH_LOGIN
ARG HTTP_AUTH_PASSWORD

ENV HTTP_AUTH_LOGIN=$HTTP_AUTH_LOGIN
ENV HTTP_AUTH_PASSWORD=$HTTP_AUTH_PASSWORD

RUN apk update \
    && apk add --virtual build-deps \
    openssl 

# COPY ./web/ /sharetoall/web/
COPY ./docker/nginx/site.conf /etc/nginx/conf.d/default.conf

ADD ./docker/nginx/entrypoint.sh /bin/entrypoint.sh
RUN chmod 777 /bin/entrypoint.sh
CMD sh /bin/entrypoint.sh
