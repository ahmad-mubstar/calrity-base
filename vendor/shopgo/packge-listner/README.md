PLN (Package Listener or Product Limit Notification)

It's a simple module, working in background as a cron job and executed every Wednesday at 3:15 (Amman time)
the cron job will check the count of *enabled* products (disabled products are NOT counted) in the store and compare it with Shopgo Packages.

Shopgo Packages are taken from http://downloader.devshopgo.me/shopgo_packages.php.
In case the server was down for and the URL cannot be accessed, it will send alert mail to us (in this case, Emad’s email) about that.

If a certain client exceeds the package limit of allowed products then it will send a notification email to Zendesk that includes:
domain of store
current package
number of allowed products,
Number of products above the limit, 

The body of the email looks like this:
***
This is an automatic informational message to notify you that the domain
http://store.com/index.php/
has exceeded the maximum number of products (SKUs) allowed for the current package.
Please upgrade your package or keep the number of products under the limit. For help, contact your On-Boarding or Growth Specialist.
Current package: Basic,
Allowed number of products: 100,
Number of products above the limit: 24
***

If the client doesn't upgrade, it'll send an email every week until they upgrade his package,
and once he upgrade the his subscription package must be changed in package.json file.

The PLN module will create a json file under the path html/var/,the file called package.json.
This file will store the package info and if this file is missing the module will send alert mail to us (in this case, Emad’s email)about that.

In general case the module will read number of skus for any package by shopgo_packages file,
but if we have a store like as maybashawri, it has special offer(Basic package with unlimited skus),
so in this case we must to set number of skus in package.json file so we can say the package.json file can also handle special cases.


Note
The following info can be easily set for all stores in one step without updating the module itself. This happens by updating this file http://downloader.devshopgo.me/shopgo_packages.php.
Increase/Decrease size of any package skus, 
Change sender mail,
Change receiver mail,
Change subject mail,
Change sender name.