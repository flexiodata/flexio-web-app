
#ifdef _MSC_VER
#define _CRT_SECURE_NO_WARNINGS
#pragma warning(disable:4996)
#endif

#include <stdio.h>
#include <stdlib.h>
#include <stddef.h>
#include <string.h>
#include <stdbool.h>
#include <ctype.h>
#include "conf.h"



struct ConfFileStruct {
    char* data;
    size_t datalen;
    char* ret;
    size_t retlen;
};

static char* find(conffile_t conf, const char* group, const char* key, size_t* return_len);


conffile_t conffile_new()
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)malloc(sizeof(struct ConfFileStruct));
    cf->data = NULL;
    cf->datalen = 0;
    cf->ret = NULL;
    cf->retlen = 0;
    return (conffile_t)(void*)cf;
}


void conffile_free(conffile_t conf)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;
    free(cf->data);
    free(cf->ret);
    free(cf);
}


bool conffile_load(conffile_t conf, const char* filename)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;

    FILE* f = fopen(filename, "rb");
    if (!f)
    {
        return false;
    }

    // get file size
    fseek(f, 0L, SEEK_END);
    int len = ftell(f);
    
    free(cf->data);
    cf->data = (char*)malloc(len*2);
    memset(cf->data, '@', len*2);
    if (cf->data == NULL)
    {
        fclose(f);
        return false;
    }

    // read in entire file
    fseek(f, 0L, SEEK_SET);
    if (len != fread(cf->data, 1, len, f))
    {
        fclose(f);
        return false;
    }


    fclose(f);
    cf->datalen = (size_t)len;
    return true;
}

bool conffile_save(conffile_t conf, const char* filename)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;

    FILE* f = fopen(filename, "wb");
    if (!f)
    {
        return false;
    }

    if (cf->datalen != fwrite(cf->data, 1, cf->datalen, f))
    {
        fclose(f);
        return false;
    }

    fclose(f);
    return true;

}

void conffile_set(conffile_t conf, const char* group, const char* key, const char* value)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;

    size_t return_len = 0;
    char* ch = find(conf, group, key, &return_len);

    int new_value_len = (int)strlen(value);


    if (ch)
    {
        // find the eq
        while (return_len > 0 && *ch != '=')
        {
            --return_len;
            ++ch;
        }

        if (*ch != '=')
            return;

        // value already exists -- replace it

        while (return_len > 0 && (*ch == '=' || isspace(*ch)))
        {
            --return_len;
            ++ch;
        }

        // determine new buffer size
        int old_value_len = (int)return_len;
        int new_size = (int)cf->datalen - old_value_len + new_value_len;

        if (old_value_len == new_value_len)
        {
            // simple update
            memcpy(ch, value, new_value_len);
        }
         else if (new_value_len < old_value_len)
        {
            // simple update, then shift rest
            memcpy(ch, value, new_value_len);
            memmove(ch+new_value_len, ch+old_value_len, (cf->data + cf->datalen) - (ch + old_value_len));
            cf->datalen = cf->datalen + new_value_len - old_value_len;
            char* test = cf->data + cf->datalen;
            *test = 'X';

        }
         else
        {
            // copy up until spot, add new data, copy rest of buffer
            ptrdiff_t ch_off = ch - cf->data;
            char* newbuf = (char*)malloc(new_size);
            memcpy(newbuf, cf->data, ch_off);
            memcpy(newbuf + ch_off, value, new_value_len);
            memcpy(newbuf + ch_off + new_value_len, cf->data + ch_off + old_value_len, (cf->data + cf->datalen) - (cf->data + ch_off + old_value_len));

            free(cf->data);
            cf->data = newbuf;
            cf->datalen = new_size;
        }
    }
     else
    {
        // value doesn't exist yet -- add it
        ch = find(conf, group, NULL, &return_len);
        if (ch)
        {
            // group exists -- add item to it

            // make it look nicer by skipping back a line, if available
            if (ch - cf->data > 1 && *(ch - 1) == '\n')
                ch--;
            if (ch - cf->data > 1 && *(ch - 1) == '\r') // windows CR/LF
                ch--;

            // copy up until spot, add new data, copy rest of buffer

            int key_size = (int)strlen(key);
            int value_size = (int)strlen(value);

            ptrdiff_t insert_off = ch - cf->data;
            char* newbuf = (char*)malloc(cf->datalen + key_size + 1 /*equals sign*/ + value_size + 4); // add four in case we need one or two extra CR/LFs

            char* p = newbuf;
            memcpy(p, cf->data, insert_off); p += insert_off;


            if (ch - cf->data > 1 && *(ch - 1) != '\n') // windows CR/LF
            {
                // add an extra CR/LF
                #ifdef WIN32
                *p = '\r'; p++;
                #endif
                *p = '\n'; p++;
            }


            memcpy(p, key, key_size); p += key_size;
            *p = '='; p++;
            memcpy(p, value, value_size); p += value_size;
            
            // add a CR/LF after our line
            #ifdef WIN32
            *p = '\r'; p++;
            #endif
            *p = '\n'; p++;

            memcpy(p, cf->data + insert_off, cf->datalen - insert_off);
            p += (cf->datalen - insert_off);

            free(cf->data);
            cf->data = newbuf;
            cf->datalen = p-newbuf;
        }
         else
        {
            // group doesn't exist yet

            int group_len = group ? (int)strlen(group) : 0;
            int key_size = (int)strlen(key);
            int value_size = (int)strlen(value);

            cf->data = realloc(cf->data, cf->datalen + 2 /*CR/LF*/ + 2 /* brackets */ + group_len + 2 /*CR/LF*/ + key_size + 1 /*=*/ + value_size + 2 /*CR/LF*/ );

            char* p = cf->data + cf->datalen;


            if (cf->datalen > 0)
            {
                #ifdef WIN32
                *p = '\r'; p++;
                #endif
                *p = '\n'; p++;
            }

            if (group_len > 0)
            {
                *p = '['; p++;
                memcpy(p, group, group_len); p += group_len;
                *p = ']'; p++;

                #ifdef WIN32
                *p = '\r'; p++;
                #endif
                *p = '\n'; p++;
            }

            memcpy(p, key, key_size); p += key_size;
            *p = '='; p++;
            memcpy(p, value, value_size); p += value_size;

            #ifdef WIN32
            *p = '\r'; p++;
            #endif
            *p = '\n'; p++;

            cf->datalen = p - cf->data;
        }
    }
}

const char* conffile_get(conffile_t conf, const char* group, const char* key)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;

    size_t return_len = 0;
    const char* ch = find(conf, group, key, &return_len);

    if (!ch)
        return NULL;

    while (return_len > 0 && *ch != '=')
    {
        --return_len;
        ++ch;
    }

    if (return_len == 0)
        return NULL;

    --return_len;
    ++ch;

    // trim front side
    while (return_len > 0 && isspace(*ch))
    {
        --return_len;
        ++ch;
    }

    // trim back side
    while (return_len > 0 && isspace(*(ch+return_len-1)))
    {
        --return_len;
    }

    if (cf->retlen < (int)(return_len+1))
    {
        cf->ret = realloc(cf->ret, return_len + 1);
        cf->retlen = (int)(return_len + 1);
    }

    memcpy(cf->ret, ch, return_len);
    *(cf->ret + return_len) = 0;

    return cf->ret;
}



static char* find(conffile_t conf, const char* group, const char* key, size_t* return_len)
{
    struct ConfFileStruct* cf = (struct ConfFileStruct*)conf;
    
    char* p = cf->data; char* end = cf->data + cf->datalen;
    char* linebegin = p;
    char* lineend;
    bool in_desired_group;
    int group_len = group ? (int)strlen(group) : 0;
    int key_len = key ? (int)strlen(key) : 0;

    // check if we are looking for things not contained in any group
    in_desired_group = (!group || !*group) ? true : false;


    while (true)
    {
        // first step, find the end of this line
        p = linebegin;
        while (p < end && *p != '\n')
            ++p;
        lineend = p;

        // process line
        p = linebegin;
        while (p < lineend && isspace(*p))
            ++p;
        if (p < lineend)
        {
            if (*p != '#') // ignore comment line
            {
                bool is_group_line = false;

                if (*p == '[')
                {
                    char* gbegin = p;
                    char* gend = lineend;
                    while (gend > linebegin && *gend != ']')
                        gend--;
                    if (*gend == ']')
                    {
                        // this is a group line
                        is_group_line = true;

                        if (in_desired_group && (!key || !*key))
                        {
                            // we are already in the desired group and 'key' was not specified,
                            // so return pointer to the end of the previous group
                            *return_len = 0;
                            return linebegin;
                        }

                        gbegin++;
                        gend--;

                        // trim
                        while (isspace(*gbegin))
                            gbegin++;
                        while (isspace(*gend))
                            gend--;


                        in_desired_group = ((gend-gbegin+1 == group_len && memcmp(group, gbegin, gend - gbegin + 1) == 0)) ? true : false;
                    }

                }
                
                if (!is_group_line && in_desired_group)
                {
                    // normal key/value line
                    char* eq = memchr(p, '=', lineend-p+1);
                    if (eq && eq > p)
                    {
                        eq--;
                        while (eq > p && isspace(*(eq-1)))
                            eq--;

                        if (eq-p+1 == key_len && memcmp(key, p, eq-p+1) == 0)
                        {
                            size_t len = lineend-linebegin;
                            while (len > 0 && *(linebegin+len-1) == '\r')
                            {
                                len--;
                            }

                            *return_len = len;
                            return linebegin;
                        }
                    }
                }
            }
        }



        // skip to next line, or exit loop if we are done
        p = lineend;
        while (p < end && (*p == '\r' || *p == '\n'))
            ++p;
        if (p >= end)
            break; // all done
        linebegin = p;
    }

    if (in_desired_group && (!key || !*key))
    {
        *return_len = 0;
        return cf->data + cf->datalen;
    }

    *return_len = 0;
    return NULL;
}



