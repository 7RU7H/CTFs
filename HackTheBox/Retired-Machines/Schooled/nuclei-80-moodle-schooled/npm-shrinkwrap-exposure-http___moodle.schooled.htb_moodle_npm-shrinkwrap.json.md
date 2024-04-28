### Node Shrinkwrap Exposure (npm-shrinkwrap-exposure) found on moodle.schooled.htb

----
**Details**: **npm-shrinkwrap-exposure** matched at moodle.schooled.htb

**Protocol**: HTTP

**Full URL**: http://moodle.schooled.htb/moodle/npm-shrinkwrap.json

**Timestamp**: Sun Apr 28 17:02:26 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | Node Shrinkwrap Exposure |
| Authors | dhiyaneshdk, noraj |
| Tags | config, exposure, npm, files, node |
| Severity | info |
| Description | A file created by npm shrinkwrap. It is identical to package-lock.json.<br> |
| shodan-query | html:"npm-shrinkwrap.json" |

**Request**
```http
GET /moodle/npm-shrinkwrap.json HTTP/1.1
Host: moodle.schooled.htb
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 354972
Accept-Ranges: bytes
Content-Type: application/json
Date: Sun, 28 Apr 2024 16:02:25 GMT
Etag: "56a9c-5a7fb078f1240"
Last-Modified: Sat, 13 Jun 2020 18:04:49 GMT
Server: Apache/2.4.46 (FreeBSD) PHP/7.4.15

{
  "name": "Moodle",
  "requires": true,
  "lockfileVersion": 1,
  "dependencies": {
    "@babel/code-frame": {
      "version": "7.0.0",
      "resolved": "https://registry.npmjs.org/@babel/code-frame/-/code-frame-7.0.0.tgz",
      "integrity": "sha512-OfC2uemaknXr87bdLUkWog7nYuliM9Ij5HUcajsVcMCpQrcLmtxRbVFTIqmcSkSeYRBFBRxs2FiUqFJDLdiebA==",
      "dev": true,
      "requires": {
        "@babel/highlight": "^7.0.0"
      }
    },
    "@babel/compat-data": {
      "version": "7.9.0",
      "resolved": "https://registry.npmjs.org/@babel/compat-data/-/compat-data-7.9.0.tgz",
      "integrity": "sha512-zeFQrr+284Ekvd9e7KAX954LkapWiOmQtsfHirhxqfdlX6MEC32iRE+pqUGlYIBchdevaCwvzxWGSy/YBNI85g==",
      "dev": true,
      "requires": {
        "browserslist": "^4.9.1",
        "invariant": "^2.2.4",
        "semver": "^5.5.0"
      },
      "dependencies": {
        "semver": {
          "version": "5.7.1",
          "resolved": "https://registry.npmjs.org/semver/-/semver-5.7.1.tgz",
          "integrity": "sha512-sauaDf/PZdVgrLTNYHRtpXa1iRiKcaebiKQ1BJdpQlWH2lCvexQdX55snPFyK7QzpudqbCI0qXFfOasHdyNDGQ==",
          "dev": true
        }
      }
    },
    "@babel/core": {
      "version": "7.9.0",
      "resolved": "https://registry.npmjs.org/@babel/core/-/core-7.9.0.tgz",
      "integrity": "sha512-kWc7L0fw1xwvI0zi8OKVBuxRVefwGOrKSQMvrQ3dW+bIIavBY3/NpXmpjMy7bQnLgwgzWQZ8TlM57YHpHNHz4w==",
      "dev": true,
      "requires": {
        "@babel/code-frame": "^7.8.3",
        "@babel/generator": "^7.9.0",
        "@babel/helper-module-transforms": "^7.9.0",
        "@babel/helpers": "^7.9.0",
        "@babel/parser": "^7.9.0",
        "@babel/template": "^7.8.6",
        "@babel/traverse": "^7.9.0",
        "@babel/types": "^7.9.0",
        "convert-source-map": "^1.7.0",
        "debug": "^4.1.0",
        "gensync": "^1.0.0-beta.1",
        "json5": "^2.1.2",
        "lodash": "^4.17.13",
        "resolve": "^1.3.2",
        "semver": "^5.4.1",
        "source-map": "^0.5.0"
      },
      "dependencies": {
        "@babel/code-frame": {
          "version": "7.8.3",
          "resolved": "https://registry.npmjs.org/@babel/code-frame/-/code-frame-7.8.3.tgz",
          "integrity": "sha512-a9gxpmdXtZEInkCSHUJDLHZVBgb1QS0jhss4cPP93EW7s+uC5bikET2twEF3KV+7rDblJcmNvTR7VJejqd2C2g==",
          "dev": true,
          "requires": {
            "@babel/highlight": "^7.8.3"
          }
        },
        "@babel/helper-module-imports": {
          "version": "7.8.3",
          "resolved": "https://registry.npmjs.org/@babel/helper-module-imports/-/helper-module-imports-7.8.3.tgz",
          "integrity": "sha512-R0Bx3jippsbAEtzkpZ/6FIiuzOURPcMjHp+Z6xPe6DtApDJx+w7UYyOLanZqO8+wKR9G10s/FmHXvxaMd9s6Kg==",
          "dev": true,
          "requires": {
            "@babel/types": "^7.8.3"
          }
        },
        "@babel/helper-module-transforms": {
          "version": "7.9.0",
          "resolved": "https://registry.npmjs.org/@babel/helper-module-transforms/-/helper-module-transforms-7.9.0.tgz",
          "integrity": "sha512-0FvKyu0gpPfIQ8EkxlrAydOWROdHpBmiCiRwLkUiBGhCUPRRbVD2/tm3sFr/c/GWFrQ/ffutGUAnx7V0FzT2wA==",
          "dev": true,
          "requires": {
            "@babel/helper-module-imports": "^7.8.3",
            "@babel/helper-replace-supers": "^7.8.6",
            "@babel/helper-simple-access": "^7.8.3",
            "@babel/helper-split-export-declaration": "^7.8.3",
            "@babel/template": "^7.8.6",
            "@babel/types": "^7.9.0",
            "lodash": "^4.17.13"
          }
        },
        "@babel/helper-simple-access": {
          "version": "7.8.3",
          "resolved": "https://registry.npmjs.org/@babel/helper-simple-access/-/helper-simple-access-7.8.3.tgz",
          "integrity": "sha512-VNGUDjx5cCWg4vvCTR8qQ7YJYZ+HBjxOgXEl7ounz+4Sn7+LMD3CFrCTEU6/qXKbA2nKg21CwhhBzO0RpRbdCw==",
          "dev": true,
          "requires": {
            "@babel/template": "^7.8.3",
            "@babel/types": "^7.8.3"
          }
        },
        "@babel/helper-split-export-declaration": {
          "version": "7.8.3",
          "resolved": "https://registry.npmjs.org/@babel/helper-split-export-declaration/-/helper-split-export-declaration-7.8.3.tgz",
          "integrity": "sha512-3x3yOeyBhW851hroze7ElzdkeRXQYQbFIb7gLK1WQYsw2GWDay5gAJNw1sWJ0VFP6z5J1whqeXH/WCdCjZv6dA==",
          "dev": true,
          "requires": {
            "@babel/types": "^7.8.3"
          }
        },
        "@babel/highlight": {
          "version": "7.9.0",
          "resolved": "https://registry.npmjs.org/@babel/highlight/-/highlight-7.9.0.tgz",
          "integrity": "sha512-lJZPilxX7Op3Nv/2cvFdnlepPXDxi29wxteT57Q965oc5R9v86ztx0jfxVrTcBk8C2kcPkkDa2Z4T3ZsPPVWsQ==",
          "dev": true,
          "requires": {
            "@babel/helper-validator-identifier": "^7.9.0",
        .... Truncated ....
```

References: 
- https://docs.npmjs.com/cli/v9/configuring-npm/npm-shrinkwrap-json

**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36' 'http://moodle.schooled.htb/moodle/npm-shrinkwrap.json'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)