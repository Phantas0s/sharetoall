FROM nginx

COPY ./docker/nginx-prod/start.sh /
COPY ./docker/nginx-prod/nginx.conf /etc/nginx/
COPY ./docker/nginx-prod/nginx-secure.conf /etc/nginx/

# COPY dhparams.pem /etc/ssl/private/
CMD /start.sh
