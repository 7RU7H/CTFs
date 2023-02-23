### WAF Detection (waf-detect:apachegeneric) found on http://10.10.165.211
---
**Details**: **waf-detect:apachegeneric**  matched at http://10.10.165.211

**Protocol**: HTTP

**Full URL**: http://10.10.165.211/

**Timestamp**: Thu Feb 23 09:28:31 +0000 GMT 2023

**Template Information**

| Key | Value |
|---|---|
| Name | WAF Detection |
| Authors | dwisiswant0, lu4nx |
| Tags | waf, tech, misc |
| Severity | info |
| Description | A web application firewall was detected. |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
POST / HTTP/1.1
Host: 10.10.165.211
User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36
Connection: close
Content-Length: 27
Content-Type: application/x-www-form-urlencoded
Accept-Encoding: gzip

_=<script>alert(1)</script>
```

**Response**
```http
HTTP/1.1 302 Found
Connection: close
Content-Length: 7916
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Thu, 23 Feb 2023 09:28:32 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Location: /login
Pragma: no-cache
Server: Apache/2.4.29 (Ubuntu)
Set-Cookie: PHPSESSID=1lgo0bhgerskv9lvik03p29bqh; path=/


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WWBuddy</title>
</head>
<body>
  <!DOCTYPE html>
<html>
    <header>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <link rel="stylesheet" href="/styles/header.css">
        <link rel="stylesheet" href="/styles/profile.css">
    </header>
    <body>
        <div class="headerDiv">
            <a href="/">
                <img src="/images/wwbuddy.svg" class="headerImg"/>
                <h1 class="siteName">World Wide Buddy</h1>
            </a>
            <a class="logout" href="/logout.php">Logout</a>
        </div>
    </body>
</html>  <div class="content">
    <div class="profile">
      <img class="profilePic" src="images/profile.jpg">
      <p class="profileName"><a href="/profile?uid="></a></p>
    </div>
    <div class="info">
      <div class="aboutdiv">
        <h2>About me</h2>
        <a href="/change">Change Password</a>
      </div>
      <p><strong>Country:</strong></p>
      <p><strong>E-mail:</strong></p>
      <p><strong>Birthday:</strong></p>
      <p><strong>Description:</strong></p>
      <div class="error has-error">
        <span class="help-block">Username error: Username cant be empty.<br>E-mail error: You need to input a valid E-mail.<br>Country error: This country is not valid.<br>Birthday error: Date format not valid</span>
        <button class="editBtn" type="button" onclick="showPage()">Edit Info</button>
      </div>
    </div>
  </div>
  <div id="editPage">
    <form action="" class="edit" method="post">
      <h2>Edit your info</h2>
      <div class="item">
        <p><strong>Change username:</strong></p>
        <input class="input" type="text" name="username" value="">
      </div>
      <div class="item">
        <p><strong>Select country:</strong></p>
        <select class="input" id="country" name="country"></select>
      </div>
      <div class="item">
        <p><strong>Change E-mail:</strong></p>
        <input class="input" type="text" name="email" value="">
      </div>
      <div class="item">
        <p><strong>Change Birthday:</strong></p>
        <input class="input" type="date" name="birthday" value="">
      </div>
      <div class="item">
        <p><strong>Change Description:</strong></p>
        <textarea class="input" name="description"></textarea>
      </div>
      <div class="buttons">
        <button type="button" onclick="hidePage()">Cancel</button>
        <button type="submit">Save</button>
      </div>
    </form>
  </div>
  
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/styles/chat.css">
    </head>
    <body>
        <div class="chatwindow">
            <div class="chatheader">
            Chat Box
            </div>
            <div class="chatbox">
            <div class="chatpeople">
                <div id="people">
                </div>
            </div>
            <div class="messagebox">
                <div id="scroll">
                <ul id="messages">
                </ul>
                </div>
                <div id="sender" action="./chat.php" method="post">
                    <input id="sendto" type="hidden" name ="sendto" value =""/>
                    <input type="text" name="message" autocomplete="off" placeholder="Type a message">
                    <button id="btnSend">Send</button>
                </div>
            </div>
            </div>
        </div>
        <script>
            var users = [];
            var uid = null;
        </script>
        <script type="text/javascript" src="./js/chat.js"></script>
        <script>
                    </script>
    </body>
</html>  <footer>
    <div>
      Icons made by <a href="https://www.flaticon.com/free-icon/earth_2909523" title="Icongeek26">Icongeek26</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
    </div>
  <footer>  <script>
    var x = document.getElementById("country");
    var countries =  ["Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Bouvet Island","Brazil","British Antarctic Territory","British Indian Ocean Territory","British Virgin Islands","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Canton and Enderbury Islands","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos [Keeling] Islands","Colombia","Comoros","Congo - Brazzaville","Congo - Kinshasa","Cook Is.... Truncated ....
```

References: 
- https://github.com/ekultek/whatwaf

**CURL Command**
```
curl -X 'POST' -d '_=<script>alert(1)</script>' -H 'Content-Type: application/x-www-form-urlencoded' -H 'Host: 10.10.165.211' -H 'User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36' 'http://10.10.165.211/'
```
---
Generated by [Nuclei 2.7.9](https://github.com/projectdiscovery/nuclei)