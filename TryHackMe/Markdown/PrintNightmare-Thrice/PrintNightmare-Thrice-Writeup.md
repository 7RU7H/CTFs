# PrintNightmare-Thrice Writeup

Name: Print Nightmare Thrice!
Date:  
Difficulty:  
Goals:  
Learnt:

- [[PrintNightmare-Thrice-Notes.md]]
- [[PrintNightmare-Thrice-CMD-by-CMDs.md]]







## What remote address did the employee navigate to?

Working out...
```c
// I just scroll, but in hindsight either 
Statists -> Ipv4 Address, toggle count tab:
```

![](2018856147.png)


Answer
```c
20.188.56.147
```


Per the PCAP, which user returns a STATUS_LOGON_FAILURE error?
![](rjonesisnottheanswer.png)

Working out...
```c
ip.addr == 20.188.56.147 and smb2.auth_frame
```

Answer
```c
THM-PRINTNIGHT0\rjones
```


## Which user successfully connects to an SMB share?

Working out...
```

```

Answer
```c

```

## What is the first remote SMB share the endpoint connected to? What was the first filename? What was the second? (**format**: answer,answer,answer)

Working out...
```

```

Answer
```c

```

## From which remote SMB share was malicious DLL obtained? What was the path to the remote folder for the first DLL? How about the second? (format: answer,answer,answer)

Working out...
```

```

Answer
```c

```

## What was the first location the malicious DLL was downloaded to on the endpoint? What was the second?

Working out...
```

```

Answer
```c

```

## What is the folder that has the name of the remote printer server the user connected to? (provide the full folder path)

Working out...
```

```

Answer
```c

```

## What is the name of the printer the DLL added?

Working out...
```

```

Answer
```c

```

## What was the process ID for the elevated command prompt? What was its parent process? (**format**: answer,answer)

Working out...
```

```

Answer
```c

```

## What command did the user perform to elevate privileges?

Working out...
```

```

Answer
```c

```
