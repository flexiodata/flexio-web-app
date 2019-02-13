#include <set>
#include <map>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <unistd.h>
#include <iostream>
#include <poll.h>
#include <signal.h>
#include <spawn.h>
#include <sys/wait.h>
#include <zmq.hpp>
#include "base64.h"





extern char **environ;
std::set<pid_t> running_processes;

std::vector<pid_t> finished_processes;
pthread_mutex_t finished_processes_mutex;
int g_exit = 0;

static std::string s_recv(zmq::socket_t & socket)
{
    try
    {
        zmq::message_t message;
        if (!socket.recv(&message))
            return "";

        return std::string(static_cast<char*>(message.data()), message.size());
    }
    catch (zmq::error_t& e)
    {
        printf("Exception (recv): %s\n", e.what());
        return s_recv(socket);
    }
}

static bool s_send(zmq::socket_t & socket, const std::string & string)
{
    zmq::message_t message(string.size());
    memcpy(message.data(), string.data(), string.size());
    return socket.send(message);
}


volatile sig_atomic_t g_sigchld = 0;
void sigchld_handler(int)
{
    g_sigchld = 1;
}


int main (int argc, char *argv[])
{
    const char* engine = getenv("FLEXIO_EXECUTE_ENGINE");
    const char* execute_home = getenv("FLEXIO_EXECUTE_HOME");
    const char* runtime_server = getenv("FLEXIO_RUNTIME_SERVER");
    const char* runtime_key = getenv("FLEXIO_RUNTIME_KEY");

    const char* args[5];

    if (execute_home == NULL || strlen(execute_home) == 0)
    {
        execute_home = "/fxruntime/src";
    }

    if (engine == NULL || strlen(engine) == 0)
    {
        engine = "python";
    }

    chdir(execute_home);
    
    zmq::context_t context(1);
    zmq::socket_t socket(context, ZMQ_REQ);
    socket.connect(runtime_server);


    std::string payload, response, s_runtime_key = (runtime_key?runtime_key:"");
    
    payload  = "{\"version\":1,\"access_key\":\"";
    payload += runtime_key;
    payload += "\",\"method\":\"hello\",\"params\":[],\"id\":\"111\"}";

    printf("Sending hello\n");
    int sres = s_send(socket, payload) ? 1 : 0;
    printf("Send results %d\n", sres);
    response = s_recv(socket);


    chdir(execute_home);

    if (0 == strcmp(engine, "python"))
    {
        args[0] = "/usr/bin/python3";
        args[1] = "-c";
        args[2] = "import flexio as f; import script as s; f.run(s)";
        args[3] = NULL;
    }
    else if (0 == strcmp(engine, "nodejs"))
    {
        args[0] = "/usr/bin/node";
        args[1] = "-e";
        args[2] = "var f = require('/fxnodejs/flexio'); var s = require('./script'); f.run(s)";
        args[3] = NULL;
    }
    else
    {
        fprintf(stderr, "Error: unknown execution engine");
        exit(EXIT_FAILURE);
    }


    struct sigaction act;
    memset(&act, 0, sizeof(struct sigaction));
    sigemptyset(&act.sa_mask);
    act.sa_handler = sigchld_handler;
    if (-1 == sigaction(SIGCHLD, &act, NULL))
    {
        perror("sigaction()");
        exit(EXIT_FAILURE);
    }


    int cout_pipe[2];
    int cerr_pipe[2];
    posix_spawn_file_actions_t action;
    pid_t pid;

    if (pipe(cout_pipe) || pipe(cerr_pipe))
    {
        fprintf(stderr, "pipe() returned an error");
        exit(1);
    }

    posix_spawn_file_actions_init(&action);
    posix_spawn_file_actions_addclose(&action, cout_pipe[0]);
    posix_spawn_file_actions_addclose(&action, cerr_pipe[0]);
    posix_spawn_file_actions_adddup2 (&action, cout_pipe[1], 1);
    posix_spawn_file_actions_adddup2 (&action, cerr_pipe[1], 2);
    posix_spawn_file_actions_addclose(&action, cout_pipe[1]);
    posix_spawn_file_actions_addclose(&action, cerr_pipe[1]);


    if (0 != posix_spawnp(&pid, args[0], &action, NULL, (char* const*)args, environ))
    {
        fprintf(stderr, "posix_spawnp() failed");
        exit(1);
    }

    struct pollfd fds[2];
    fds[0].fd = cout_pipe[0];
    fds[0].events = POLLIN | POLLERR | POLLHUP;
    fds[1].fd = cerr_pipe[0];
    fds[1].events = POLLIN | POLLERR | POLLHUP;

    unsigned char buf[16384];
    ssize_t buflen;
    int poll_ret;

    while (true)
    {
        if (g_sigchld == 0)
        {
            printf("Polling...\n");
            poll_ret = poll(fds, 2, -1);
            printf("Poll returned %d\n", poll_ret);
            if (poll_ret <= 0)
            {
                // signal occurred
                continue;
            }
        }
        else
        {
            printf("Final polling...\n");
            poll_ret = poll(fds, 2, 0);    // don't wait
            printf("Poll returned %d\n", poll_ret);
            if (poll_ret <= 0)
            {
                // SIGCHLD received and no more data is available; exit loop
                break;
            }
        }

        if (fds[0].revents & POLLIN)
        {
            printf("Reading...\n");
            buflen = read(cout_pipe[0], buf, 16384);
            if (buflen > 0)
            {
                printf("Writing...\n");

                payload  = "{\"version\":1,\"id\":\"199$*#991\",\"access_key\":\"";
                payload += s_runtime_key;
                payload += "\",\"method\":\"write\",\"params\":[1,\"~199$*#991/bin.b64:";
                payload += base64_encode(buf, (unsigned int)buflen);
                payload += "\"]}";
                
                s_send(socket, payload);
                response = s_recv(socket);
            }
            printf("Done with r/w\n");
        }
        else if (fds[1].revents & POLLIN)
        {
            printf("Reading...\n");
            buflen = read(cerr_pipe[0], buf, 16384);
            if (buflen > 0)
            {
                printf("Writing...\n");

                payload  = "{\"version\":1,\"id\":\"199$*#991\",\"access_key\":\"";
                payload += s_runtime_key;
                payload += "\",\"method\":\"compile_error\",\"params\":[\"~199$*#991/bin.b64:";
                payload += base64_encode(buf, (unsigned int)buflen);
                payload += "\"]}";
                
                s_send(socket, payload);
                response = s_recv(socket);
            }
            printf("Done with r/w\n");
        }
    } 


    printf("Closing...\n");
    close(cout_pipe[1]);
    close(cerr_pipe[1]);

    int exit_code = 0;
    waitpid(pid, &exit_code, 0);
    printf("Child exited with status code %d\n", exit_code);


    payload = "{\"version\":1,\"access_key\":\"";
    payload += s_runtime_key;
    payload += "\",\"method\":\"exit_loop\",\"params\":[],\"id\":\"111\"}";
    s_send(socket, payload) ? 1 : 0;
    response = s_recv(socket);
}






