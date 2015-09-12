Ionize CMS
==========

Version: 1.0.8

Not released yet

### About Ionize

Ionize is a free professional and natively multilingual PHP CMS, developed with user experience in mind.

Ionize is dedicated to webdesigners and web agencies to simply make their clients happy.

Ionize uses the PHP framework CodeIgniter.

Official website: http://www.ionizecms.com

![Screenshot](https://github.com/ionize/ionize/raw/master/files/screenshot_ionize_dashboard.jpg)

### Authors

* [Michel-Ange Kuntz](http://www.partikule.net)
* [Christophe Prudent](http://www.toopixel.ch)

### Contributors
* [Kay Stenschke](http://www.coexec.com)
* [İskender Totoğlu] (http://www.altivebir.com.tr)
* Martin Wernstahl
* [Bhagya Nirmaan Silva](http://about.me/bhagyas)


### Installation

IMPORTANT: Before any update, make a backup of your database.
We will not be responsible for any loss of data.

These instruction take into account that this version is in developement.

* From Ionize 0.9.x
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.x config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer: http://your_domain/install
  * The installer will migrate the database and the user accounts


* From Ionize 0.9.7 (dev version)
  * Copy your database, you will start working with this copy,
  * Go in your 0.9.6 config/config.php file and copy your encryption key in the new config/config.php file,
  * Launch the installer: http://your_domain/install
  * The installer will migrate the database (not the user accounts)
  * Launch ONE TIME ONLY: http://your_domain/install/?step=migrate_users_to_ci2

### Translation packages: 
* [Translations on ionizecms.com](http://ionizecms.com/en/support/translations/download-translations)
* [Translation packages on github](https://github.com/ionize/ionize-translations)
 

### Code Migration

* From Ionize 0.9.x
  * Tags were rewritten for a more logical use. Please refer to the documentation to see how to migrate your views.

* From Ionize 0.9.6
  * Articles Tag: Change your <ion:article filter="title:!=''" > attributes from "title:!=''" to "title !=''" (remove ":")
  * Articles Tag: Change <ion:article filter="type:='your_type'"> to  <ion:article type="your_type" >
  * Navigation Tag: Add the "level" attribute: <ion:navigation level="0" />

### Updates
See CHANGELOG.md
 
