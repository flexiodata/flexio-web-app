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


#include <stdlib.h>
#include <stddef.h>
#include "sha256.h"

// adapted from public domain SHA256 code

static const unsigned int k[64] = {
    0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5,
    0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174,
    0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
    0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967,
    0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
    0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3,
    0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2
};

#define SHA256_BLOCK_SIZE 64
#define SHA256_HASH_SIZE 32

#define ROTL(x, n) (((x) << (n)) | ((x) >> (32 - (n))))
#define ROTR(x, n) (((x) >> (n)) | ((x) << (32 - (n))))

#define S0(x) (ROTR(x, 2) ^ ROTR(x,13) ^ ROTR(x, 22))
#define S1(x) (ROTR(x, 6) ^ ROTR(x,11) ^ ROTR(x, 25))
#define s0(x) (ROTR(x, 7) ^ ROTR(x,18) ^ (x >> 3))
#define s1(x) (ROTR(x,17) ^ ROTR(x,19) ^ (x >> 10))

#define BLK0(i) (w[i] = data[i])
#define BLK2(i) (w[i&15] += s1(w[(i-2)&15]) + w[(i-7)&15] + s0(w[(i-15)&15]))

#define CH(x,y,z) (z^(x&(y^z)))
#define MAJ(x,y,z) ((x&y)|(z&(x|y)))

#define R(a,b,c,d,e,f,g,h, i) h += S1(e) + CH(e,f,g) + k[i+j] + (j ? BLK2(i) : BLK0(i)); \
        d += h; h += S0(a) + MAJ(a, b, c)

#define RX_8(i) \
  R(a,b,c,d,e,f,g,h, i); \
  R(h,a,b,c,d,e,f,g, i+1); \
  R(g,h,a,b,c,d,e,f, i+2); \
  R(f,g,h,a,b,c,d,e, i+3); \
  R(e,f,g,h,a,b,c,d, i+4); \
  R(d,e,f,g,h,a,b,c, i+5); \
  R(c,d,e,f,g,h,a,b, i+6); \
  R(b,c,d,e,f,g,h,a, i+7)


void sha256_init(sha256_ctx_t* ctx)
{
    ctx->state[0] = 0x6a09e667;
    ctx->state[1] = 0xbb67ae85;
    ctx->state[2] = 0x3c6ef372;
    ctx->state[3] = 0xa54ff53a;
    ctx->state[4] = 0x510e527f;
    ctx->state[5] = 0x9b05688c;
    ctx->state[6] = 0x1f83d9ab;
    ctx->state[7] = 0x5be0cd19;
    ctx->count = 0;
}

static void sha256_transform(unsigned int *state, const unsigned int* data)
{
    unsigned int w[16];
    unsigned j;

    unsigned int a, b, c, d, e, f, g, h;
    a = state[0];
    b = state[1];
    c = state[2];
    d = state[3];
    e = state[4];
    f = state[5];
    g = state[6];
    h = state[7];

    for (j = 0; j < 64; j += 16)
    {
        RX_8(0);
        RX_8(8);
    }

    state[0] += a;
    state[1] += b;
    state[2] += c;
    state[3] += d;
    state[4] += e;
    state[5] += f;
    state[6] += g;
    state[7] += h;
}


static void sha256_writebyteblock(sha256_ctx_t* ctx)
{
    unsigned int data[16];
    unsigned i;

    for (i = 0; i < 16; i++)
    {
        data[i] = ((unsigned int)(ctx->buffer[i * 4]) << 24) +
                  ((unsigned int)(ctx->buffer[i * 4 + 1]) << 16) +
                  ((unsigned int)(ctx->buffer[i * 4 + 2]) << 8) +
                  ((unsigned int)(ctx->buffer[i * 4 + 3]));
    }

    sha256_transform(ctx->state, data);
}

void sha256_update(sha256_ctx_t* ctx, const unsigned char* data, size_t size)
{
    unsigned int curpos = (unsigned int)ctx->count & 0x3F;

    while (size > 0)
    {
        ctx->buffer[curpos++] = *data++;
        ctx->count++;
        size--;
        if (curpos == 64)
        {
            curpos = 0;
            sha256_writebyteblock(ctx);
        }
    }
}

void sha256_final(sha256_ctx_t* ctx, unsigned char *digest)
{
    unsigned long long length_bits = (ctx->count << 3);
    unsigned int pos = (unsigned int)ctx->count & 0x3F;
    unsigned i;

    ctx->buffer[pos++] = 0x80;
    while (pos != (64 - 8))
    {
        pos &= 0x3F;
        if (pos == 0)
            sha256_writebyteblock(ctx);
        ctx->buffer[pos++] = 0;
    }

    for (i = 0; i < 8; ++i)
    {
        ctx->buffer[pos++] = (unsigned char)(length_bits >> 56);
        length_bits <<= 8;
    }

    sha256_writebyteblock(ctx);

    for (i = 0; i < 8; ++i)
    {
        *digest++ = (unsigned char)(ctx->state[i] >> 24);
        *digest++ = (unsigned char)(ctx->state[i] >> 16);
        *digest++ = (unsigned char)(ctx->state[i] >> 8);
        *digest++ = (unsigned char)(ctx->state[i]);
    }
}



void sha256_hmac(const unsigned char* message, size_t message_len, const unsigned char* key, size_t key_len, unsigned char* digest)
{
    sha256_ctx_t ctx;
    unsigned char temp_key[SHA256_HASH_SIZE];
    unsigned char i_key_pad[SHA256_BLOCK_SIZE];
    unsigned char o_key_pad[SHA256_BLOCK_SIZE];
    size_t i;

    if (key_len > SHA256_BLOCK_SIZE)
    {
        sha256_init(&ctx);
        sha256_update(&ctx, key, key_len);
        sha256_final(&ctx, temp_key);
        key = temp_key;
        key_len = SHA256_HASH_SIZE;
    }

    for (i = 0; i < SHA256_BLOCK_SIZE; ++i)
    {
        if (i < key_len)
        {
            o_key_pad[i] = key[i] ^ 0x5c;
            i_key_pad[i] = key[i] ^ 0x36;
        }
         else
        {
            o_key_pad[i] = 0x5c;
            i_key_pad[i] = 0x36;
        }
    }

    sha256_init(&ctx);
    sha256_update(&ctx, i_key_pad, SHA256_BLOCK_SIZE);
    sha256_update(&ctx, message, message_len);
    sha256_final(&ctx, temp_key);  // place the output in temp_key

    sha256_init(&ctx);
    sha256_update(&ctx, o_key_pad, SHA256_BLOCK_SIZE);
    sha256_update(&ctx, temp_key, SHA256_HASH_SIZE);
    sha256_final(&ctx, digest);
}

