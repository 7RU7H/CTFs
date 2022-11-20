# SQHell Writeup
Name: SQHell
Date:  
Difficulty: Medium
Description: Try and find all the flags in the SQL Injections
Goals: OSCP Prep
Learnt:
- I continuely glad I live in a time after SQLmap..

First flag is in the login input.
![](flagone.png)

You cant register


Post parametre is vulnerable - error based?
![](postParam.png)

Hmming away
![](hmm.png)

Closer after read errors corrected again..
![](closer.png)

Objective make it get the flag table


User?id= Just seems to return "Cannot find user
