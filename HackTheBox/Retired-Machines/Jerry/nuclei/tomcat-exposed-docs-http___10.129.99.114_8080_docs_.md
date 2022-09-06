### Tomcat exposed docs (tomcat-exposed-docs) found on http://10.129.99.114:8080
---
**Details**: **tomcat-exposed-docs**  matched at http://10.129.99.114:8080

**Protocol**: HTTP

**Full URL**: http://10.129.99.114:8080/docs/

**Timestamp**: Tue May 31 09:11:57 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Tomcat exposed docs |
| Authors | podalirius |
| Tags | version, tomcat, docs |
| Severity | info |

**Request**
```http
GET /docs/ HTTP/1.1
Host: 10.129.99.114:8080
User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 19677
Accept-Ranges: bytes
Content-Type: text/html
Date: Tue, 31 May 2022 15:11:56 GMT
Etag: W/"19677-1525691772000"
Last-Modified: Mon, 07 May 2018 11:16:12 GMT
Server: Apache-Coyote/1.1

<html><head><META http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><title>Apache Tomcat 7 (7.0.88) - Documentation Index</title><meta name="author" content="Craig R. McClanahan"><meta name="author" content="Remy Maucherat"><meta name="author" content="Yoav Shapira"><style type="text/css" media="print">
    .noPrint {display: none;}
    td#mainBody {width: 100%;}
</style><style type="text/css">
code {background-color:rgb(224,255,255);padding:0 0.1em;}
code.attributeName, code.propertyName {background-color:transparent;}


table {
  border-collapse: collapse;
  text-align: left;
}
table *:not(table) {
  /* Prevent border-collapsing for table child elements like <div> */
  border-collapse: separate;
}

th {
  text-align: left;
}


div.codeBox pre code, code.attributeName, code.propertyName, code.noHighlight, .noHighlight code {
  background-color: transparent;
}
div.codeBox {
  overflow: auto;
  margin: 1em 0;
}
div.codeBox pre {
  margin: 0;
  padding: 4px;
  border: 1px solid #999;
  border-radius: 5px;
  background-color: #eff8ff;
  display: table; /* To prevent <pre>s from taking the complete available width. */
  /*
  When it is officially supported, use the following CSS instead of display: table
  to prevent big <pre>s from exceeding the browser window:
  max-width: available;
  width: min-content;
  */
}

div.codeBox pre.wrap {
  white-space: pre-wrap;
}


table.defaultTable tr, table.detail-table tr {
    border: 1px solid #CCC;
}

table.defaultTable tr:nth-child(even), table.detail-table tr:nth-child(even) {
    background-color: #FAFBFF;
}

table.defaultTable tr:nth-child(odd), table.detail-table tr:nth-child(odd) {
    background-color: #EEEFFF;
}

table.defaultTable th, table.detail-table th {
  background-color: #88b;
  color: #fff;
}

table.defaultTable th, table.defaultTable td, table.detail-table th, table.detail-table td {
  padding: 5px 8px;
}


p.notice {
    border: 1px solid rgb(255, 0, 0);
    background-color: rgb(238, 238, 238);
    color: rgb(0, 51, 102);
    padding: 0.5em;
    margin: 1em 2em 1em 1em;
}
</style></head><body bgcolor="#ffffff" text="#000000" link="#525D76" alink="#525D76" vlink="#525D76"><table border="0" width="100%" cellspacing="0"><!--PAGE HEADER--><tr><td><!--PROJECT LOGO--><a href="http://tomcat.apache.org/"><img src="./images/tomcat.gif" align="right" alt="
      The Apache Tomcat Servlet/JSP Container
    " border="0"></a></td><td><h1><font face="arial,helvetica,sanserif">Apache Tomcat 7</font></h1><font face="arial,helvetica,sanserif">Version 7.0.88, May 7 2018</font></td><td><!--APACHE LOGO--><a href="http://www.apache.org/"><img src="./images/asf-logo.svg" align="right" alt="Apache Logo" border="0" style="width: 266px;height: 83px;"></a></td></tr></table><table border="0" width="100%" cellspacing="4"><!--HEADER SEPARATOR--><tr><td colspan="2"><hr noshade size="1"></td></tr><tr><!--LEFT SIDE NAVIGATION--><td width="20%" valign="top" nowrap class="noPrint"><p><strong>Links</strong></p><ul><li><a href="index.html">Docs Home</a></li><li><a href="http://wiki.apache.org/tomcat/FAQ">FAQ</a></li><li><a href="#comments_section">User Comments</a></li></ul><p><strong>User Guide</strong></p><ul><li><a href="introduction.html">1) Introduction</a></li><li><a href="setup.html">2) Setup</a></li><li><a href="appdev/index.html">3) First webapp</a></li><li><a href="deployer-howto.html">4) Deployer</a></li><li><a href="manager-howto.html">5) Manager</a></li><li><a href="host-manager-howto.html">6) Host Manager</a></li><li><a href="realm-howto.html">7) Realms and AAA</a></li><li><a href="security-manager-howto.html">8) Security Manager</a></li><li><a href="jndi-resources-howto.html">9) JNDI Resources</a></li><li><a href="jndi-datasource-examples-howto.html">10) JDBC DataSources</a></li><li><a href="class-loader-howto.html">11) Classloading</a></li><li><a href="jasper-howto.html">12) JSPs</a></li><li><a href="ssl-howto.html">13) SSL/TLS</a></li><li><a href="ssi-howto.html">14) SSI</a></li><li><a href="cgi-howto.html">15) CGI</a></li><li><a href="proxy-howto.html">16) Proxy Support</a></li><li><a href="mbeans-descriptors-howto.html">17) MBeans Descriptors</a></li><li><a href="default-servlet.html">18) Default Servlet</a></li><li><a href="cluster-howto.html">19) Clustering</a></li><li><a href="balancer-howto.html">20) Load Balancer</a></li><li><a href="connectors.html">21) Connectors</a></li><li><a href="monitoring.html">22) Monitoring and Management</a></li><li><a href="logging.html">23) Logging</a></li><li><a href="apr.html">24) APR/Native</a></li><li><a href="virtual-hosting-howto.html">25) Virtual Hosting</a></li><li><a href="aio.html">26) Advanced IO</a></li><li><a href="extras.html">27) Additional Components</a></li><li><a href="maven-jars.html">28) Mave.... Truncated ....
```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/4E423F' 'http://10.129.99.114:8080/docs/'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)