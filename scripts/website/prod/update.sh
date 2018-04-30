#!/bin/bash

case "$1" in
    "website")
        export HOME=/root
        cd /srv/www/flexio-docs
        git pull
        cd /srv/www/website
        hexo clean
        git pull
        hexo generate
        echo "Done!"
        ;;
    *)
        echo Unknown parameter
        exit 1
        ;;
esac
