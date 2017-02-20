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


#ifndef __LIBFLEXIO_H
#define __LIBFLEXIO_H

#ifdef __cplusplus
extern "C" {
#endif

#include <stddef.h>
#include <stdbool.h>

typedef void* flexio_handle_t;
typedef void* flexio_paramset_t;

typedef enum {
  FLEXIO_OK = 0,
  FLEXIO_ERROR = 1,
  FLEXIO_ERROR_SSL = 2,
  FLEXIO_ERROR_AUTHENTICATION = 3,
  FLEXIO_ERROR_FORBIDDEN = 4,
  FLEXIO_ERROR_FILE_OPEN = 5,
  FLEXIO_ERROR_INVALID_PIPE = 6
} flexio_error_t;



typedef enum {
    FLEXIO_CALLBACK_BEGIN = 1,
    FLEXIO_CALLBACK_DATA = 2,
    FLEXIO_CALLBACK_END = 3
} flexio_callback_type_t;


typedef bool (*flexio_run_callback_t)(flexio_callback_type_t type, const unsigned char* data, size_t len, void* param);


flexio_paramset_t flexio_paramset_new();
void flexio_paramset_push(flexio_paramset_t handle, const char* key, const char* value);
void flexio_paramset_free(flexio_paramset_t handle);


flexio_handle_t flexio_new();
void flexio_set_host(flexio_handle_t handle, const char* host);
void flexio_set_sslverify(flexio_handle_t handle, bool ssl_verify);
void flexio_set_port(flexio_handle_t handle, int port);
void flexio_set_apikey(flexio_handle_t handle, const char* apikey);
void flexio_set_pipe(flexio_handle_t handle, const char* pipe);
void flexio_set_debug(flexio_handle_t handle, bool value);
void flexio_set_params(flexio_handle_t handle, flexio_paramset_t params);
void flexio_set_callback(flexio_handle_t handle, flexio_run_callback_t callback, void* param);
void flexio_add_file(flexio_handle_t handle, const char* file);
flexio_error_t flexio_run(flexio_handle_t handle);
void flexio_free(flexio_handle_t handle);



#ifdef __cplusplus
}
#endif


#endif // __LIBFLEXIO_H
