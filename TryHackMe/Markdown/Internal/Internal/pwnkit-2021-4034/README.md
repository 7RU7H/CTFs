# README

Source: https://www.exploit-db.com/exploits/50689

# Makefile 

all:
```
	gcc -shared -o evil.so -fPIC evil-so.c
	gcc exploit.c -o exploit
```
clean:
```
	rm -r ./GCONV_PATH=. && rm -r ./evildir && rm exploit && rm evil.so
```
