# Squashed Notes

## Data 

IP: 10.129.228.109
OS:
Hostname:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Credentials:

## DO NOT LOOK AT WRITEUP
## FAILS - Learnt always Rescan a box!


## Objectives

## Target Map

![](Squashed-map.excalidraw.md)

## Solution Inventory Map




### Todo 

### Done

![](showyourmnts.png)

comingformonicasmoistmaker.png
flagfun.png
rossmoistmaker.png

noflags.png
remountageathtml.png

weirdnessofnfs.png

geniunelyconcernedformysanity.png

We cant user `addgroup` we must edit the file manually by making a group name 2017 and making sure www-data has bash to login on Kali and drop a shell
```bash
# Edit the /etc/group file, will prompt for nano, vim-* so
sudo vigr 
# Reload the /etc/group file and update /etc/gshadow
sudo grpck 
# Edit the /etc/passwd file, will prompt for nano, vim-* so
sudo vipw 
```

To copy the entire mounted www directory
```bash
# As www-data
www-data@kali:/home/kali/Squashed$ cp -r www /tmp/mntedWWW 
kali@kali:/home/kali/Squashed$ sudo move /tmp/mntedWWW ~/Squashed/
kali @kali:/home/kali/Squashed$ sudo chown -R kali:kali mntedWWW
```

becomethewwwdata.png

makeitexecutablenfs.png