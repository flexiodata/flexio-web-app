# Flex.io application
FROM ubuntu:16.04
MAINTAINER Ben Williams <ben@flex.io>

RUN apt-get -y update
RUN apt-get -y upgrade
RUN apt-get -y install apt-utils
RUN apt-get -y install openssh-client
RUN apt-get -y install git

RUN cat /dev/zero | ssh-keygen -q -N ""

RUN apt-get -y install apache2
RUN apt-get -y install php7.0
RUN apt-get -y install libapache2-mod-php7.0
RUN apt-get -y install php7.0-curl php7.0-dev php7.0-gd php7.0-opcache php7.0-pgsql php7.0-mysql php7.0-sqlite3 php7.0-xml php7.0-mcrypt php7.0-mbstring
RUN apt-get -y install php-redis



COPY id_rsa /root/.ssh/id_rsa
COPY id_rsa.pub /root/.ssh/id_rsa.pub
RUN echo Host code.flex.io >> /root/.ssh/config
RUN echo     StrictHostKeyChecking no >> /root/.ssh/config

RUN mkdir -p /srv/www/flexio/log
RUN mkdir -p /srv/www/flexio/ssl
RUN git clone git@code.flex.io:flexio.git /srv/www/flexio/src
RUN cd /srv/www/flexio/src && git checkout develop

RUN cp /srv/www/flexio/src/scripts/install/flexio-with-ssl.conf /etc/apache2/sites-available
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /srv/www/flexio/ssl/app_flex_io.key -out /srv/www/flexio/ssl/app_flex_io.crt -subj "/C=US/ST=Illinois/L=Chicago/O=Flex.io/OU=Development/CN=app.flex.io"
RUN a2dissite 000-default
RUN a2ensite flexio-with-ssl
RUN a2enmod headers rewrite ssl
RUN service apache2 restart

