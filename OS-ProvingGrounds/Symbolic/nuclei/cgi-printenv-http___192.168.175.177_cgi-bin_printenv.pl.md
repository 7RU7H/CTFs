### CGI script environment variable (cgi-printenv) found on http://192.168.175.177
---
**Details**: **cgi-printenv**  matched at http://192.168.175.177

**Protocol**: HTTP

**Full URL**: http://192.168.175.177/cgi-bin/printenv.pl

**Timestamp**: Thu Oct 20 12:07:13 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | CGI script environment variable |
| Authors | emadshanab |
| Tags | exposure, generic, cgi |
| Severity | medium |
| Description | A test CGI (Common Gateway Interface) script was found on this server. The response page returned by this CGI script is leaking a list of server environment variables. |

**Request**
```http
GET /cgi-bin/printenv.pl HTTP/1.1
Host: 192.168.175.177
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Content-Type: text/plain; charset=iso-8859-1
Date: Thu, 20 Oct 2022 11:07:29 GMT
Server: Apache/2.4.48 (Win64) OpenSSL/1.1.1k PHP/8.0.7

COMSPEC="C:\Windows\system32\cmd.exe"
CONTEXT_DOCUMENT_ROOT="/xampp/cgi-bin/"
CONTEXT_PREFIX="/cgi-bin/"
DOCUMENT_ROOT="C:/xampp/htdocs"
GATEWAY_INTERFACE="CGI/1.1"
HTTP_ACCEPT="*/*"
HTTP_ACCEPT_ENCODING="gzip"
HTTP_ACCEPT_LANGUAGE="en"
HTTP_CONNECTION="close"
HTTP_HOST="192.168.175.177"
HTTP_USER_AGENT="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36"
MIBDIRS="/xampp/php/extras/mibs"
MYSQL_HOME="\xampp\mysql\bin"
OPENSSL_CONF="/xampp/apache/bin/openssl.cnf"
PATH="C:\Windows\system32;C:\Windows;C:\Windows\System32\Wbem;C:\Windows\System32\WindowsPowerShell\v1.0\;C:\Windows\System32\OpenSSH\;C:\Users\p4yl0ad\AppData\Local\Microsoft\WindowsApps"
PATHEXT=".COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC"
PHPRC="\xampp\php"
PHP_PEAR_SYSCONF_DIR="\xampp\php"
QUERY_STRING=""
REMOTE_ADDR="192.168.49.175"
REMOTE_PORT="51304"
REQUEST_METHOD="GET"
REQUEST_SCHEME="http"
REQUEST_URI="/cgi-bin/printenv.pl"
SCRIPT_FILENAME="C:/xampp/cgi-bin/printenv.pl"
SCRIPT_NAME="/cgi-bin/printenv.pl"
SERVER_ADDR="192.168.175.177"
SERVER_ADMIN="postmaster@localhost"
SERVER_NAME="192.168.175.177"
SERVER_PORT="80"
SERVER_PROTOCOL="HTTP/1.1"
SERVER_SIGNATURE="<address>Apache/2.4.48 (Win64) OpenSSL/1.1.1k PHP/8.0.7 Server at 192.168.175.177 Port 80</address>\n"
SERVER_SOFTWARE="Apache/2.4.48 (Win64) OpenSSL/1.1.1k PHP/8.0.7"
SYSTEMROOT="C:\Windows"
TMP="\xampp\tmp"
WINDIR="C:\Windows"

```

References: 
- https://www.acunetix.com/vulnerabilities/web/test-cgi-script-leaking-environment-variables/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.175.177/cgi-bin/printenv.pl'
```
---
Generated by [Nuclei 2.7.8](https://github.com/projectdiscovery/nuclei)