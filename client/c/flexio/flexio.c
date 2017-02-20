
#ifdef _MSC_VER
#define _CRT_SECURE_NO_WARNINGS
#pragma warning(disable:4996)
#endif

#include "libflexio.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>
#include <sys/stat.h>
#include "conf.h"



#ifdef _MSC_VER
#include <direct.h> // for mkdir
#include <io.h>     // for CRT findfirst/next
#include <windows.h>
#else
#include <unistd.h>
#include <sys/types.h>
#include <sys/stat.h>
#define _mkdir(dir) mkdir(dir,0700)
#endif


char g_config_file[1024];



static bool argEquals(const char* arg, const char* s)
{
    size_t len = strlen(s);
    if (0 != strncmp(arg, s, len))
        return false;
    return (arg[len] == '\0' || arg[len] == '=') ? true : false;
}



bool output_callback(flexio_callback_type_t type, const unsigned char* data, size_t len, void* param);


void parseCommandLine(flexio_handle_t handle, flexio_paramset_t params, int argc, char** argv)
{
#define EQUALS(s1,s2) (0==strcmp((s1),(s2)))

    bool is_run = (argc > 1 && 0 == strcmp(argv[1], "run")) ? true:false;

    int i;
    for (i = 1; i < argc; ++i)
    {
        if (argv[i][0] == '-')
        {
            char* eq = strchr(argv[i], '=');
            char* value = (eq ? eq+1 : (i + 1 < argc ? argv[i+1] : NULL));

            if (argEquals(argv[i], "--host") || argEquals(argv[i], "-h"))
            {
                flexio_set_host(handle, value);
                if (!eq) i++;
            }
            if (argEquals(argv[i], "--insecure"))
            {
                flexio_set_sslverify(handle, false);
                if (!eq) i++;
            }
            else if (argEquals(argv[i], "--port") || argEquals(argv[i], "-x"))
            {
                if (value)
                    flexio_set_port(handle, atoi(value));
                if (!eq) i++;
            }
            else if (argEquals(argv[i], "--pipe") || argEquals(argv[i], "-P"))
            {
                if (value)
                    flexio_set_pipe(handle, value);
                if (!eq) i++;
            }
            else if (argEquals(argv[i], "--debug") || argEquals(argv[i], "-X"))
            {
                flexio_set_debug(handle, true);
            }
            else if (argEquals(argv[i], "--test") || argEquals(argv[i], "-T"))
            {
                flexio_set_host(handle, "test.flex.io");
            }
            else if (argEquals(argv[i], "--localhost"))
            {
                flexio_set_host(handle, "localhost");
            }
            else if (argEquals(argv[i], "--output"))
            {
                flexio_set_callback(handle, output_callback, NULL);
            }
            else if (0 == strncmp(argv[i], "--x-", 4))
            {
                if (eq) *eq = 0;
                if (value) flexio_paramset_push(params, argv[i]+4, value);
                if (eq) *eq = '=';
            }

        }
    }

}



void configure(const char* profile_name)
{
    char apikey[255];

    printf("Configuring profile '%s'\n", profile_name);
    
    printf("Please enter your API key: ");
    fgets(apikey, sizeof(apikey), stdin);
    size_t len = strlen(apikey);
    if (len == 0)
    {
        fprintf(stderr, "Cancelled");
        return;
    }
    if (apikey[len-1] == '\n') apikey[len-1] = 0;


    // make sure HOME/.flexio directory is present

    #ifdef WIN32
    const char* homedir = getenv("USERPROFILE");
    #else
    const char* homedir = getenv("HOME");
    #endif

    if (!homedir)
    {
        fprintf(stderr, "Home directory environment variable not specified");
        return;
    }

    char* config_path = malloc(strlen(homedir) + 50);
    strcpy(config_path, homedir);
    strcat(config_path,  "/.flexio");
    _mkdir(config_path);
    strcat(config_path, "/config");


    char* group_name = malloc(strlen(profile_name) + 50);
    strcpy(group_name, "profile ");
    strcat(group_name, profile_name);

    conffile_t cf = conffile_new();
    conffile_set(cf, group_name, "api_key", apikey);
    conffile_save(cf, config_path);
    conffile_free(cf);

    #ifndef WIN32
    chmod(config_path, 0600);
    #endif

    free(config_path);
    free(group_name);
}


void determineConfigFilePath(int argc, char** argv)
{
    g_config_file[0] = 0;


    const char* config_file_env = getenv("FLEXIO_CONFIG_FILE");
    if (config_file_env)
    {
        if (strlen(config_file_env) >= sizeof(g_config_file)-1)
        {
            fprintf(stderr, "Value of FLEXIO_CONFIG_FILE environment variable too large\n");
            exit(0);
        }

        strcpy(g_config_file, config_file_env);
        return;
    }



#ifdef WIN32
    const char* homedir = getenv("USERPROFILE");
#else
    const char* homedir = getenv("HOME");
#endif

    if (homedir)
    {
        if (strlen(homedir) > 512)
        {
            fprintf(stderr, "Home directory path too large\n");
            exit(0);
        }

        strcpy(g_config_file, homedir);
    }
     else
    {
        // no home directory variable; use /etc on *ix and system directory on windows
        #ifdef WIN32
        if (GetSystemDirectoryA(g_config_file, sizeof(g_config_file)) >= sizeof(g_config_file))
        {
            fprintf(stderr, "System directory path too large\n");
            exit(0);
        }
        #else
        strcpy(g_config_file, "/etc");
        #endif
    }

    // convert all slashes to forward slashes
#ifdef WIN32
    char* p = g_config_file;
    while (*p)
    {
        if (*p == '\\')
            *p = '/';
        ++p;
    }
#endif

    size_t len = strlen(g_config_file);
    if (len == 0)
    {
        fprintf(stderr, "Empty config file directory path\n");
        exit(0);
    }
    
    if (g_config_file[len-1] == '/')
    {
        g_config_file[len-1] = 0;
    }


    strcat(g_config_file, "/.flexio");
    _mkdir(g_config_file);
    strcat(g_config_file, "/config");
}


void loadConfigFile(flexio_handle_t f, const char* profile_name)
{
    char group_name[1024];
    if (strlen(profile_name) > 512)
    {
        fprintf(stderr, "Profile name too long\n");
        exit(0);
    }
    strcpy(group_name, "profile ");
    strcat(group_name, profile_name);


    conffile_t cf = conffile_new();
    if (!conffile_load(cf, g_config_file))
    {
        conffile_free(cf);
        return;
    }

    const char* api_key = conffile_get(cf, group_name, "api_key");

    if (api_key)
    {
        flexio_set_apikey(f, api_key);
    }

    conffile_free(cf);


#ifndef WIN32
    chmod(g_config_file, 0600);
#endif
}


static void displayVersion()
{
    fprintf(stdout, "flex.io Command Line Client - version 0.2 (clang)\n");
}

static void displayUsage()
{
    displayVersion();
    fprintf(stdout, "\nUsage: flexio <command>\n\n");
    fprintf(stdout, "where <command> is one of:\n");
    fprintf(stdout, "configure     configure the command line client\n");
    fprintf(stdout, "help          display this help message\n");
    fprintf(stdout, "pipes         pipe-related commands\n");
    fprintf(stdout, "version       display current version\n");
    fprintf(stdout, "\nAdditional Flags:\n\n");
    fprintf(stdout, "--debug       debug output\n");
    fprintf(stdout, "--insecure    turn off SSL certificate validation (validation is on by default)\n");
    fprintf(stdout, "--output      print the pipe output to stdout\n");
    fprintf(stdout, "\nExamples:\n\n");
    fprintf(stdout, "flexio pipes run my-pipe-name *.txt\n");
    fprintf(stdout, "flexio pipes run my-pipe-name file1.txt --output\n");
    fprintf(stdout, "flexio pipes run my-pipe-name < file1.txt\n");
    fprintf(stdout, "cmd1 | cmd2 | flexio pipes run my-pipe-name (use stdin as pipe input)\n");
}


static void addFileSpec(flexio_handle_t flexio, const char* filespec)
{

#ifdef WIN32

    struct _finddata_t find_data;
    intptr_t h;

    const char* value = filespec;
    const char* p = value;
    const char* last_slash = value;
    while (*p)
    {
        if (*p == '/' || *p == '\\') last_slash = p;
        ++p;
    }

    // Find first .c file in current directory 
    if ((h = _findfirst(value, &find_data)) != -1L)
    {
        char fullpath[1024];

        do {
            if (0 == strcmp(find_data.name, ".") || 0 == strcmp(find_data.name, ".."))
                continue;

            fullpath[0] = 0;

            if (last_slash - value + strlen(find_data.name) > sizeof(fullpath) - 1)
            {
                // filename too big
                continue;
            }
            memcpy(fullpath, value, last_slash - value);
            fullpath[last_slash - value] = 0;
            if (strlen(fullpath) > 0)
                strcat(fullpath, "/");
            strcat(fullpath, find_data.name);

            {
                // make slashes uniform
                char* p = fullpath;
                while (*p) {
                    if (*p == '\\') *p = '/';
                    p++;
                }
            }

            flexio_add_file(flexio, fullpath);
        } while (_findnext(h, &find_data) == 0);
        _findclose(h);
    }
#else
    // linux command line expansion done by shell
    flexio_add_file(flexio, filespec);
#endif

}


bool output_callback(flexio_callback_type_t type, const unsigned char* data, size_t len, void* param)
{
    if (type == FLEXIO_CALLBACK_DATA)
    {
        fwrite(data, 1, len, stdout);
    }

    return true;
}



int main(int argc, char** argv)
{
    determineConfigFilePath(argc, argv);

    if (argc < 2)
    {
        displayUsage();
        return 90;
    }

    const char* pclass = argv[1];
    const char* pverb = (argc >= 3 ? argv[2] : "");

    if (0 == strcmp(pclass, "help"))
    {
        displayUsage();
        return 90;
    }
    else if (0 == strcmp(pclass, "configure"))
    {
        configure(argc > 2 ? argv[2] : "default");
        return 0;
    }
    else if (0 == strcmp(pclass, "version"))
    {
        displayVersion();
        return 90;
    }
    else if (0 == strcmp(pclass, "pipes") && 0 == strcmp(pverb, "run"))
    {
        // flexio pipes run [pipe]
        const char* pipe = (argc >= 4 ? argv[3] : "");
        if (strlen(pipe) == 0)
        {
            displayUsage();
            return 90;
        }
         else
        {
            flexio_handle_t f = flexio_new();
            flexio_paramset_t params = flexio_paramset_new();

            flexio_set_host(f, "www.flex.io");
            //flexio_set_host(f, "localhost");
            flexio_set_port(f, 443);
            flexio_set_pipe(f, pipe);
            flexio_set_params(f, params);

            loadConfigFile(f, "default");
            parseCommandLine(f, params, argc, argv);


            int i;
            bool stdin_added = false;
            for (i = 4; i < argc; ++i)
            {
                if (0 == strcmp(argv[i], "-"))
                {
                    flexio_add_file(f, NULL);
                    stdin_added = true;
                }
                 else
                {
                    addFileSpec(f, argv[i]);
                }
            }


            if (!stdin_added && !isatty(fileno(stdin)))
            {
                // stdin is a redirected file or pipe (and wasn't already added via - on cmdline)
                flexio_add_file(f, NULL);
            }
    


            int res = flexio_run(f);

            switch (res)
            {
                case FLEXIO_OK:                     break;
                case FLEXIO_ERROR:                  fprintf(stderr, "FLEXIO_ERROR\n"); break;
                case FLEXIO_ERROR_SSL:              fprintf(stderr, "FLEXIO_ERROR_SSL\n"); break;
                case FLEXIO_ERROR_AUTHENTICATION:   fprintf(stderr, "FLEXIO_AUTHENTICATION\n"); break;
                case FLEXIO_ERROR_FORBIDDEN:        fprintf(stderr, "FLEXIO_FORBIDDEN\n"); break;
                case FLEXIO_ERROR_FILE_OPEN:        fprintf(stderr, "FLEXIO_ERROR_FILE_OPEN\n"); break;
                case FLEXIO_ERROR_INVALID_PIPE:     fprintf(stderr, "FLEXIO_ERROR_INVALID_PIPE\n"); break;
            }

            flexio_paramset_free(params);
            flexio_free(f);

            fflush(stdout);

            if (res != FLEXIO_OK)
            {
                return res;
            }
        }
    }

    return 0;
}







/*
char* str = "ABC";
unsigned char buf[32];
sha256_ctx_t ctx;
sha256_init(&ctx);
sha256_update(&ctx, str, strlen(str));
sha256_final(&ctx, buf);

for (int i = 0; i < 32; ++i)
printf("%x%x", buf[i] >> 4, buf[i] & 0xf);
printf("\n");
return 0;
*/

/*
char* message = "This is a test using a larger than block-size key and a larger than block-size data. The key needs to be hashed before being used by the HMAC algorithm.";
size_t message_len = strlen(message);

char* key = "\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa\xaa";
size_t key_len = strlen(key);

unsigned char buf[32];

sha256_hmac(message, message_len, key, key_len, buf);

for (int i = 0; i < 32; ++i)
printf("%x%x", buf[i] >> 4, buf[i] & 0xf);
printf("\n");
return 0;
*/

