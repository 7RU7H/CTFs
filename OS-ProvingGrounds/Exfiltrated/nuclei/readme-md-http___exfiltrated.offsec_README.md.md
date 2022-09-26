### README.md file disclosure (readme-md) found on http://exfiltrated.offsec
---
**Details**: **readme-md**  matched at http://exfiltrated.offsec

**Protocol**: HTTP

**Full URL**: http://exfiltrated.offsec/README.md

**Timestamp**: Mon Sep 26 20:19:57 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | README.md file disclosure |
| Authors | ambassify |
| Tags | exposure, markdown |
| Severity | info |
| Description | Internal documentation file often used in projects which can contain sensitive information. |
| shodan-query | html:"README.MD" |

**Request**
```http
GET /README.md HTTP/1.1
Host: exfiltrated.offsec
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4794
Accept-Ranges: bytes
Content-Type: text/markdown
Date: Mon, 26 Sep 2022 19:20:07 GMT
Etag: "12ba-56e930a344980"
Last-Modified: Thu, 14 Jun 2018 05:04:54 GMT
Server: Apache/2.4.41 (Ubuntu)

# Subrion CMS

[![Semver compliant](https://img.shields.io/badge/Semver-2.0.0-yellow.svg)](http://semver.org/spec/v2.0.0.html)
[![Join the chat at https://gitter.im/intelliants/subrion](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/intelliants/subrion?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![OpenCollective](https://opencollective.com/subrion/backers/badge.svg)](#backers) 
[![OpenCollective](https://opencollective.com/subrion/sponsors/badge.svg)](#sponsors)



## What is Subrion?

* Subrion is a **Content Management System** (CMS) which allows you to build websites for any purpose. Yes, from blog to corporate mega portal.
* It is a powerful web application which requires a server with PHP / MySQL to run.
* Subrion is a **free and open source software** distributed under the GPL v3.

## Development Roadmap

We migrated our development to GitHub with the release of Subrion 4.x version. Please check our previous issue tracker [here](https://dev.subrion.org/projects/subrion-cms/roadmap).

## Updates are free!

Always use the **[latest version](https://subrion.org/download/)**.

## Contributing

We appreciate any contribution to Subrion, whether it is related to bugs, grammar, or simply a suggestion or improvement. However, we ask that any contributions follow our simple guidelines in order to be properly received.

What you mainly want to know is that:

* All the main activity happens in the `develop` branch. Any pull request should be addressed only to that branch. We will not consider pull requests made to the `master`.
* It's very well appreciated, and highly suggested, to start a new feature whenever you want to make changes or add new functionality. It will make it much easier for us to just checkout your feature branch and test it, before merging it into `develop`

Please read [Contribution Guidelines](CONTRIBUTING.md)

## Backers

Support us with a monthly donation and help us continue our activities. [[Become a backer](https://opencollective.com/subrion#backer)]

<a href="https://opencollective.com/subrion/backer/0/website" target="_blank"><img src="https://opencollective.com/subrion/backer/0/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/1/website" target="_blank"><img src="https://opencollective.com/subrion/backer/1/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/2/website" target="_blank"><img src="https://opencollective.com/subrion/backer/2/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/3/website" target="_blank"><img src="https://opencollective.com/subrion/backer/3/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/4/website" target="_blank"><img src="https://opencollective.com/subrion/backer/4/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/5/website" target="_blank"><img src="https://opencollective.com/subrion/backer/5/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/6/website" target="_blank"><img src="https://opencollective.com/subrion/backer/6/avatar.svg"></a>
<a href="https://opencollective.com/subrion/backer/7/website" target="_blank"><img src="https://opencollective.com/subrion/backer/7/avatar.svg"></a>


## Sponsors

Become a sponsor and get your logo on our README on Github with a link to your site. [[Become a sponsor](https://opencollective.com/subrion#sponsor)]

<a href="https://opencollective.com/subrion/sponsor/0/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/0/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/1/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/1/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/2/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/2/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/3/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/3/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/4/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/4/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/5/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/5/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/6/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/6/avatar.svg"></a>
<a href="https://opencollective.com/subrion/sponsor/7/website" target="_blank"><img src="https://opencollective.com/subrion/sponsor/7/avatar.svg"></a>


## Copyright

* Copyright (C) 2008 - 2017 Intelliants LLC. All rights reserved.
* Distributed under the GNU GPL v3
* See [License details](https://subrion.org/license.html)

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://exfiltrated.offsec/README.md'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)