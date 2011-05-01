MooTools FileManager
====================

A filemanager for the web based on MooTools that allows you to (pre)view, upload and modify files and folders via your browser.

![Screenshot](https://github.com/frozeman/mootools-filemanager/raw/master/screenshot.png)

### Authors

* [Christoph Pojer](http://cpojer.net)
* [Fabian Vogelsteller](http://frozeman.de)
* [Ger Hobbelt](http://hobbelt.com / http://hebbut.net)

### Features

* Browse through Files and Folders on your Server
* Rename, Delete, Move (Drag&Drop), Copy (Drag + hold CTRL) and Download Files
* View detailed Previews of Images, Text-Files, Compressed-Files or Audio Content
* Nice User Interface ;)
* Upload Files via FancyUpload (integrated Feature)
* Option to automatically resize big Images when uploading
* Use it to select a File anywhere you need to specify one inside your Application's Backend
* Use as a FileManager in TinyMCE or CKEditor
* Provides your client with the most possible convenience
* Create galleries using the Gallery-Plugin
* History and state management
* Backend PHP support for mod_alias/mod_vhost_alias/otherwise nonlinear mapped filesystems
* Auto-adjusts directory views, balancing performance and the amount of data shown, unsuring optimum user experience

### Issues

  - sometimes "illegal character (Error #2038) mootools-core-1.3.js (line 5015)" when uploading multiple files

How to use
----------

### Demos

* Open the "Demos/" folder and have fun

### Installation

* First you need to include the follwing scripts
  * Source/FileManager.js
  * Source/Uploader/Fx.ProgressBar.js
  * Source/Uploader/Swiff.Uploader.js
  * Source/Uploader.js
  * Source/Gallery.js (if you want to create a gallery, see example in the Demos/index.html)
  * Language/Language.en.js (or the language(s) do you need)

* Then you need to modify the "Demos/manager.php" or "Demos/selectImage.php" to set up your upload folder etc
* See the "Demos/index.html" for examples, but basically you need to do the following:

      var myFileManager = new FileManager({
        url: 'path/to/the/manager.php',
        assetBasePath: '../Assets'
      });
      myFileManager.show();

### Configurable Options

Options

* url: (string) The base url to a file with an instance of the FileManager php class (FileManager.php), without QueryString
* assetBasePath: (string) The path to all images and swf files used by the filemanager
* directory: (string, relative to the directory set in to the filemanager php class) Can be used to load a subfolder instead of the base folder
* language: (string, defaults to *en*) The language used for the FileManager
* selectable: (boolean, defaults to *false*) If true, provides a button to select a file
* destroy: (boolean, defaults to *false*) Whether to allow deletion of files or not
* rename: (boolean, defaults to *false*) Whether to allow renaming of files or not
* download: (boolean, defaults to *false*) Whether to allow downloading of files or not
* createFolders: (boolean, defaults to *false*) Whether to allow creation of folders or not
* filter: (string) If specified, it reduces the shown and upload-able filetypes to these mimetypes. possible options are
  * image: *.jpg; *.jpeg; *.bmp; *.gif; *.png
  * video: *.avi; *.flv; *.fli; *.movie; *.mpe; *.qt; *.viv; *.mkv; *.vivo; *.mov; *.mpeg; *.mpg; *.wmv; *.mp4
  * audio: *.aif; *.aifc; *.aiff; *.aif; *.au; *.mka; *.kar; *.mid; *.midi; *.mp2; *.mp3; *.mpga; *.ra; *.ram; *.rm; *.rpm; *.snd; *.wav; *.tsi
  * text: *.txt; *.rtf; *.rtx; *.html; *.htm; *.css; *.as; *.xml; *.tpl
  * application: *.ai; *.bin; *.ccad; *.class; *.cpt; *.dir; *.dms; *.drw; *.doc; *.dvi; *.dwg; *.eps; *.exe; *.gtar; *.gz; *.js; *.latex; *.lnk; *.lnk; *.oda; *.odt; *.ods; *.odp; *.odg; *.odc; *.odf; *.odb; *.odi; *.odm; *.ott; *.ots; *.otp; *.otg; *.pdf; *.php; *.pot; *.pps; *.ppt; *.ppz; *.pre; *.ps; *.rar; *.set; *.sh; *.skd; *.skm; *.smi; *.smil; *.spl; *.src; *.stl; *.swf; *.tar; *.tex; *.texi; *.texinfo; *.tsp; *.unv; *.vcd; *.vda; *.xlc; *.xll; *.xlm; *.xls; *.xlw; *.zip;
* hideClose: (boolean, defaults to *false*) Whether to hide the close button in the right corner
* hideOnClick: (boolean, defaults to *false*) When true, hides the FileManager when the area outside of it is clicked
* hideOverlay: (boolean, defaults to *false*) When true, hides the background overlay
* hideQonDelete: (boolean, defaults to *false*) When true, hides the Dialog asking 'are you sure' when you have clicked on any 'delete file/directory' button
* listPaginationSize: (integer, defaults to *100*) When non-zero, add pagination, i.e. split the view of huge directories into pages of N items each (this speeds up rendering and interaction)
* listPaginationAvgWaitTime: (integer, defaults to *2000*) When non-zero, enable adaptive pagination: strive to, on average, not to spend more than this number of milliseconds on rendering a directory view. This is a great help to adapt the view to match the power of your clients' machines.
* propagateData: (object, defaults to *empty*) Specify extra elements, all of which will be sent with every request to the backend

Options if Uploader is included

* upload: (boolean, defaults to *true*)
* uploadAuthData: (object, defaults to *empty*) Extra data to be send with the GET-Request of an Upload as Flash ignores authenticated clients
* resizeImages: (boolean, defaults to *true*) Whether to show the option to resize big images or not

Events

* onComplete(path, file, legal_url, cur_dir, url): fired when a file gets selected via the "Select file" button
* onModify(file): fired when a file gets renamed/deleted or modified in another way
* onShow(): fired when the FileManager opens
* onHide(): event fired when FileManager closes
* onPreview(src): event fired when the user clicks an image in the preview
* onDetails(json): event fired when an item is picked form the files list, supplies object (e.g. {width: 123, height:456} )
* onHidePreview(): event fired when the preview is hidden (e.g. when uploading)

Backend

* See Assets/Connector/FileManager.php and Assets/Connector/FMgr4Alias.php for all available server-side options

* Note that you can configure these items by changing the related PHP define:

  - MTFM_THUMBNAIL_JPEG_QUALITY  (default: 75) the jpeg quality for the largest thumbnails (smaller ones are automatically done at increasingly higher quality to ensure they look good)

  - MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE  (default: 1) the number of directory levels in the thumbnail cache; set to 2 when you expect to handle huge image collections.  Note that each directory level distributes the files evenly across 256 directories; hence, you may set this level count to 2 when you expect to handle more than 32K images in total -- as each image will have two thumbnails: a 48px small one and a 250px large one.

### Custom Authentication and Authorization

* As Flash and therefore the Uploader ignores authenticated clients[*] you need to specify your own authentication / session initialization. This is taken care of by FileManager itself, so you don't need to bother, except provide a tiny bit of custom code in the "UploadIsAuthorized_cb" callback function on the serverside, manually initializing and starting your session-based authentication.

  [*] More specifically: Flash does not pass along any cookies of itself, hence the FileManager will place the cookies in the GET URI query section for extraction by the server.
  
* You may pass along additional (key, value) elements to the server during upload by adding those items in the 'uploadAuthData' options' section. These will all be passed along in the GET URL query section.

* Any (key, value) elements included in the "propagateData" options' section are sent to the server as part of every request URI (action) and will show up in the $_GET[] array, where you can extract them.

* FM now provides a server-side callback hook for each request so you can apply your own business logic to determine if a given (file or directory, user context) mix is indeed permitted to be viewed / detailed / thumbnailed / uploaded / created / deleted / moved / renamed / copied / downloaded.

  These hooks can be configured as part of the server-side options for the Backend/FileManager instance. For more info and a sample see the Demos/manager.php and Demos/selectImage.php files.

  Server-side authorization hooks:

  * UploadIsAuthorized_cb
  * DownloadIsAuthorized_cb
  * CreateIsAuthorized_cb
  * DestroyIsAuthorized_cb
  * MoveIsAuthorized_cb
  * ViewIsAuthorized_cb
  * DetailIsAuthorized_cb
  * ThumbnailIsAuthorized_cb



### Credits

Loosely based on a Script by [Yannick Croissant](http://dev.k1der.net/dev/brooser-un-browser-de-fichier-pour-mootools/)
