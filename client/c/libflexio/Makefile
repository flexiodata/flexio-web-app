
CC = gcc
INCLUDES = -I./include 
DEFINES = -D_FILE_OFFSET_BITS=64 -D_LARGE_FILES
CFLAGS = -fPIC 
LIBS = 
OBJECTS = libflexio.o

VPATH=src:lib

all: libflexio.a

libflexio.a:$(OBJECTS)
	ar rc libflexio.a $(OBJECTS)
	ranlib libflexio.a

clean:
	rm -f *.o libflexio.a

$(OBJECTS): %.o : %.c
	@echo $<
	@$(CC) $(CFLAGS) $(INCLUDES) $(DEFINES) -c $<



