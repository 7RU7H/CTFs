### wadl file disclosure (wadl-api:http-get) found on http://192.168.141.98:8080
---
**Details**: **wadl-api:http-get**  matched at http://192.168.141.98:8080

**Protocol**: HTTP

**Full URL**: http://192.168.141.98:8080/application.wadl

**Timestamp**: Sun Sep 18 20:56:31 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | wadl file disclosure |
| Authors | 0xrudra, manuelbua |
| Tags | exposure, api |
| Severity | info |

**Request**
```http
GET /application.wadl HTTP/1.1
Host: 192.168.141.98:8080
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
Content-Length: 18901
Content-Type: application/vnd.sun.wadl+xml
Server: Jetty(1.0)

<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<application xmlns="http://wadl.dev.java.net/2009/02">
    <doc xmlns:jersey="http://jersey.java.net/" jersey:generatedBy="Jersey: 1.9.1 09/14/2011 02:36 PM"/>
    <grammars>
        <include href="application.wadl/xsd0.xsd">
            <doc title="Generated" xml:lang="en"/>
        </include>
    </grammars>
    <resources base="http://192.168.141.98:8080/">
        <resource path="exhibitor/v1/cluster">
            <resource path="state">
                <method id="getStatus" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="log">
                <method id="getLog" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="status">
                <method id="getClusterStatus" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="state/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <method id="remoteGetStatus" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="set/{type}/{value}/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="type" style="template" type="xs:string"/>
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="value" style="template" type="xs:boolean"/>
                <method id="remoteSetControlPanelSetting" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="restart/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <method id="remoteStopStartZooKeeper" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="start/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <method id="remoteStartZooKeeper" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="stop/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <method id="remoteStopZooKeeper" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="4ltr/{word}/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="word" style="template" type="xs:string"/>
                <method id="remoteGetFourLetterWord" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="log/{hostname}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="hostname" style="template" type="xs:string"/>
                <method id="remoteGetLog" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="4ltr/{word}">
                <param xmlns:xs="http://www.w3.org/2001/XMLSchema" name="word" style="template" type="xs:string"/>
                <method id="getFourLetterWord" name="GET">
                    <response>
                        <representation mediaType="application/json"/>
                    </response>
                </method>
            </resource>
            <resource path="restart">
                <method i.... Truncated ....
```

References: 
- https://github.com/dwisiswant0/wadl-dumper
- https://www.nopsec.com/leveraging-exposed-wadl-xml-in-burp-suite/

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2820.59 Safari/537.36' 'http://192.168.141.98:8080/application.wadl'
```
---
Generated by [Nuclei 2.7.7](https://github.com/projectdiscovery/nuclei)