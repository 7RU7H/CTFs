# Busqueda Writeup

Name: Busqueda
Date:  
Difficulty:  
Goals:  
Learnt:
Beyond Root:

- [[Busqueda-Notes.md]]
- [[Busqueda-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](HackTheBox/Retired-Machines/Busqueda/Screenshots/ping.png)

![](hostname-nmapscan.png)

Its the best distribution to ask a Scottish person to say: JAAAMMEY
![](JAMMY.png)


![](searchforwtfisbusquedas.png)

![](emptyredirectparameter.png)


![](redirectforid.png)

![](youtube-headers.png)

![](more-youtube-headers.png)

![](onecss-elementatatime.png)

... Not knowing how many there would be and was interested to see how many there are over 300. It is amazing just how much is transmitted, but the redirect actually works so that is crucial to avoid out of scope non-fun. 

One quick search for busqueda and it just means search
![](spanishforsearch.png)

`false` 
![](WERKZEUG.png)


If the Engine is not in the list it reflects back the www-root page... so

How is it handling encoding the query?
![](WERKZEUG.png)
URL
![](badcharsprimilary2.png)

And for third check with differing characters `$()/`
![](encodethisyoub.png)

```
#\n+print("hello");
```

![](searchingfortheconsoleonsearcherhtb.png)

After some dorking for versions while running ffuf to find Vhosts 150~k and 20k and found none thew good old look at the source: 
![](shouldhavecurledoutthesource.png)

![](evalinjection.png)

> Description
 An issue in Arjun Sharda's Searchor before version v.2.4.2 allows an attacker to  
 execute arbitrary code via a crafted script to the eval() function in Searchor's src/searchor/main.py file, affecting the search feature in Searchor's CLI (Command Line Interface).
> Impact
> Versions equal to, or below 2.4.1 are affected.
> Patches
 Versions above, or equal to 2.4.2 have patched the vulnerability.
  References

We could be lazy at some point:
[https://github.com/nikn0laty/Exploit-for-Searchor-2.4.0-Arbitrary-CMD-Injection](https://github.com/nikn0laty/Exploit-for-Searchor-2.4.0-Arbitrary-CMD-Injection)  
[https://github.com/nexis-nexis/Searchor-2.4.0-POC-Exploit-](https://github.com/nexis-nexis/Searchor-2.4.0-POC-Exploit-)  
[https://github.com/jonnyzar/POC-Searchor-2.4.2](https://github.com/jonnyzar/POC-Searchor-2.4.2)  
[#130](https://github.com/ArjunSharda/Searchor/pull/130)

After I remembered to look at it in the correct version..
![1080](evalinpre2v4v1.png)

```python
def search(engine, query, open, copy):
    try:
        url = eval(
            f"Engine.{engine}.search('{query}', copy_url={copy}, open_web={open})"
        )
        click.echo(url)
        searchor.history.update(engine, query, url)
        if open:
            click.echo("opening browser...")
        if copy:
            click.echo("link copied to clipboard")
    except AttributeError:
        print("engine not recognized")


```

AS `"` and `'` are both url encoded
![](considerthefstring.png)

To speed up the learning I decided to click: https://github.com/nikn0laty/Exploit-for-Searchor-2.4.0-Arbitrary-CMD-Injection and then go IMMEDIATELY to: https://security.snyk.io/package/pip/searchor/2.4.0 and then dork about ` unsafe eval method "python"` .

Like wadding through some horrific fire in the past I went to stackoverflow just to make the joke of it: https://stackoverflow.com/questions/1832940/why-is-using-eval-a-bad-practice on why it is bad practice and used a almost SSTI 

![](triedalmostsstipayload.png)
Measuring twice and cutting once on with another 200 response - maybe it is executing so we will pull out a tactic ping in python and turn-up-for tcpdump 
![](norefelctionisgoodreflectioninthiscase.png)

Sadly
![](HackTheBox/Retired-Machines/Busqueda/Screenshots/PONG.png)
and confirmation via a different means..
![](sadHELLO.png)

Simplely explained once scrolled down: https://github.com/nikn0laty/Exploit-for-Searchor-2.4.0-Arbitrary-CMD-Injection as ..
```python
# There is no comma in the explaination...
__import__('os').system('<CMD>')
```

Regardless of whining 
`eval(f"Engine.{engine}.search('{query}', copy_url={copy}, open_web={open})")`  I did not ask how is it dynamically evaluating and the comma, being the delimiter to each argument, means we are injecting, a comma then what we want.

![](usingtheexploit.png)
![](instructionsunclearmemenotvalid.png)
```bash
ls -1 ~/.local/bin
flask
pyjwt

/usr/bin/searchor


/etc/ld.so.conf.d/fakeroot-x86_64-linux-gnu.conf
/etc/ld.so.conf.d/libc.conf
/etc/ld.so.conf.d/x86_64-linux-gnu.conf

# Check see profile.d/apps-bin-paths.sh below
# check systemd's path PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin

You have write privileges over /etc/init/ubuntu-fan.conf
You have write privileges over /etc/init.d/auditd
```
![](reallyreally.png)

For the lowest of hanging fruits...
![](codyrootpasswdfailed.png)

![](gitEEEEEEEEEEAAAAAAASPORTSITSWORDLISTPAIN.png)

Wordlist horrors return...
![](giteaAAARG.png)
And it is 656722 down! 
![](656722.png)

![](127001.png)


![](giteaversion.png)

After some wondering and wandering - especial why the file permission were so weird the `$USER` is root. WHAT.

![](weENVroot.png)

Basically we need something owned by root that reads the environment variable for some weird reason. 

/root/.pm is node related - grep went to fast..
## Exploit

## Foothold

## Privilege Escalation

## Post-Root-Reflection  

Should have spent more time on `eval()` and what dynamic evaluation is and ask how is it evaluating some input and then executing 

## Beyond Root


