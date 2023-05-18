### Apache Detection (apache-detect) found on http://10.200.121.12
---
**Details**: **apache-detect**  matched at http://10.200.121.12

**Protocol**: HTTP

**Full URL**: http://10.200.121.12

**Timestamp**: Thu May 11 19:59:00 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | Apache Detection |
| Authors | philippedelteil |
| Tags | tech, apache |
| Severity | info |
| Description | Some Apache servers have the version on the response header. The OpenSSL version can be also obtained |

**Request**
```http
GET / HTTP/1.1
Host: 10.200.121.12
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Type: text/html; charset=UTF-8
Date: Thu, 11 May 2023 18:58:59 GMT
Server: Apache/2.4.29 (Ubuntu)
Vary: Accept-Encoding

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>VPN Request Portal</title>
  </head>
  <style>
  .body {
    background-color: #FFF8D4;
  }
  .set-container {
    text-align: center;
    padding-left: 30%;
    padding-right: 30%;
    padding-top: 10%;
  }
  .box-container {
    text-align: center;
    border-style: solid;
  }
  .subtitle-align-right {
    text-align: left;
    padding-left: 3%;
  }
  .login-container {
    background-color: #F0F0F0;
    border-style: solid none none none;
    padding-top: 3%;
    padding-right: 1%;
    padding-left: 1%;
  }
  .padding {
    padding-bottom: 3%;
  }
  .form-inline {
  display: flex;
  flex-flow: row wrap;
  align-items: center;
}

.form-inline label {
  margin: 5px 10px 5px 0;
}

.form-inline input {
  vertical-align: middle;
  margin: 5px 10px 5px 0;
  padding: 10px;
  background-color: #fff;
  border: 1px solid #ddd;
}

.form-inline button {
  padding: 10px 20px;
  background-color: dodgerblue;
  border: 1px solid #ddd;
  color: white;
  cursor: pointer;
}

.form-inline button:hover {
  background-color: royalblue;
}
.submit-container {
  padding-bottom: 3%;
  padding-top: 3%;
}
H3 {
  font-family: "Courier New";
}
</style>
  <body>
    <div class='set-container'>
      <div class='box-container'>
        <img src='thereserve.png'></img>
        <H3>VPN Portal Login</H3>
        <div class='login-container'>
          <form class="form-inline" action="/login.php">
            <label for="email">User:</label>
              <input type="user" id="user" placeholder="Enter user" name="user">
            <label for="pwd">Password:</label>
              <input type="password" id="pwd" placeholder="Enter password" name="password">
              <br/>
              Note: Your internal account should be used.
              <br/>
            <div class='submit-container'>
              <label>
              <button type="submit">Submit</button>
                <input type="checkbox" name="remember"> Remember me
              </label>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>

```

**Extra Information**

**Extracted results**:

- Apache/2.4.29 (Ubuntu)



**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36' 'http://10.200.121.12'
```
---
Generated by [Nuclei v2.9.3](https://github.com/projectdiscovery/nuclei)