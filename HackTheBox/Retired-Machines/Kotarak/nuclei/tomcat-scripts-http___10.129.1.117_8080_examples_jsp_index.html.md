### Detect Tomcat Exposed Scripts (tomcat-scripts) found on http://10.129.1.117:8080
---
**Details**: **tomcat-scripts**  matched at http://10.129.1.117:8080

**Protocol**: HTTP

**Full URL**: http://10.129.1.117:8080/examples/jsp/index.html

**Timestamp**: Thu Feb 2 12:22:58 +0000 GMT 2023

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
Host: 10.129.1.117:8080
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 
Connection: close
Content-Length: 14326
Accept-Ranges: bytes
Content-Type: text/html
Date: Thu, 02 Feb 2023 12:22:58 GMT
Etag: W/"14326-1472673233000"
Last-Modified: Wed, 31 Aug 2016 19:53:53 GMT

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
<!DOCTYPE html><html lang="en">
<head>
   <meta charset="UTF-8"/>
   <meta name="Author" content="Anil K. Vijendran" />
   <title>JSP Examples</title>
   <style type="text/css">
   img { border: 0; }
   th { text-align: left; }
   tr { vertical-align: top; }
   </style>
</head>
<body>
<h1>JSP
Samples</h1>
<p>This is a collection of samples demonstrating the usage of different
parts of the Java Server Pages (JSP) specification.  Both JSP 2.0 and
JSP 1.2 examples are presented below.
<p>These examples will only work when these pages are being served by a
servlet engine; of course, we recommend
<a href="http://tomcat.apache.org/">Tomcat</a>.
They will not work if you are viewing these pages via a
"file://..." URL.
<p>To navigate your way through the examples, the following icons will
help:</p>
<ul style="list-style-type: none; padding-left: 0;">
<li><img src="images/execute.gif" alt=""> Execute the example</li>
<li><img src="images/code.gif" alt=""> Look at the source code for the example</li>
<!-- <li><img src="images/read.gif" alt=""> Read more about this feature</li> -->
<li><img src="images/return.gif" alt=""> Return to this screen</li>
</ul>

<p>Tip: For session scoped beans to work, the cookies must be enabled.
This can be done using browser options.</p>
<h2>JSP 2.0 Examples</h2>

<table style="width: 85%;">
<tr>
<th colspan="3">Expression Language</th>
</tr>

<tr>
<td>Basic Arithmetic</td>
<td style="width: 30%;"><a href="jsp2/el/basic-arithmetic.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/el/basic-arithmetic.jsp">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/el/basic-arithmetic.html"><img src="images/code.gif" alt=""></a><a href="jsp2/el/basic-arithmetic.html">Source</a></td>
</tr>

<tr>
<td>Basic Comparisons</td>
<td style="width: 30%;"><a href="jsp2/el/basic-comparisons.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/el/basic-comparisons.jsp">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/el/basic-comparisons.html"><img src="images/code.gif" alt=""></a><a href="jsp2/el/basic-comparisons.html">Source</a></td>
</tr>

<tr>
<td>Implicit Objects</td>
<td style="width: 30%;"><a href="jsp2/el/implicit-objects.jsp?foo=bar"><img src="images/execute.gif" alt=""></a><a href="jsp2/el/implicit-objects.jsp?foo=bar">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/el/implicit-objects.html"><img src="images/code.gif" alt=""></a><a href="jsp2/el/implicit-objects.html">Source</a></td>
</tr>
<tr>

<td>Functions</td>
<td style="width: 30%;"><a href="jsp2/el/functions.jsp?foo=JSP+2.0"><img src="images/execute.gif" alt=""></a><a href="jsp2/el/functions.jsp?foo=JSP+2.0">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/el/functions.html"><img src="images/code.gif" alt=""></a><a href="jsp2/el/functions.html">Source</a></td>
</tr>

<tr>
<td>Composite Expressions</td>
<td style="width: 30%;"><a href="jsp2/el/composite.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/el/composite.jsp">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/el/composite.html"><img src="images/code.gif" alt=""></a><a href="jsp2/el/composite.html">Source</a></td>
</tr>


<tr>
<th colspan="3"><br />SimpleTag Handlers and JSP Fragments</th>
</tr>

<tr>
<td>Hello World Tag</td>
<td style="width: 30%;"><a href="jsp2/simpletag/hello.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/simpletag/hello.jsp">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/simpletag/hello.html"><img src="images/code.gif" alt=""></a><a href="jsp2/simpletag/hello.html">Source</a></td>
</tr>

<tr>
<td>Repeat Tag</td>
<td style="width: 30%;"><a href="jsp2/simpletag/repeat.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/simpletag/repeat.jsp">Execute</a></td>

<td style="width: 30%;"><a href="jsp2/simpletag/repeat.html"><img src="images/code.gif" alt=""></a><a href="jsp2/simpletag/repeat.html">Source</a></td>
</tr>

<tr>
<td>Book Example</td>
<td style="width: 30%;"><a href="jsp2/simpletag/book.jsp"><img src="images/execute.gif" alt=""></a><a href="jsp2/simpletag/book.jsp">Execute</a></td>

<td style="width: 30%.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.129.1.117:8080/examples/jsp/index.html'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)