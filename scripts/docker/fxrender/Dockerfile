FROM ubuntu:16.04
MAINTAINER ben@flex.io

RUN apt-get -y update
RUN apt-get -y upgrade
RUN apt-get -y install sudo
RUN apt-get -y install wget
RUN apt-get -y install curl

# following line is for dependencies only
RUN apt-get -y install chromium-browser libgconf2-4

RUN curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
RUN apt-get -y install nodejs
RUN mkdir /render
RUN echo {} > /render/package.json
RUN cd /render && npm install puppeteer
RUN cd /render && npm install command-line-args
COPY run.js /render/run.js
#COPY kits.js /render/kits.js

