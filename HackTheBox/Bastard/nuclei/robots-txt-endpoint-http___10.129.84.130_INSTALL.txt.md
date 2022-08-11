### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.129.84.130
---
**Details**: **robots-txt-endpoint**  matched at http://10.129.84.130

**Protocol**: HTTP

**Full URL**: http://10.129.84.130/INSTALL.txt

**Timestamp**: Thu Aug 11 09:55:15 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /INSTALL.txt HTTP/1.1
Host: 10.129.84.130
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
Content-Length: 17995
Accept-Ranges: bytes
Content-Type: text/plain
Date: Thu, 11 Aug 2022 08:55:14 GMT
Etag: "a521908a9da0d21:0"
Last-Modified: Sun, 19 Mar 2017 10:42:44 GMT
Server: Microsoft-IIS/7.5
X-Powered-By: ASP.NET


CONTENTS OF THIS FILE
---------------------

 * Requirements and notes
 * Optional server requirements
 * Installation
 * Building and customizing your site
 * Multisite configuration
 * More information

REQUIREMENTS AND NOTES
----------------------

Drupal requires:

- A web server. Apache (version 2.0 or greater) is recommended.
- PHP 5.2.4 (or greater) (http://www.php.net/).
- One of the following databases:
  - MySQL 5.0.15 (or greater) (http://www.mysql.com/).
  - MariaDB 5.1.44 (or greater) (http://mariadb.org/). MariaDB is a fully
    compatible drop-in replacement for MySQL.
  - Percona Server 5.1.70 (or greater) (http://www.percona.com/). Percona
    Server is a backwards-compatible replacement for MySQL.
  - PostgreSQL 8.3 (or greater) (http://www.postgresql.org/).
  - SQLite 3.3.7 (or greater) (http://www.sqlite.org/).

For more detailed information about Drupal requirements, including a list of
PHP extensions and configurations that are required, see "System requirements"
(http://drupal.org/requirements) in the Drupal.org online documentation.

For detailed information on how to configure a test server environment using a
variety of operating systems and web servers, see "Local server setup"
(http://drupal.org/node/157602) in the Drupal.org online documentation.

Note that all directories mentioned in this document are always relative to the
directory of your Drupal installation, and commands are meant to be run from
this directory (except for the initial commands that create that directory).

OPTIONAL SERVER REQUIREMENTS
----------------------------

- If you want to use Drupal's "Clean URLs" feature on an Apache web server, you
  will need the mod_rewrite module and the ability to use local .htaccess
  files. For Clean URLs support on IIS, see "Clean URLs with IIS"
  (http://drupal.org/node/3854) in the Drupal.org online documentation.

- If you plan to use XML-based services such as RSS aggregation, you will need
  PHP's XML extension. This extension is enabled by default on most PHP
  installations.

- To serve gzip compressed CSS and JS files on an Apache web server, you will
  need the mod_headers module and the ability to use local .htaccess files.

- Some Drupal functionality (e.g., checking whether Drupal and contributed
  modules need updates, RSS aggregation, etc.) require that the web server be
  able to go out to the web and download information. If you want to use this
  functionality, you need to verify that your hosting provider or server
  configuration allows the web server to initiate outbound connections. Most web
  hosting setups allow this.

INSTALLATION
------------

1. Download and extract Drupal.

   You can obtain the latest Drupal release from http://drupal.org -- the files
   are available in .tar.gz and .zip formats and can be extracted using most
   compression tools.

   To download and extract the files, on a typical Unix/Linux command line, use
   the following commands (assuming you want version x.y of Drupal in .tar.gz
   format):

     wget http://drupal.org/files/projects/drupal-x.y.tar.gz
     tar -zxvf drupal-x.y.tar.gz

   This will create a new directory drupal-x.y/ containing all Drupal files and
   directories. Then, to move the contents of that directory into a directory
   within your web server's document root or your public HTML directory,
   continue with this command:

     mv drupal-x.y/* drupal-x.y/.htaccess /path/to/your/installation

2. Optionally, download a translation.

   By default, Drupal is installed in English, and further languages may be
   installed later. If you prefer to install Drupal in another language
   initially:

   - Download a translation file for the correct Drupal version and language
     from the translation server: http://localize.drupal.org/translate/downloads

   - Place the file into your installation profile's translations directory.
     For instance, if you are using the Standard installation profile,
     move the .po file into the directory:

       profiles/standard/translations/

   For detailed instructions, visit http://drupal.org/localize

3. Create the Drupal database.

   Because Drupal stores all site information in a database, you must create
   this database in order to install Drupal, and grant Drupal certain database
   privileges (such as the ability to create tables). For details, consult
   INSTALL.mysql.txt, INSTALL.pgsql.txt, or INSTALL.sqlite.txt. You may also
   need to consult your web hosting provider for instructions specific to your
   web host.

   Take note of the username, password, database name, and hostname as you
   create the database. You will enter this information during the install.

4. Run the install script.

   To run the install script, point your browser to the base URL of your
   website (e.g., http://www.example.com).

.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36' 'http://10.129.84.130/INSTALL.txt'
```
---
Generated by [Nuclei 2.7.5](https://github.com/projectdiscovery/nuclei)