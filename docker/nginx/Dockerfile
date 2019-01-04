FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf
COPY fastcgi.conf /etc/nginx/fastcgi.conf
COPY server.conf /etc/nginx/conf.d/default.conf
COPY localhost.key /etc/ssl/private/localhost.key
COPY localhost.crt /etc/ssl/certs/localhost.crt
