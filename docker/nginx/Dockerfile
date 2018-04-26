FROM nginx:stable-alpine

RUN apk update \
    && apk add --virtual build-deps \
    openssl 

# COPY ./web/ /sharetoall/web/
COPY ./docker/nginx/site.conf /etc/nginx/conf.d/default.conf

ADD ./docker/nginx/entrypoint.sh /bin/entrypoint.sh
RUN chmod 777 /bin/entrypoint.sh
CMD sh /bin/entrypoint.sh
