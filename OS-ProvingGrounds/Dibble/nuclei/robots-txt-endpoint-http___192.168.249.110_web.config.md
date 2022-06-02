### robots.txt endpoint prober (robots-txt-endpoint) found on http://192.168.249.110
---
**Details**: **robots-txt-endpoint**  matched at http://192.168.249.110

**Protocol**: HTTP

**Full URL**: http://192.168.249.110/web.config

**Timestamp**: Thu Jun 2 07:52:57 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | robots.txt endpoint prober |
| Authors | caspergn, pdteam |
| Severity | info |

**Request**
```http
GET /web.config HTTP/1.1
Host: 192.168.249.110
User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4566
Accept-Ranges: bytes
Content-Type: text/xml
Date: Thu, 02 Jun 2022 06:52:56 GMT
Etag: "11d6-5af71434f77c0"
Last-Modified: Wed, 16 Sep 2020 17:04:39 GMT
Server: Apache/2.4.46 (Fedora)

<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <!-- Don't show directory listings for URLs which map to a directory. -->
    <directoryBrowse enabled="false" />

    <!--
       Caching configuration was not delegated by default. Some hosters may not
       delegate the caching configuration to site owners by default and that
       may cause errors when users install. Uncomment this if you want to and
       are allowed to enable caching.
     -->
    <!--
    <caching>
      <profiles>
        <add extension=".php" policy="DisableCache" kernelCachePolicy="DisableCache" />
        <add extension=".html" policy="CacheForTimePeriod" kernelCachePolicy="CacheForTimePeriod" duration="14:00:00" />
      </profiles>
    </caching>
     -->

    <rewrite>
      <rules>
        <rule name="Protect files and directories from prying eyes" stopProcessing="true">
          <match url="\.(engine|inc|install|module|profile|po|sh|.*sql|theme|twig|tpl(\.php)?|xtmpl|yml|svn-base)$|^(code-style\.pl|Entries.*|Repository|Root|Tag|Template|all-wcprops|entries|format|composer\.(json|lock)|\.htaccess)$" />
          <action type="CustomResponse" statusCode="403" subStatusCode="0" statusReason="Forbidden" statusDescription="Access is forbidden." />
        </rule>

        <rule name="Force simple error message for requests for non-existent favicon.ico" stopProcessing="true">
          <match url="favicon\.ico" />
          <action type="CustomResponse" statusCode="404" subStatusCode="1" statusReason="File Not Found" statusDescription="The requested file favicon.ico was not found" />
          <conditions>
            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
          </conditions>
        </rule>
     <!-- If running on a PHP version affected by httpoxy vulnerability
      uncomment the following rule to mitigate it's impact. To make this
      rule work, you will also need to add HTTP_PROXY to the allowed server
      variables manually in IIS. See https://www.drupal.org/node/2783079.
        <rule name="Erase HTTP_PROXY" patternSyntax="Wildcard">
          <match url="*.*" />
          <serverVariables>
            <set name="HTTP_PROXY" value="" />
          </serverVariables>
          <action type="None" />
        </rule>
    -->
    <!-- To redirect all users to access the site WITH the 'www.' prefix,
     http://example.com/foo will be redirected to http://www.example.com/foo)
     adapt and uncomment the following:   -->
    <!--
        <rule name="Redirect to add www" stopProcessing="true">
          <match url="^(.*)$" ignoreCase="false" />
          <conditions>
            <add input="{HTTP_HOST}" pattern="^example\.com$" />
          </conditions>
          <action type="Redirect" redirectType="Permanent" url="http://www.example.com/{R:1}" />
        </rule>
    -->

    <!-- To redirect all users to access the site WITHOUT the 'www.' prefix,
     http://www.example.com/foo will be redirected to http://example.com/foo)
     adapt and uncomment the following:   -->
    <!--
        <rule name="Redirect to remove www" stopProcessing="true">
          <match url="^(.*)$" ignoreCase="false" />
          <conditions>
            <add input="{HTTP_HOST}" pattern="^www\.example\.com$" />
          </conditions>
          <action type="Redirect" redirectType="Permanent" url="http://example.com/{R:1}" />
        </rule>
    -->

        <!-- Pass all requests not referring directly to files in the filesystem
         to index.php. -->
        <rule name="Short URLS" stopProcessing="true">
          <match url="^(.*)$" ignoreCase="false" />
          <conditions>
            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
            <add input="{URL}" pattern="^/favicon.ico$" ignoreCase="false" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php" />
        </rule>
      </rules>
    </rewrite>

  <!-- If running Windows Server 2008 R2 this can be commented out -->
    <!-- httpErrors>
      <remove statusCode="404" subStatusCode="-1" />
      <error statusCode="404" prefixLanguageFilePath="" path="/index.php" responseMode="ExecuteURL" />
    </httpErrors -->

    <defaultDocument>
     <!-- Set the default document -->
      <files>
         <clear />
        <add value="index.php" />
      </files>
    </defaultDocument>

  </system.webServer>
</configuration>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://192.168.249.110/web.config'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)