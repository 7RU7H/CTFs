### Detect Tomcat Exposed Scripts (tomcat-scripts) found on http://10.129.99.114:8080
---
**Details**: **tomcat-scripts**  matched at http://10.129.99.114:8080

**Protocol**: HTTP

**Full URL**: http://10.129.99.114:8080/examples/jsp/index.html

**Timestamp**: Tue May 31 09:11:35 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Detect Tomcat Exposed Scripts |
| Authors | co0nan |
| Tags | apache, tomcat |
| Severity | info |

**Request**
```http
GET /examples/jsp/index.html HTTP/1.1
Host: 10.129.99.114:8080
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 17695
Accept-Ranges: bytes
Content-Type: text/html
Date: Tue, 31 May 2022 15:11:34 GMT
Etag: W/"17695-1525691766000"
Last-Modified: Mon, 07 May 2018 11:16:06 GMT
Server: Apache-Coyote/1.1

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<!--
 Licensed to the Apache Software Foundation (ASF) under one or more
  contributor license agreements.  See the NOTICE file distributed with
  this work for additional information regarding copyright ownership.
  The ASF licenses this file to You under the Apache License, Version 2.0
  (the "License"); you may not use this file except in compliance with
  the License.  You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
-->
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <meta name="GENERATOR" content="Mozilla/4.61 [en] (WinNT; I) [Netscape]">
   <meta name="Author" content="Anil K. Vijendran">
   <title>JSP Examples</title>
</head>
<body bgcolor="#FFFFFF">
<b><font face="Arial, Helvetica, sans-serif"><font size=+2>JSP
Samples</font></font></b>
<p>This is a collection of samples demonstrating the usage of different
parts of the Java Server Pages (JSP) specification.  Both JSP 2.0 and
JSP 1.2 examples are presented below.
<p>These examples will only work when these pages are being served by a
servlet engine; of course, we recommend
<a href="http://tomcat.apache.org/">Tomcat</a>.
They will not work if you are viewing these pages via a
"file://..." URL.
<p>To navigate your way through the examples, the following icons will
help:
<br>&nbsp;
<table BORDER=0 CELLSPACING=5 WIDTH="85%" >
<tr VALIGN=TOP>
<td WIDTH="30"><img SRC="images/execute.gif" ></td>

<td>Execute the example</td>
</tr>

<tr VALIGN=TOP>
<td WIDTH="30"><img SRC="images/code.gif" height=24 width=24></td>

<td>Look at the source code for the example</td>
</tr>

<!--<tr VALIGN=TOP>
<td WIDTH="30"><img SRC="images/read.gif" height=24 width=24></td>

<td>Read more about this feature</td>
-->

</tr>
<tr VALIGN=TOP>
<td WIDTH="30"><img SRC="images/return.gif" height=24 width=24></td>

<td>Return to this screen</td>
</tr>
</table>

<p>Tip: For session scoped beans to work, the cookies must be enabled.
This can be done using browser options.
<br>&nbsp;
<br>
<b><u><font size="+1">JSP 2.0 Examples</font></u></b><br>

<table BORDER=0 CELLSPACING=5 WIDTH="85%" >
<tr valign=TOP>
<td><b>Expression Language</b></td>
</tr>

<tr valign=TOP>
<td>Basic Arithmetic</td>
<td valign=TOP width="30%"><a href="jsp2/el/basic-arithmetic.jsp"><img src="images/execute.gif" hspace=4 border=0  align=top></a><a href="jsp2/el/basic-arithmetic.jsp">Execute</a></td>

<td width="30%"><a href="jsp2/el/basic-arithmetic.html"><img SRC="images/code.gif" HSPACE=4 BORDER=0 height=24 width=24 align=TOP></a><a href="jsp2/el/basic-arithmetic.html">Source</a></td>
</tr>

<tr valign=TOP>
<td>Basic Comparisons</td>
<td valign=TOP width="30%"><a href="jsp2/el/basic-comparisons.jsp"><img src="images/execute.gif" hspace=4 border=0  align=top></a><a href="jsp2/el/basic-comparisons.jsp">Execute</a></td>

<td width="30%"><a href="jsp2/el/basic-comparisons.html"><img SRC="images/code.gif" HSPACE=4 BORDER=0 height=24 width=24 align=TOP></a><a href="jsp2/el/basic-comparisons.html">Source</a></td>
</tr>

<tr valign=TOP>
<td>Implicit Objects</td>
<td valign=TOP width="30%"><a href="jsp2/el/implicit-objects.jsp?foo=bar"><img src="images/execute.gif" hspace=4 border=0  align=top></a><a href="jsp2/el/implicit-objects.jsp?foo=bar">Execute</a></td>

<td width="30%"><a href="jsp2/el/implicit-objects.html"><img SRC="images/code.gif" HSPACE=4 BORDER=0 height=24 width=24 align=TOP></a><a href="jsp2/el/implicit-objects.html">Source</a></td>
</tr>
<tr valign=TOP>

<td>Functions</td>
<td valign=TOP width="30%"><a href="jsp2/el/functions.jsp?foo=JSP+2.0"><img src="images/execute.gif" hspace=4 border=0  align=top></a><a href="jsp2/el/functions.jsp?foo=JSP+2.0">Execute</a></td>

<td width="30%"><a href="jsp2/el/functions.html"><img SRC="images/code.gif" HSPACE=4 BORDER=0 height=24 width=24 align=TOP></a><a href="jsp2/el/functions.html">Source</a></td>
</tr>

<tr valign=TOP>
<td>Composite Expressions</td>
<td valign=TOP width="30%"><a href="jsp2/el/composite.jsp"><img src="images/execute.gif" hspace=4 border=0  align=top></a><a href="jsp2/el/composite.jsp">Execute</a></td>

<td width="30%"><a href="jsp2/el/composite.html"><img SRC="images/code.gif" HSPACE=4 BORDER=0 height=24 width=24 align=TOP></a><a href="jsp2/el/composite.html">Source</a></td>
</tr>


<tr valign=TOP>
<td><br><b>SimpleTag Handlers and JSP Fragments</b></td>.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1866.237 Safari/537.36' 'http://10.129.99.114:8080/examples/jsp/index.html'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)