# Distopia: A discussion and debate forum.

Distopia is a discussion forum that features indented replies, color-coded pseudo-anonymity (each anonymous user is given a distinct color), and much more.

# Installation
There is currently no automated installation.
1. Place the distopia folder somewhere in your web server.
2. Enable mod_rewrite if it is not already enabled.
3. Create a new MySQL user and corresponding database.
4. Open application/config/database-sample.php and fill in the credentials to your database.
5. Save as database.php
6. Run distopia.sql on the database. This will set up the necessary tables and create one board.
7. See that board at [install location]/board/a/

# Features

Features that work:
* Posting and indented replies
* Color-coded users
* Tripcodes
* Multiple boards

Features that need to be re-implemented
(They exist in the system but are not implemented well)
* Sticky posts
* Admin posts
* The admin panel
* Post deletion
* Almost all admin-related functions (reports, deletion, bans)

Features that need to be added:
* User logins
* Automated installation


# License
Creative Commons Attribution-NonCommercial-ShareAlike 3.0
http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en_US