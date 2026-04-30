# Dog Writeup

Name: Dog
Date:  
Difficulty: Easy
Goals:  
- Use the Guided Mode to push through
- Test my CTF automation tool 
- Add UDP check for snmp
Learnt:

Beyond Root:
- https://github.com/TheSnowWight/hackdocs/tree/master learn from a relevant page
- Test and update my CTF automation tool


- [[Dog-Notes.md]]
- [[Dog-CMD-by-CMDs.md]]


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)
Running Nmap for TCP ports enumerating 22 and 80
![](nmap-tcp.png)

I used `-sC` and `-sV` for more information on those ports.
![](alotofexposeddirectoriesandfile.png)

I decided I should directory bust before scanning with `nitko` and nuclei plus whatever CMS scanner. This might be the rabbithole find-the-creds-in-the-haystack-full-of-creds box. 
![](biggobuster.png)
And further `gobuster`  usage to enumerate directories.
![](hopingfornonwordlisthellscapinga.png)

`nikto` scan to see if there are old vulnerabilities and security issues
![3000](nikto.png)

Food is love for some people, but poor doggo can't stop scavenging.
![](dogisbig.png)

HTB's kind reminder Good dog food, not over feeding and helping your dog's mental health with play and normal life. 

I dumped the .git directory with [git-dumper](https://github.com/arthaud/git-dumper)
```bash
# pip install git-dumper # official way, but 
git-dumper http://sadwebsite.$tdl/.git outputDirectory # --proxy 127.0.0.1 8080
```

And considered how best to fuzz this parameter `?q=FUZZ` as we not only have - `?q=admin`, but also `?q=user/password` potential hits and potential still using the parameter for directory traversal or some injection attack. Also there is a lot places to check out of the gate so [[Dog-Notes]] was required.

Checking to validate exposed files and directories
![](webconfig-fp.png)
And `/admin`
![](admin-fp.png)
The critical part is that previous tool before running nuclei just read the robots.txt, but `gobuster` could not find them so there is not a misconfigured rabbithole of where to look and test. But the `.git` is dumpable, `?q=` maybe important

![](nuclei.png)

Having not needed to see a robots.txt the clean and unclean urls jargon is a bit weird.
![](cleanuncleanurls.png)
So to the dev `?q=` is dirty, but is doing some (early part in) routablity 
![](accessdenied.png)
Testing to see `?q=` and core
![](testingroutabilitywithcore.png)

![](verystrangedirtycore.png)
Checking for a version I got this...
![](wecanexecute.png)

Not sure if this means anything is executable

![](yikes.png)

![](drupal7.png)
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

![](Dog-map.excalidraw.md)

## Beyond Root


Initial starting point 
![](firstrunningofenum.png)
Change the method and removed the Start() and Wait() method, concatenating the error handling
![](pingworksnow.png)
Then I fixed the way the curl command was run
![](fixingcurldownloadingwebroot.png)
By making the changes below:
```go
func downloadWebRootSource(ipAddress, protocol string) error {
        requestURL := fmt.Sprintf("%s://%s", protocol, ipAddress)
        outputFile := fmt.Sprintf("%s-www-root.html", protocol)
        curlWebRootHTTP := exec.Command("curl", requestURL, "-o", outputFile)
        curlWebRootHTTP.Stdout = os.Stdout
        curlWebRootHTTP.Stderr = os.Stderr

        if err := curlWebRootHTTP.Run(); err != nil {
                fmt.Fprintln(os.Stderr, "Error:", err)
                fmt.Fprintf(os.Stdout, "Unable to execute curl %s -o %s\n", requestURL, outputFile)
                return err
        }

        time.Sleep(1 * time.Second)
        fmt.Fprintf(os.Stdout, "Completed attempts to download the web root with curl %s://%s", protocol, ipAddress)

        return nil
}
```