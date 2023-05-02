# Areas To Improve Checklist

- Notes and CMD-by-CMD only - use screenshot_and_report_format_loader.sh
- Marshall data better and make it visibile
	- "PrivEsc/FootHold - pieces" 
		-  ie cronjob, wget vulnerable, authbind and pyftpdlib is on the box
	 - Requirements lists for exploitation chains
	 - Add comments to the modified exploit code so that I understand and have questioned what it is doing in the context of the box.
- Read exploit twice and once after changes
- When Living in Burp sublive in the devtools 

- 
- "racing mind practice"
- Helpthrough



```bash
echo "
## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

## Exploit

## Foothold

## PrivEsc

## Beyond Root
"
```


```bash

project=(find . -type d -name "$1" 2>/dev/null)
cd $project;
screenshots=$(ls -tr Screenshots/)  
fmt_pngs=$(for png in screenshots; do echo '![]($png)'; done;)
for png in $fmt_pngs; do
	echo $fmt_png >> *-Writeup
done	

```