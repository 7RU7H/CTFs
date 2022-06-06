### Exposed sharepoint list (exposed-sharepoint-list) found on http://10.129.1.183
---
**Details**: **exposed-sharepoint-list**  matched at http://10.129.1.183

**Protocol**: HTTP

**Full URL**: http://10.129.1.183/_vti_bin/lists.asmx?WSDL

**Timestamp**: Mon Jun 6 16:47:56 +0100 BST 2022

**Template Information**

| Key | Value |
|---|---|
| Name | Exposed sharepoint list |
| Authors | elsfa7110 |
| Tags | config, exposure, sharepoint |
| Severity | low |

**Request**
```http
GET /_vti_bin/lists.asmx?WSDL HTTP/1.1
Host: 10.129.1.183
User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 72656
Cache-Control: private
Content-Type: text/xml; charset=utf-8
Date: Mon, 06 Jun 2022 15:47:55 GMT
Microsoftsharepointteamservices: 15.0.0.4420
Request-Id: 05be44a0-5cbd-f075-0000-0315860f90e6
Server: Microsoft-IIS/10.0
Spiislatency: 397
Sprequestduration: 1204
Sprequestguid: 05be44a0-5cbd-f075-0000-0315860f90e6
X-Aspnet-Version: 4.0.30319
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-Ms-Invokeapp: 1; RequireReadOnly
X-Powered-By: ASP.NET
X-Sharepointhealthscore: 6


<wsdl:definitions xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:tns="http://schemas.microsoft.com/sharepoint/soap/" xmlns:s1="http://microsoft.com/wsdl/types/" xmlns:s="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" targetNamespace="http://schemas.microsoft.com/sharepoint/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
  <wsdl:types>
    <s:schema elementFormDefault="qualified" targetNamespace="http://schemas.microsoft.com/sharepoint/soap/">
      <s:import namespace="http://www.w3.org/2001/XMLSchema" />
      <s:import namespace="http://microsoft.com/wsdl/types/" />
      <s:element name="GetList">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="listName" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="GetListResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="GetListResult">
              <s:complexType mixed="true">
                <s:sequence>
                  <s:any />
                </s:sequence>
              </s:complexType>
            </s:element>
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="GetListAndView">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="listName" type="s:string" />
            <s:element minOccurs="0" maxOccurs="1" name="viewName" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="GetListAndViewResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="GetListAndViewResult">
              <s:complexType mixed="true">
                <s:sequence>
                  <s:any />
                </s:sequence>
              </s:complexType>
            </s:element>
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="DeleteList">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="listName" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="DeleteListResponse">
        <s:complexType />
      </s:element>
      <s:element name="AddList">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="listName" type="s:string" />
            <s:element minOccurs="0" maxOccurs="1" name="description" type="s:string" />
            <s:element minOccurs="1" maxOccurs="1" name="templateID" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AddListResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="AddListResult">
              <s:complexType mixed="true">
                <s:sequence>
                  <s:any />
                </s:sequence>
              </s:complexType>
            </s:element>
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AddListFromFeature">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="listName" type="s:string" />
            <s:element minOccurs="0" maxOccurs="1" name="description" type="s:string" />
            <s:element minOccurs="1" maxOccurs="1" name="featureID" type="s1:guid" />
            <s:element minOccurs="1" maxOccurs="1" name="templateID" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AddListFromFeatureResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="AddListFromFeatureResult">
              <s:complexType mixed="true">
                <s:sequence>
                  <s:any />
                </s:sequence>
              </s:complexType>
            </s:element>
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="UpdateList">
        <s:complexType>
          <s:sequence>
            <s:element min.... Truncated ....
```

References: 
- https://hackerone.com/reports/761158
- https://hackerone.com/reports/300539

**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36' 'http://10.129.1.183/_vti_bin/lists.asmx?WSDL'
```
---
Generated by [Nuclei 2.7.1](https://github.com/projectdiscovery/nuclei)