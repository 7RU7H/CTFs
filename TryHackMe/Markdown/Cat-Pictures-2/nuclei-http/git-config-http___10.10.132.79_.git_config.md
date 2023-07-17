### Git Configuration - Detect (git-config) found on http://10.10.132.79/

----
**Details**: **git-config** matched at http://10.10.132.79/

**Protocol**: HTTP

**Full URL**: http://10.10.132.79/.git/config

**Timestamp**: Sat Jul 1 18:24:15 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Git Configuration - Detect |
| Authors | pdteam, pikpikcu, mah3sec_ |
| Tags | config, git, exposure |
| Severity | medium |
| Description | Git configuration was detected via the pattern /.git/config and log file on passed URLs. |
| CVSS-Metrics | [CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N](https://www.first.org/cvss/calculator/3.1#CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 5.30 |

**Request**
```http
GET /.git/config HTTP/1.1
Host: 10.10.132.79
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 266
Accept-Ranges: bytes
Content-Type: application/octet-stream
Date: Sat, 01 Jul 2023 17:23:30 GMT
Etag: "57543c70-10a"
Last-Modified: Sun, 05 Jun 2016 14:51:28 GMT
Server: nginx/1.4.6 (Ubuntu)

[core]
	repositoryformatversion = 0
	filemode = true
	bare = false
	logallrefupdates = true
[remote "origin"]
	url = https://github.com/electerious/Lychee.git
	fetch = +refs/heads/*:refs/remotes/origin/*
[branch "master"]
	remote = origin
	merge = refs/heads/master

```


**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://10.10.132.79/.git/config'
```

----

Generated by [Nuclei v2.9.7](https://github.com/projectdiscovery/nuclei)