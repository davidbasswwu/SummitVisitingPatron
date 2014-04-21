David Bass @ WWU
19 Dec 2013
Summit Visiting Patron

To allow visiting patrons (who do not have standard WWU user accounts) to borrow items and have them delivered to our library, we verify their active 
user status at their own university by having them login to their own version of Primo in front of one of our staff members, who then creates an account 
for them in Alma (as a Summit Visiting Patron, or SVP).

Once the visiting patron has an account in our Alma database, they can be authenticated by choosing the SVP option on the login form.
Please see 'Summit Visiting Patron - flowchart.pdf' for an overview of how it works behind the scenes.

To use this in your environment, you'll need to:
1 - edit your ezproxy/user.txt file (see user.txt)
2 - add the iii-fake.txt file to your ezproxy folder 
3 - tweak your ezproxy/docs/login.html file (see login-form.html.txt)
4 - modify your web server's (Apache) configuration (see httpd.conf snippet.txt)
5 - add the files in the 'alma' folder to your webserver.  Customization instructions are included in each file.
6 - restart Ezproxy and Apache after you make changes to those configuration files.


PS_
Thanks to:
 - Paul Sitko @ OCLC for referring me to Bill Jordan @ UW, who showed me how to use the III API impersonator
 - Chris Zagar who helped with ezproxy issues
 - Benjamin Florin (https://github.com/BCLibraries/php-alma) for sharing your Alma web services code
 