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
    "flexiodata.github.io")
        export HOME=/root
        cd /srv/www/main
        git pull
        jekyll build --drafts
        cd /srv/www/main/_site
        ln -s ../../update.php update-7def1dae5e5a66f47233.php
        ;;
    "templates-app")
        export HOME=/root
        cd /srv/www/templates
        rm yarn.lock
        git pull
        yarn
        yarn upgrade
        yarn run generate
        ;;
    "docs-app")
        export HOME=/root
        cd /srv/www/docs-app
        rm yarn.lock
        git pull
        yarn
        yarn run generate
        ;;
    "docs-web-app")
        cd /srv/www/docs-web-app
        git pull
        ;;
    "docs-api")
        cd /srv/www/docs-api
        git pull
        ;;
    "docs-getting-started")
        cd /srv/www/docs-getting-started
        git pull
        ;;
    *)
        echo Unknown parameter
        exit 1
        ;;
esac
