#!/bin/bash

case "$1" in
    "website")
        export HOME=/root
        cd /srv/www/flexio-docs
        git pull
        cd /srv/www/website
        hexo clean
        git pull
        hexo generate --config _staging.yml
        echo "User-agent: *" > /srv/www/website/public/robots.txt
        echo "Disallow: /"   >> /srv/www/website/public/robots.txt
        echo "Done!"
        ;;
    *)
        echo Unknown parameter
        exit 1
        ;;
esac
