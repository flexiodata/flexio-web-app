/**
*
* Copyright (c) 2016, Gold Prairie LLC.  All rights reserved.
*
* Project:  Flex.io Client Library
* Author:   Benjamin I. Williams
* Created:  2016-10-19
*
* @package libflexio
* @subpackage
*/


#ifndef __SHA256_H
#define __SHA256_H



typedef struct {
    unsigned int state[8];
    unsigned char buffer[64];
    unsigned long long count;
} sha256_ctx_t;


void sha256_init(sha256_ctx_t* ctx);
void sha256_update(sha256_ctx_t* ctx, const unsigned char* data, size_t len);
void sha256_final(sha256_ctx_t* ctx, unsigned char* digest);

void sha256_hmac(const unsigned char* message, size_t message_len, const unsigned char* key, size_t key_len, unsigned char* digest);


#endif


