### composer-config-file (composer-config:composer.json) found on https://sup3rs3cr3t.brainfuck.htb
---
**Details**: **composer-config:composer.json**  matched at https://sup3rs3cr3t.brainfuck.htb

**Protocol**: HTTP

**Full URL**: https://sup3rs3cr3t.brainfuck.htb/vendor/composer/installed.json

**Timestamp**: Wed Aug 17 20:53:16 +0100 BST 2022

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
Host: sup3rs3cr3t.brainfuck.htb
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
Content-Length: 151730
Accept-Ranges: bytes
Content-Type: application/json
Date: Wed, 17 Aug 2022 19:53:17 GMT
Etag: "58f50b5f-250b2"
Last-Modified: Mon, 17 Apr 2017 18:37:19 GMT
Server: nginx/1.10.0 (Ubuntu)

[
    {
        "name": "tobscure/json-api",
        "version": "v0.3.0",
        "version_normalized": "0.3.0.0",
        "source": {
            "type": "git",
            "url": "https://github.com/tobscure/json-api.git",
            "reference": "663d1c1299d4363758e8e440e5849134f218f45c"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/tobscure/json-api/zipball/663d1c1299d4363758e8e440e5849134f218f45c",
            "reference": "663d1c1299d4363758e8e440e5849134f218f45c",
            "shasum": ""
        },
        "require": {
            "php": ">=5.5.9"
        },
        "require-dev": {
            "phpunit/phpunit": "^4.8 || ^5.0"
        },
        "time": "2016-03-31 09:25:28",
        "type": "library",
        "extra": {
            "branch-alias": {
                "dev-master": "1.0-dev"
            }
        },
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Tobscure\\JsonApi\\": "src/"
            }
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "Toby Zerner",
                "email": "toby.zerner@gmail.com"
            }
        ],
        "description": "JSON-API responses in PHP",
        "keywords": [
            "api",
            "json",
            "jsonapi",
            "standard"
        ]
    },
    {
        "name": "nikic/fast-route",
        "version": "v0.6.0",
        "version_normalized": "0.6.0.0",
        "source": {
            "type": "git",
            "url": "https://github.com/nikic/FastRoute.git",
            "reference": "31fa86924556b80735f98b294a7ffdfb26789f22"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/nikic/FastRoute/zipball/31fa86924556b80735f98b294a7ffdfb26789f22",
            "reference": "31fa86924556b80735f98b294a7ffdfb26789f22",
            "shasum": ""
        },
        "require": {
            "php": ">=5.4.0"
        },
        "time": "2015-06-18 19:15:47",
        "type": "library",
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "FastRoute\\": "src/"
            },
            "files": [
                "src/functions.php"
            ]
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "BSD-3-Clause"
        ],
        "authors": [
            {
                "name": "Nikita Popov",
                "email": "nikic@php.net"
            }
        ],
        "description": "Fast request router for PHP",
        "keywords": [
            "router",
            "routing"
        ]
    },
    {
        "name": "psr/http-message",
        "version": "1.0.1",
        "version_normalized": "1.0.1.0",
        "source": {
            "type": "git",
            "url": "https://github.com/php-fig/http-message.git",
            "reference": "f6561bf28d520154e4b0ec72be95418abe6d9363"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/php-fig/http-message/zipball/f6561bf28d520154e4b0ec72be95418abe6d9363",
            "reference": "f6561bf28d520154e4b0ec72be95418abe6d9363",
            "shasum": ""
        },
        "require": {
            "php": ">=5.3.0"
        },
        "time": "2016-08-06 14:39:51",
        "type": "library",
        "extra": {
            "branch-alias": {
                "dev-master": "1.0.x-dev"
            }
        },
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Psr\\Http\\Message\\": "src/"
            }
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "PHP-FIG",
                "homepage": "http://www.php-fig.org/"
            }
        ],
        "description": "Common interface for HTTP messages",
        "homepage": "https://github.com/php-fig/http-message",
        "keywords": [
            "http",
            "http-message",
            "psr",
            "psr-7",
            "request",
            "response"
        ]
    },
    {
        "name": "dflydev/fig-cookies",
        "version": "v1.0.2",
        "version_normalized": "1.0.2.0",
        "source": {
            "type": "git",
            "url": "https://github.com/dflydev/dflydev-fig-cookies.git",
            "reference": "883233c159d00d39e940bd12cfe42c0d23420c1c"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/dflydev/dflydev-fig-cookies/zipball/883233c159d00d39e940bd12cfe42c0d23420c1c",
            "reference": "883233c159d00d39e9.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'https://sup3rs3cr3t.brainfuck.htb/vendor/composer/installed.json'
```
---
Generated by [Nuclei 2.7.4](https://github.com/projectdiscovery/nuclei)