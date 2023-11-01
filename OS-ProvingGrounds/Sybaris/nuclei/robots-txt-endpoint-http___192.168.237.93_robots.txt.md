### robots.txt endpoint prober (robots-txt-endpoint) found on http://192.168.237.93/

----
**Details**: **robots-txt-endpoint** matched at http://192.168.237.93/

**Protocol**: HTTP

**Full URL**: http://192.168.237.93/robots.txt

**Timestamp**: Thu Oct 26 15:39:25 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Tags | misc, generic |
| Severity | info |

**Request**
```http
GET /robots.txt HTTP/1.1
Host: 192.168.237.93
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Accept-Ranges: bytes
Content-Type: text/plain; charset=UTF-8
Date: Thu, 26 Oct 2023 14:39:18 GMT
Etag: "482-5a4f8214b9740-gzip"
Last-Modified: Wed, 06 May 2020 10:21:41 GMT
Server: Apache/2.4.6 (CentOS) PHP/7.3.22
Vary: Accept-Encoding

#
# robots.txt
#
# This file is to prevent the crawling and indexing of certain parts
# of your site by web crawlers and spiders run by sites like Yahoo!
# and Google. By telling these "robots" where not to go on your site,
# you save bandwidth and server resources.
#
# This file will be ignored unless it is at the root of your host:
# Used:    http://example.com/robots.txt
# Ignored: http://example.com/site/robots.txt
#
# For more information about the robots.txt standard, see:
# http://www.robotstxt.org/wc/robots.html
#
# For syntax checking, see:
# http://www.sxw.org.uk/computing/robots/check.html

User-agent: *

# Disallow directories
Disallow: /config/
Disallow: /system/
Disallow: /themes/
Disallow: /vendor/
Disallow: /cache/

# Disallow files
Disallow: /changelog.txt
Disallow: /composer.json
Disallow: /composer.lock
Disallow: /composer.phar

# Disallow paths
Disallow: /search/
Disallow: /admin/

# Allow themes
Allow: /themes/*/css/
Allow: /themes/*/images/
Allow: /themes/*/img/
Allow: /themes/*/js/
Allow: /themes/*/fonts/

# Allow content images
Allow: /content/images/*.jpg
Allow: /content/images/*.png
Allow: /content/images/*.gif
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://192.168.237.93/robots.txt'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)