### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.129.84.130
---
**Details**: **robots-txt-endpoint**  matched at http://10.129.84.130

**Protocol**: HTTP

**Full URL**: http://10.129.84.130/CHANGELOG.txt

**Timestamp**: Thu Aug 11 09:56:46 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /CHANGELOG.txt HTTP/1.1
Host: 10.129.84.130
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 110781
Accept-Ranges: bytes
Content-Type: text/plain
Date: Thu, 11 Aug 2022 08:56:43 GMT
Etag: "e45e8b8a9da0d21:0"
Last-Modified: Sun, 19 Mar 2017 10:42:44 GMT
Server: Microsoft-IIS/7.5
X-Powered-By: ASP.NET


Drupal 7.54, 2017-02-01
-----------------------
- Modules are now able to define theme engines (API addition:
  https://www.drupal.org/node/2826480).
- Logging of searches can now be disabled (new option in the administrative
  interface).
- Added menu tree render structure to (pre-)process hooks for theme_menu_tree()
  (API addition: https://www.drupal.org/node/2827134).
- Added new function for determining whether an HTTPS request is being served
  (API addition: https://www.drupal.org/node/2824590).
- Fixed incorrect default value for short and medium date formats on the date
  type configuration page.
- File validation error message is now removed after subsequent upload of valid
  file.
- Numerous bug fixes.
- Numerous API documentation improvements.
- Additional performance improvements.
- Additional automated test coverage.

Drupal 7.53, 2016-12-07
-----------------------
- Fixed drag and drop support on newer Chrome/IE 11+ versions after 7.51 update
  when jQuery is updated to 1.7-1.11.0.

Drupal 7.52, 2016-11-16
-----------------------
- Fixed security issues (multiple vulnerabilities). See SA-CORE-2016-005.

Drupal 7.51, 2016-10-05
-----------------------
- The Update module now also checks for updates to a disabled theme that is
  used as an admin theme.
- Exceptions thrown in dblog_watchdog() are now caught and ignored.
- Clarified the warning that appears when modules are missing or have moved.
- Log messages are now XSS filtered on display.
- Draggable tables now work on touch screen devices.
- Added a setting for allowing double underscores in CSS identifiers
  (https://www.drupal.org/node/2810369).
- If a user navigates away from a page while an Ajax request is running they
  will no longer get an error message saying "An Ajax HTTP request terminated
  abnormally".
- The system_region_list() API function now takes an optional third parameter
  which allows region name translations to be skipped when they are not needed
  (API addition: https://www.drupal.org/node/2810365).
- Numerous performance improvements.
- Numerous bug fixes.
- Numerous API documentation improvements.
- Additional automated test coverage.

Drupal 7.50, 2016-07-07 
-----------------------
- Added a new "administer fields" permission for trusted users, which is
  required in addition to other permissions to use the field UI
  (https://www.drupal.org/node/2483307).
- Added clickjacking protection to Drupal core by setting the X-Frame-Options
  header to SAMEORIGIN by default (https://www.drupal.org/node/2735873).
- Added support for full UTF-8 (emojis, Asian symbols, mathematical symbols) on
  MySQL and other database drivers when the site and database are configured to
  allow it (https://www.drupal.org/node/2761183).
- Improved performance by avoiding a re-scan of directories when a file is
  missing; instead, trigger a PHP warning (minor API change:
  https://www.drupal.org/node/2581445).
- Made it possible to use any PHP callable in Ajax form callbacks, form API
  form-building functions, and form API wrapper callbacks (API addition:
  https://www.drupal.org/node/2761169).
- Fixed that following a password reset link while logged in leaves users unable
  to change their password (minor user interface change:
  https://www.drupal.org/node/2759023).
- Implemented various fixes for automated test failures on PHP 5.4+ and PHP 7.
  Drupal core automated tests now pass in these environments.
- Improved support for PHP 7 by fixing various problems.
- Fixed various bugs with PHP 5.5+ imagerotate(), including when incorrect
  color indices are passed in.
- Fixed a regression introduced in Drupal 7.43 that allowed files uploaded by
  anonymous users to be lost after form validation errors, and that also caused
  regressions with certain contributed modules.
- Fixed a regression introduced in Drupal 7.36 which caused the default value
  of hidden textarea fields to be ignored.
- Fixed robots.txt to allow search engines to access CSS, JavaScript and image
  files.
- Changed wording on the Update Manager settings page to clarify that the
  option to check for disabled module updates also applies to uninstalled
  modules (administrative-facing translatable string change).
- Changed the help text when editing menu links and configuring URL redirect
  actions so that it does not reference "Drupal" or the drupal.org website
  (administrative-facing translatable string change).
- Fixed the locale safety check that is used to ensure that translations are
  safe to allow for tokens in the href/src attributes of translated strings.
- Fixed that URL generation only works on port 80 when using domain based
  language negotation.
- Made method="get" forms work inside the administrative overlay. The fix adds
  a new hidden field to these forms when they appear inside the overlay (minor
  data st.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.129.84.130/CHANGELOG.txt'
```
---
Generated by [Nuclei 2.7.5](https://github.com/projectdiscovery/nuclei)