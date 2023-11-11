### Wappalyzer Technology Detection (tech-detect:php) found on http://192.168.217.41/test/

----
**Details**: **tech-detect:php** matched at http://192.168.217.41/test/

**Protocol**: HTTP

**Full URL**: http://192.168.217.41/test/

**Timestamp**: Sat Nov 11 19:44:33 +0000 GMT 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Wappalyzer Technology Detection |
| Authors | hakluke |
| Tags | tech |
| Severity | info |

**Request**
```http
GET /test/ HTTP/1.1
Host: 192.168.217.41
User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.0 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Sun, 12 Nov 2023 00:44:15 GMT
Last-Modified: Sun, 12 Nov 2023 00:44:15 GMT
Server: Apache/2.2.14 (Ubuntu)
Set-Cookie: zenphoto_auth=deleted; expires=Sat, 12-Nov-2022 00:44:14 GMT; path=/test
Set-Cookie: zenphoto_ssl=deleted; expires=Sat, 12-Nov-2022 00:44:14 GMT; path=/test
Status: 200 OK
Vary: Accept-Encoding
X-Powered-By: PHP/5.3.2-1ubuntu4.10

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<script type="text/javascript" src="/test/zp-core/js/jquery.js"></script>
	<script type="text/javascript" src="/test/zp-core/js/zenphoto.js"></script>
	<script type="text/javascript">
		// <!-- <![CDATA[
		var deleteAlbum1 = "Are you sure you want to delete this entire album?";
		var deleteAlbum2 = "Are you Absolutely Positively sure you want to delete the album? THIS CANNOT BE UNDONE!";
		var deleteImage = "Are you sure you want to delete the image? THIS CANNOT BE UNDONE!";
		var deleteArticle = "Are you sure you want to delete this article? THIS CANNOT BE UNDONE!";
		var deletePage = "Are you sure you want to delete this page? THIS CANNOT BE UNDONE!";
		// ]]> -->
		</script>
			<title>Gallery</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="/test/themes/default/styles/light.css" type="text/css" />
	<link rel="alternate" type="application/rss+xml" title="Gallery RSS" href="http://192.168.217.41/test/index.php?rss&amp;lang=en_US" />
</head>

<body>

<div id="main">

	<div id="gallerytitle">
				<script type="text/javascript" src="/test/zp-core/js/admin.js"></script>
			<div id="search"><!-- search form -->

		<form method="post" action="/test/index.php?p=search" id="search_form">
			<script type="text/javascript">
				// <!-- <![CDATA[
				function reset_search() {
					lastsearch='';
					$('#reset_search').hide();
					$('#search_submit').attr('title', 'Search');
					$('#search_input').val('');
				}
				var lastsearch = '';
				var savedlastsearch = lastsearch;
				$('#search_form').submit(function(){
					if (lastsearch) {
						var newsearch = $.trim($('#search_input').val());
						if (newsearch.substring(newsearch.length - 1)==',') {
							newsearch = newsearch.substr(0,newsearch.length-1);
						}
						if (newsearch.length > 0) {
							$('#search_input').val('('+lastsearch+') AND ('+newsearch+')');
						} else {
							$('#search_input').val(lastsearch);
						}
					}
					return true;
				});
				// ]]> -->
			</script>
						<input type="text" name="words" value="" id="search_input" size="10" />
							<a href="javascript:toggle('searchextrashow');" ><img src="/test/zp-core/images/searchfields_icon.png" title="select search fields" alt="fields" id="searchfields_icon" /></a>
						<span style="white-space:nowrap;display:none" id="reset_search"><a href="javascript:reset_search();" title="Clear search"><img src="/test/zp-core/images/reset_icon.png" alt="Reset search" /></a></span>
			<input type="submit" value="Search" title="Search" class="pushbutton" id="search_submit"  />
						<br />
							<ul style="display:none;" id="searchextrashow">
				<li><label><input id="SEARCH_city" name="SEARCH_city" type="checkbox" checked="checked"  value="city"  /> City</label></li>
<li><label><input id="SEARCH_country" name="SEARCH_country" type="checkbox" checked="checked"  value="country"  /> Country</label></li>
<li><label><input id="SEARCH_desc" name="SEARCH_desc" type="checkbox" checked="checked"  value="desc"  /> Description</label></li>
<li><label><input id="SEARCH_location" name="SEARCH_location" type="checkbox" checked="checked"  value="location"  /> Location/Place</label></li>
<li><label><input id="SEARCH_state" name="SEARCH_state" type="checkbox" checked="checked"  value="state"  /> State</label></li>
<li><label><input id="SEARCH_tags" name="SEARCH_tags" type="checkbox" checked="checked"  value="tags"  /> Tags</label></li>
<li><label><input id="SEARCH_title" name="SEARCH_title" type="checkbox" checked="checked"  value="title"  /> Title</label></li>
				</ul>
						</form>
	</div><!-- end of search form -->
			<h2>Gallery</h2>
	</div>

		<div id="padbox">
		You can insert your Gallery description on the Admin Options Gallery tab.		<div id="albums">
					</div>
		<br clear="all" />
		<div class="pagelist disabled_nav">
<ul class="pagelist disabled_nav">
<li class="prev"><span class="disabledlink">&laquo; prev</span></li>
<li class="current">1</li>
<li class="next"><span class="disabledlink">next &raquo;</span></li>
</ul>
</div>
	</div>

</div>

<div id="credit">
<a  href="/test/index.php?rss&amp;lang=en_US" title="Latest images RSS" rel="nofollow">RSS <img src="http://192.168.217.41/test/zp-core/images/rss.png" alt="RSS Feed" /></a> | <a href="/test/index.php?p=archive"  title="Archive View">Archive View</a> |


Powered by <a href="http://www.zenphoto.org" title="A simpler web album"><span id="zen-part">zen</span><span id="photo-part">PHOTO</span></a></di.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36' 'http://192.168.217.41/test/'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)