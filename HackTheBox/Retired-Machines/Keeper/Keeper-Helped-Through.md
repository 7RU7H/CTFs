# Keeper Helped-Through

Name: Keeper
Date: 30/4/2026
Difficulty: Easy 
Goals:  
- OSCP-like easy machine
- Write some Golang 
- Questioning what to do with this year and life in general after binge-ing physics for a year
- Write the initial working initial enumeration to automate `ping` and various quick enumerations that could be done manually but would cost time and save time in later `nmap` scans
Learnt:
- Copy and Pasta requirements on all things to notes
- Putty Keys
- Dorking better and often - always ask a question then a question about the question you asked when dorking MOSS
Beyond Root:
- Golang CTF basic enumeration binary 

- [[Keeper-Notes.md]]
- [[Keeper-CMD-by-CMDs.md]]

Although this is a easy machine and it started as a writeup I decided to make it a helped-through to decide what to focus on for the upcoming years. I have been learning a lot about Physics for various reasons and with AI, boom or bust and the general economy in the tech space bound to change. I decided if I am going to pass my OSCP I am going to have automate some time away so and ease back into going machines regularly. So I decided the bulk of next couple of machines would dedicated to improving my recon automation. I really invented into Cyber Security education and content creation and Physics just seems like they are just waiting for an Anomaly, which I cannot really participate in or problem solve from a distance. I followed along after the Password reuse did not work and change of life plans [IppSec Keeper.htb YouTube Writeup](https://www.youtube.com/watch?v=0AafRQIaWmQ). 

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Regular TCP scan against the keeper.htb enumerated two ports, 22 and 80 - nothing weird.
![](nmap-all-tcp.png)
Then a `nmap` scan of the found ports with `sC` and `sV.`
![](nmap-sc-sv.png)
Tried Nuclei scanning the web server
![1080](nuclei.png)

Tried out Katana to see what that does
![](katana-test.png)
Did not do much against http and IP.


![](pleaseraiseit.png)
The above page points and is identical to the keeper.htb
![](diffthehostsandip.png)

Adding domain name to /etc/hosts
![](errorhosts.png)
I removed the / from the second entry
![](nodomains.png)
connection issues persist
![](nokeepiesvhost.png)

Till my patience pays off
![](finally.png)

Request tracker has a default password
![](defaultpass.png)

And it works
![](rootpassword.png)

![](liseuser.png)

`Inorgaard : Welcome2023!` for Request tracker works
![](Welcome2023.png)

No password reuse
![](noeasysshpassreuse.png)

No ping back and the url differs
![](possccrf.png)

At this point I need a win in life and some confirmation I actually enjoy doing with my limited setup. I also wanted to code something in Golang just check if I actually like programming anymore. At this point I decided to update me AutomateRecon suite of scripts with a Golang source code that would. I then learnt that was the password for the machine from the introduction of the Ippsec Video. And I should have tried

![](initialpasswordretry.png)

![](keepasscrashreport.png)

I also tried `webmaster: Welcome2023!`. Then as I watched and was reminded of the copy and paste everything policy of old. It is a `l` not a `i`. Rough. 
## Foothold

Learning the fundamental copy-pasta mentality for this reason is part of CTFs. 
![](user.png)

## Privilege Escalation

There is the Keepass.dmp file in the RT30000.zip. Dorking `keepass extract password from dump` leads to [GitHub keepass-password-dumper](https://github.com/vdohney/keepass-password-dumper). This will not initial run on the HTB PwnBox so I first updated it. After unsuccessfully attempting to install DotNet 7 I followed along with the video, I have install different dotnet at some other points on older version of Kali than current. I then changed the version in the csproj file and that worked.

![](mdgrodmedflode.png)

`M}dgrød med fløde` dorked got `Rødgrød med fløde`, then uncapitalised the r `rødgrød med fløde`. I install and run the following to access .kdx file
```bash
# Pick one
apt install keepassxc -y
apt install keepass2 -y
 # Python tool
apt install kpcli -y # pip3 install kpcli # worked on ParrotOS
```

![](passwordkdxopenned.png)

IppSec also shows a manual way using `strings` and `grep`
```bash
strings -e S KeePassDumpFile.dmp | grep -a ^$(printf \\xCF\\x25\\xCF\\x25)
```

![](puttyroot.png)

Following along with IppSec as I have never used Putty Key files
```
F4><3K0nd!
```
This is just a junk password as there is no encryption

root.ppk
```
PuTTY-User-Key-File-3: ssh-rsa
Encryption: none
Comment: rsa-key-20230519
Public-Lines: 6
AAAAB3NzaC1yc2EAAAADAQABAAABAQCnVqse/hMswGBRQsPsC/EwyxJvc8Wpul/D
8riCZV30ZbfEF09z0PNUn4DisesKB4x1KtqH0l8vPtRRiEzsBbn+mCpBLHBQ+81T
EHTc3ChyRYxk899PKSSqKDxUTZeFJ4FBAXqIxoJdpLHIMvh7ZyJNAy34lfcFC+LM
Cj/c6tQa2IaFfqcVJ+2bnR6UrUVRB4thmJca29JAq2p9BkdDGsiH8F8eanIBA1Tu
FVbUt2CenSUPDUAw7wIL56qC28w6q/qhm2LGOxXup6+LOjxGNNtA2zJ38P1FTfZQ
LxFVTWUKT8u8junnLk0kfnM4+bJ8g7MXLqbrtsgr5ywF6Ccxs0Et
Private-Lines: 14
AAABAQCB0dgBvETt8/UFNdG/X2hnXTPZKSzQxxkicDw6VR+1ye/t/dOS2yjbnr6j
oDni1wZdo7hTpJ5ZjdmzwxVCChNIc45cb3hXK3IYHe07psTuGgyYCSZWSGn8ZCih
kmyZTZOV9eq1D6P1uB6AXSKuwc03h97zOoyf6p+xgcYXwkp44/otK4ScF2hEputY
f7n24kvL0WlBQThsiLkKcz3/Cz7BdCkn+Lvf8iyA6VF0p14cFTM9Lsd7t/plLJzT
VkCew1DZuYnYOGQxHYW6WQ4V6rCwpsMSMLD450XJ4zfGLN8aw5KO1/TccbTgWivz
UXjcCAviPpmSXB19UG8JlTpgORyhAAAAgQD2kfhSA+/ASrc04ZIVagCge1Qq8iWs
OxG8eoCMW8DhhbvL6YKAfEvj3xeahXexlVwUOcDXO7Ti0QSV2sUw7E71cvl/ExGz
in6qyp3R4yAaV7PiMtLTgBkqs4AA3rcJZpJb01AZB8TBK91QIZGOswi3/uYrIZ1r
SsGN1FbK/meH9QAAAIEArbz8aWansqPtE+6Ye8Nq3G2R1PYhp5yXpxiE89L87NIV
09ygQ7Aec+C24TOykiwyPaOBlmMe+Nyaxss/gc7o9TnHNPFJ5iRyiXagT4E2WEEa
xHhv1PDdSrE8tB9V8ox1kxBrxAvYIZgceHRFrwPrF823PeNWLC2BNwEId0G76VkA
AACAVWJoksugJOovtA27Bamd7NRPvIa4dsMaQeXckVh19/TF8oZMDuJoiGyq6faD
AF9Z7Oehlo1Qt7oqGr8cVLbOT8aLqqbcax9nSKE67n7I5zrfoGynLzYkd3cETnGy
NNkjMjrocfmxfkvuJ7smEFMg7ZywW7CBWKGozgz67tKz9Is=
Private-MAC: b0a0fd2edf4f0e557200121aa673732c9e76750739db05adc3ab65ec34c55cb0
```

We need putty-gen to convert to ssh key
```bash
apt install putty-tools
puttygen root.ppk -O private-openssh -o id_rsa
chmod 600 id_rsa
```

![](root.png)

## Post-Root-Reflection  

- I did not note the Request Tracker version
- Handled dotnet madness without waiting too long

## Beyond Root

My start at basic automated enumeration that I normally would do manually - untested will test on next box.
```go 
package main

import (
	"context"
	"flag"
	"fmt"
	"net/http"
	"os"
	"os/exec"
	"os/signal"
	"strings"
	"syscall"
	"time"
)

// Wrapper that handles cancel() for CTRL+C termination
func handleTermination(cancel context.CancelFunc) {
	fmt.Printf("Terminating application...\n")
	cancel()
	os.Exit(0)
}

// Ping host to get TTL data to detirmine OS type
func pingHost(ipAddress string) (bool, error) {
	pingHostThrice := exec.Command("ping", "-c 3", ipAddress)
	_, err := pingHostThrice.StdoutPipe()
	if err != nil {
		panic(err)
	}
	if err := pingHostThrice.Start(); err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
		fmt.Fprintf(os.Stdout, "Unable to execute `%s %s`\n", "ping -c 3 ", ipAddress)
		return false, err
	}
	if err := pingHostThrice.Wait(); err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
		fmt.Fprintf(os.Stdout, "Unable to complete execution of `%s %s`\n", "ping -c 3 ", ipAddress)
		return false, err
	}

	time.Sleep(1 * time.Second)
	fmt.Fprintf(os.Stdout, "Completed attempts to ping host, additional reminder to screenshot ping.png for notes")

	return true, nil
}

func testWebserverConnectivity(protocol, ipAddress string) (bool, error) {
	requestURL := fmt.Sprintf("%s://%s", protocol, ipAddress)
	req, err := http.NewRequestWithContext(ctx, "GET", requestURL, nil)
	if err != nil {
		fmt.Printf("Request creation failed: %v\n", err)
		os.Exit(1)
	}
	req.Header.Set("User-Agent", "Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0")

	res, err := http.DefaultClient.Do(req)
	if err != nil {
		fmt.Fprintln(os.Stderr,"Client Error making http request:", err)
		return false, err
	}

	fmt.Fprintln(os.Stdout, "client: got response!\n")
	fmt.Fprintln(os.Stdout, "client: status code: %d\n", res.StatusCode)
	if res.StatusCode != 200 {
		return false, nil
	} else {
		return true, nil
	}
}

// Use first test connectivity with curl and then to download the http and https root webpages
func downloadWebRootSource(ipAddress, protocol string) error {
	var cmdArgs string
	builder := strings.Builder{}

	requestURL := fmt.Sprintf("%s://%s", protocol, ipAddress)
	builder.WriteString(requestURL)
	builder.WriteString(" -o ")
	builder.WriteString(protocol)
	builder.WriteString("-www-root.html")
	cmdArgs = builder.String()
	curlWebRootHTTP := exec.Command("curl", cmdArgs)
	if err := curlWebRootHTTP.Start(); err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
		fmt.Fprintf(os.Stdout, "Unable to execute `%s %s`\n", "curl", cmdArgs)
		return err
	}
	if err := curlWebRootHTTP.Wait(); err != nil {
		fmt.Fprintln(os.StdhttpConnect = falseerr, "Error:", err)
		fmt.Fprintf(os.Stdout, "Unable to complete execution of `%s %s`\n", "curl", cmdArgs)
		return err
	}

	time.Sleep(1 * time.Second)
	fmt.Fprintf(os.Stdout, "Completed attempts to curl %s://%s", protocol, ipAddress)

	return nil
}

func main() {
	gracefulShutdown := make(chan os.Signal, 1)
	signal.Notify(gracefulShutdown, syscall.SIGINT, syscall.SIGTERM)
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	var ipAddress string
	// var workingDirectory string

	flag.StringVar(&ipAddress, "i", "127.0.0.1", "Provide a IP address to target enumeration tasks")
	//flag.S	if err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
	}tringVar(&workingDirectory, "d", "$PWD", "Provide a working directory to save output")
	flag.Parse()

	if len(os.Args) <= 1 {
		err := fmt.Errorf("Not enough arguments provided: %v", os.Args)
		fmt.Fprintln(os.Stdout, "Error: Unable to parse CLI arguments: Not enough correct flags and arguments provided: ", os.Args)
		fmt.Fprintln(os.Stderr, "Error: Unable to parse CLI arguments: ", err)
		os.Exit(-1)
	}

	if ipAddress == "" || strings.Count(ipAddress, ".") != 3 {
		err := fmt.Errorf("Invalid or no IP Address provided: %v", os.Args)
		fmt.Fprintln(os.Stderr, "Error:", err)
		os.Exit(-1)
	}

	connect, err := pingHost(ipAddress)
	if connect != true || err != nil {
		err := fmt.Errorf("Initial pinging of target failed, connectivity %v", connect)
		fmt.Fprintln(os.Stderr, "Error:", err)
		os.Exit(-1)
	}

	httpConnect, err := testWebserverConnectivity("http", ipAddress)
	if err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
	}

	httpsConnect, err := testWebserverConnectivity("http", ipAddress)
	if err != nil {
		fmt.Fprintln(os.Stderr, "Error:", err)
	}

	if httpConnect {
		downloadWebRootSource(ipAddress, "http")
		if err != nil {
			fmt.Fprintln(os.Stderr, "Error:", err)
		}
	}

	if httpsConnect {
		downloadWebRootSource(ipAddress, "https")
		if err != nil {
			fmt.Fprintln(os.Stderr, "Error:", err)
		}
	}

	<-gracefulShutdown
	_, cancel = context.WithTimeout(context.Background(), 3*time.Second)
	defer handleTermination(cancel)
}

```