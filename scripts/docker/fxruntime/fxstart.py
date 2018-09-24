# import flexio as flexioext


# def flexio_start_handler(context):
#     import fxmodule
#     handler = getattr(fxmodule, 'flexio_handler', None)
#     if callable(handler):
#         handler(context)


# flexioext.run(flexio_start_handler)



import flexio as flexioext
import sys
import subprocess
import select
import os
from fcntl import fcntl, F_GETFL, F_SETFL
from os import O_NONBLOCK

context = flexioext.create_context()

proxy = flexioext.CallProxy()
str = proxy.invoke("get_script", [])
print("GOT " + str)




engine = os.environ['FLEXIO_EXECUTE_ENGINE']

if engine == 'python':
    file = open("/fxpython/script.py", "w")
    file.write(str)
    file.close()
    cwd = '/fxpython'
    command = ["/usr/bin/python3", "-c", "import flexio as f; import script as s; f.run(s)"]
elif engine == 'javascript':
    file = open("/fxnodejs/script.js", "w")
    file.write(str)
    file.close()
    cwd = '/fxnodejs'
    command = ["/usr/bin/node", "script.js"]
else:
    sys.exit("Error: unknown execution engine")


p = subprocess.Popen(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE, cwd=cwd)

stdout_fileno = p.stdout.fileno()
stderr_fileno = p.stderr.fileno()

flags = fcntl(p.stdout, F_GETFL)
fcntl(p.stdout, F_SETFL, flags | O_NONBLOCK)

flags = fcntl(p.stderr, F_GETFL)
fcntl(p.stderr, F_SETFL, flags | O_NONBLOCK)

while p.poll() is None:
    r,w,x = select.select([stdout_fileno,stderr_fileno],[],[],0)
    if len(r) == 0:
        continue

    for fd in r:
        if fd == stdout_fileno:
            buf = b''
            while True:
                chunk = p.stdout.read(1024)
                if chunk is None or len(chunk) == 0 or len(chunk) > 16384:
                    break;
                buf = buf + chunk
            if len(buf) > 0:
                context.output.write(buf)
        if fd == stderr_fileno:
            buf = b''
            while True:
                chunk = p.stderr.read(1024)
                if chunk is None or len(chunk) == 0 or len(chunk) > 16384:
                    break;
                buf = buf + chunk
            if len(buf) > 0:
                #context.output.write(buf)
                proxy.invoke("compile_error", [buf.decode()])









proxy.invoke("exit_loop")

#proxy.invoke("debug", [ val ])

# import zmq
# import sys
# import os
# import json
# import random
# import string

# from zmq.utils.monitor import recv_monitor_message

# context = zmq.Context()




# #  Socket to talk to server
# print("Connecting to hello world server")
# socket = context.socket(zmq.REQ)
# socket.connect(os.environ['FLEXIO_RUNTIME_SERVER'])



# socket.setsockopt(zmq.RCVTIMEO, 250)
# monitor = socket.get_monitor_socket(zmq.EVENT_CLOSED)



# def invoke(method, params):

#     call_id = ''.join(random.SystemRandom().choice(string.ascii_letters + string.digits) for _ in range(20))

#     payload = {
#         "version": 1,
#         "access_key": os.environ['FLEXIO_RUNTIME_KEY'],
#         "method": method,
#         "params": params,
#         "id": call_id
#     }



#     #socket.send(b"Hello123")
#     socket.send(json.dumps(payload).encode())

#     while True:
#         try:
#             resobj = socket.recv_json()
#             return resobj['result'] 
#         except zmq.ZMQError:
#             pass
        
#         try:
#             evt = recv_monitor_message(monitor, zmq.NOBLOCK)
#             if evt['event'] == zmq.EVENT_CLOSED:
#                 print("Connection broken")
#                 sys.exit(1)
#         except zmq.ZMQError:
#             pass



#     #  Get the reply.
#     #message = socket.recv()
#     #print("Received reply %s [ %s ]" % (request, message))

# str = invoke("upper", [ "Hello, there"])
# print("GOT " + str)
# invoke("debug", [ str ])


# invoke("exit_loop", [])
