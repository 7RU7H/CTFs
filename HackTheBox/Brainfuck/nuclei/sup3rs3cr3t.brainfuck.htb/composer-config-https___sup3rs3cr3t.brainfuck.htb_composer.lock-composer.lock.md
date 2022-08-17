### composer-config-file (composer-config:composer.lock) found on https://sup3rs3cr3t.brainfuck.htb
---
**Details**: **composer-config:composer.lock**  matched at https://sup3rs3cr3t.brainfuck.htb

**Protocol**: HTTP

**Full URL**: https://sup3rs3cr3t.brainfuck.htb/composer.lock

**Timestamp**: Wed Aug 17 20:53:15 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | composer-config-file |
| Authors | mahendra purbia (mah3sec_) |
| Tags | config, exposure |
| Severity | info |

**Request**
```http
GET /composer.lock HTTP/1.1
Host: sup3rs3cr3t.brainfuck.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 163873
Accept-Ranges: bytes
Content-Type: application/octet-stream
Date: Wed, 17 Aug 2022 19:53:16 GMT
Etag: "58f50b5f-28021"
Last-Modified: Mon, 17 Apr 2017 18:37:19 GMT
Server: nginx/1.10.0 (Ubuntu)

{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file",
        "This file is @generated automatically"
    ],
    "hash": "41216568050377c942cfdeba45e5cf79",
    "content-hash": "1d6985b76e1c3e1ab29b208e6c9c8866",
    "packages": [
        {
            "name": "components/font-awesome",
            "version": "4.7.0",
            "source": {
                "type": "git",
                "url": "https://github.com/components/font-awesome.git",
                "reference": "885308b939369d147bec93174722786bc2c4eedd"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/components/font-awesome/zipball/885308b939369d147bec93174722786bc2c4eedd",
                "reference": "885308b939369d147bec93174722786bc2c4eedd",
                "shasum": ""
            },
            "type": "component",
            "extra": {
                "component": {
                    "styles": [
                        "css/font-awesome.css"
                    ],
                    "files": [
                        "css/font-awesome.min.css",
                        "css/font-awesome.css.map",
                        "fonts/*"
                    ]
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT",
                "OFL-1.1"
            ],
            "description": "The iconic font designed for use with Twitter Bootstrap.",
            "time": "2016-10-25 10:56:23"
        },
        {
            "name": "danielstjules/stringy",
            "version": "1.10.0",
            "source": {
                "type": "git",
                "url": "https://github.com/danielstjules/Stringy.git",
                "reference": "4749c205db47ee5b32e8d1adf6d9aff8db6caf3b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/danielstjules/Stringy/zipball/4749c205db47ee5b32e8d1adf6d9aff8db6caf3b",
                "reference": "4749c205db47ee5b32e8d1adf6d9aff8db6caf3b",
                "shasum": ""
            },
            "require": {
                "ext-mbstring": "*",
                "php": ">=5.3.0"
            },
            "require-dev": {
                "phpunit/phpunit": "~4.0"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Stringy\\": "src/"
                },
                "files": [
                    "src/Create.php"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Daniel St. Jules",
                    "email": "danielst.jules@gmail.com",
                    "homepage": "http://www.danielstjules.com"
                }
            ],
            "description": "A string manipulation library with multibyte support",
            "homepage": "https://github.com/danielstjules/Stringy",
            "keywords": [
                "UTF",
                "helpers",
                "manipulation",
                "methods",
                "multibyte",
                "string",
                "utf-8",
                "utility",
                "utils"
            ],
            "time": "2015-07-23 00:54:12"
        },
        {
            "name": "davis/flarum-animatedtag",
            "version": "v0.0.1beta8",
            "source": {
                "type": "git",
                "url": "https://github.com/dav-is/flarum-animatedtag.git",
                "reference": "83855dae4d28790ba670c801459301b626fb9458"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/dav-is/flarum-animatedtag/zipball/83855dae4d28790ba670c801459301b626fb9458",
                "reference": "83855dae4d28790ba670c801459301b626fb9458",
                "shasum": ""
            },
            "require": {
                "flarum/core": "^0.1.0-beta.3",
                "flarum/tags": "v0.1.0-beta.4"
            },
            "type": "flarum-extension",
            "extra": {
                "flarum-extension": {
                    "title": "Animated Tags",
                    "icon": {
                        "image": "icon.png",
                        "backgroundColor": "#F7FBFF",
                        "backgroundSize": "75%",
                        "backgroundRepeat": "no-repeat",
                        "backgroundPosition": "center"
                    }
                }
            },
            "autoload": {
        .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36' 'https://sup3rs3cr3t.brainfuck.htb/composer.lock'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)