# Exploring the Caverns Part 1

Firstly I sent some time setting some more tools so that I can test them for later incorparation into other work.
While Throwback is its own network, I took advantage of this by also doing the regular recon for Internal and Relevant rooms in Kali box on Tryhackme.
Firstly I want to add reconftw as parallel branch to my bug bounty hunting python project, as it is very large recon framework in as bash script.  
[reconftw](https://github.com/six2dez/reconftw)  
It encompasses OSINT to Web vulnerabilities and although I generally have had lots of success with Nikto some times more is better in the sense that you more potential for information based on configuration divergences.
A problem with scripting frameworks is maintaince responsibility of the tools you are using is obvious down the repository holders. 
It seems from to me this problem is a being solved by the community and institutions in more formalised collections like [project-discovery](https://github.com/projectdiscovery) and most of the security OSes by suppporting open-source tool making, but keeping the tools open-source.
My personal hope the stance toward open-source tool independence and collaboration, but I feel incredibly lucky that I have kind of lived under a proverbial rock with regards all hacking and programming, prior to 2021.
This is because I feel like I am almost overwhelm by how well culivated everything is, there is lots of support, tools that are already well regarded are more maintained.  


Added the network address with `/24` to `/etc/hosts`  
Made a throwbacktarget.txt:
```bash
echo "throwback.thm" > throwbacktarget.txt
```
Ran reconftw
```bash
root@kali:~# ./reconftw.sh -d throwback.thm -a -o /root/throwback/
```

OSINT and Host returned nothing, web returned a password_dict.txt that I could have made in [cewl](https://github.com/digininja/CeWL)  

Re-installed go as I want to try hakrawler no Internal as it had js related directoires, but tried reconftw again just in case it was my fault setting up the kali vm incorrectly.
```bash
apt install golang-go
go install github.com/hakluke/hakrawler@latest
~/go/bin/hakrawler
No urls detected. Hint: cat urls.txt | hakrawler
```
 I then went back to the prelimarily nmap scan from Entering the Breach, changed the `/etc/hosts` to .local from .thm
```bash
root@kali:~/reconftw# ./reconftw.sh -d throwback.local -a -o /root/throwback/
```
Also nothing. I consider that it was possible how I was applying the tools to the network. As changing the `/etc/hosts` back to .thm; a manual curl later: 
```bash
root@kali:~# curl http://throwback.thm
curl: (6) Could not resolve host: throwback.thm
```
So almost certianly I note why I both wanted to test tools for Bug Bounty Hunting python framework on a real network. My network related knowledge seemed to be something that I lacked when going through PK100. I also released with my Internal.thm is that reconftw needs alot of directory urls as I got a screenshot.