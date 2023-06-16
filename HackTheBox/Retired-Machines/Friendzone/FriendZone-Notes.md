# FriendZone Notes

## Data 

IP: 10.129.165.17
OS:
Hostname: 
Domain:  friendzone.red
organization Name: CODERED
Machine Purpose: 
Services:  21,22,53,80,139,443. 445
Service Languages:
Users:
Credentials:


## Objectives

- Past a 3 hour mark, 1 hour more 

## Solution Inventory Map


### Todo 

- Edit read-settings.js in browser to see if I can manipulate anything
- Or it is password mangling...


smb 
- /etc/Files

usernames



TL;DR spooflocalhost with rogue dns? DNSchef beyondroot? For privesc?


[https://security.stackexchange.com/questions/262357/how-can-you-exploit-a-website-that-resolves-to-localhost](https://security.stackexchange.com/questions/262357/how-can-you-exploit-a-website-that-resolves-to-localhost)


Oooh, that's very dangerous. There are two main attack vectors against such systems.

1. Cross-user same-machine access. Loopback network sockets are notable for being the only mainstream form of IPC with absolutely no support for authentication or access control (you can try bolting something on at the application layer, like TLS, of course). As such, an attacker on the same machine - likely even one within a sandbox such as an app from an app store or a malicious page in a browser - can connect to the server as a client, or possibly even spoof the server (launching before the server does and squatting its port) and wait for the legit client to connect. This can allow local EoP to attack other users and/or break out of a sandbox.
2. Spoof DNS and impersonate the server. If you have local network access (e.g. on public WiFi at an airport or cafe) you can spoof DNS responses and make the DNS query return your own IP instead of the loopback address. You can then run a server yourself and impersonate the real server (optionally forwarding traffic _back_ to the real server - after modification, if desired - assuming it listens on external interfaces). Obviously this won't work if the client expects the server to use a trusted TLS certificate, but this sort of system almost never does. This allows a remote attack on the system, and depending what the client does, you might be able to gain local code execution, steal credentials, or otherwise compromise the user.

---

> sometimes i see domains named like 127-0-0-1.domain.com and it resolves to localhost, why do the developers even need such a thing ?

CORS and/or cookies, mostly. They want to be able to serve content (almost always web content) from a local process, but also want it to be treated as a "real" subdomain that shares a root domain with "the rest of" their servers. This means you can share cookies across the subdomains, use a simple CORS policy that forbids foreign domains, or even lower the page origin to the same root domain on both the local and remote servers' pages and actually be treated as same-origin for purposes such as iframes. Some browsers and platforms (mostly Apple's) also treat "real" domains better than IP addresses even when not required by the spec.

It can also be done for TLS - no real CA will issue a cert for "127.0.0.1" or even for "localhost", but "127-0-0-1.domain.com" works - but it would be very weird (and possibly dangerous) if that's happening here. A CA-issued cert for that domain would require that the private key be distributed with the application so that the server could use it, and then of course anybody with the app could steal the key and use it themselves, completely breaking the point of TLS (and also requiring, by policy, immediate revocation of the certificate). Alternatively, you can create a self-signed certificate and install it in the machine's/browser's trust store, but you can do that without bothering to use a "real" domain on DNS.

https://security.stackexchange.com/a/263030 


### Done

friendzone.png
cmenosigning.png
nmapsmbenum.png
cmesmbshares.png
cmeftpnoanonymous.png
seriously.png
notarealwordpresssite.png

Sharing Localhost over DNS! Amazing
dnslocalhost.png

Consider my options with the above I pillaged the smb shares
cmeandsmbmaptodownloadthecredentials.png

` admin : WORKWORKHhallelujah@# ` 

cmeadmintestingshares.png
sadftpandssh.png

friend user from admin enum4linux
e4lwithadmin.png

nopasswordreuse.png

two times the javascript what could go wrong
jsjs.png


aXZFU0ZYQkJpTDE2ODY4Mzc1MDNCamNXb0pEOWxT
weirdness.png

NjVpMGVWVHBkbjE2ODY4Mzc2MTBzVEljeXAySEdH
weirdnesschanges.png

YlA5cWpWbmVxVjE2ODY4Mzc2Mzc5VW1WWFdkMUpp


zoneadmin.png
This could be a nasty rabbithole

2023-06-15T15:08:07.995Z justgotzoned zonedman

Tried `admin : WORKWORKHhallelujah@# `  as a cookie, I had been wondering how to reach the source code of whatever was creating the weird string
sourcecode.png

[deobfuscate.io](https://deobfuscate.io/) replace hex values, but someways this seems worse.
```js
(function (corbet, rayquann) {
  const kingjosiah = corbet();
  while (true) {
    try {
      const shekina = parseInt(a4_0x59be(197, 680)) / 1 * (-parseInt(a4_0x59be(199, 677)) / 2) + parseInt(a4_0x59be(194, 673)) / 3 * (parseInt(a4_0x59be(208, 686)) / 4) + parseInt(a4_0x59be(213, 695)) / 5 * (parseInt(a4_0x59be(191, 679)) / 6) + -parseInt(a4_0x59be(209, 675)) / 7 * (-parseInt(a4_0x59be(210, 686)) / 8) + -parseInt(a4_0x59be(203, 672)) / 9 * (parseInt(a4_0x59be(192, 671)) / 10) + -parseInt(a4_0x59be(206, 689)) / 11 * (parseInt(a4_0x59be(190, 676)) / 12) + parseInt(a4_0x59be(212, 684)) / 13;
      if (shekina === rayquann) break; else kingjosiah.push(kingjosiah.shift());
    } catch (jeffie) {
      kingjosiah.push(kingjosiah.shift());
    }
  }
}(a4_0x2acc, 591506));
function a4_0x2acc() {
  const ramont = ["z2v0vvjm", "CNvUDgLTzq", "ls0Gre9nieLUDMfKzxi6iezHAwXLzcb0BYbWyxjZzsbZzxr0Aw5NCW", "nJG5mdu4s3fgz1zh", "ls0Gre9nieLUDMfKzxi6ienVDwXKig5VDcbSB2fKihnLDhrPBMDZlIbuCNKGzw5HyMXPBMCGDgHLihjLBw92zsbWzxjTAxnZAw9UCYbWB2XPy3KGAgvHzgvYigLUihnLDhrPBMDZlG", "zxjYB3i", "mZK4otyXmNjZAvbADq", "qMjizKO", "nZiWEhv5BM1A", "otaXnJDeCujbweG", "ndG4CvjrtKjh", "CgfYC2u", "nJeZmJG5m2HfuLrmta", "nte2otbXEKPIrw4", "mtjltwXpq2G", "mZK2txzoyNPJ", "ndb1rKjoz1K", "B3bLBG", "nJm3nvHvzgzYCq", "CMvZCg9UC2vuzxH0", "C2vUza", "mJmWodrSreTzDNO", "C2v0DgLUz3m", "otj1BMLvtxe"];
  a4_0x2acc = function () {
    return ramont;
  };
  return a4_0x2acc();
}
function loadSettingsFromJson(delloyd) {
  const kristofe = {BbHfJ: a4_0x59be(202, 746)};
  let keyontae = new XMLHttpRequest;
  try {
    keyontae[a4_0x59be(193, 752)]("GET", delloyd, false), keyontae[a4_0x59be(196, 746)](null);
  } catch (yeeleng) {
    console.error(a4_0x59be(204, 757));
    return;
  }
  try {
    return JSON[a4_0x59be(211, 769)](keyontae[a4_0x59be(195, 758)]);
  } catch (buelah) {
    console[a4_0x59be(205, 767)](kristofe[a4_0x59be(207, 765)], buelah);
  }
}
function a4_0x59be(shakkia, alian) {
  const lucah = a4_0x2acc();
  return a4_0x59be = function (yonael, remilee) {
    yonael = yonael - 190;
    let abbeygale = lucah[yonael];
    if (a4_0x59be.xkpsxp === undefined) {
      var damarco = function (latreice) {
        const aerika = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=";
        let ameina = "", kyrston = "";
        for (let luzell = 0, maya, rickey, kasy = 0; rickey = latreice.charAt(kasy++); ~rickey && (maya = luzell % 4 ? maya * 64 + rickey : rickey, luzell++ % 4) ? ameina += String.fromCharCode(255 & maya >> (-2 * luzell & 6)) : 0) {
          rickey = aerika.indexOf(rickey);
        }
        for (let devell = 0, landrey = ameina.length; devell < landrey; devell++) {
          kyrston += "%" + ("00" + ameina.charCodeAt(devell).toString(16)).slice(-2);
        }
        return decodeURIComponent(kyrston);
      };
      a4_0x59be.GfObPL = damarco, shakkia = arguments, a4_0x59be.xkpsxp = true;
    }
    const kiylie = lucah[0], zahmir = yonael + kiylie, dott = shakkia[zahmir];
    return !dott ? (abbeygale = a4_0x59be.GfObPL(abbeygale), shakkia[zahmir] = abbeygale) : abbeygale = dott, abbeygale;
  }, a4_0x59be(shakkia, alian);
}
window[a4_0x59be(198, -595)] = loadSettingsFromJson(chrome[a4_0x59be(201, -589)][a4_0x59be(200, -593)]("settings.json"));

```
ChatGPT just confirmed my assumptions from skimming reading. Importantly it did highlight the obvious:
- settings.json - where is these settings 

nosettings.png

At this point I am scratching my head so I headed a scratch noggin to forums for hints, but there is not Official Forums so abandon hope of hints. Try harder
headscratchinglocalhostnojsjs.png



0b768b3dcab5eac6581a735e648c9f0f700b493421c83885
aa2b8813a1e113453ac054e9648c9eed5eca816203e9a072

returntodns.png

root.localhost.friendzone.red is really slow I also then increased the delay, but it did not help
yikesondelay.png


Edit in Browser as that is something that has been issue in the past. 

https://www.rapid7.com/db/vulnerabilities/cifs-smb-signing-not-required/


Going over the credentials 
admindirectory.png

Decided to redo content discovery
gbcom-admin.png

sadly nothing came of this.