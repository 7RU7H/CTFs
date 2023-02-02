### Tomcat Manager Path Normalization (tomcat-manager-pathnormalization) found on http://10.129.1.117:8080
---
**Details**: **tomcat-manager-pathnormalization**  matched at http://10.129.1.117:8080

**Protocol**: HTTP

**Full URL**: http://10.129.1.117:8080/2LBNWe94DQni2OEQVy4l2CW5GmL/..;/manager/html

**Timestamp**: Thu Feb 2 12:23:01 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Tomcat Manager Path Normalization |
| Authors | brenocss, organiccrap |
| Tags | panel, tomcat, apache |
| Severity | info |
| Description | A Tomcat Manager login panel was discovered via path normalization. Normalizing a path involves modifying the string that identifies a path or file so that it conforms to a valid path on the target operating system. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
GET /2LBNWe94DQni2OEQVy4l2CW5GmL/..;/manager/html HTTP/1.1
Host: 10.129.1.117:8080
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 401 
Connection: close
Content-Length: 2473
Cache-Control: private
Content-Type: text/html;charset=ISO-8859-1
Date: Thu, 02 Feb 2023 12:23:01 GMT
Expires: Wed, 31 Dec 1969 19:00:00 EST
Www-Authenticate: Basic realm="Tomcat Manager Application"

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
  <title>401 Unauthorized</title>
  <style type="text/css">
    <!--
    BODY {font-family:Tahoma,Arial,sans-serif;color:black;background-color:white;font-size:12px;}
    H1 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:22px;}
    PRE, TT {border: 1px dotted #525D76}
    A {color : black;}A.name {color : black;}
    -->
  </style>
 </head>
 <body>
   <h1>401 Unauthorized</h1>
   <p>
    You are not authorized to view this page. If you have not changed
    any configuration files, please examine the file
    <tt>conf/tomcat-users.xml</tt> in your installation. That
    file must contain the credentials to let you use this webapp.
   </p>
   <p>
    For example, to add the <tt>manager-gui</tt> role to a user named
    <tt>tomcat</tt> with a password of <tt>s3cret</tt>, add the following to the
    config file listed above.
   </p>
<pre>
&lt;role rolename="manager-gui"/&gt;
&lt;user username="tomcat" password="s3cret" roles="manager-gui"/&gt;
</pre>
   <p>
    Note that for Tomcat 7 onwards, the roles required to use the manager
    application were changed from the single <tt>manager</tt> role to the
    following four roles. You will need to assign the role(s) required for
    the functionality you wish to access.
   </p>
    <ul>
      <li><tt>manager-gui</tt> - allows access to the HTML GUI and the status
          pages</li>
      <li><tt>manager-script</tt> - allows access to the text interface and the
          status pages</li>
      <li><tt>manager-jmx</tt> - allows access to the JMX proxy and the status
          pages</li>
      <li><tt>manager-status</tt> - allows access to the status pages only</li>
    </ul>
   <p>
    The HTML interface is protected against CSRF but the text and JMX interfaces
    are not. To maintain the CSRF protection:
   </p>
   <ul>
    <li>Users with the <tt>manager-gui</tt> role should not be granted either
        the <tt>manager-script</tt> or <tt>manager-jmx</tt> roles.</li>
    <li>If the text or jmx interfaces are accessed through a browser (e.g. for
        testing since these interfaces are intended for tools not humans) then
        the browser must be closed afterwards to terminate the session.</li>
   </ul>
   <p>
    For more information - please see the
    <a href="/docs/manager-howto.html">Manager App HOW-TO</a>.
   </p>
 </body>

</html>

```

References: 
- https://i.blackhat.com/us-18/wed-august-8/us-18-orange-tsai-breaking-parser-logic-take-your-path-normalization-off-and-pop-0days-out-2.pdf

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36' 'http://10.129.1.117:8080/2LBNWe94DQni2OEQVy4l2CW5GmL/..;/manager/html'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)