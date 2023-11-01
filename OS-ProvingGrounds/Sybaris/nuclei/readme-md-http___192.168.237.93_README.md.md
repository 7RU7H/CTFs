### README.md file disclosure (readme-md) found on http://192.168.237.93/

----
**Details**: **readme-md** matched at http://192.168.237.93/

**Protocol**: HTTP

**Full URL**: http://192.168.237.93/README.md

**Timestamp**: Thu Oct 26 15:38:37 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | README.md file disclosure |
| Authors | ambassify |
| Tags | exposure, markdown, files |
| Severity | info |
| Description | Internal documentation file often used in projects which can contain sensitive information. |
| shodan-query | html:"README.MD" |

**Request**
```http
GET /README.md HTTP/1.1
Host: 192.168.237.93
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36
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
Date: Thu, 26 Oct 2023 14:38:30 GMT
Etag: "1fee-5a4f8214b9740-gzip"
Last-Modified: Wed, 06 May 2020 10:21:41 GMT
Server: Apache/2.4.6 (CentOS) PHP/7.3.22
Vary: Accept-Encoding

<a href="https://www.htmly.com" target="_blank">![Logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo-big.png)</a>

HTMLy is an open source Databaseless Blogging Platform or Flat-File Blog prioritizes simplicity and speed written in PHP. HTMLy can be referred to as Flat-File CMS either since it will also manage your content.

You do not need to use a VPS to run HTMLy, shared hosting or even free hosting should work as long as the host supports at least PHP 5.3.

Demo
----
Visit <a href="https://demo.htmly.com" target="_blank">HTMLy demo</a> as blog.

Features
---------
- Admin Panel
- Markdown editor with live preview and image upload
- Categorization with category and tags (multiple tagging support)
- Static Pages (e.g. Contact Page, About Page)
- Meta canonical, description, and rich snippets for SEO
- Pagination
- Author Page
- Multi author support
- Social Links
- Disqus Comments (optional)
- Facebook Comments (optional)
- Google Analytics
- Built-in Search
- Related Posts
- Per Post Navigation (previous and next post)
- Body class for easy theming
- Breadcrumb
- Archive page (by year, year-month, or year-month-day)
- JSON API
- OPML
- RSS Feed
- RSS 2.0 Importer (basic)
- Sitemap.xml
- Archive and Tag Cloud Widget
- SEO Friendly URLs
- Teaser thumbnail for images and Youtube videos
- Responsive Design
- User Roles
- Online Backup
- File Caching
- Auto Update
- Post Draft
- i18n

Requirements
------------
HTMLy requires PHP 5.3 or greater and php-xml package.

Installations
-------------
If you have an OpenSSL enabled server (usually enabled by default), use the [installer.php](https://github.com/danpros/htmly/releases/latest) and read the following [instructions](https://docs.htmly.com/basics/installations) to get started. If you don't have OpenSSL, please download the latest version, extract it, then upload the extracted files to your server. Also, make sure the installation folder is writeable by your server.

Configurations
--------------
Set written permission for the `cache` and `content` directories.

Rename `config.ini.example` inside the `config` folder to `config.ini` (or you can create a new `config/config.ini` file) then change the site settings there.

Create `YourUsername.ini` inside the `config/users` folder or simply rename the `username.ini.example` file and write down your password there:

````cfg
password = YourPassword
````

In addition, HTMLy support admin user role. To do so, simply add the following line to your choosen user:

````cfg
role = admin
````

Users assigned with the admin role can edit/delete all users' posts.

To access the admin panel, add `/login` to the end of your site's URL.
e.g. `www.yoursite.com/login`

### Lighttpd
The following is an example configuration for lighttpd:

````php
$HTTP["url"] =~ "^/config" {
  url.access-deny = ( "" )
}
$HTTP["url"] =~ "^/system/includes" {
  url.access-deny = ( "" )
}
$HTTP["url"] =~ "^/system/admin/views" {
  url.access-deny = ( "" )
}

url.rewrite-once = (
  "^/(themes|system|vendor)/(.*)" => "$0",
  "^/(.*\.php)" => "$0",

  # Everything else is handles by htmly
  "^/(.*)$" => "/index.php/$1"
)
````

### Nginx
The following is a basic configuration for Nginx:

````nginx
server {
  listen 80;

  server_name example.com www.example.com;
  root /usr/share/nginx/html;

  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log error;

  index index.php;

  location ~ /config/ {
     deny all;
  }

  location / {
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME   $document_root$fastcgi_script_name;
        include        fastcgi_params;
  }
}
````

Making a secure password
----------------------
Passwords can be stored in `username.ini` (where "username" is the user's username) in either plaintext, encryption algorithms supported by php `hash` or bcrypt (recommended). To generate a bcrypt encrypted password:
````
$ php -a
> echo password_hash('desiredpassword', PASSWORD_BCRYPT);
````
This will produce a hash which is to be placed in the `password` field in `username.ini`. Ensure that the `encryption` field is set to `bcrypt`.


Both Online or Offline
----------------------
The built-in editor found in the admin panel, also provides you the ability to write to Markdown files offline by uploading them (see naming convention below) into the `content/username/blog/category/type/`:

* `username` must match `config/users/username.ini`.
* `category` must match the `category.md` inside `content/data/category/category.md` except the `uncategorized` category.
* `type` is the content type. Available content type `post`, `video`, `audio`, `link`, `quote`.

For static pages you can upload it to the.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://192.168.237.93/README.md'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)