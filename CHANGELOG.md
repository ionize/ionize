Ionize CMS - Changelog
======================

* Version 1.0.8 - Not released yet
 * Added: autocorrection on blur to url field of page and article
 * Added: Extend field key is suggested from label
 * Improved: FTL parser performance
 * Changed: Database driver - removed usage of deprecated mysql_escape_string
 * Added: Accordion to Element Definition List
 * Bugfix: Extend Lists inside Content Elements were not sortable
 * Bugfix: Prevent Content Element from crashing Item Manager
 * Changed: Made content element edit popup generic sized, using more of the available screen size
 * Added: Extend Fields show their key via title
 * Added: Extend field key is suggested from label
 * Bugfix: Added missing refresh after delete extend field
 * Bugfix: Tagmanager - PHP error handling for 'expression' attribute evaluation
 * Bugfix: Naming home-page different to 'home' caused "undefined index" notice
 * Bugfix: Save orphan article: undefined index notice
 * Bugfix: fixed extend field type integrity
 * Bugfix: install directory detection on case sensitive systems
 * Bugfix: Navigation helper - class attribute generation for navigation submenus
 * Bugfix: Permissions list in backend - the lock symbols in the menu are now displayed again
 * Improved: Media browsing: made previous/next media button wrap-around
 * Improved: Media list filter styles
 * Bugfix: extend field model: fixed fatal typo in unlink_from_context()
 * Added: Emails (contact, info, technical) of website settings are now preset from administrator during fresh installation
 * Added: Optional article type relation on extend fields of articles
 * Added: Number.formatMoney()
 * Added: Backend font size can now be changed in settings (helpful on large screen)
 * Added: New watermark function
 * Bugfix: Remove tag from item did not work
 * Added: Tags and Categories can now be assigned to pages as well
 * Bugfix: Article linking receiver_rel condition
 * Added: New extend field type: Color
 * Bugfix: Clear field option in edit-media popup
 * Added: Mac specific style for mocha window (backend popups) titlebar
 * Added: Additional save (without close) button to media popup
 * Added: Options to browse to next/previous media to media popup
 * Added: "Select all files" option to filemanager
 * Added: Simple log system
 * Added: Ionize backend overlay in frontend can now be configured to be positioned alternatively to the right border
 * Added: Active view mode of media list is now highlighted
 * Added: Optional website HTML source beautifier (indent, merge+minify+move inline JS to bottom, reduce whitespace, etc)
 * Bugfix: Made installer work on PHP >= 5.5
 * Added: Active pagetree items are now visually marked
 * Added: Shortcut button to quickly return to page from article editing
 * Added: Backend login initially focusses the username field now
 * Added: "active" classname to current page in breadcrumps
 * Added: "Delete thumbs" option to advanced settings
 * Change: Codeigniter's (core mod.) delete files can now handle hidden files / directories
 * Bugfix: JS error - Article edit view didn't display media list
 * Added: When there's only one language in settings, it's automatically activated
 * Added: Page attribute id_parent to page tag manager
 * Added: Article editing options panel now contains option to select article type
 * Added: New content element can be saved via enter key
 * Bugfix: Expand/Collapse pagetree nodes
 * Added: Advanced settings now has option to remove deleted pages and rel. entities from DB
 * Added: Support for multilingual sitemap generation.
 * Changed: Sitemaps no longer add offline pages.
 * Changed: Improved MySqlI driver
 * Bugfix: SEO options correction
 * Bugfix: memory_limit checks when it is disabled
 * Changed: Improved media unsharp filter, static items, new extend "Color picker"
 * Bugfix: is_dir parameter of Event:fire of Filemanager.destroy.success method. Also respect the open_basedir restriction when deleting
 * Bugfix: File upload when memory_limit in php configured to have no limits
 * Bugfix: Corrected password validation and creation of new user
 * Added: Youtube reduced URL management in get_service_info()
 * Added: Content element links
 * Bugfix: Success and error messages of AJAX form
 * Bugfix: Date remove corrections
 * Added: Static items sortable
 * Bugfix: Role deletion
 * Added: Clean unused language depending settings to clean tables handling 
 * Added: Changelog
 
* Version 1.0.7 - Released in July 2014

* Version 1.0.6 - Released in April 2014

* Version 1.0.5 to 1.0.5.2 - Released in January 2014

* Version 1.0.4 - Released in July 2013

* Version 1.0.3 - Released in June 2013

* Version 1.0.0 to 1.0.2 - Released in May 2013

* Version 0.9.7 to 0.9.9.5 - Released form September 2011 to May 2013
  * Changed: Moved code versioning to git
