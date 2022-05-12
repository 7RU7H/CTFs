Name: Juicy Details
Date: 12/05/2022 
Difficulty: Easy
Description: A popular juice shop has been breached! Analyze the logs to see what had happened... 
Better Description: 
Goals: Brand into my brain the importance of logs.  
Learnt:  


What tools did the attacker use? (Order by the occurrence in the log)
This question is just `cat access.log` scroll down and each tool will declare itself in the log.
```{toggle}
nmap, hydra, sqlmap, curl, feroxbuster
```
What endpoint was vulnerable to a brute-force attack?
The second tool is a brute force tool 
```{toggle}
/rest/user/login
```

What endpoint was vulnerable to SQL injection?
Third tool will be targetting this endpoint
```{toggle}
/rest/products/search
```

What parameter was used for the SQL injection?
This the letter after the question mark
```{toggle}
q
```

What endpoint did the attacker try to use to retrieve files? (Include the /)
The final tool has a pause feature to stop mid-Content discovery
```{toggle}
/ftp
```

What section of the website did the attacker use to scrape user email addresses?
API call to whoami of a user surround checks that users would input text to the site
```{toggle}
product reviews
```

Was their brute-force attack successful? If so, what is the timestamp of the successful login? (Yay/Nay, 11/Apr/2021:09:xx:xx +0000)
The only 200 response code for the second tool
```{toggle}
yay, 11/Apr/2021:09:16:31 +0000
```

What user information was the attacker able to retrieve from the endpoint vulnerable to SQL injection?
I look though the sql output with `cat access.log | grep sqlmap`, but I guessed it was the answer below given attacker is scraping for emails and the sql datebase would store authentication validatation data
```{toggle}
email, password
```

What files did they try to download from the vulnerable endpoint? (endpoint from the previous task, question #5)
` cat access.log | grep ftp` will help
```{toggle}
coupons_2013.md.bak, www-data.bak
```

What service and account name were used to retrieve files from the previous question? (service, username)
`cat vsftpd.log` is a big hint as well as the help from the last question, the user name given is contraction of the full word.
```{toggle}
ftp, anonymous
```

What service and username were used to gain shell access to the server? (service, username)
`cat auth.log` systemd creates sessions for the user, the \*\*\*d is the server  
```{toggle}
ssh, www-data
```

