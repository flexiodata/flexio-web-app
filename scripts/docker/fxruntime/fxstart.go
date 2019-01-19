package main


import (
    zmq "github.com/pebbe/zmq4"
    "encoding/base64"
    "os"
    "os/exec"
    "bufio"
    //"fmt"
    "encoding/json"
)


func main() {

    engine := os.Getenv("FLEXIO_EXECUTE_ENGINE")
    runtime_server := os.Getenv("FLEXIO_RUNTIME_SERVER")
    runtime_key := os.Getenv("FLEXIO_RUNTIME_KEY")

    requester, _ := zmq.NewSocket(zmq.REQ)
	defer requester.Close()
	requester.Connect(runtime_server)


    var payload string
    payload = `{
        "version": 1,
        "access_key": "` + runtime_key + `",
        "method": "hello",
        "params": [],
        "id": "111"
    }`

    requester.Send(payload, 0)
    reply, _ := requester.Recv(0)



    //var dat map[string]interface{}
    //if err := json.Unmarshal([]byte(reply), &dat); err != nil {
    //    panic(err)
    //}
    //code := dat["result"].(string)

    var prog string
    var cmdline []string
    var dir string
    if engine == "python" {
        //f, _ := os.Create("/fxpython/script.py")
        //f.Write([]byte(code))
        //f.Close()
        prog = "/usr/bin/python3"
        cmdline = []string{"-c", "import flexio as f; import script as s; f.run(s)"}
        //dir = "/fxpython"
        dir = "/fxruntime/src"
    } else if engine == "nodejs" {
        //f, _ := os.Create("/fxnodejs/script.js")
        //f.Write([]byte(code))
        //f.Close()
        prog = "/usr/bin/node"
        cmdline = []string{"-e", "var f = require('/fxnodejs/flexio'); var s = require('./script'); f.run(s)"}
        dir = "/fxruntime/src"
    } else {
        panic("Error: unknown execution engine")
    }

    //fmt.Printf("Code is %s\n", code)
    //fmt.Printf("Running %s %s\n", prog, cmdline)

    os.Chdir(dir)
    //prog = "/bin/ls"
    //cmdline = []string{"-al"}

    cmd := exec.Command(prog, cmdline...)
    cmd.Dir = dir
    //out, err := cmd.Output()


    cmd_reader, _ := cmd.StdoutPipe()
    scanner := bufio.NewScanner(cmd_reader)
    go func() {
        for scanner.Scan() {
            //fmt.Printf(scanner.Text())

            payload = `{
                "version": 1,
                "access_key": "` + runtime_key + `",
                "method": "write",
                "params": [1, "~199$*#991/bin.b64:` + base64.StdEncoding.EncodeToString(scanner.Bytes()) + `"],
                "id": "199$*#991"
            }`

            requester.Send(payload, 0)
            reply, _ = requester.Recv(0)
        
        }
    }()

    stderr_reader, _ := cmd.StderrPipe()
    scanner_stderr := bufio.NewScanner(stderr_reader)
    go func() {
        for  scanner_stderr.Scan() {
            //fmt.Printf( scanner_stderr.Text())

            json_string, _ := json.Marshal(scanner_stderr.Text())
            payload = `{
                "version": 1,
                "access_key": "` + runtime_key + `",
                "method": "compile_error",
                "params": [`+string(json_string)+`],
                "id": "199$*#991"
            }`

            requester.Send(payload, 0)
            reply, _ = requester.Recv(0)
        
        }
    }()

    cmd.Start()
    cmd.Wait()



    payload = `{
        "version": 1,
        "access_key": "` + runtime_key + `",
        "method": "exit_loop",
        "params": [],
        "id": "111"
    }`

    requester.Send(payload, 0)
    reply, _ = requester.Recv(0)

}
