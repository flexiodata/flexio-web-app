FROM ubuntu:16.04
MAINTAINER ben@flex.io
RUN apt-get -y update
RUN apt-get -y install python3-pip

# image, http, and datetime handling
RUN pip3 install pillow
RUN pip3 install requests
RUN pip3 install arrow

# numeric, plotting, data handling
RUN pip3 install numpy
RUN pip3 install scipy
RUN pip3 install matplotlib
RUN pip3 install plotly
RUN pip3 install pandas

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

COPY flexio.py /usr/lib/python3.5/flexio.py

