### WordPress Readme File (wordpress-readme-file) found on http://10.10.221.6/retro
---
**Details**: **wordpress-readme-file**  matched at http://10.10.221.6/retro

**Protocol**: HTTP

**Full URL**: http://10.10.221.6/retro/readme.html

**Timestamp**: Fri Jun 16 19:33:26 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | WordPress Readme File |
| Authors | tess |
| Tags | exposure, wordpress, wp, readme, files |
| Severity | info |
| shodan-query | http.component:"wordpress" |

**Request**
```http
GET /retro/readme.html HTTP/1.1
Host: 10.10.221.6
User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
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
Content-Type: text/html
Date: Fri, 16 Jun 2023 18:32:55 GMT
Etag: "0367ed6cd16d51:0"
Last-Modified: Thu, 30 May 2019 09:55:40 GMT
Server: Microsoft-IIS/10.0
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>WordPress &#8250; ReadMe</title>
	<link rel="stylesheet" href="wp-admin/css/install.css?ver=20100228" type="text/css" />
</head>
<body>
<h1 id="logo">
	<a href="https://wordpress.org/"><img alt="WordPress" src="wp-admin/images/wordpress-logo.png" /></a>
</h1>
<p style="text-align: center">Semantic Personal Publishing Platform</p>

<h2>First Things First</h2>
<p>Welcome. WordPress is a very special project to me. Every developer and contributor adds something unique to the mix, and together we create something beautiful that I&#8217;m proud to be a part of. Thousands of hours have gone into WordPress, and we&#8217;re dedicated to making it better every day. Thank you for making it part of your world.</p>
<p style="text-align: right">&#8212; Matt Mullenweg</p>

<h2>Installation: Famous 5-minute install</h2>
<ol>
	<li>Unzip the package in an empty directory and upload everything.</li>
	<li>Open <span class="file"><a href="wp-admin/install.php">wp-admin/install.php</a></span> in your browser. It will take you through the process to set up a <code>wp-config.php</code> file with your database connection details.
		<ol>
			<li>If for some reason this doesn&#8217;t work, don&#8217;t worry. It doesn&#8217;t work on all web hosts. Open up <code>wp-config-sample.php</code> with a text editor like WordPad or similar and fill in your database connection details.</li>
			<li>Save the file as <code>wp-config.php</code> and upload it.</li>
			<li>Open <span class="file"><a href="wp-admin/install.php">wp-admin/install.php</a></span> in your browser.</li>
		</ol>
	</li>
	<li>Once the configuration file is set up, the installer will set up the tables needed for your site. If there is an error, double check your <code>wp-config.php</code> file, and try again. If it fails again, please go to the <a href="https://wordpress.org/support/forums/" title="WordPress support">support forums</a> with as much data as you can gather.</li>
	<li><strong>If you did not enter a password, note the password given to you.</strong> If you did not provide a username, it will be <code>admin</code>.</li>
	<li>The installer should then send you to the <a href="wp-login.php">login page</a>. Sign in with the username and password you chose during the installation. If a password was generated for you, you can then click on &#8220;Profile&#8221; to change the password.</li>
</ol>

<h2>Updating</h2>
<h3>Using the Automatic Updater</h3>
<p>If you are updating from version 2.7 or higher, you can use the automatic updater:</p>
<ol>
	<li>Open <span class="file"><a href="wp-admin/update-core.php">wp-admin/update-core.php</a></span> in your browser and follow the instructions.</li>
	<li>You wanted more, perhaps? That&#8217;s it!</li>
</ol>

<h3>Updating Manually</h3>
<ol>
	<li>Before you update anything, make sure you have backup copies of any files you may have modified such as <code>index.php</code>.</li>
	<li>Delete your old WordPress files, saving ones you&#8217;ve modified.</li>
	<li>Upload the new files.</li>
	<li>Point your browser to <span class="file"><a href="wp-admin/upgrade.php">/wp-admin/upgrade.php</a>.</span></li>
</ol>

<h2>Migrating from other systems</h2>
<p>WordPress can <a href="https://codex.wordpress.org/Importing_Content">import from a number of systems</a>. First you need to get WordPress installed and working as described above, before using <a href="wp-admin/import.php" title="Import to WordPress">our import tools</a>.</p>

<h2>System Requirements</h2>
<ul>
	<li><a href="https://secure.php.net/">PHP</a> version <strong>5.6.20</strong> or higher.</li>
	<li><a href="https://www.mysql.com/">MySQL</a> version <strong>5.0</strong> or higher.</li>
</ul>

<h3>Recommendations</h3>
<ul>
	<li><a href="https://secure.php.net/">PHP</a> version <strong>7.3</strong> or higher.</li>
	<li><a href="https://www.mysql.com/">MySQL</a> version <strong>5.6</strong> or higher.</li>
	<li>The <a href="https://httpd.apache.org/docs/2.2/mod/mod_rewrite.html">mod_rewrite</a> Apache module.</li>
	<li><a href="https://wordpress.org/news/2016/12/moving-toward-ssl/">HTTPS</a> support.</li>
	<li>A link to <a href="https://wordpress.org/">wordpress.org</a> on your site.</li>
</ul>

<h2>Online Resources</h2>
<p>If you have any questions that aren&#8217;t addressed in this document, please take advantage of WordPress&#8217; numerous online resources:</p>
<dl>
	<dt><a href="https://codex.wordpress.org/">The WordPress Codex</a></dt>
		<dd>The Codex is the encyclopedia of all things WordPress. It is the most comprehensive source of information for WordPress available.</dd>
	<dt><a href="https://wordpress.org/news/">The WordPress Blog</a></dt>
		<dd>This is where you&#8217;ll find .... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.10.221.6/retro/readme.html'
```
---
Generated by [Nuclei v2.9.4](https://github.com/projectdiscovery/nuclei)