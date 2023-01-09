# Bucket Helped-Through

Name: Bucket
Date:  09/01/2023
Difficulty:  Medium
Goals:  
- Compare AWS with Azure  
- Have some idea of what AWS other than the words s3 and it uses an api for cloud stuff
- Perform a Linux Hardening and Persistence
- Hunt for 3 more content creators for this type of learning exercise - want to do one a week to compliment start really focusing on completing one HTB Active machine a week from Febuary. 
Learnt:
Beyond Root:
- Harden box
- Add a low and high priv Persistence + plus create a vuln on the box
- Return to every description of AWS vs Azure - Look at what my notes say, then search engine dork and ChatGPT the answers for a average time per AWS  vs Azure point.

I will be helped through by [Alh4zr3d](https://www.youtube.com/watch?v=vSug24hrQdo) as part of Newbie Tuesday; this video covers "Bucket" from HacktheBox, in which we tackle some common misconfigurations in Amazon Web Services (AWS)! My caveat and challenge being while doing this box is to both add AWS to the archive as a baseline definition of what it is and how to hack it, but more important perform a recall exercise about Azure while I am going along. So I will be stopping and starting the video alot abit like drink everytime someone says X, its explain how the same thing works in Azure without looking at notes or looking up. I think this is a cool way to kill all the birds with one stone by hacking away with someone parasocially, doing Azure recall to see where I do not know - good luck with `az` and `powershell` commands future me, learning about AWS via Azure knownledge and Hacking AWS for the second timne other than a THM AoC that was not very in depth or I did not take it that seriously over a year ago. 

AWS according to ChatGPT in two sentences: *"Amazon Web Services (AWS) is a cloud computing platform that provides a wide range of services such as computing, storage, networking, database, analytics, machine learning, security, and application development. These services are offered on a pay-as-you-go basis and can be accessed over the internet, allowing organizations to scale and innovate quickly."*

Azure provides similar services, but absent of the description above is that Azure provides:
- Dedicated Lift-and-shift and on-premise to cloud connective with Azure Connect
- Access over Azure backbone in addition to over vpns and internet
- Azure Active Directory 
- Templating for build consistency, scalability, etc wtih ARM and Bicep
- `az` cloud binary for Linux and Windows.

## Recon

The time to live(ttl) indicates its OS. It is a decrementation from each hop back to original ping sender. Linux is < 64, Windows is < 128.
![ping](Screenshots/ping.png)

Just ports 22 and 80; the root webpage is bare of links
![](rootpage.png)

But is sourcing images from s3.bucket.htb
![](butissourcingimagesfroms3buckets.png)

[Hacktricks : buckets just recommend ScoutSuite](https://book.hacktricks.xyz/network-services-pentesting/pentesting-web/buckets) and AWS hacking Tricks is not found. Checking the s3 with burp:
![](tisrunning.png)
Decide to feroxbuster with a large wordlist just to background recursive fuzzing of the api initially, before using Postman and Ffuf.

[ScoutSuite](https://github.com/nccgroup/ScoutSuite)

It's is 49 minutes into the video and Alh4zr3d is just finished scanning the machine; given that there has not been Azure vs AWS
![](manualdrilldown.png)

#### AWS vs Azure
S3 Buckets use API calls to fetch stored content
Guess: bash, \*Shell or `Az` binary is used to query resources or wrap queries to resources that have query languages.

Alh4zr3d points out a trick to figure out how to: Clarify host OS version from webserver version?

[S3 Amazo Augmented Definitions](https://docs.aws.amazon.com/AmazonS3/latest/userguide/Welcome.html): *"Amazon Simple Storage Service (Amazon S3) is an object storage service - [using Amazon S3 storage classes](https://docs.aws.amazon.com/AmazonS3/latest/userguide/storage-class-intro.html) that offers industry-leading scalability, data availability, security, and performance. Customers of all sizes and industries can use Amazon S3 to store and protect any amount of data for a range of use cases, such as data lakes, websites, mobile applications, backup and restore, archive, enterprise applications, IoT devices, and big data analytics. Amazon S3 provides management features so that you can optimize, organize, and configure access to your data to meet your specific business, organizational, and compliance requirements."*

Al ask: What should we poke at or look for vulnerabililty-wise?
My answers:
- Exposed API keys
- API queries for figured the API structure and API typology. 

"It may be misconfigured where we could upload files" - AL  - "Poorly configured permission is prevalent given the complexity of permissions in AWS"

While Al struggle with install and dependency issues - he saved by Docker, I will spend a hour making all the Docker related infrastructure I need to basic setup, add scripts - also RustScan. 

#### AWS vs Azure

```bash
sudo apt install aws-cli


```

## Exploit

## Foothold

## PrivEsc

## Beyond Root

      
