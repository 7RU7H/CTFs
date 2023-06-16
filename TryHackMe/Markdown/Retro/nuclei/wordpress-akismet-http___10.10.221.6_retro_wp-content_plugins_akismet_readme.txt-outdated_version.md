### Akismet Anti-Spam' Spam Protection Detection (wordpress-akismet:outdated_version) found on http://10.10.221.6/retro
---
**Details**: **wordpress-akismet:outdated_version**  matched at http://10.10.221.6/retro

**Protocol**: HTTP

**Full URL**: http://10.10.221.6/retro/wp-content/plugins/akismet/readme.txt

**Timestamp**: Fri Jun 16 19:33:28 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Akismet Anti-Spam' Spam Protection Detection |
| Authors | ricardomaia |
| Tags | tech, wordpress, wp-plugin, top-100, top-200 |
| Severity | info |
| plugin_namespace | akismet |
| wpscan | https://wpscan.com/plugin/akismet |

**Request**
```http
GET /retro/wp-content/plugins/akismet/readme.txt HTTP/1.1
Host: 10.10.221.6
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
Content-Length: 20046
Accept-Ranges: bytes
Content-Type: text/plain
Date: Fri, 16 Jun 2023 18:32:57 GMT
Etag: "0bd11dacd16d51:0"
Last-Modified: Thu, 30 May 2019 09:55:46 GMT
Server: Microsoft-IIS/10.0

=== Akismet Anti-Spam ===
Contributors: matt, ryan, andy, mdawaffe, tellyworth, josephscott, lessbloat, eoigal, cfinke, automattic, jgs, procifer, stephdau
Tags: akismet, comments, spam, antispam, anti-spam, anti spam, comment moderation, comment spam, contact form spam, spam comments
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 4.1.2
License: GPLv2 or later

Akismet checks your comments and contact form submissions against our global database of spam to protect you and your site from malicious content.

== Description ==

Akismet checks your comments and contact form submissions against our global database of spam to prevent your site from publishing malicious content. You can review the comment spam it catches on your blog's "Comments" admin screen.

Major features in Akismet include:

* Automatically checks all comments and filters out the ones that look like spam.
* Each comment has a status history, so you can easily see which comments were caught or cleared by Akismet and which were spammed or unspammed by a moderator.
* URLs are shown in the comment body to reveal hidden or misleading links.
* Moderators can see the number of approved comments for each user.
* A discard feature that outright blocks the worst spam, saving you disk space and speeding up your site.

PS: You'll need an [Akismet.com API key](https://akismet.com/get/) to use it.  Keys are free for personal blogs; paid subscriptions are available for businesses and commercial sites.

== Installation ==

Upload the Akismet plugin to your blog, Activate it, then enter your [Akismet.com API key](https://akismet.com/get/).

1, 2, 3: You're done!

== Changelog ==

= 4.1.2 =
*Release Date - 14 May 2019*

* Fixed a conflict between the Akismet setup banner and other plugin notices.
* Reduced the number of API requests made by the plugin when attempting to verify the API key.
* Include additional data in the pingback pre-check API request to help make the stats more accurate.
* Fixed a bug that was enabling the "Check for Spam" button when no comments were eligible to be checked.
* Improved Akismet's AMP compatibility.

= 4.1.1 =
*Release Date - 31 January 2019*

* Fixed the "Setup Akismet" notice so it resizes responsively.
* Only highlight the "Save Changes" button in the Akismet config when changes have been made.
* The count of comments in your spam queue shown on the dashboard show now always be up-to-date.

= 4.1 =
*Release Date - 12 November 2018*

* Added a WP-CLI method for retrieving stats.
* Hooked into the new "Personal Data Eraser" functionality from WordPress 4.9.6.
* Added functionality to clear outdated alerts from Akismet.com.

= 4.0.8 =
*Release Date - 19 June 2018*

* Improved the grammar and consistency of the in-admin privacy related notes (notice and config).
* Revised in-admin explanation of the comment form privacy notice to make its usage clearer. 
* Added `rel="nofollow noopener"` to the comment form privacy notice to improve SEO and security.

= 4.0.7 =
*Release Date - 28 May 2018*

* Based on user feedback, the link on "Learn how your comment data is processed." in the optional privacy notice now has a `target` of `_blank` and opens in a new tab/window.
* Updated the in-admin privacy notice to use the term "comment" instead of "contact" in "Akismet can display a notice to your users under your comment forms."
* Only show in-admin privacy notice if Akismet has an API Key configured

= 4.0.6 =
*Release Date - 26 May 2018*

* Moved away from using `empty( get_option() )` to instantiating a variable to be compatible with older versions of PHP (5.3, 5.4, etc).  

= 4.0.5 =
*Release Date - 26 May 2018*

* Corrected version number after tagging. Sorry...

= 4.0.4 =
*Release Date - 26 May 2018*

* Added a hook to provide Akismet-specific privacy information for a site's privacy policy.
* Added tools to control the display of a privacy related notice under comment forms.
* Fixed HTML in activation failure message to close META and HEAD tag properly.
* Fixed a bug that would sometimes prevent Akismet from being correctly auto-configured.

= 4.0.3 =
*Release Date - 19 February 2018*

* Added a scheduled task to remove entries in wp_commentmeta that no longer have corresponding comments in wp_comments.
* Added a new `akismet_batch_delete_count` action to the batch delete methods for people who'd like to keep track of the numbers of records being processed by those methods.

= 4.0.2 =
*Release Date - 18 December 2017*

* Fixed a bug that could cause Akismet to recheck a comment that has already been manually approved or marked as spam.
* Fixed a bug that could cause Akismet to claim that some comments are still waiting to be checked when no comments are waiting to be checked.

= 4.0.1 =
*Release Date - 6 November 2017*

* Fixed a bug that could prevent some users from connecting Akismet via their Jetpa.... Truncated ....
```

**Extra Information**

**Extracted results**:

- 4.1.2

**Metadata**:

- last_version: 5.1


References: 
- https://wordpress.org/plugins/akismet/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.3319.102 Safari/537.36' 'http://10.10.221.6/retro/wp-content/plugins/akismet/readme.txt'
```
---
Generated by [Nuclei v2.9.4](https://github.com/projectdiscovery/nuclei)