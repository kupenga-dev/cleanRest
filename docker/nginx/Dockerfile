FROM nginx

COPY conf.d/nginx.conf /etc/nginx/nginx.conf
COPY conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/

CMD ["nginx", "-g", "daemon off;"]