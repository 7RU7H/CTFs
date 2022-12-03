### composer-config-file (composer-config:composer.json) found on http://monitor.bart.htb
---
**Details**: **composer-config:composer.json**  matched at http://monitor.bart.htb

**Protocol**: HTTP

**Full URL**: http://monitor.bart.htb/vendor/composer/installed.json

**Timestamp**: Sat Dec 3 17:54:01 +0000 GMT 2022

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
Host: monitor.bart.htb
User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 22031
Accept-Ranges: bytes
Content-Type: application/json
Date: Sat, 03 Dec 2022 17:54:00 GMT
Etag: "896549a35c3cd31:0"
Last-Modified: Tue, 03 Oct 2017 15:31:09 GMT
Server: Microsoft-IIS/10.0

[
    {
        "name": "indigophp/hash-compat",
        "version": "v1.1.0",
        "version_normalized": "1.1.0.0",
        "source": {
            "type": "git",
            "url": "https://github.com/indigophp/hash-compat.git",
            "reference": "43a19f42093a0cd2d11874dff9d891027fc42214"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/indigophp/hash-compat/zipball/43a19f42093a0cd2d11874dff9d891027fc42214",
            "reference": "43a19f42093a0cd2d11874dff9d891027fc42214",
            "shasum": ""
        },
        "require": {
            "php": ">=5.3"
        },
        "require-dev": {
            "phpunit/phpunit": "~4.4"
        },
        "time": "2015-08-22T07:03:35+00:00",
        "type": "library",
        "extra": {
            "branch-alias": {
                "dev-master": "1.2-dev"
            }
        },
        "installation-source": "dist",
        "autoload": {
            "files": [
                "src/hash_equals.php",
                "src/hash_pbkdf2.php"
            ]
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "Márk Sági-Kazár",
                "email": "mark.sagikazar@gmail.com"
            }
        ],
        "description": "Backports hash_* functionality to older PHP versions",
        "homepage": "https://indigophp.com",
        "keywords": [
            "hash",
            "hash_equals",
            "hash_pbkdf2"
        ]
    },
    {
        "name": "paragonie/random_compat",
        "version": "1.1.6",
        "version_normalized": "1.1.6.0",
        "source": {
            "type": "git",
            "url": "https://github.com/paragonie/random_compat.git",
            "reference": "e6f80ab77885151908d0ec743689ca700886e8b0"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/paragonie/random_compat/zipball/e6f80ab77885151908d0ec743689ca700886e8b0",
            "reference": "e6f80ab77885151908d0ec743689ca700886e8b0",
            "shasum": ""
        },
        "require": {
            "php": ">=5.2.0"
        },
        "require-dev": {
            "phpunit/phpunit": "4.*|5.*"
        },
        "suggest": {
            "ext-libsodium": "Provides a modern crypto API that can be used to generate random bytes."
        },
        "time": "2016-01-29T16:19:52+00:00",
        "type": "library",
        "installation-source": "dist",
        "autoload": {
            "files": [
                "lib/random.php"
            ]
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "Paragon Initiative Enterprises",
                "email": "security@paragonie.com",
                "homepage": "https://paragonie.com"
            }
        ],
        "description": "PHP 5.x polyfill for random_bytes() and random_int() from PHP 7",
        "keywords": [
            "csprng",
            "pseudorandom",
            "random"
        ]
    },
    {
        "name": "php-pushover/php-pushover",
        "version": "dev-master",
        "version_normalized": "9999999-dev",
        "source": {
            "type": "git",
            "url": "https://github.com/phpservermon/php-pushover.git",
            "reference": "d13d08dbf5f1cfa73f4adca7e8d27f79c804dd7b"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/phpservermon/php-pushover/zipball/d13d08dbf5f1cfa73f4adca7e8d27f79c804dd7b",
            "reference": "d13d08dbf5f1cfa73f4adca7e8d27f79c804dd7b",
            "shasum": ""
        },
        "time": "2014-07-30T13:55:53+00:00",
        "type": "library",
        "installation-source": "dist",
        "autoload": {
            "files": [
                "Pushover.php"
            ]
        },
        "description": "PHP class for the Pushover.net project",
        "support": {
            "source": "https://github.com/phpservermon/php-pushover/tree/master"
        }
    },
    {
        "name": "phpmailer/phpmailer",
        "version": "v5.2.6",
        "version_normalized": "5.2.6.0",
        "source": {
            "type": "git",
            "url": "https://github.com/PHPMailer/PHPMailer.git",
            "reference": "4d9434e394496a5bb7acd9e73046587184b413df"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/PHPMailer/PHPMailer/zipball/4d9434e394496a5bb7acd9e73046587184b413df",
            "reference": "4d9434e394496a5bb7acd9e73046587184b413df",
            "shasum": ""
        },
        "require": {
            "php": ">=5.0.0"
        },
        "require-dev": {
 .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://monitor.bart.htb/vendor/composer/installed.json'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)