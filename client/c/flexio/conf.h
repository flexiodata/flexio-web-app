


#include <stdbool.h>



typedef void* conffile_t;

conffile_t conffile_new();
void conffile_free(conffile_t f);
bool conffile_load(conffile_t f, const char* filename);
bool conffile_save(conffile_t f, const char* filename);
void conffile_set(conffile_t f, const char* group, const char* key, const char* value);
const char* conffile_get(conffile_t f, const char* group, const char* key);



