# Flex.io application
FROM ubuntu:16.04
MAINTAINER Ben Williams <ben@flex.io>
ARG VERSION=master

RUN apt-get -y update
#RUN apt-get -y upgrade
RUN DEBIAN_FRONTEND=noninteractive apt-get -o Dpkg::Options::="--force-confnew" upgrade -y
RUN apt-get -y install apt-utils
RUN apt-get -y install openssh-client
RUN apt-get -y install git

RUN cat /dev/zero | ssh-keygen -q -N ""

RUN apt-get -y install apache2
RUN service apache2 stop
RUN apt-get -y install php7.0
RUN apt-get -y install libapache2-mod-php7.0
RUN apt-get -y install php7.0-curl php7.0-dev php7.0-gd php7.0-opcache php7.0-pgsql php7.0-mysql php7.0-sqlite3 php7.0-xml php7.0-mcrypt php7.0-mbstring php7.0-intl
RUN apt-get -y install php-redis




COPY id_rsa /root/.ssh/id_rsa_github
COPY id_rsa.pub /root/.ssh/id_rsa_github.pub
RUN echo Host github.com >> /root/.ssh/config
RUN echo HostName github.com >> /root/.ssh/config
RUN echo User git >> /root/.ssh/config
RUN echo IdentityFile /root/.ssh/id_rsa_github >> /root/.ssh/config
RUN echo StrictHostKeyChecking no >> /root/.ssh/config >> /root/.ssh/config
RUN chmod 600 /root/.ssh/id_rsa_github
RUN chmod 644 /root/.ssh/id_rsa_github.pub

RUN mkdir -p /srv/www/flexio/log
#RUN mkdir -p /srv/www/flexio/ssl
RUN git clone git@github.com:flexiodata/flexio.git /srv/www/flexio/src

# note that if VERSION is not set, it will simply fail and the source
# will simply stay on the master branch
RUN cd /srv/www/flexio/src && git checkout tags/${VERSION}


RUN rm /root/.ssh/id_rsa_github
RUN rm /root/.ssh/id_rsa_github.pub

COPY config.json /srv/www/flexio/src/config/config.json

#RUN cd /srv/www/flexio/src && git checkout develop
#RUN cp /srv/www/flexio/src/scripts/install/flexio-with-ssl.conf /etc/apache2/sites-available
RUN cp /srv/www/flexio/src/scripts/install/flexio.conf /etc/apache2/sites-available
#RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /srv/www/flexio/ssl/app_flex_io.key -out /srv/www/flexio/ssl/app_flex_io.crt -subj "/C=US/ST=Illinois/L=Chicago/O=Flex.io/OU=Development/CN=app.flex.io"
RUN a2dissite 000-default
RUN a2ensite flexio
RUN a2enmod headers rewrite
#RUN a2enmode ssl


# creation of python runtime
RUN mkdir /root/fxpython
RUN cp /srv/www/flexio/src/scripts/python_include/flexio.py /root/fxpython/flexio.py
RUN echo FROM ubuntu:16.04                            > /root/fxpython/Dockerfile
RUN echo MAINTAINER ben@flex.io                      >> /root/fxpython/Dockerfile
RUN echo RUN apt-get -y update                       >> /root/fxpython/Dockerfile
RUN echo RUN apt-get -y install python3-pip          >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install Pillow                     >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install numpy                      >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install scipy                      >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install pandas                     >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install beautifulsoup4             >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install gensim                     >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install matplotlib                 >> /root/fxpython/Dockerfile
RUN echo RUN pip3 install plotly                     >> /root/fxpython/Dockerfile
RUN echo COPY flexio.py /usr/lib/python3.5/flexio.py >> /root/fxpython/Dockerfile
RUN echo USER nobody                                 >> /root/fxpython/Dockerfile




RUN apt-get -y install docker.io
RUN cd /root/fxpython && docker build -t fxpython .

# allow www-data to run docker containers
RUN usermod -a -G docker www-data

#RUN apt-get -y install python3-pip
#RUN pip3 install Pillow



RUN service apache2 start

