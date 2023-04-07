
Cleaning up in a more professional manner and a too professional manner
Windows/Linux - Go?

- try arbituarly write data to disk over all locations of where the ADDecrypt.zip, ADDecrypt and its contents are stored as data for OS record keeping and the disk and how as DFIR would you counter that if you could hold any VMs or Live system in a suspended state or have a holographic file system where there is session layer of previous session and current session for dynamic analysis in Malware Analysis. 

TrustedSec Tool Lab
https://github.com/trustedsec/social-engineer-toolkit
Unicorn - https://github.com/trustedsec/unicorn

- https://github.com/trustedsec/trevorc2
- https://github.com/trustedsec/ptf
- https://github.com/trustedsec/hate_crack

Seatbelt compile and used

Host Vulnhub box and do both Red and Blue Teaming 





Firewall fun
```powershell
# Check Profile
netsh advfirewall show currentprofile

# Turn off the Firewall
NetSh Advfirewall set allprofiles state off

# Modify a Firewall Rule for a program
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=in enable=yes action=allow profile=Private
netsh advfirewall firewall add rule name="<Rule Name>" program="<FilePath>" protocol=tcp dir=out enable=yes action=allow profile=Private
# Delete a rule
netsh.exe advfirewall firewall delete rule "<Rule Name>"
```
[test](https://www.itninja.com/blog/view/how-to-add-firewall-rules-using-netsh-exe-advanced-way)


```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block 
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block |
Format-Table -Property 
DisplayName, 
@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},
@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}}, @{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},
@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}},
Enabled,
Profile,
Direction,
Action"
```

```powershell
powershell -c "Get-NetFirewallRule -Direction Outbound -Enabled True -Action Block | Format-Table -Property DisplayName,@{Name='Protocol';Expression={($PSItem | Get-NetFirewallPortFilter).Protocol}},@{Name='LocalPort';Expression={($PSItem | Get-NetFirewallPortFilter).LocalPort}},@{Name='RemotePort';Expression={($PSItem | Get-NetFirewallPortFilter).RemotePort}},@{Name='RemoteAddress';Expression={($PSItem | Get-NetFirewallAddressFilter).RemoteAddress}}, Enabled, Profile,Direction,Action"

```

```powershell
powershell -c Get-NetFirewallRule -Direction Outbound -Enabled True -Action Allow
```



- Enabling RDP with cme and impacket kerboros tickets 
	- Harden and or implement AMSI with powershell 
- Create an alert based on .exe and .ps1 from PowerUP, Winpeas 
- Remote interaction with box that would no lead to compromise
- Open RDP for a new user and Get Sysinternals on box


https://github.com/dubs3c/sudo_sniff/blob/master/sudo_sniff.c


Alh4zr3d says sliver is better than empire 
https://github.com/BishopFox/sliver

https://github.com/0vercl0k - looks insane https://github.com/0vercl0k/clairvoyance


Procmon something and follow along

Atomic wannabe APT 
https://github.com/redcanaryco/invoke-atomicredteam





A memory safe rust rootkits - [Diamorphine](https://github.com/m0nad/Diamorphine/blob/master/README.md) and [Kris Nova's boopkit](https://github.com/krisnova/boopkit)

Bootkit

A C polymorphic code
https://github.com/m0nad/PSG/blob/master/psg.c


RUST, python And Golang C2 with Rootkit - barebones nothing fancy extra customisable

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