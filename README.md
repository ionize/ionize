Ionize CMS
====================

Ionize is a free professional and multilingual PHP CMS, developped with user experience in mind.
Ionize is dedicated to webdesigners and web agencies to simply make their clients happy.
Official website : http://www.ionizecms.com

![Screenshot](https://github.com/ionize/ionize/raw/master/files/screenshot_ionize_dashboard.jpg)

### Authors

* [Michel-Ange Kuntz](http://www.partikule.net.net)
* [Martin Wernstahl]
* [Christophe Prudent](http://www.toopixel.ch)


About this branche (with_ci2)
----------

This branche is experimental, it may not work.
We provide no support on this branche, but every issue report is welcome.


### Installation

IMPORTANT : Before any update, make a backup of your database.
We will not be responsible for any loose of data.

These instruction takes in account that this version is in developement.

* From Ionize 0.9.6
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.6 config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer : http://your_domain/install
  * The installer will migrate the database and the user accounts
  
* From Ionize 0.9.7 (other branche than with_ci2)
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.6 config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer : http://your_domain/install
  * The installer will migrate the database (not the user accounts)
  * Launch ONE TIME ONLY : http://your_domain/install/?step=migrate_users_to_ci2
  

