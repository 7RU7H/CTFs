### HTTP Missing Security Headers (http-missing-security-headers:access-control-expose-headers) found on http://10.129.127.63
---
**Details**: **http-missing-security-headers:access-control-expose-headers**  matched at http://10.129.127.63

**Protocol**: HTTP

**Full URL**: https://10.129.127.63/

**Timestamp**: Fri Sep 2 08:16:58 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.
 |

**Request**
```http
GET / HTTP/1.1
Host: 10.129.127.63
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Transfer-Encoding: chunked
Cache-Control: max-age=180000
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Content-Type: text/html
Date: Fri, 02 Sep 2022 07:17:10 GMT
Expires: Sun, 04 Sep 2022 09:17:10 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Last-Modified: Fri, 02 Sep 2022 07:17:10 GMT
Pragma: no-cache
Server: lighttpd/1.4.35
Set-Cookie: PHPSESSID=d8070c2659e6eab08623216a0af7c333; path=/; secure; HttpOnly
Set-Cookie: cookie_test=1662106630
X-Frame-Options: SAMEORIGIN


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<script type="text/javascript" src="/javascript/jquery.js"></script>
		<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function() { jQuery('#usernamefld').focus(); });
		//]]>
		</script>

		<title>Login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" href="/themes/pfsense_ng/images/icons/favicon.ico" />
				<link rel="stylesheet" type="text/css" href="/themes/pfsense_ng/login.css" media="all" />
				<script type="text/javascript">
		//<![CDATA[
			function page_load() {}
			function clearError() {
				if($('#inputerrors'))
				$('#inputerrors').html('');
			}
			
    var input_errors = '';
    jQuery(document).ready(init);
  
    var noAjaxOnSubmit = false;

    function init() {
      if(jQuery('#submit') && ! noAjaxOnSubmit) {
        // debugging helper
        //alert('adding observe event for submit button');
        
        jQuery("#submit").click(submit_form);
        jQuery('#submit').click(function() {return false;});
        var to_insert = "<div style='visibility:hidden' id='loading' name='loading'><img src='/themes/pfsense_ng/images/misc/loader.gif' alt='loader' \/><\/div>";
        jQuery('#submit').before(to_insert);
      }
    }
    
    function submit_form(e){
      // debugging helper
      //alert(Form.serialize($('iform')));

      if(jQuery('#inputerrors'))
        jQuery('#inputerrors').html('<center><b><i>Loading...<\/i><\/b><\/center>');
        
      /* dsh: Introduced because pkg_edit tries to set some hidden fields
       *      if executing submit's onclick event. The click gets deleted
       *      by Ajax. Hence using onkeydown instead.
       */
      if(jQuery('#submit').prop('keydown')) {
        jQuery('#submit').keydown();
        jQuery('#submit').css('visibility','hidden');
      }
      if(jQuery('#cancelbutton'))
        jQuery('#cancelbutton').css('visibility','hidden');
      jQuery('#loading').css('visibility','visible');
      // submit the form using Ajax
    }
   
    function formSubmitted(resp) {
      var responseText = resp.responseText;
      
      // debugging helper
      // alert(responseText);
      
      if(responseText.indexOf('html') > 0) {
        /* somehow we have been fed an html page! */
        //alert('Somehow we have been fed an html page! Forwarding to /.');
        document.location.href = '/';
      }
      
      eval(responseText);
    }
    
    /* this function will be called if an HTTP error will be triggered */
    function formFailure(resp) {
	    showajaxmessage(resp.responseText);
		if(jQuery('#submit'))
		  jQuery('#submit').css('visibility','visible');
		if(jQuery('#cancelbutton'))
		  jQuery('#cancelbutton').css('visibility','visible');
		if(jQuery('#loading'))
		  jQuery('#loading').css('visibility','hidden');

    }
    
    function showajaxmessage(message) {
      var message_html;

      if (message == '') {
        NiftyCheck();
        Rounded("div#redbox","all","#FFF","#990000","smooth");
        Rounded("td#blackbox","all","#FFF","#000000","smooth");

        if(jQuery('#submit'))
          jQuery('#submit').css('visibility','visible');
        if(jQuery('#cancelbutton'))
          jQuery('#cancelbutton').css('visibility','visible');
        if(jQuery('#loading'))
          jQuery('#loading').css('visibility','hidden');

        return;
      }

      message_html = '<table height="32" width="100%" summary="redbox"><tr><td>';
      message_html += '<div style="background-color:#990000" id="redbox">';
      message_html += '<table width="100%" summary="message"><tr><td width="8%">';
      message_html += '<img style="vertical-align:center" src="/themes/pfsense_ng/images/icons/icon_exclam.gif" width="28" height="32" alt="exclamation" \/>';
      message_html += '<\/td><td width="70%"><font color="white">';
      message_html += '<b>' + message + '<\/b><\/font><\/td>';

      if(message.indexOf('apply') > 0) {
        message_html += '<td>';
        message_html += '<input name="apply" type="submit" class="formbtn" id="apply" value="Apply changes" \/>';
        message_html += '<\/td>';
      }

      message_html += '<\/tr><\/table><\/div><\/td><\/table><br \/>';
      jQuery('#inputerrors').html(message_html);

      NiftyCheck();
      Rounded("div#redbox","all","#FFF","#990000","smooth");
      Rounded("td#blackbox","all",".... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'Referer: http://10.129.127.63' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36' 'https://10.129.127.63/'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)