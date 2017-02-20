/**
*
* Copyright (c) 2016, Gold Prairie LLC.  All rights reserved.
*
* Project:  Flex.io Client Library
* Author:   Benjamin I. Williams
* Created:  2016-05-06
*
* @package libflexio
* @subpackage
*/

#ifdef _MSC_VER
#define _CRT_SECURE_NO_WARNINGS
#pragma warning(disable:4996)
#endif

#ifdef _MSC_VER
#include <io.h>
#include <fcntl.h>
#endif


#include "libflexio.h"

#define CURL_STATICLIB
#include <curl/curl.h>
#include <stdlib.h>
#include <stdbool.h>
#include <ctype.h>
#include <string.h>



#ifdef __cplusplus
extern "C" {
#endif







typedef struct SimpleStringStruct {
    char* str;
    size_t len;
    size_t alloclen;
} SimpleString;


static void simplestring_init(SimpleString* ps)
{
    ps->str = (char*)malloc(512);
    ps->str[0] = 0;
    ps->alloclen = 512;
    ps->len = 0;
}

static void simplestring_clear(SimpleString* s)
{
    s->len = 0;
    s->str[0] = 0;
}

static void simplestring_append(SimpleString* s, const char* value)
{
    if (!value || !*value)
        return;

    size_t len_value = strlen(value);

    size_t needed = s->len + len_value + 1;
    if (needed >= s->alloclen)
    {
        s->str = (char*)realloc(s->str, needed + 512);
        s->alloclen = needed + 512;
    }

    strcpy(s->str + s->len, value);
    s->len += len_value;
}

static void simplestring_append_char(SimpleString* s, char value)
{
    size_t needed = s->len + 2;
    if (needed >= s->alloclen)
    {
        s->str = (char*)realloc(s->str, needed + 512);
        s->alloclen = needed + 512;
    }

    s->str[s->len] = value;
    s->len++;
    s->str[s->len] = 0;
}


static void simplestring_append_buf(SimpleString* s, const void* value, size_t len)
{
    size_t needed = s->len + len + 1;
    if (needed >= s->alloclen)
    {
        s->str = (char*)realloc(s->str, needed + 512);
        s->alloclen = needed + 512;
    }

    memcpy(s->str + s->len, value, len);
    s->len += len;
    s->str[s->len] = 0;
}

static void simplestring_add_urlkeypair(SimpleString* ps, const char* name, const char* value)
{
    size_t len_name = strlen(name);
    size_t len_value = strlen(value);

    size_t needed = ps->len + len_name + len_value + 1 + 1; // 1 for &, 1 for =
    if (needed >= ps->alloclen)
    {
        ps->str = (char*)realloc(ps->str, needed + 512);
        ps->alloclen = needed + 512;
    }

    if (ps->len > 0 && ps->str[ps->len-1] != '?' && ps->str[ps->len - 1] != '&')
    {
        ps->str[ps->len] = '&';
        ps->len++;
    }

    strcpy(ps->str + ps->len, name);
    ps->len += len_name;

    ps->str[ps->len] = '=';
    ps->len++;

    strcpy(ps->str + ps->len, value);
    ps->len += len_value;

    ps->str[ps->len] = 0;
}


static void simplestring_add_jsonesc(SimpleString* ps, const char* str)
{
    simplestring_append_char(ps, '"');
    while (*str)
    {
        if (*str == '"')
            simplestring_append(ps, "\\\"");
        if (*str == '\\')
            simplestring_append(ps, "\\\\");
        else if (*str == '\r')
            simplestring_append(ps, "\\r");
        else if (*str == '\n')
            simplestring_append(ps, "\\n");
        else if (*str == '\t')
            simplestring_append(ps, "\\t");
        else
            simplestring_append_char(ps, *str);
        ++str;
    }
    simplestring_append_char(ps, '"');
}

static void simplestring_add_jsonkeypair(SimpleString* ps, const char* name, const char* value, bool quote_value)
{
    size_t len_name = strlen(name);
    size_t len_value = strlen(value);

    simplestring_add_jsonesc(ps, name);
    simplestring_append_char(ps, ':');
    if (quote_value)
        simplestring_add_jsonesc(ps, value);
         else
        simplestring_append(ps, value);
}

static void simplestring_erase(SimpleString* ps, size_t off, size_t len)
{
    if (off >= ps->len || len == 0)
        return;

    if (off + len >= ps->len)
    {
        // caller wants to erase past end of string-- just set length
        // to offset start
        ps->len = off;
        ps->str[ps->len] = 0;
        return;
    }
        
    size_t len_to_copy = ps->len - off - len;
    memmove(ps->str + off, ps->str + off + len, len_to_copy);
    ps->len -= len;
    ps->str[ps->len] = 0;
}

static const char* simplestring_get(SimpleString* ps)
{
    return ps->str;
}

static size_t simplestring_length(SimpleString* ps)
{
    return ps->len;
}

static void simplestring_free(SimpleString* ps)
{
    free(ps->str);
    ps->str = NULL;
    ps->len = 0;
    ps->alloclen = 0;
}









typedef struct SimpleStringListStruct {
    char** arr;
    size_t size;
    size_t alloc;
} SimpleStringList;

static void simplestringlist_init(SimpleStringList* sl)
{
    sl->size = 0;
    sl->alloc = 0;
    sl->arr = NULL;
}

static void simplestringlist_push(SimpleStringList* sl, const char* value)
{
    if (sl->size >= sl->alloc)
    {
        sl->alloc += 20;
        sl->arr = realloc(sl->arr, sizeof(char*) * sl->alloc);
    }

    sl->arr[sl->size++] = value ? strdup(value) : NULL;
}

static const char* simplestringlist_get(SimpleStringList* sl, size_t idx)
{
    if (idx >= sl->size)
        return NULL;
    return sl->arr[idx];
}

static size_t simplestringlist_size(SimpleStringList* sl)
{
    return sl->size;
}

static void simplestringlist_free(SimpleStringList* sl)
{
    size_t i;

    for (i = 0; i < sl->size; ++i)
        free(sl->arr[i]);

    free(sl->arr);
    sl->size = 0;
    sl->alloc = 0;
    sl->arr = NULL;
}










typedef struct FlexioStruct {
    CURL* curl;
    char* host;
    int port;
    char* user;
    char* password;
    char* apikey;
    char* pipe;
    bool ssl_verify;
    bool debug_output;
    flexio_paramset_t params;
    flexio_run_callback_t callback;
    void* callback_param;
    SimpleStringList files;
} Flexio;



// poor man's json value extractor -- does not handle embedded quotes
static char* getJsonValue(const char* json, const char* key)
{
    size_t keylen = strlen(key);

    while (*json)
    {
        if (*json == '"')
        {
            json++;
            if (0 == memcmp(json, key, keylen) && *(json+keylen) == '"')
            {
                json += (keylen+1);
                while (isspace(*json))
                    json++;
                if (*json != ':')
                    return false; // missing colon
                json++;
                while (isspace(*json))
                    json++;
                if (!*json)
                    return false; // unexpected end

                const char* p = json;

                if (*p == '"')
                {
                    p++;
                    json++;
                    while (*p && *p != '"')
                        p++;
                    if (!*p)
                    {
                        // reached end -- not found
                        return false;
                    }

                }
                 else
                {
                    while (*p && *p != ',' && *p != '}')
                        p++;
                    if (!*p)
                    {
                        // reached end -- not found
                        return false;
                    }
                }

                size_t retlen = (p - json);
                char* ret = malloc(retlen+1);
                memcpy(ret, json, retlen);
                *(ret + retlen) = 0;
                return ret;
            }
        }

        json++;
    }

    return NULL;
}








struct FlexioParamSet {
    struct FlexioParamSet* next;
    char* key;
    char* value;
};

flexio_paramset_t flexio_paramset_new()
{
    struct FlexioParamSet* f = (struct FlexioParamSet*)malloc(sizeof(struct FlexioParamSet));
    f->next = NULL;
    f->key = NULL;
    f->value = NULL;
    return (flexio_paramset_t)f;
}

void flexio_paramset_push(flexio_paramset_t handle, const char* key, const char* value)
{
    struct FlexioParamSet* p = (struct FlexioParamSet*)handle;

    if (p->key)
    {
        // already something here -- go to the end
        while (p->next)
            p = p->next;

        p->next = flexio_paramset_new();
        p = p->next;
    }

    p->key = strdup(key);
    p->value = strdup(value);
}

void flexio_paramset_free(flexio_paramset_t handle)
{
    struct FlexioParamSet* p = (struct FlexioParamSet*)handle;
    struct FlexioParamSet* next;

    do
    {
        free(p->key);
        free(p->value);
        p = p->next;
    }
    while (p);

    p = (struct FlexioParamSet*)handle;
    do
    {
        next = p->next;
        free(p);
        p = next;
    } while (p);
}

void flexio_paramset_tojson(flexio_paramset_t handle, SimpleString* json)
{
    simplestring_clear(json);

    simplestring_append_char(json, '{');

    struct FlexioParamSet* p = (struct FlexioParamSet*)handle;
    size_t cnt = 0;
    do
    {
        if (p->key)
        {
            if (cnt > 0)
            {
                simplestring_append(json, ",");
            }
            simplestring_add_jsonkeypair(json, p->key, p->value, true);
        }

        p = p->next;
    } while (p);

    simplestring_append_char(json, '}');
}

static bool flexio_paramset_isempty(flexio_paramset_t handle)
{
    struct FlexioParamSet* p = (struct FlexioParamSet*)handle;
    return p->key ? false : true;
}








flexio_handle_t flexio_new()
{
    // initialize curl
    if (0 != curl_global_init(CURL_GLOBAL_ALL))
    {
        // failure
        return (flexio_handle_t)0;
    }

    Flexio* f = (Flexio*)malloc(sizeof(Flexio));
    f->curl = curl_easy_init();
    f->host = strdup("www.flex.io");
    f->ssl_verify = true;
    f->user = NULL;
    f->password = NULL;
    f->apikey = NULL;
    f->pipe = NULL;
    f->port = 443;
    f->debug_output = false;
    f->params = NULL;
    f->callback = NULL;
    f->callback_param = NULL;
    simplestringlist_init(&f->files);

    return (flexio_handle_t)f;
}


void flexio_free(flexio_handle_t handle)
{
    Flexio* f = (Flexio*)handle;
    if (f)
    {
        curl_easy_cleanup(f->curl);
        if (f->host) free(f->host);
        if (f->user) free(f->user);
        if (f->password) free(f->password);
        if (f->apikey) free(f->apikey);
        if (f->pipe) free(f->pipe);
        if (f->params) 
        simplestringlist_free(&f->files);
        free(f);
    }
}

void flexio_set_host(flexio_handle_t handle, const char* host)
{
    Flexio* f = (Flexio*)handle;
    if (f->host) free(f->host);
    f->host = strdup(host);
}

void flexio_set_sslverify(flexio_handle_t handle, bool ssl_verify)
{
    Flexio* f = (Flexio*)handle;
    f->ssl_verify = ssl_verify;
}

void flexio_set_port(flexio_handle_t handle, int port)
{
    Flexio* f = (Flexio*)handle;
    f->port = port;
}

void flexio_set_user(flexio_handle_t handle, const char* user)
{
    Flexio* f = (Flexio*)handle;
    if (f->user) free(f->user);
    f->user = strdup(user);
}

void flexio_set_password(flexio_handle_t handle, const char* password)
{
    Flexio* f = (Flexio*)handle;
    if (f->password) free(f->password);
    f->password = strdup(password);
}

void flexio_set_apikey(flexio_handle_t handle, const char* apikey)
{
    Flexio* f = (Flexio*)handle;
    if (f->apikey) free(f->apikey);
    f->apikey = strdup(apikey);
}

void flexio_set_pipe(flexio_handle_t handle, const char* pipe)
{
    Flexio* f = (Flexio*)handle;
    if (f->pipe) free(f->pipe);
    f->pipe = strdup(pipe);
}

void flexio_add_file(flexio_handle_t handle, const char* file)
{
    Flexio* f = (Flexio*)handle;
    simplestringlist_push(&f->files, file);
}


void flexio_set_params(flexio_handle_t handle, flexio_paramset_t params)
{
    Flexio* f = (Flexio*)handle;
    f->params = params;
}

void flexio_set_callback(flexio_handle_t handle, flexio_run_callback_t callback, void* param)
{
    Flexio* f = (Flexio*)handle;
    f->callback = callback;
    f->callback_param = param;
}

void flexio_set_debug(flexio_handle_t handle, bool value)
{
    Flexio* f = (Flexio*)handle;
    f->debug_output = value;
}

static size_t simplestring_writer(void* ptr, size_t size, size_t nmemb, void* stream)
{
    SimpleString* s = (SimpleString*)stream;
    simplestring_append_buf(s, ptr, size*nmemb);
    return (size*nmemb);
}

static size_t simplestring_reader(void* ptr, size_t size, size_t nmemb, void* stream)
{
    SimpleString* s = (SimpleString*)stream;
    size_t bytes = (size*nmemb);
    size_t slen = simplestring_length(s);
    if (bytes >= slen)
    {
        memcpy(ptr, simplestring_get(s), slen);
        return slen/size;
    }

    memcpy(ptr, simplestring_get(s), bytes);
    simplestring_erase(s, 0, bytes);
    return (size*nmemb);
}


static size_t post_data_source(void* ptr, size_t size, size_t nmemb, void* stream)
{
    size_t num = fread(ptr, size, nmemb, (FILE*)stream);
    return (size*num);
}

static size_t callback_proxy(void* ptr, size_t size, size_t nmemb, void* stream)
{
    Flexio* f = (Flexio*)stream;
    f->callback(FLEXIO_CALLBACK_DATA, (unsigned char*)ptr, size*nmemb, f->callback_param);
    return (size*nmemb);
}


static bool flexio_run_upload_file(flexio_handle_t handle, const char* filename, const char* auth_header, const char* process_eid)
{
    Flexio* f = (Flexio*)handle;

    SimpleString result, url;
    CURLcode curl_result;
    struct curl_slist* headers = NULL;


    // open file to post
    FILE* file;
    int filelen = 0;

    if (filename == NULL)
    {
        // use stdin -- read in entire stdin stream so we can know the size;
        // it would be better to use http Connection: close and no Content-Length,
        // but libcurl doesn't support this

#ifdef _MSC_VER
        setmode(fileno(stdin), O_BINARY);
#endif

        file = tmpfile();

        #define BUFSIZE 32768
        unsigned char buf[BUFSIZE];

        while (true)
        {
            size_t bytes_read = fread(buf, 1, BUFSIZE, stdin);
            filelen += (int)fwrite(buf, 1, bytes_read, file);
            if (bytes_read == 0)
                break;
        }

        fseek(file, 0L, SEEK_SET);
    }
     else
    {
        file = fopen(filename, "rb");
        if (!file)
            return FLEXIO_ERROR_FILE_OPEN;
        fseek(file, 0L, SEEK_END);
        filelen = ftell(file);
        fseek(file, 0L, SEEK_SET);
    }



    simplestring_init(&result);
    simplestring_init(&url);
    simplestring_append(&url, "https://");
    simplestring_append(&url, f->host);
    simplestring_append(&url, "/api/v1/processes/");
    simplestring_append(&url, process_eid);
    simplestring_append(&url, "/input");


    if (f->debug_output)
    {
        fprintf(stderr, "POST %s file=%s\n", url.str, filename);
    }
    
    curl_result = curl_easy_setopt(f->curl, CURLOPT_URL, simplestring_get(&url));
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POST, 1);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEDATA, (void*)&result);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEFUNCTION, (void*)simplestring_writer);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_READFUNCTION, (void*)post_data_source);

    headers = NULL;
    if (auth_header && auth_header[0])
    {
        headers = curl_slist_append(headers, auth_header);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPHEADER, headers);
    }

    struct curl_httppost* form_fields = NULL;
    struct curl_httppost* form_fields_last = NULL;


    curl_formadd(&form_fields, &form_fields_last,
        CURLFORM_PTRNAME, "file",
        CURLFORM_FILENAME, (filename ? filename : "input.dat"),
        CURLFORM_STREAM, (void*)file,
        CURLFORM_CONTENTTYPE, "application/octet-stream",
        CURLFORM_CONTENTSLENGTH, filelen,
        CURLFORM_END);


    // set the post multipart parameters
    curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPPOST, form_fields);
    curl_result = curl_easy_perform(f->curl);
    curl_slist_free_all(headers);

    if (f->debug_output)
    {
        fprintf(stderr, "result: %s\n", result.str);
    }

    fclose(file);
 
    simplestring_free(&url);
    simplestring_free(&result);

    if (curl_result != CURLE_OK)
    {
        return FLEXIO_ERROR;
    }

    curl_formfree(form_fields);

    return FLEXIO_OK;
}

flexio_error_t flexio_run(flexio_handle_t handle)
{
    Flexio* f = (Flexio*)handle;

    // make an authorization header, if desired
    char auth_header[255];
    auth_header[0] = 0;
    struct curl_slist* headers = NULL;

    char process_eid[255];
    char* p;

    CURLcode curl_result;

    SimpleString url;
    SimpleString result;
    SimpleString json, json2;


    if (f->apikey)
    {
        if (snprintf(auth_header, sizeof(auth_header), "Authorization: Bearer %s", f->apikey) >= sizeof(auth_header))
            return FLEXIO_ERROR_AUTHENTICATION; // buffer too small
    }


    // check to make sure pipe is valid
    if (!f->pipe || strlen(f->pipe) == 0)
        return FLEXIO_ERROR_INVALID_PIPE;

    if (!f->host || strlen(f->host) == 0)
        return FLEXIO_ERROR_INVALID_PIPE;




    /*
    // first, log in (if we don't have an api key)
    if (!f->apikey)
    {
        simplestring_init(&url);
        simplestring_append(&url, "https://");
        simplestring_append(&url, f->host);
        simplestring_append(&url, "/api/v1/login");

        simplestring_init(&ps);
        simplestring_add_urlkeypair(&ps, "username", f->user ? f->user : "");
        simplestring_add_urlkeypair(&ps, "password", f->password ? f->password : "");

        simplestring_init(&result);

        curl_result = curl_easy_setopt(f->curl, CURLOPT_URL, simplestring_get(&url));
        curl_result = curl_easy_setopt(f->curl, CURLOPT_POST, 1);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_POSTFIELDS, simplestring_get(&ps));
        curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEDATA, (void*)&result);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEFUNCTION, (void*)simplestring_writer);
        curl_result = curl_easy_perform(f->curl);
        simplestring_free(&ps);
        simplestring_free(&url);
        int success = (0 == strcmp(simplestring_get(&result), "true") ? 1:0);
        simplestring_free(&result);
        if (curl_result != CURLE_OK)
        {
            return FLEXIO_ERROR;
        }
        if (!success)
        {
            return FLEXIO_ERROR_AUTHENTICATION;
        }
    }
    */
    

    // if user wants, turn off ssl host verify
    if (!f->ssl_verify)
    {
        curl_easy_setopt(f->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_easy_setopt(f->curl, CURLOPT_SSL_VERIFYPEER, 0);
    }



    // create a process object


    simplestring_init(&json);
    simplestring_append(&json, "{");
    simplestring_add_jsonkeypair(&json, "parent_eid", f->pipe, true);

    if (f->params)
    {
        simplestring_init(&json2);
        flexio_paramset_tojson(f->params, &json2);
        simplestring_append_char(&json, ',');
        simplestring_add_jsonkeypair(&json, "params", simplestring_get(&json2), false);
        simplestring_free(&json2);
    }
    simplestring_append(&json, "}");


    simplestring_init(&result);
    simplestring_init(&url);
    simplestring_append(&url, "https://");
    simplestring_append(&url, f->host);
    simplestring_append(&url, "/api/v1/processes");

    if (f->debug_output)
    {
        fprintf(stderr, "POST %s\n", url.str);
    }

    curl_result = curl_easy_setopt(f->curl, CURLOPT_URL, simplestring_get(&url));
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POST, 1);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POSTFIELDS, simplestring_get(&json));
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEDATA, (void*)&result);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEFUNCTION, (void*)simplestring_writer);


    headers = NULL;
    if (auth_header[0])
    {
        headers = curl_slist_append(headers, auth_header);
    }

    headers = curl_slist_append(headers, "Content-Type: application/json");
    curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPHEADER, headers);
    curl_result = curl_easy_perform(f->curl);
    curl_slist_free_all(headers);

    if (f->debug_output)
    {
        fprintf(stderr, "result: %s\n", result.str);
    }

    p = getJsonValue(result.str, "eid");
    process_eid[0] = 0;
    if (p)
    {
        strcpy(process_eid, p);
        free(p);
    }

    simplestring_free(&json);
    simplestring_free(&url);
    simplestring_free(&result);


    switch (curl_result)
    {
        case CURLE_SSL_CONNECT_ERROR:   return FLEXIO_ERROR_SSL;
        case CURLE_ABORTED_BY_CALLBACK: return FLEXIO_ERROR;
    }

    long http_code = 0;
    curl_easy_getinfo(f->curl, CURLINFO_RESPONSE_CODE, &http_code);

    switch (http_code)
    {
        case 400: return FLEXIO_ERROR; // general error
        case 401: return FLEXIO_ERROR_AUTHENTICATION;
        case 403: return FLEXIO_ERROR_FORBIDDEN;
        case 404: return FLEXIO_ERROR_INVALID_PIPE;
    }

    // catch all
    if (curl_result != CURLE_OK || !p || strlen(p) > sizeof(process_eid) - 1)
    {
        return FLEXIO_ERROR;
    }






    // upload a stream to the process


    size_t i, len = simplestringlist_size(&f->files);
    for (i = 0; i < len; ++i)
    {
        flexio_run_upload_file(handle, simplestringlist_get(&f->files, i), auth_header, process_eid);
    }






    // execute the process

    simplestring_init(&url);
    simplestring_append(&url, "https://");
    simplestring_append(&url, f->host);
    simplestring_append(&url, "/api/v1/processes/");
    simplestring_append(&url, process_eid);
    simplestring_append(&url, "/run?background=false");

    if (f->debug_output)
    {
        fprintf(stderr, "POST %s\n", url.str);
    }

    simplestring_init(&result);

    curl_result = curl_easy_setopt(f->curl, CURLOPT_URL, simplestring_get(&url));
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POST, 1);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POSTFIELDS, NULL);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_POSTFIELDSIZE, 0);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEDATA, (void*)&result);
    curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEFUNCTION, (void*)simplestring_writer);

    headers = NULL;
    if (auth_header[0])
    {
        headers = curl_slist_append(headers, auth_header);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPHEADER, headers);
    }

    curl_result = curl_easy_perform(f->curl);
    curl_slist_free_all(headers);

    if (f->debug_output)
    {
        fprintf(stderr, "result: %s\n", result.str);
    }

    //simplestring_free(&ps);
    simplestring_free(&url);
    simplestring_free(&result);





    // get results, if desired
    if (f->callback)
    {

        // execute the process

        simplestring_init(&url);
        simplestring_append(&url, "https://");
        simplestring_append(&url, f->host);
        simplestring_append(&url, "/api/v1/processes/");
        simplestring_append(&url, process_eid);
        simplestring_append(&url, "/output?fields=content&format=text/plain");

        if (f->debug_output)
        {
            fprintf(stderr, "POST %s\n", url.str);
        }

        simplestring_init(&result);

        curl_result = curl_easy_setopt(f->curl, CURLOPT_URL, simplestring_get(&url));
        curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPGET, 1);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEDATA, (void*)f);
        curl_result = curl_easy_setopt(f->curl, CURLOPT_WRITEFUNCTION, (void*)callback_proxy);

        headers = NULL;
        if (auth_header[0])
        {
            headers = curl_slist_append(headers, auth_header);
            curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPHEADER, headers);
        }

        f->callback(FLEXIO_CALLBACK_BEGIN, NULL, 0, f->callback_param);

        curl_result = curl_easy_perform(f->curl);
        curl_slist_free_all(headers);

        //simplestring_free(&ps);
        simplestring_free(&url);
        simplestring_free(&result);

        f->callback(FLEXIO_CALLBACK_END, NULL, 0, f->callback_param);
    }


    return FLEXIO_OK;


    /*
    struct curl_httppost* form_fields = NULL;
    struct curl_httppost* form_fields_last = NULL;

    curl_formadd(&form_fields, &form_fields_last,
    CURLFORM_PTRNAME, "file",
    CURLFORM_FILENAME, (f->file ? f->file : "file.txt"),
    CURLFORM_STREAM, (void*)file,
    CURLFORM_END);

    // set the post multipart parameters
    curl_result = curl_easy_setopt(f->curl, CURLOPT_HTTPPOST, form_fields);
    curl_result = curl_easy_perform(f->curl);

    curl_formfree(form_fields);

    */



    return FLEXIO_OK;
}





#ifdef __cplusplus
}
#endif
