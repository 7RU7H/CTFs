### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.129.95.193
---
**Details**: **robots-txt-endpoint**  matched at http://10.129.95.193

**Protocol**: HTTP

**Full URL**: http://10.129.95.193/UPGRADE.txt

**Timestamp**: Tue Jun 14 20:19:39 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /UPGRADE.txt HTTP/1.1
Host: 10.129.95.193
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
Content-Type: text/plain
Date: Tue, 14 Jun 2022 19:19:39 GMT
Etag: "278b-56e5ff48c305d-gzip"
Last-Modified: Mon, 11 Jun 2018 16:08:07 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding

INTRODUCTION
------------
This document describes how to:

  * Update your Drupal site from one minor 7.x version to another minor 7.x
    version; for example, from 7.8 to 7.9, or from 7.6 to 7.10.

  * Upgrade your Drupal site's major version from 6.x to 7.x.

First steps and definitions:

  * If you are upgrading to Drupal version x.y, then x is known as the major
    version number, and y is known as the minor version number. The download
    file will be named drupal-x.y.tar.gz (or drupal-x.y.zip).

  * All directories mentioned in this document are relative to the directory of
    your Drupal installation.

  * Make a full backup of all files, directories, and your database(s) before
    starting, and save it outside your Drupal installation directory.
    Instructions may be found at http://drupal.org/upgrade/backing-up-the-db

  * It is wise to try an update or upgrade on a test copy of your site before
    applying it to your live site. Even minor updates can cause your site's
    behavior to change.

  * Each new release of Drupal has release notes, which explain the changes made
    since the previous version and any special instructions needed to update or
    upgrade to the new version. You can find a link to the release notes for the
    version you are upgrading or updating to on the Drupal project page
    (http://drupal.org/project/drupal).

UPGRADE PROBLEMS
----------------
If you encounter errors during this process,

  * Note any error messages you see.

  * Restore your site to its previous state, using the file and database backups
    you created before you started the upgrade process. Do not attempt to do
    further upgrades on a site that had update problems.

  * Consult one of the support options listed on http://drupal.org/support

More in-depth information on upgrading can be found at http://drupal.org/upgrade

MINOR VERSION UPDATES
---------------------
To update from one minor 7.x version of Drupal to any later 7.x version, after
following the instructions in the INTRODUCTION section at the top of this file:

1. Log in as a user with the permission "Administer software updates".

2. Go to Administration > Configuration > Development > Maintenance mode.
   Enable the "Put site into maintenance mode" checkbox and save the
   configuration.

3. Remove all old core files and directories, except for the 'sites' directory
   and any custom files you added elsewhere.

   If you made modifications to files like .htaccess or robots.txt, you will
   need to re-apply them from your backup, after the new files are in place.

   Sometimes an update includes changes to default.settings.php (this will be
   noted in the release notes). If that's the case, follow these steps:

   - Locate your settings.php file in the /sites/* directory. (Typically
     sites/default.)

   - Make a backup copy of your settings.php file, with a different file name.

   - Make a copy of the new default.settings.php file, and name the copy
     settings.php (overwriting your previous settings.php file).

   - Copy the custom and site-specific entries from the backup you made into the
     new settings.php file. You will definitely need the lines giving the
     database information, and you will also want to copy in any other
     customizations you have added.

   You can find the release notes for your version at
   https://www.drupal.org/project/drupal. At bottom of the project page under
   "Downloads" use the link for your version of Drupal to view the release
   notes. If your version is not listed, use the 'View all releases' link. From
   this page you can scroll down or use the filter to find your version and its
   release notes.

4. Download the latest Drupal 7.x release from http://drupal.org to a
   directory outside of your web root. Extract the archive and copy the files
   into your Drupal directory.

   On a typical Unix/Linux command line, use the following commands to download
   and extract:

     wget http://drupal.org/files/projects/drupal-x.y.tar.gz
     tar -zxvf drupal-x.y.tar.gz

   This creates a new directory drupal-x.y/ containing all Drupal files and
   directories. Copy the files into your Drupal installation directory:

     cp -R drupal-x.y/* drupal-x.y/.htaccess /path/to/your/installation

   If you do not have command line access to your server, download the archive
   from http://drupal.org using your web browser, extract it, and then use an
   FTP client to upload the files to your web root.

5. Re-apply any modifications to files such as .htaccess or robots.txt.

6. Run update.php by visiting http://www.example.com/update.php (replace
   www.example.com with your domain name). This will update the core database
   tables.

   If you are unable to access update.php do the following:

   - Open settings.php with a text editor.

   - Find the.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://10.129.95.193/UPGRADE.txt'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)