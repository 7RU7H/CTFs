### HTTP Missing Security Headers (http-missing-security-headers:x-permitted-cross-domain-policies) found on mailing.htb

----
**Details**: **http-missing-security-headers:x-permitted-cross-domain-policies** matched at mailing.htb

**Protocol**: HTTP

**Full URL**: http://mailing.htb

**Timestamp**: Sun May 5 17:41:54 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass, jub0bs |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: mailing.htb
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/9.1.2 Safari/605.1.15
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4681
Content-Type: text/html; charset=UTF-8
Date: Sun, 05 May 2024 16:41:54 GMT
Server: Microsoft-IIS/10.0
X-Powered-By: PHP/8.3.3
X-Powered-By: ASP.NET

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailing</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('assets/background_image.jpg'); /* Path to your background image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        header {
            background-color: rgba(0, 0, 0, 0.6); /* Add semi-transparent background to header */
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Add semi-transparent background to container */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .about-section {
            margin-bottom: 40px;
        }
        .team-section {
            text-align: center;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .team-member {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .team-member h3 {
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        .team-member p {
            margin: 0;
            color: #666;
        }
        .download-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .download-button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mailing - The ultimate mail server</h1>
    </header>
    
    <div class="container">
        <div class="about-section">
            <h2>About us</h2>
            <p>Chatting around the world, in a secure way. In Mailing we take care of the security of our clients, protecting them from scams and phishing.</p>
        </div>
        <div class="software-section">
            <h2>The server</h2>
            <p>Using any mail client you can connect to our server with your account with any system (Linux, MacOS or Windows) and you're ready to start mailing! Powered by <a href="https://www.hmailserver.com/">hMailServer</a></p>
        </div>
        <div class="software-section">
            <h2>Contact us</h2>
            <p>In case of any issues using our services, please contact us reporting the issue</p>
        </div>
        
        <h2>Our Team</h2>
        <div class="team-section">
            <div class="team-member">
                <img src="assets/ruyalonso.jpg" alt="Ruy Alonso">
                <h3>Ruy Alonso</h3>
                <p>IT Team</p>
            </div>
            <div class="team-member">
                <img src="assets/mayabendito.jpg" alt="Maya Bendito">
                <h3>Maya Bendito</h3>
                <p>Support Team</p>
            </div>
            <div class="team-member">
                <img src="assets/johnsmith.jpg" alt="Gregory Smith">
                <h3>Gregory Smith</h3>
                <p>Founder and CEO</p>
            </div>
            <!-- Add more team members here -->
        </div>
        <div class="software-section">
            <h2>Installation</h2>
            <p>In order to connect your computer to our mail service, please follow the instructions below.</p>
        </div>
        
        <a href="download.php?file=instructions.pdf" class="download-button">Download Instructions</a>
    </div>
</body>
</html>

```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/9.1.2 Safari/605.1.15' 'http://mailing.htb'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)