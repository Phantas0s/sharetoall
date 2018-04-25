#!/bin/bash

if [ "${HTTP_AUTH_PASSWORD}" != "" ]; then
  rm -rf /etc/nginx/.htpasswd
  echo -n $HTTP_AUTH_LOGIN:$(openssl passwd -apr1 $HTTP_AUTH_PASSWORD) >> /etc/nginx/conf.d/.htpasswd
  echo "Basic auth is on for user ${HTTP_AUTH_LOGIN}..."
else
  echo "Basic auth is off (HTTP_AUTH_PASSWORD not provided)"
fi

nginx -g "daemon off;"
