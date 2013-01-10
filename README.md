TwitterPx
=========

A php twitter proxy to continue using unauthenticated twitter requests after the API will switch to v1.1.
Author @jpsarda ( http://twitter.com/jpsarda )

How does it work?
=================

TwitterPx is based on tmhOAuth by @themattharris.
You first create a pool of available user tokens. Then you send unauthenticated requests to your server, that forward an authenticated request to the twitter API, using one of the user token in the pool.
The more tokens you have in the pool, the more request you can send per hour.

Install
=======

1) Get the TwitterPx package and decompress on your local web server.

2) Create a twitter app on http://dev.twitter.com/

3) Add you consumer key and secret in twitterpx/config.php

4) On your local server, add some twitter users to the pool of available tokens : http://localhost/twitterpx/setup/

5) Test an API request like http://localhost/twitterpx/statuses/user_timeline?screen_name=jpsarda

6) Upload on your server the 3 directories (twitterpx, twitterpool, tmhOAuth) and the .htaccess 

7) Test an API request like http://myserver.com/twitterpx/statuses/user_timeline?screen_name=jpsarda