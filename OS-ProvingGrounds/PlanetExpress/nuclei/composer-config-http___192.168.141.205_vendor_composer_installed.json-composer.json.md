### composer-config-file (composer-config:composer.json) found on http://192.168.141.205
---
**Details**: **composer-config:composer.json**  matched at http://192.168.141.205

**Protocol**: HTTP

**Full URL**: http://192.168.141.205/vendor/composer/installed.json

**Timestamp**: Tue Sep 20 19:06:25 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | composer-config-file |
| Authors | mahendra purbia (mah3sec_) |
| Tags | config, exposure |
| Severity | info |

**Request**
```http
GET /vendor/composer/installed.json HTTP/1.1
Host: 192.168.141.205
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 23215
Accept-Ranges: bytes
Content-Type: application/json
Date: Tue, 20 Sep 2022 18:06:25 GMT
Etag: "5aaf-5d27f7b211b00"
Last-Modified: Mon, 06 Dec 2021 19:44:12 GMT
Server: Apache/2.4.38 (Debian)

{
    "packages": [
        {
            "name": "erusev/parsedown",
            "version": "1.8.0-beta-7",
            "version_normalized": "1.8.0.0-beta7",
            "source": {
                "type": "git",
                "url": "https://github.com/erusev/parsedown.git",
                "reference": "fe7a50eceb4a3c867cc9fa9c0aa906b1067d1955"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/erusev/parsedown/zipball/fe7a50eceb4a3c867cc9fa9c0aa906b1067d1955",
                "reference": "fe7a50eceb4a3c867cc9fa9c0aa906b1067d1955",
                "shasum": ""
            },
            "require": {
                "ext-mbstring": "*",
                "php": ">=5.3.0"
            },
            "require-dev": {
                "phpunit/phpunit": "^4.8.35"
            },
            "time": "2019-03-17T18:47:21+00:00",
            "type": "library",
            "installation-source": "dist",
            "autoload": {
                "psr-0": {
                    "Parsedown": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Emanuil Rusev",
                    "email": "hello@erusev.com",
                    "homepage": "http://erusev.com"
                }
            ],
            "description": "Parser for Markdown.",
            "homepage": "http://parsedown.org",
            "keywords": [
                "markdown",
                "parser"
            ],
            "support": {
                "issues": "https://github.com/erusev/parsedown/issues",
                "source": "https://github.com/erusev/parsedown/tree/1.8.0-beta-7"
            },
            "install-path": "../erusev/parsedown"
        },
        {
            "name": "erusev/parsedown-extra",
            "version": "0.8.0-beta-1",
            "version_normalized": "0.8.0.0-beta1",
            "source": {
                "type": "git",
                "url": "https://github.com/erusev/parsedown-extra.git",
                "reference": "e756b1bf8642ab1091403e902b0503f1cec7527d"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/erusev/parsedown-extra/zipball/e756b1bf8642ab1091403e902b0503f1cec7527d",
                "reference": "e756b1bf8642ab1091403e902b0503f1cec7527d",
                "shasum": ""
            },
            "require": {
                "erusev/parsedown": "^1.8.0|^1.8.0-beta-4",
                "ext-dom": "*",
                "ext-mbstring": "*",
                "php": ">=5.3.6"
            },
            "require-dev": {
                "phpunit/phpunit": "^4.8.35"
            },
            "time": "2018-05-08T21:54:32+00:00",
            "type": "library",
            "installation-source": "dist",
            "autoload": {
                "psr-0": {
                    "ParsedownExtra": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Emanuil Rusev",
                    "email": "hello@erusev.com",
                    "homepage": "http://erusev.com"
                }
            ],
            "description": "An extension of Parsedown that adds support for Markdown Extra.",
            "homepage": "https://github.com/erusev/parsedown-extra",
            "keywords": [
                "markdown",
                "markdown extra",
                "parsedown",
                "parser"
            ],
            "support": {
                "issues": "https://github.com/erusev/parsedown-extra/issues",
                "source": "https://github.com/erusev/parsedown-extra/tree/master"
            },
            "install-path": "../erusev/parsedown-extra"
        },
        {
            "name": "picocms/composer-installer",
            "version": "v1.0.1",
            "version_normalized": "1.0.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/picocms/composer-installer.git",
                "reference": "6b5036c83aa091ed76e2a76ed9335885f95a7db7"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/picocms/composer-installer/zipball/6b5036c83aa091ed76e2a76ed9335885f95a7db7",
                "reference": "6b5036c83aa091ed76e2a76ed9335885f95a7db7",
                "shasum": ""
            },
            "time": "2019-11-24T22:50:47+00:00",
            "type": "composer-installer",
            "extra": {
                "class": "Pico\\Composer\\Installer.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://192.168.141.205/vendor/composer/installed.json'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)