Ionize CMS
====================

Current version : 0.9.7

Ionize is a free professional and natively multilingual PHP CMS, developped with user experience in mind.

Ionize is dedicated to webdesigners and web agencies to simply make their clients happy.

Official website : http://www.ionizecms.com

Translations : http://mygengo.com/string/p/ionize-language-packs-1

![Screenshot](https://github.com/ionize/ionize/raw/master/files/screenshot_ionize_dashboard.jpg)

### Authors

* [Michel-Ange Kuntz](http://www.partikule.net)
* [Martin Wernstahl]
* [Christophe Prudent](http://www.toopixel.ch)

### Installation

IMPORTANT : Before any update, make a backup of your database.
We will not be responsible for any loose of data.

These instruction takes in account that this version is in developement.

* From Ionize 0.9.6
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.6 config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer : http://your_domain/install
  * The installer will migrate the database and the user accounts
  
* From Ionize 0.9.7 (dev version)
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.6 config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer : http://your_domain/install
  * The installer will migrate the database (not the user accounts)
  * Launch ONE TIME ONLY : http://your_domain/install/?step=migrate_users_to_ci2
  

### Code Migration

* From Ionize 0.9.6
  * Articles Tag : Change your <ion:article filter="title:!=''" > attributes from "title:!=''" to "title !=''" (remove ":")
  * Articles Tag : Change <ion:article filter="type:='your_type'"> to  <ion:article type="your_type" >
  * Navigation Tag : Add the "level" attribute : <ion:navigation level="0" />