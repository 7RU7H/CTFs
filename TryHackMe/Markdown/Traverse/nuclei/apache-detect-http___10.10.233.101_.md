### Apache Detection (apache-detect) found on http://10.10.233.101/

----
**Details**: **apache-detect** matched at http://10.10.233.101/

**Protocol**: HTTP

**Full URL**: http://10.10.233.101/

**Timestamp**: Sat Aug 5 10:41:03 +0100 BST 2023

**Template Information**

| Key | Value |
| --- | --- |
| Name | Apache Detection |
| Authors | philippedelteil |
| Tags | tech, apache |
| Severity | info |
| Description | Some Apache servers have the version on the response header. The OpenSSL version can be also obtained |

**Request**
```http
GET / HTTP/1.1
Host: 10.10.233.101
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Cache-Control: no-store, no-cache, must-revalidate
Content-Type: text/html; charset=UTF-8
Date: Sat, 05 Aug 2023 09:40:43 GMT
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Pragma: no-cache
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: PHPSESSID=mt7c864ko2d2m8ihoq20ki4a7f; path=/
Vary: Accept-Encoding

<!-- Rest PHP code and html content -->


<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tourism Website</title>
 
    <script src='/tailwind.min.js'></script> <!-- THIS IS OFFICIAL FILE - DO NOT CHANGE IT -->
  <script src='custom.min.js'></script> <!-- THIS IS CUSTOM JS FILE-->
  <link rel="stylesheet" href="/style.css">
</head>


<body>
  <!-- Navigation Bar -->
  <nav class="bg-gray-900 text-white p-6">
    <div class="flex justify-between items-center">
      <a href="/" class="text-lg font-bold">Tourism MHT </a>
      <ul class="flex items-center gap-5">
	  <!--  <li><a href="/img" class="hover:text-gray-300">Logs</a></li>  Please keep all images in this folder -->
      <!--  <li><a href="./logs" class="hover:text-gray-300">Logs</a></li>  DevOps team to check and remove it later on -->


        
              
      </ul>
    </div>
  </nav>

  <!-- Main Content -->
  <main class=" mx-auto py-8  h-[80vh] flex items-center justify-center">

    <div class="rounded overflow-hidden shadow-lg bg-white  p-8 flex ">
		        <h2 class="text-gray-700 text-3xl py-6"> FINALLY HACKED !!! I HATE MINIFIED JAVASCRIPT</h2>
	    </div>

  </main>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white flex items-center justify-center">
    <div class="text-center p-4">
      <p>&copy; 2023 Tourism.mht. All rights reserved.</p>
    </div>
  </footer></body>

</html>
```

**Extra Information**

**Extracted results:**

- Apache/2.4.41 (Ubuntu)



**CURL command**
```sh
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36' 'http://10.10.233.101/'
```

----

Generated by [Nuclei v2.9.9](https://github.com/projectdiscovery/nuclei)