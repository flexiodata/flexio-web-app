FROM ubuntu:16.04
MAINTAINER ben@flex.io

RUN apt-get -y update
RUN apt-get -y install sudo
RUN apt-get -y install wget
RUN apt-get -y install apt-utils
RUN apt-get -y install curl

# python3 runtime
RUN apt-get -y install python3-pip

# for communication between container and host
RUN pip3 install zmq
RUN pip3 install json-rpc

# image, http, and datetime handling
RUN pip3 install pillow
RUN pip3 install requests
RUN pip3 install aiohttp
RUN pip3 install arrow

# numeric, plotting, data handling
RUN pip3 install numpy
RUN pip3 install scipy
RUN pip3 install matplotlib
RUN pip3 install plotly
RUN pip3 install pandas

# pdf support
RUN pip3 install pypdf2
RUN pip3 install fpdf

# excel support
RUN pip3 install openpyxl

# web scraping, html parsing
#RUN pip3 install scrapy                     >> /root/fxpython/Dockerfile
RUN pip3 install beautifulsoup4

# language processing
RUN pip3 install nltk
RUN pip3 install gensim

#RUN pip3 install spacy                      >> /root/fxpython/Dockerfile

# machine learning
RUN pip3 install tensorflow
RUN pip3 install keras

# Amazon AWS
RUN pip3 install boto3


# nodejs javascript runtime
#RUN apt-get -y install nodejs npm
RUN curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
RUN apt-get -y install nodejs

# puppeteer prequisites for rendering
RUN apt-get -y install chromium-browser libgconf2-4
#RUN mkdir /fxrender
#RUN echo {} > /fxrender/package.json
#RUN cd /fxrender && npm install puppeteer
#RUN cd /fxrender && npm install command-line-args

#COPY kits.js /fxrender/kits.js


# flexio bindings for nodejs
RUN mkdir /fxnodejs
COPY package.json /fxnodejs/package.json
RUN cd /fxnodejs && npm install
COPY index.js /fxnodejs/index.js
COPY run.js /fxnodejs/run.js
COPY render.js /fxnodejs/render.js


# flexio bindings for python
RUN mkdir /fxpython
COPY flexio.py /usr/lib/python3.5/flexio.py
COPY fxstart.py /fxpython/fxstart.py
