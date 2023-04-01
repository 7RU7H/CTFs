### HTTP Missing Security Headers (http-missing-security-headers:access-control-allow-origin) found on http://10.10.90.32:5000
---
**Details**: **http-missing-security-headers:access-control-allow-origin**  matched at http://10.10.90.32:5000

**Protocol**: HTTP

**Full URL**: http://10.10.90.32:5000

**Timestamp**: Sat Apr 1 20:04:57 +0100 BST 2023

**Template Information**

| Key | Value |
|---|---|
| Name | HTTP Missing Security Headers |
| Authors | socketz, geeknik, g4l1t0, convisoappsec, kurohost, dawid-czarnecki, forgedhallpass |
| Tags | misconfig, headers, generic |
| Severity | info |
| Description | This template searches for missing HTTP security headers. The impact of these missing headers can vary.<br> |

**Request**
```http
GET / HTTP/1.1
Host: 10.10.90.32:5000
User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 4486
Content-Type: text/html; charset=utf-8
Date: Sat, 01 Apr 2023 19:04:57 GMT
Server: Werkzeug/2.1.2 Python/3.8.10

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <title>Math</title>
  </head>
  <body>
    <p id="title">Math Formulas</p>

    <main>
      <section>  <!-- Sections within the main -->

				<h3 id="titles"> Feel free to use any of the calculators below:</h3>
        <br>
				<article> <!-- Sections within the section -->
					
          <h4 id="titles">Quadratic formula</h4> 
          
          <form method="post" action="">
            <table id=qtables>
              
                <tr>
                <td>a =</td><td><input id="a" name="a" required type="text" value="1"></td> <!-- each field within the form -->
                </tr>
              
                <tr>
                <td>b =</td><td><input id="b" name="b" required type="text" value="3"></td> <!-- each field within the form -->
                </tr>
              
                <tr>
                <td>c =</td><td><input id="c" name="c" required type="text" value="1"></td> <!-- each field within the form -->
                </tr>
              
            </table>
            <p id=results>
              
              
              
            </p>
              <div class="button"><input type="submit" value="Submit"></div>
          
          </form>
				
          <br>

				</article>
				
				<article>
					<h4 id="titles">Prime Numbers</h4>

          <form method="post" action="">
            <div id=stitles> Enter a number to see if it's prime</div>
            <table id=ptables>
              
                <tr>
                <td><input id="number" name="number" required type="text" value="3"></td>
                </tr>
              
            </table>
            <p id=results>
              
              
            </p>
              <div class="button"><input type="submit" value="Submit"></div>
          
          </form>
        </article>

        <br>

        <article>

          <h4 id="titles">Bisection Method</h4>
          
          <form method="post" action="">
            <div id=stitles> The formula is x^6 - x - 1</div>
            <table id=qtables>
              
                <tr>
                <td>xa =</td><td><input id="xa" name="xa" required type="text" value="1"></td>
                </tr>
              
                <tr>
                <td>xb =</td><td><input id="xb" name="xb" required type="text" value="3"></td>
                </tr>
              
            </table>
            <p id=results>
              
            </p>
              <div class="button"><input type="submit" value="Submit"></div>
          
          </form>
	  <br>
	      <p style="text-align:center">Download the source code from <a align="center"href="static/source.zip" download>here</a></p>
	
	</article>
			</section>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
  </body>

  <!-- Below are the CSS Styles-->
  <style> 
      body {
          background-color: #212121;
          color: white;
      }
      #title {
        color: white;
        background-color: #212121;
        text-align: center;
        font-size: 60px;
        font-style: italic;
      }
      #titles {
        color: white;
        background-color: #212121;
        text-align: center;
        font-size: 30px;
        font-style: italic;
      }

      #stitles {
        color: white;
        background-color: #212121;
        text-align: center;;

      }
      #results {
        text-align: center;
        font-style: italic;
        font-size: 20px;
      }

      .button {
        text-align: center;
        display: block;
        margin: 0 auto;
      }
      #qtables {
          width: 15%;
          height: 30px;
          padding: 12px 20px;
          resize: none;
          margin-left: 42.5%;
          margin-right: 42.5%;
        }
      #ptables {
        width: 11.5%;
        height: 30px;
        padding: 12px 20px;
        resize: block;
        margin-left: 44.25%;
        margin-right: 44.25%;
        }

  </style>
</html>

```


**CURL Command**
```
curl -X 'GET' -d '' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36' 'http://10.10.90.32:5000'
```
---
Generated by [Nuclei 2.9.0](https://github.com/projectdiscovery/nuclei)