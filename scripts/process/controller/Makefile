
EXENAME = fxcontroller 
INCLUDES = 
CPP = g++
CFLAGS = 
LIBS = -lzmq -ljsoncpp -lpthread
DEPENDLIBS =

OBJECTS = controller.o

all: $(EXENAME)
    
$(EXENAME): $(OBJECTS) $(DEPENDLIBS)
		$(CPP) $(OBJECTS) $(LIBDIRS) $(LIBS) -o $(EXENAME)

clean:
	rm -f *.o *.gch $(EXENAME)

$(OBJECTS): %.o : %.cpp
	$(CPP) $(CFLAGS) $(INCLUDES) -c $<

