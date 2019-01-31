#include <zmq.hpp>
#include <unistd.h>
#include <iostream>
#include <jsoncpp/json/json.h>


static std::string s_recv (zmq::socket_t & socket) {

    zmq::message_t message;
    socket.recv(&message);

    return std::string(static_cast<char*>(message.data()), message.size());
}

static bool s_send (zmq::socket_t & socket, const std::string & string) {

    zmq::message_t message(string.size());
    memcpy (message.data(), string.data(), string.size());

    bool rc = socket.send (message);
    return (rc);
}

int main (int argc, char *argv[])
{
    zmq::context_t context(1);

    zmq::socket_t responder(context, ZMQ_REP);
    //responder.connect("tcp://localhost:5560");
    //responder.connect("ipc:///tmp/mulch");
    responder.bind("ipc:///fxruntime/controller/pipes/controller");

    Json::Reader reader;
    Json::Value root;how to 

    printf("Listening...\n");
    while(1)
    {
        //  Wait for next request from client
        std::string string = s_recv (responder);
        
        std::cout << "Received request: " << string << std::endl;

        reader.parse(string, root);
        printf("Got command: %s\n", root["cmd"].asString().c_str()); 

        std::string cmdline = root["cmdline"].asString();
        char chunk[255];
        std::string reply;

        FILE* fp = popen(cmdline.c_str(), "r");
        if (fp)
        {
            while (fgets(chunk, sizeof(chunk)-1, fp) != NULL) { reply += chunk; }
            pclose(fp);
        }
        
        // Do some 'work'
        //sleep (1);
        
        //  Send reply back to client
        s_send (responder, reply);
    }
}


