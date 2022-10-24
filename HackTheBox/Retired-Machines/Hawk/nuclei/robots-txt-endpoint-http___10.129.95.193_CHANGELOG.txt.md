### robots.txt endpoint prober (robots-txt-endpoint) found on http://10.129.95.193
---
**Details**: **robots-txt-endpoint**  matched at http://10.129.95.193

**Protocol**: HTTP

**Full URL**: http://10.129.95.193/CHANGELOG.txt

**Timestamp**: Tue Jun 14 20:19:45 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /CHANGELOG.txt HTTP/1.1
Host: 10.129.95.193
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36
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
Date: Tue, 14 Jun 2022 19:19:44 GMT
Etag: "1b4f3-56e5ff48ba3bd-gzip"
Last-Modified: Mon, 11 Jun 2018 16:08:07 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding


Drupal 7.58, 2018-03-28
-----------------------
- Fixed security issues (multiple vulnerabilities). See SA-CORE-2018-002.

Drupal 7.57, 2018-02-21
-----------------------
- Fixed security issues (multiple vulnerabilities). See SA-CORE-2018-001.

Drupal 7.56, 2017-06-21
-----------------------
- Fixed security issues (access bypass). See SA-CORE-2017-003.

Drupal 7.55, 2017-06-07
-----------------------
- Fixed incompatibility with PHP versions 7.0.19 and 7.1.5 due to duplicate
  DATE_RFC7231 definition.
- Made Drupal core pass all automated tests on PHP 7.1.
- Allowed services such as Let's Encrypt to work with Drupal on Apache, by
  making Drupal's .htaccess file allow access to the .well-known directory
  defined by RFC 5785.
- Made new Drupal sites work correctly on Apache 2.4 when the mod_access_compat
  Apache module is disabled.
- Fixed Drupal's URL-generating functions to always encode '[' and ']' so that
  the URLs will pass HTML5 validation.
- Various additional bug fixes.
- Various API documentation improvements.
- Additional automated test coverage.

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
  regressions with certai.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36' 'http://10.129.95.193/CHANGELOG.txt'
```
---
Generated by [Nuclei 2.7.2](https://github.com/projectdiscovery/nuclei)