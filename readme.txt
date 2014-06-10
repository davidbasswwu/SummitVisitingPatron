David Bass @ WWU
10 June 2014
Summit Visiting Patron

To allow visiting patrons (who do not have standard WWU user accounts) to borrow items and have them delivered to our library, we verify their active 
user status at their own university by having them login to their own version of Primo in front of one of our staff members, who then creates an account 
for them in Alma (as a Summit Visiting Patron, or SVP).

The technique we're using is to send normal users (LDAP authentication) directly to EZproxy (as it has always been done), and SVP users to a PHP script that uses Alma to authenticate the user, generate an EZproxy ticket, and then send them back to EZproxy, where they are then redirected to their desired destination.  There is a PDF flowchart in this repository, but here is another version in case you can't see it online:  https://www.dropbox.com/s/udvuxsdig24sgrp/svp-alma-ezproxy-authentication-flowchart.pdf

Once the visiting patron has an account in our Alma database, they can be authenticated by choosing the SVP option on the login form.
Please see the flowchart for an overview of how it works behind the scenes.

To use this in your environment, you'll need to:
1 - edit your ezproxy/user.txt file (see user.txt)
2 - add the iii-fake.txt file to your ezproxy folder 
3 - tweak your ezproxy/docs/login.html file (see login-form.html.txt)
4 - modify your web server's (Apache) configuration (see httpd.conf snippet.txt).  IIS users might be able to accomplish this via http://www.iis.net/downloads/microsoft/url-rewrite or similar add-on.
5 - add the files in the 'alma' folder to your webserver.  Customization instructions are included in each file.
6 - restart Ezproxy and Apache after you make changes to those configuration files.

Troubleshooting Tips:
1 - go through each step of the flowchart, and make sure each component is working individually. For instance, check http://your.server.edu/PATRONAPI/theusername/dump to make sure it is producing the right output.
It should look something like:
 Content-type: text/html USER NAME[pu]=theusername
 PATRN NAME[givenname]=Summit
 PATRN NAME[sn]=Testuser
 EMAIL ADDR[pz]=someone@example.com
 CATEGORY[p47]=SummitVP
 EXP DATE[p43]=10-18-2016

2 - check your error logs (including ezproxy/messages.txt)

----------------

PS_
Thanks to:
 - Paul Sitko @ OCLC for referring me to Bill Jordan @ UW, who showed me how to use the III API impersonator
 - Chris Zagar who helped with ezproxy issues
 - Benjamin Florin (https://github.com/BCLibraries/php-alma) for sharing your Alma web services code
 
