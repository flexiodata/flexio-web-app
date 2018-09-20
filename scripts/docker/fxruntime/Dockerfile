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

# python3 runtime
RUN apt-get -y install python3-pip

# nodejs runtime
RUN curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
RUN apt-get -y install nodejs

# puppeteer prequisites for rendering
RUN apt-get -y install chromium-browser libgconf2-4
#RUN mkdir /fxrender
#RUN echo {} > /fxrender/package.json
#RUN cd /fxrender && npm install puppeteer
#RUN cd /fxrender && npm install command-line-args
#COPY kits.js /fxrender/kits.js

# flexio bindings for python
RUN mkdir /fxpython
COPY flexio.py /usr/lib/python3.5/flexio.py
COPY fxstart.py /fxpython/fxstart.py

# flexio bindings for nodejs
RUN mkdir /fxnodejs
COPY package.json /fxnodejs/package.json
RUN cd /fxnodejs && npm install
COPY index.js /fxnodejs/index.js
COPY run.js /fxnodejs/run.js
COPY render.js /fxnodejs/render.js

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

# Amazon AWS
RUN pip3 install boto3

# POTENTIAL: web scraping (spacy)
# RUN apt-get install -y libffi-dev libxml2-dev libxslt-dev lib32z1-dev libssl-dev
# RUN pip3 install scrapy

# POTENTIAL: network
# RUN pip3 install twisted

# POTENTIAL: language processing
# RUN pip3 install spacy

# POTENTIAL: data generation
#RUN pip3 install faker



########################################
# nodejs libraries
########################################

# nodejs: yarn package manager; for downloading libraries using yarn
RUN npm install -g yarn

# nodejs: lodash
RUN yarn add lodash

