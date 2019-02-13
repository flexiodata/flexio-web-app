
EXENAME = fxstart
INCLUDES = 
CPP = g++
CFLAGS = 
LIBS = -lzmq
DEPENDLIBS =

OBJECTS = start.o base64.o

all: $(EXENAME)
    
$(EXENAME): $(OBJECTS) $(DEPENDLIBS)
		$(CPP) $(OBJECTS) $(LIBDIRS) $(LIBS) -o $(EXENAME)

clean:
	rm -f *.o *.gch $(EXENAME)

$(OBJECTS): %.o : %.cpp
	$(CPP) $(CFLAGS) $(INCLUDES) -c $<

