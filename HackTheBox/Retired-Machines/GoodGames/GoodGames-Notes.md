# GoodGames Notes

## Data 

IP: 10.129.162.53
OS: 
Hostname:
Machine Purpose: 
Services:
Service Languages: Werkzeug/2.0.2 Python/3.9.2
Users:
Credentials:

## Objectives

## Target Map

![](GoodGames-map.excalidraw.md)

## Solution Inventory Map


### Todo 



### Done
      

host: goodgames.htb

Gospider has alot of data -> reduced_gospider.out

```bash
cat gospider/10_129_172_47 | grep -v 'static\|%7C' > reduced_gospider.out
```

- Created an account - http://10.129.161.148/profile
- Cookie `.eJw1yzEOgCAMBdC7_Jk4MHbyJqaGAk0AE1om49118e3vxpGnWAVlbiYB0lkbCJy6jt3FXEfZqp8I0ASKAe0qRZIOkM_1lWUyB3f5V8TzAhyZHYc.ZJF61A.mhGdqwZiVRf_TMT5UH_2a6Etu94`

nosstiportprofilecreation.png

justwow.png

nosubdomains.png

apidisclosure.png

[Fuzzing to greedly and deeply...into the mines of API fuzzing we go... ](https://www.youtube.com/watch?v=PfhZB7rQ7iA)