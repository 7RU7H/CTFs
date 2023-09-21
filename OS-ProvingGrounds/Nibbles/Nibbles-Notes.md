# Nibbles Notes

## Data 

IP: 192.168.182.47
OS: Linux
Arch: Debian 10 Buster
Hostname: 
DNS:
Domain:  / Domain SID:
Machine Purpose: 
Services: 21, 22,80, 139, 445, 5437 (Postgresql)
Service Languages:
Users:
Email and Username Formatting:
Credentials:

## Objectives

`\#\# Target Map ![](Nibbles-map.excalidraw.md)`

#### Machine Connects to list and reason:

[[]] - Reason X

## Data Collected

#### Credentials
```
```

#### HUMINT


#### Solution Inventory Map


#### Todo 

[The default Postgres user is `postgres`](https://enterprisedb.com/postgres-tutorials/connecting-postgresql-using-psql-and-pgadmin) 

#### Timeline of tasks complete

- Automated Recon
- Searchsploited:
	- PostgreSQL
	- Apache has local PrivEsc - linux/local/46676.php
- Manual Web:
	- Need to Directory bust


[miguelaeh on Github Issue states:](https://github.com/bitnami/charts/issues/5150) *"The databases `template0` and `template1` are used to create new database when using the `CREATE DATABASE` command"*

There are no tables