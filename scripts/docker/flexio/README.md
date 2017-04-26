To do an incremental build

    docker build -t flexio .

To do a full build

    docker build -t flexio . --no-cache


To run the image interactively

    docker run -it flexio

To run the image interactively with port mapping

    docker run -p 8080:80 -p 8443:443 -it flexio

To run an image with start

    docker create --name aaaaa -it flexio
    docker start aaaaa
    docker exec aaaaa echo Hi
    docker stop aaaaa
    docker rm aaaaa

