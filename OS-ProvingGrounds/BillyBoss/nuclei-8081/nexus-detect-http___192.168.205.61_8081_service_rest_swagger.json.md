### Nexus Repository Manager (NRM) Instance Detection Template (nexus-detect) found on http://192.168.205.61:8081/

----
**Details**: **nexus-detect** matched at http://192.168.205.61:8081/

**Protocol**: HTTP

**Full URL**: http://192.168.205.61:8081/service/rest/swagger.json

**Timestamp**: Thu Oct 12 11:38:31 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Nexus Repository Manager (NRM) Instance Detection Template |
| Authors | righettod |
| Tags | tech, nexus |
| Severity | info |
| Description | Try to detect the presence of a NRM instance via the REST API OpenDocument descriptor.<br> |
| shodan-query | http.html:"Nexus Repository Manager" |

**Request**
```http
GET /service/rest/swagger.json HTTP/1.1
Host: 192.168.205.61:8081
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: application/json
Date: Thu, 12 Oct 2023 10:38:08 GMT
Server: Nexus/3.21.0-05 (OSS)
X-Content-Type-Options: nosniff

{
  "swagger" : "2.0",
  "info" : {
    "version" : "3.21.0-05",
    "title" : "Nexus Repository Manager REST API"
  },
  "basePath" : "/service/rest/",
  "tags" : [ {
    "name" : "Security Management"
  }, {
    "name" : "Security Management: Users"
  }, {
    "name" : "Security Management: Privileges"
  }, {
    "name" : "Security Management: Realms"
  }, {
    "name" : "Security Management: Roles"
  }, {
    "name" : "tasks"
  }, {
    "name" : "Blob Store"
  }, {
    "name" : "lifecycle"
  }, {
    "name" : "read-only"
  }, {
    "name" : "Security: Certificates"
  }, {
    "name" : "Repository Management"
  }, {
    "name" : "assets"
  }, {
    "name" : "components"
  }, {
    "name" : "content-selectors"
  }, {
    "name" : "repositories"
  }, {
    "name" : "routing-rules"
  }, {
    "name" : "search"
  }, {
    "name" : "formats"
  }, {
    "name" : "script"
  }, {
    "name" : "email"
  }, {
    "name" : "status"
  }, {
    "name" : "support"
  }, {
    "name" : "Security Management: LDAP"
  }, {
    "name" : "Product Licensing"
  }, {
    "name" : "Manage IQ Server configuration"
  } ],
  "paths" : {
    "/beta/security/user-sources" : {
      "get" : {
        "tags" : [ "Security Management" ],
        "summary" : "Retrieve a list of the available user sources.",
        "description" : "",
        "operationId" : "getUserSources",
        "produces" : [ "application/json" ],
        "parameters" : [ ],
        "responses" : {
          "200" : {
            "description" : "successful operation",
            "schema" : {
              "type" : "array",
              "items" : {
                "$ref" : "#/definitions/ApiUserSource"
              }
            }
          },
          "403" : {
            "description" : "The user does not have permission to perform the operation."
          }
        }
      }
    },
    "/beta/security/users/{userId}" : {
      "put" : {
        "tags" : [ "Security Management: Users" ],
        "summary" : "Update an existing user.",
        "description" : "",
        "operationId" : "updateUser",
        "consumes" : [ "application/json" ],
        "produces" : [ "application/json" ],
        "parameters" : [ {
          "name" : "userId",
          "in" : "path",
          "description" : "The userid the request should apply to.",
          "required" : true,
          "type" : "string"
        }, {
          "in" : "body",
          "name" : "body",
          "description" : "A representation of the user to update.",
          "required" : false,
          "schema" : {
            "$ref" : "#/definitions/ApiUser"
          }
        } ],
        "responses" : {
          "400" : {
            "description" : "Password was not supplied in the body of the request"
          },
          "403" : {
            "description" : "The user does not have permission to perform the operation."
          },
          "404" : {
            "description" : "User or user source not found in the system."
          }
        }
      },
      "delete" : {
        "tags" : [ "Security Management: Users" ],
        "summary" : "Delete a user.",
        "description" : "",
        "operationId" : "deleteUser",
        "consumes" : [ "application/json" ],
        "produces" : [ "application/json" ],
        "parameters" : [ {
          "name" : "userId",
          "in" : "path",
          "description" : "The userid the request should apply to.",
          "required" : true,
          "type" : "string"
        } ],
        "responses" : {
          "400" : {
            "description" : "Password was not supplied in the body of the request"
          },
          "403" : {
            "description" : "The user does not have permission to perform the operation."
          },
          "404" : {
            "description" : "User or user source not found in the system."
          }
        }
      }
    },
    "/beta/security/users/{userId}/change-password" : {
      "put" : {
        "tags" : [ "Security Management: Users" ],
        "summary" : "Change a user's password.",
        "description" : "",
        "operationId" : "changePassword",
        "consumes" : [ "text/plain" ],
        "produces" : [ "application/json" ],
        "parameters" : [ {
          "name" : "userId",
          "in" : "path",
          "description" : "The userid the request should apply to.",
          "required" : true,
          "type" : "string"
        }, {
          "in" : "body",
          "name" : "body",
          "description" : "The new password to use.",
          "required" : false,
          "schema" : {
            "type" : "string"
          }
        } ],
        "responses" : {
          "400" : {
            "description" : "Password was not supplied in the body o.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36' 'http://192.168.205.61:8081/service/rest/swagger.json'
```

----

Generated by [Nuclei v2.9.11](https://github.com/projectdiscovery/nuclei)