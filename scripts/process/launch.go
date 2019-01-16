package main

import "fmt"
import "os"
import "os/exec"
import "syscall"

func main() {
    args := os.Args[1:]
    fmt.Println("Launching...")
    fmt.Println(args)
    if len(args) < 4 {
        fmt.Println("Usage: launch <path_to_os_image> <process_eid> <process base directory> command line...")
	os.Exit(1)
    }

    fmt.Println(os.Getuid())
    if os.Getuid() != 0 {
        fmt.Println("launch wants to be run as root")
	os.Exit(1)
    }

    var os_image_path = args[0]
    var process_eid = args[1]
    var process_base_dir = args[2]
    var commands = args[3:]

    var process_dir = process_base_dir + "/" + process_eid
    var newroot_dir = process_dir + "/newroot"
    var work_dir = process_dir + "/work"
    var upper_dir = process_dir + "/upper"

    os.Mkdir(process_dir, 0700)
    os.Mkdir(upper_dir, 0755)
    os.Mkdir(work_dir, 0755)
    fmt.Println("Making " + newroot_dir)
    os.Mkdir(newroot_dir, 0755)

    if err := syscall.Mount("none", newroot_dir, "overlay", 0, "lowerdir="+os_image_path+",upperdir="+upper_dir+",workdir="+work_dir); err != nil {
        fmt.Println("Mount error: " + err.Error())
	os.Exit(1)
    }

    if err := syscall.Mount("none", newroot_dir + "/proc", "proc", 0, ""); err != nil {
        fmt.Println("Mount (proc) error: " + err.Error())
	os.Exit(1)
    }


    //cmd := exec.Command("/usr/sbin/chroot", "--userspec=nobody:nogroup", newroot_dir, "/bin/date")

    cmdline := []string{"--userspec=nobody:nogroup", newroot_dir}
    cmdline = append(cmdline, commands...)
    fmt.Printf("Running %v", cmdline)
    cmd := exec.Command("/usr/sbin/chroot", cmdline...)
    //cmd := exec.Command("/bin/ls -al /")
    out, err := cmd.CombinedOutput()
    if err != nil {
        fmt.Println("Error running command: " + err.Error())
    }

    fmt.Printf("Output: %s\n", out)


    if err := syscall.Unmount(newroot_dir + "/proc", 0); err != nil {
        fmt.Println("Unmount (proc) error: " + err.Error())
	os.Exit(1)
    }

    if err := syscall.Unmount(newroot_dir, 0); err != nil {
        fmt.Println("Unmount error: " + err.Error())
	os.Exit(1)
    }


    os.RemoveAll(process_dir)
}

