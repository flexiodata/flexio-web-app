#FROM blitznote/debase:18.04
FROM ubuntu:18.04
MAINTAINER ben@flex.io



########################################
# core installation
########################################

RUN apt -y update && \
    apt -y install sudo wget apt-utils curl git python3-pip libzmq3-dev libzmq5 libjsoncpp-dev golang chromium-browser libgconf2-4 nodejs npm python3-psycopg2 && \
    npm install -g yarn

# nodejs runtime
#RUN curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -

########################################
# nodejs libraries
########################################

# utility
RUN yarn add underscore && \
    yarn add lodash && \
    yarn add async && \
    yarn add request && \
    yarn add axios && \
    yarn add moment && \
    yarn add csv && \
    yarn add cheerio && \
    yarn add d3-node && \
    yarn add validator && \
    yarn add faker

########################################
# python libraries
########################################

# network
RUN pip3 install zmq && \
    pip3 install requests && \ 
    pip3 install aiohttp && \
    pip3 install arrow && \ 
    pip3 install pendulum && \ 
    pip3 install numpy && \ 
    pip3 install scipy && \ 
    pip3 install scikit-learn && \ 
    pip3 install sympy && \ 
    pip3 install pandas && \ 
    pip3 install pillow && \ 
    pip3 install beautifulsoup4 && \ 
    pip3 install lxml && \ 
    pip3 install pypdf2 && \ 
    pip3 install fpdf && \ 
    pip3 install openpyxl && \
    pip3 install matplotlib && \
    pip3 install bokeh && \ 
    pip3 install seaborn && \ 
    pip3 install plotly && \ 
    pip3 install nltk && \ 
    pip3 install gensim && \ 
    pip3 install tensorflow && \ 
    pip3 install keras && \ 
    pip3 install cerberus && \ 
    pip3 install faker && \ 
    pip3 install sqlalchemy && \ 
    pip3 install plaid && \ 
    pip3 install boto3

# POTENTIAL: web scraping (spacy)
# RUN apt-get install -y libffi-dev libxml2-dev libxslt-dev lib32z1-dev libssl-dev
# RUN pip3 install scrapy

# POTENTIAL: network
# RUN pip3 install twisted

# POTENTIAL: language processing
# RUN pip3 install spacy

# for matplotlib, configure with AGG backend so plots can be saved to buffers
RUN mkdir -p /root/.config/matplotlib && echo "backend : AGG" > /root/.config/matplotlib/matplotlibrc

RUN mkdir /fxruntime && \
    mkdir /fxruntime/go && \
    mkdir /fxruntime/build && \
    mkdir /fxruntime/build/controller && \
    mkdir /fxruntime/build/start && \
    mkdir /fxpython && \
    mkdir /fxnodejs

ENV GOPATH=/fxruntime/go

# flexio bindings for nodejs
COPY package.json /fxnodejs/package.json
RUN cd /fxnodejs && npm install

COPY flexio.py /usr/lib/python3.6/flexio.py
RUN cd /usr/lib/python3.6 && python3 -m compileall flexio.py
COPY fxstart.py /fxpython/fxstart.py
RUN cd /fxpython && python3 -m compileall fxstart.py

COPY flexio.js /fxnodejs/flexio.js
COPY run.js /fxnodejs/run.js
COPY render.js /fxnodejs/render.js

RUN go get github.com/pebbe/zmq4
COPY fxstart.go /fxruntime/build/start/
RUN cd /fxruntime/build/start/ && go build -o fxstartgo fxstart.go  && mv /fxruntime/build/start/fxstartgo /fxruntime

COPY ./controller/Makefile ./controller/controller.cpp /fxruntime/build/controller/
COPY ./start/Makefile ./start/start.cpp ./start/base64.cpp ./start/base64.h /fxruntime/build/start/
RUN cd /fxruntime/build/controller && make && mv /fxruntime/build/controller/fxcontroller /fxruntime && rm *.o
RUN cd /fxruntime/build/start      && make && mv /fxruntime/build/start/fxstart /fxruntime   && rm *.o


# POTENTIAL: csv
# node-cvg
# csv

# POTENTIAL: pdf
# pdfkit

# POTENTIAL: image
# graphicsmagick
# mmagemagick
# sharp
# spritesmith

# POTENTIAL: markdown
# marked
# markdown

# POTENTIAL: visualization
# chart.js

# POTENTIAL: workflow
# gulp

# POTENTIAL: parsing
# body-parser

