FROM redhat/ubi8

RUN yum update -y
RUN dnf module enable -y php:8.0
RUN dnf install -y php php-mysqlnd php-xml php-mbstring php-phar php-intl php-apcu php-gd

RUN mkdir /run/php-fpm && php-fpm

EXPOSE 80
CMD apachectl -D FOREGROUND