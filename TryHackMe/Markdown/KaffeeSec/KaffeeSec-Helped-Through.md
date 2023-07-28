# KaffeeSec Helped Through

Name: KaffeeSec
Date:  28/7/2023
Difficulty:  Medium
Goals: 
- Learn the basics of SOCMINT
- Scratch a need to sharpen OSINT cheatsheet pages 
- Get the tooling
Learnt:
- Learning social media like I am a boomer 
- Recursive WayBackMachine
- Spiderfoot
Beyond Root: 
- Make a Spi derfoot Docker container! 
- [Complete the IMINT Room](https://tryhackme.com/room/searchlightosint) with write-ups if required - I am not a geoguessing guru, just want the high-level analytical bits of information to incorporate   - Tomorrow!


##### **Background Information**:  

*You are Aleks Juulut, a private eye based out of Greenland. You don't usually work digitally, but have recently discovered OSINT techniques to make that aspect of your job much easier. You were recently hired by a mysterious person under the moniker "H" to investigate a suspected cheater, named Thomas Straussman. After a brief phone-call with his wife, Francesca Hodgerint, you've learned that he's been acting suspicious lately, but she isn't sure exactly what he could be doing wrong. She wants you to investigate him and report back anything you find. Unfortunately, you're out of the country on a family emergency and cannot get back to Greenland to meet the deadline of the investigation, so you're going to have to do all of it digitally. Good luck!*

We get a username:
tstraussman

Scope is:
- Twitter and Reddit.. both have gone through a lot access changes in the last year.
- Only Passive Recon no direct contact required.

#### Initial stumbling into walls of change and my lack of Social Media care over the decades...

Well firstly the restriction on account for X - used to be twitter then means that this is be default a Helped-Through. 

![](xrestrictions.png)

With relevancy of these challenges out of the proverbial window it the nuggets of gold like - make a graph of how the information connects this probably the important part here:
![](nobodytotherescue.png)

For Help with Twitter bypassing I used the write up- https://bryanleong98.medium.com/performs-osint-on-thomas-straussman-osint-challenge-e5ab91937137
![](twitterhelp1.png)

Mouse over for the date - I need the writeup again. Well... yep.
![](mouseover.png)

![](whoisconnected.png)

For all the twitter related SOCMINT for Thomas's Wife I used the write up. 

- What is the basic profile of target?
	- Name
	- Naming Conventions
	- Age
	- Employment, Roles and History
	- Linked Accounts
	- Relations 
		- Family, Friends, Work, etc?
	- Interests	
	- Potential dense areas of knowledge? 
	- Psycho((Cognitive)-Linguistic)-Analytic **potentially attributive** profile?
		- cold-to-hot read potential clues 
		- Neuro-diverse? 
		- Linguist:
			- Paralinguistic (non-verbal communication) in pictures and videos
			- Phonetic: Dialect
			- Lexical - Idiolect, Socialect
			- Syntax: Is there emphasis? Idiot highlighting (e.g double negatives)
			- Semantics: dense clusters of areas of meaning or meaninglessness (for various high order reasons)
			- Pragmatic: How does the target approach cultural and social communication Maxims - Grice's, Relevancy Theory, etc 
			- Psycho-to-Cognitive-and-maybe-guessing-the-Neuro Linguistics: 
				- Deixis: space, time
				- Indicators of level of competency in critical thinking 
				- Indicators of level of emotional intelligence
				- Improvisational competency?
				- Disorders or under-or-over development, Indicators of traits:
					- Clues for: Aphasias, Speech disorders, Neurological Disorders, Habitual Lying, lack or excess affect, Personality, etc 
- Where do they go offline and online?  
- What do the images they post entail - IMINT?
- How do use a site and account?
- How do they socially connect to other users?
	- Why do they connect?


#### Welcome Spiderfoot

[Spiderfoor](https://github.com/smicallef/spiderfoot) *"automates OSINT for threat intelligence and mapping your attack surface ..SpiderFoot is an open source intelligence (OSINT) automation tool. It integrates with just about every data source available and utilises a range of methods for data analysis, making that data easy to navigate.
- Features:
	- Web based UI or CLI
	- Over 200 modules
	- Python 3.7+
	- YAML-configurable [correlation engine](https://github.com/smicallef/spiderfoot/blob/master/correlations/README.md) with [37 pre-defined rules](https://github.com/smicallef/spiderfoot/blob/master/correlations)
	- CSV/JSON/GEXF export
	- API key export/import
	- SQLite back-end for custom querying
	- Highly configurable
	- Fully documented
	- Visualisations
	- TOR integration for dark web searching
	- Dockerfile for Docker-based deployments
	- Can call other tools like DNSTwist, Whatweb, Nmap and CMSeeK
	- [Actively developed since 2012!](https://medium.com/@micallst/lessons-learned-from-my-10-year-open-source-project-4a4c8c2b4f64)
	- For more go to [intel471](https://intel471.com/solutions/attack-surface-protection)
- You can target the following entities in a SpiderFoot scan:
	- IP address
	- Domain/sub-domain name
	- Hostname
	- Network subnet (CIDR)
	- ASN
	- E-mail address
	- Phone number
	- Username
	- Person's name
	- Bitcoin address

Docker Container Setup to bypass the Python dependency hellscape and do something for Beyond Root  

Make a docker container for Spiderfoot
```bash
git clone https://github.com/smicallef/spiderfoot.git
cd spiderfoot/
docker build -t spiderfoot .
```
Running as a Docker Container - **beware we are exposing this on 0.0.0.0** - consider `proxychains` with [[Proxies]]
```bash
# Usage:
sudo docker build -t spiderfoot .
sudo docker run -p 5001:5001 --security-opt no-new-privileges spiderfoot
# Using Docker volume for spiderfoot data
sudo docker run -p 5001:5001 -v /mydir/spiderfoot:/var/lib/spiderfoot spiderfoot
# Using SpiderFoot remote command line with web server
docker run --rm -it spiderfoot sfcli.py -s http://my.spiderfoot.host:5001/
# Running spiderfoot commands without web server (can optionally specify volume)
sudo docker run --rm spiderfoot sf.py -h
# Running a shell in the container for maintenance
sudo docker run -it --entrypoint /bin/sh spiderfoot
# Running spiderfoot unit tests in container
sudo docker build -t spiderfoot-test --build-arg REQUIREMENTS=test/requirements.txt .
sudo docker run --rm spiderfoot-test -m pytest --flake8 .
```

#### Turning on the Wayback Machine

The plugin is pretty awesome it makes a multiple stage process now only two
![](coolplugin.png)

Hans from work... https://www.reddit.com/user/minikhans
![](HANS.png)

More Wayback Machine. old Reddit 
![](oldredditgoodreddit.png)

Continuing on
![](disappointshans.png)


IU use  thiisisfinx writeup for [https://shadowban.eu/.api/tstraussman](https://shadowban.eu/.api/tstraussman) - https://thisisfinx.medium.com/1-4-tryhackme-kaffeesec-somesint-writeup-e1a7286b4824

