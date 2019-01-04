FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf
COPY fastcgi.conf /etc/nginx/fastcgi.conf
COPY server.conf /etc/nginx/conf.d/default.conf

COPY index.php /var/www/html
COPY adminer-4.7.0.php /var/www/html