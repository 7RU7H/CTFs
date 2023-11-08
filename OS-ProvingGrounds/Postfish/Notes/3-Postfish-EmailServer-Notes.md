# 3 - Email Servers

```
25/tcp  open  smtp     Postfix smtpd
110/tcp open  pop3     Dovecot pop3d
143/tcp open  imap     Dovecot imapd (Ubuntu)
993/tcp open  ssl/imap Dovecot imapd (Ubuntu)
995/tcp open  ssl/pop3 Dovecot pop3d
```

Create names.txt -> potential-usernames.txt
```
Claire Madison
Mike Ross
Brian Moore
Sarah Lorem
```

```bash
potential-usernames.txt | awk '{print $1"@<domaingoeshere.tld>"}' > potential-email-addresses.txt
# Appended a subsituted `.` for a `-` - remember to change the domain again!
cat potential-email-addr.txt | awk -F@ '{print $1}' | grep '.' | sed 's/\./-/g' | awk '{print $1"@<domaingoeshere.tld>"}' | tee -a potential-email-addr.txt
```

```
claire.madison@postfish.off
c.madison@postfish.off
cmadison@postfish.off
madison.claire@postfish.off
mike.ross@postfish.off
m.ross@postfish.off
mross@postfish.off
ross.mike@postfish.off
brian.moore@postfish.off
b.moore@postfish.off
bmoore@postfish.off
moore.brian@postfish.off
sarah.lorem@postfish.off
s.lorem@postfish.off
slorem@postfish.off
lorem.sarah@postfish.off
claire-madison@postfish.off
c-madison@postfish.off
cmadison@postfish.off
madison-claire@postfish.off
mike-ross@postfish.off
m-ross@postfish.off
mross@postfish.off
ross-mike@postfish.off
brian-moore@postfish.off
b-moore@postfish.off
bmoore@postfish.off
moore-brian@postfish.off
sarah-lorem@postfish.off
s-lorem@postfish.off
slorem@postfish.off
lorem-sarah@postfish.off
```

```bash
# Enumerate potiental email addresses for valid ones
smtp-user-enum -M VRFY -U potential-email-addresses.txt -t $TargetIP -p $PORT | tee -a  smtp-user-enum-$PORT.out 
# Then make a file of valid emails
cat smtp-user-enum-25.out | grep exists | awk -F: '{print $2}' | sed 's/ exists//g'| tr -d ' ' > valid-emails.txt
```

