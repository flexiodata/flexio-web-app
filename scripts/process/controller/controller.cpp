#include <set>
#include <map>
#include <cstring>
#include <cstdlib>
#include <unistd.h>
#include <iostream>
#include <signal.h>
#include <spawn.h>
#include <sys/wait.h>
#include <zmq.hpp>
#include <jsoncpp/json/json.h>


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

//volatile sig_atomic_t g_child_changed = 0;
void sigchld_handler(int)
{
//    int serrno = errno;
//    while(waitpid( -1, NULL, WNOHANG ) > 0) {}
//    errno = serrno;
}


void* signals_thread(void*)
{
    pid_t pid;
    int status;

    while (1)
    {
        sleep(999999999); // sleep "forever" 
        if (g_exit)
        {
            return 0;
        }

        while((pid = waitpid( -1, &status, WNOHANG )) > 0)
        {
            printf("signals_thread(): pid %d now has status %d\n", pid, status);
            if (WIFEXITED(status) || WIFSIGNALED(status) || WIFSTOPPED(status))
            {
                pthread_mutex_lock(&finished_processes_mutex);
                finished_processes.push_back(pid); 
                pthread_mutex_unlock(&finished_processes_mutex);
            }
        }
    } 
}

int main (int argc, char *argv[])
{
    int idle_counter = 0;

    if (0 != pthread_mutex_init(&finished_processes_mutex, NULL))
    {
        perror("could not create mutex");
        exit(EXIT_FAILURE);
    }


    struct sigaction act;
    memset(&act, 0, sizeof(struct sigaction));
    sigemptyset(&act.sa_mask);
    act.sa_handler = sigchld_handler;
    //act.sa_flags = SA_RESTART | SA_NOCLDSTOP;
    //act.sa_flags = SA_SIGINFO;
    if (-1 == sigaction(SIGCHLD, &act, NULL))
    {
        perror("sigaction()");
        exit(EXIT_FAILURE);
    }


    // create a thread that will receive the signals
    pthread_t receive_signals_thread; 
    if (pthread_create(&receive_signals_thread, NULL, signals_thread, NULL))
    {
        fprintf(stderr, "Error creating thread.\n");
        return 1;
    }

    // we don't want SIGCHLD received in main thread;
    // so it doesn't disturb our zmq
    sigset_t x;
    sigemptyset (&x);
    sigaddset(&x, SIGCHLD);
    sigprocmask(SIG_BLOCK, &x, NULL);


    zmq::context_t context(1);

    zmq::socket_t socket(context, ZMQ_REP);
    //socket.connect("tcp://localhost:5560");
    //socket.connect("ipc:///tmp/mulch");
    //socket.bind("ipc:///tmp/mulch");
    socket.bind("ipc:///fxruntime/base/controller");


    // set permissions
    if (argc > 1)
    {
        int uid = atoi(argv[1]);
        printf("setting socket permission (uid = %d)\n", uid);
        int res = chown("fxruntime/base/controller", uid, -1);
        printf("res = %d, errno %d\n", res, (res != 0 ? errno : 0));
    }


    // set timeout so we can loop
    socket.setsockopt(ZMQ_RCVTIMEO, 1000);



    Json::Reader reader;
    Json::Value root;
    std::string payload, cmd, envkey;
    std::vector<pid_t>::iterator it;

    const char* prog;
    const char* args[255];

    while(1)
    {
        pthread_mutex_lock(&finished_processes_mutex);
        for (it = finished_processes.begin(); it != finished_processes.end(); ++it)
        {
            printf("removing pid %d from running_processes\n", *it);
            running_processes.erase(*it);
            idle_counter = 0;
        } 
        finished_processes.clear();
        pthread_mutex_unlock(&finished_processes_mutex);


        //  Wait for next request from client
        payload = s_recv(socket);
        if (payload.empty())
        {
            if (idle_counter == 0)
            {
                printf("Waiting for message...\n");
            }

            if (++idle_counter == 30)
            {
                if (running_processes.size() > 0)
                {
                    printf("Still waiting for running processes to finish...\n");
                    idle_counter = 0;
                }
                else
                {
                    printf("Exiting due to inactivity...\n");
                    break;
                }
            }

            continue;
        }


        // message received -- reset idle timer
        idle_counter = 0;


        std::cout << "Received request: " << payload << std::endl;

        reader.parse(payload, root);
        cmd = root["cmd"].asString();
        printf("Got command: %s\n", cmd.c_str()); 

        if (cmd == "run")
        {
            Json::Value& cmdline = root["cmdline"];
            size_t args_len = cmdline.size();

            if (cmdline.type() != Json::arrayValue || args_len == 0)
            {
                s_send(socket, "{\"error\": 1, \"error_msg\": \"cmdline must be an array with at least one value\"}");
                continue;
            }
            
            prog = cmdline[0].asCString();
            if (!prog) prog = "";

            printf("Running ");
            args[0] = NULL;

            unsigned int i;
            for (i = 0; i < args_len; ++i)
            {
                args[i] = cmdline[i].asCString();
                if (!args[i]) args[i] = "";
                args[i+1] = NULL;
                printf("%s ", args[i]);
            }


            bool custom_environment = root.isMember("setenv");
            char** final_env = environ;
            std::vector<void*> to_free;
            std::vector<void*>::iterator free_it;
            std::vector<std::string> members;
            std::vector<std::string>::iterator member_it;

            if (custom_environment)
            {
                // environment
                size_t environ_entry_count = 0;
                char** e = environ;
                while (*e++)
                {
                    ++environ_entry_count;
                }

                Json::Value& setenv_node = root["setenv"];
                size_t setenv_count = setenv_node.size();

                final_env = new char*[environ_entry_count + setenv_count + 1]; // add 1 for terminator
                memset(final_env, 0, sizeof(char*)*(environ_entry_count + setenv_count + 1));

                for (i = 0; i < environ_entry_count; ++i)
                {
                    char* eq = strchr(environ[i], '=');
                    if (!eq)
                    {
                        continue;
                    }

                    envkey.assign(environ[i], eq - environ[i]);
                    
                    if (setenv_node.isMember(envkey))
                    {
                        envkey = envkey + "=" + setenv_node.removeMember(envkey).asString();
                        char* dup = strdup(envkey.c_str());
                        to_free.push_back((void*)dup);
                        final_env[i] = dup;
                    }
                    else
                    {
                        final_env[i] = environ[i];
                    }
                }

                members = setenv_node.getMemberNames();
                for (member_it = members.begin(); member_it != members.end(); ++member_it)
                {
                    envkey = *member_it + "=" + setenv_node[*member_it].asString();
                    char* dup = strdup(envkey.c_str());
                    to_free.push_back((void*)dup);
                    final_env[i++] = dup;
                }

                // for debugging
                //e = final_env;
                //while (*e) 
                //{
                //    printf("%s\n", *e);
                //    ++e;
                //}
            }


            pid_t pid;
            int status;
            if (0 == posix_spawn(&pid, prog, NULL, NULL, (char* const*)args, final_env))
            {
                printf("... success.\n");
                running_processes.insert(pid);
            }
            else
            {
                printf("... failed.\n");
            }


            if (custom_environment)
            {
                for (free_it = to_free.begin(); free_it != to_free.end(); ++free_it)
                {
                    free(*free_it);
                }
                to_free.clear();
                delete[] final_env;
            }

            s_send(socket, "{\"error\":0}");
        }
        else if (cmd == "runtest")
        {
            std::string cmdline = root["cmdline"].asString();
            char chunk[255];
            std::string reply;

            FILE* fp = popen(cmdline.c_str(), "r");
            if (fp)
            {
                while (fgets(chunk, sizeof(chunk)-1, fp) != NULL) { reply += chunk; }
                pclose(fp);
            }
 
            s_send(socket, reply);
        }
        else if (cmd == "hello")
        {
            s_send(socket, "{\"response\":\"hello\"}");
        }
        else
        {
            s_send(socket, "{\"error\":1}");
        }
    }


    g_exit = 1;
    pthread_kill(receive_signals_thread, SIGCHLD);
    pthread_join(receive_signals_thread, NULL);
    pthread_mutex_destroy(&finished_processes_mutex);
}



