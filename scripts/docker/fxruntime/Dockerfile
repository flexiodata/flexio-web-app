FROM ubuntu:16.04
MAINTAINER ben@flex.io



########################################
# core installation
########################################

RUN apt-get -y update
RUN apt-get -y install sudo
RUN apt-get -y install wget
RUN apt-get -y install apt-utils
RUN apt-get -y install curl

# zeromq libraries
RUN apt-get -y install libzmq3-dev libzmq5

# python3 runtime
RUN apt-get -y install python3-pip

# nodejs runtime
RUN curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
RUN apt-get -y install nodejs

# puppeteer prequisites for rendering
RUN apt-get -y install chromium-browser libgconf2-4

# flexio bindings for python
RUN mkdir /fxpython

# flexio bindings for nodejs
RUN mkdir /fxnodejs
COPY package.json /fxnodejs/package.json
RUN cd /fxnodejs && npm install

# for communication between container and host
RUN pip3 install zmq



########################################
# python libraries
########################################

# network
RUN pip3 install requests
RUN pip3 install aiohttp

# datetime
RUN pip3 install arrow
RUN pip3 install pendulum

# numeric and data
RUN pip3 install numpy
RUN pip3 install scipy
RUN pip3 install scikit-learn
RUN pip3 install sympy
RUN pip3 install pandas

# image
RUN pip3 install pillow

# html
RUN pip3 install beautifulsoup4

# xml
RUN pip3 install lxml

# pdf
RUN pip3 install pypdf2
RUN pip3 install fpdf

# excel
RUN pip3 install openpyxl

# visualization
RUN pip3 install matplotlib
RUN pip3 install bokeh
RUN pip3 install seaborn
RUN pip3 install plotly

# language processing
RUN pip3 install nltk
RUN pip3 install gensim

# machine learning
RUN pip3 install tensorflow
RUN pip3 install keras

# validation
RUN pip3 install cerberus

# data generation
RUN pip3 install faker

# Amazon AWS
RUN pip3 install boto3

# POTENTIAL: web scraping (spacy)
# RUN apt-get install -y libffi-dev libxml2-dev libxslt-dev lib32z1-dev libssl-dev
# RUN pip3 install scrapy

# POTENTIAL: network
# RUN pip3 install twisted

# POTENTIAL: language processing
# RUN pip3 install spacy



########################################
# nodejs libraries
########################################

# yarn package manager; for downloading libraries using yarn
RUN npm install -g yarn

# utility
RUN yarn add underscore
RUN yarn add lodash
RUN yarn add async

# network
RUN yarn add request
RUN yarn add axios

# datetime
RUN yarn add moment

# html
RUN yarn add cheerio

# visualization
RUN yarn add d3-node

# validation
RUN yarn add validator

# data generation
RUN yarn add faker



COPY flexio.py /usr/lib/python3.5/flexio.py
COPY fxstart.py /fxpython/fxstart.py

COPY index.js /fxnodejs/index.js
COPY run.js /fxnodejs/run.js
COPY render.js /fxnodejs/render.js

