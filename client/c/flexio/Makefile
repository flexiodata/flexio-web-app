CC = gcc
EXENAME = flexio
INCLUDES = -I../libflexio/include 
DEFINES = -D_FILE_OFFSET_BITS=64 -D_LARGE_FILES
CFLAGS = 
LIBDIRS = 
LIBS = ../libflexio/libflexio.a -lcurl

OBJECTS = conf.o flexio.o

all: $(EXENAME)

$(EXENAME):$(OBJECTS)
	$(CC) $^ $(LINKFLAGS) $(LIBDIRS) $(LIBS) -o $(EXENAME)

clean:
	rm -f *.o $(EXENAME)

$(OBJECTS): %.o : %.c
	$(CC) $(CFLAGS) $(INCLUDES) $(DEFINES) -c $<


