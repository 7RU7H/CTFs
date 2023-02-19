# SQHell Writeup

Name: SQHell
Date:  18/2/2023
Difficulty: Medium
Description: Try and find all the flags in the SQL Injections
Goals: OSCP Prep
Learnt:
- I continuely glad I live in a time after SQLmap..

[Alh4zr3d](https://www.youtube.com/watch?v=MpJasg3IQNI)

## Login Flag

First flag is in the login input.
![](flagone.png)


##  Post


Post parametre is vulnerable - error based?
![](postParam.png)

Hmming away
![](hmm.png)

Closer after read errors corrected again..
![](closer.png)
Objective make it get the flag table

Al leads the way at 1:29
![1080](alLeadstheway.png)
My pitfall in my thinking is not considering legimate SQL as initial vulnerability and thinking that I must always break the SQL query and then fix it.

Al explains it may look like this:
```sql
-- the id param is used at the end of query like this:
SELECT id,name,author, content FROM posts WHERE id=<id param>;
-- 
SELECT id,name,author, content FROM posts WHERE id=1 UNION SELECT 1,2,3,4;
-- Look if any appear is printed onto the page
SELECT id,name,author, content FROM posts WHERE id='id','Al','root','kekw';
```
Tiberious explain another *tib bit* try the zero page. The reason being is the first query of the UNION is empty so the only row is ours. 

Inital result of the parameter had to be invalid


![1080](idididid.png)

![](mappingoutwheretoprint.png)

Al tries of database version
![1080](characterproblem.png)

[Swedish Character Set](https://stackoverflow.com/questions/3029321/troubleshooting-illegal-mix-of-collations-error-in-mysql), but Tiberious is the chat again:
![1080](buttibsisinthechat.png)

May need to try out Tiberious's Web App course...

```sql
database()
version()
user()
```

Pentest Monkey has issue with quotes. I fixed this and

```
GET /post?id=100+UNION+ALL+SELECT+'1',concat(table_schema,'+:+',table_name),database(),'4'+FROM+informational_schema.tables+WHERE+table_schema%2b!%253d%2b'mysql'+AND+table_schema+!%253d%2b'information_schema'--%2b-+ 
```

[Al](https://www.youtube.com/watch?v=MpJasg3IQNI&t=4322s)

User?id=
![](userpage.png)



We cant register
`http://10.10.48.199/user?id=1`
![](withidpara.png)

## Registration

Returning back to the Registration, because why not, why does it not work.
![](theregistationcode.png)

Read the code, test what happens.
![](avaliabletrue.png)

https://www.youtube.com/watch?v=MpJasg3IQNI 1:12