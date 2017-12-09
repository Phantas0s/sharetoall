FROM nginx:stable-alpine

COPY ./web /sharetoall/web/
COPY ./docker/nginx/.htpasswd /etc/nginx/conf.d/.htpasswd
COPY ./docker/nginx/site.conf /etc/nginx/conf.d/default.conf
