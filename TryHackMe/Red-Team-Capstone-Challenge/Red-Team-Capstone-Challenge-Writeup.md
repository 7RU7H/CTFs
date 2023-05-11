# Red-Team-Capstone-Challenge Writeup

Name: Red-Team-Capstone-Challenge
Date:  
Difficulty:  Hard
Goals:  
- Red Teaming with the Alh4zr3d Community'
- Get the badge!
Learnt:

![](october.png)

- [[Red-Team-Capstone-Challenge-Notes.md]]
- [[Red-Team-Capstone-Challenge-CMD-by-CMDs.md]]


![](Red-Team-Capstone-Challenge-map.excalidraw.md)


![](cooltext.png)

## OSINT  


```
crackmapexec <proto> 10.200.121.0/24 -u '' -p ''
```

![](cme-init.png)

![](webroot.png)
- Aimee Walker & Patrick Edwards.

Harvesting usernames http://10.200.121.13/october/index.php/demo/meettheteam -> linked to http://10.200.121.13/october/themes/demo/assets/images/ 
```bash
curl http://10.200.121.13/october/themes/demo/assets/images/ -o images
cat images| cut -d '"' -f 8 | grep '.jpeg' | sed 's/.jpeg//g' > users.txt
echo "aimee.walker" > users.txt
echo "patrick.edwards" > users.txt
```

I tried injection techniques against the To Do Lists
![](sendacvtothereserve.png)



## Perimeter Breach
## Initial Compromise of Active Directory
## Full Compromise of CORP Domain
## Full Compromise of Parent Domain
## Full Compromise of BANK Domain
## Compromise of SWIFT and Payment Transfer
