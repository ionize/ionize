MooTools FileManager
====================

A filemanager for the web based on MooTools that allows you to (pre)view, upload and modify files and folders via your browser.

![Screenshot](https://github.com/frozeman/mootools-filemanager/raw/master/screenshot.png)

### Authors

* [Christoph Pojer](http://cpojer.net)
* [Fabian Vogelsteller](http://frozeman.de)
* [Ger Hobbelt](http://hobbelt.com) (http://hebbut.net)

### Features

* Browse through Files and Folders on your Server
* Rename, Delete, Move (Drag&Drop), Copy (Drag + hold CTRL) and Download Files
* View detailed Previews of Images, Text-Files, Compressed-Files or Audio Content
* Nice User Interface ;)
* Upload Files via FancyUpload (integrated Feature)
* Option to automatically resize big Images when uploading
* Use it to select a File anywhere you need to specify one inside your Application's Backend
* Use as a FileManager in TinyMCE, Xinha or CKEditor
* Provides your client with the most possible convenience
* Create galleries using the Gallery-Plugin
* History and state management
* Backend PHP support for mod_alias/mod_vhost_alias/otherwise nonlinear mapped filesystems
* Auto-adjusts directory views, balancing performance and the amount of data shown, ensuring optimum user experience

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
* move_or_copy: (boolean, defaults to *false*) Whether to allow moving or copying files to other directories (parent directory or subdirectories)
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
* hideOnSelect: (boolean, defaults to *true*) If set to false, it leavers the FM open after you've clicked the select button, allowing for faster interaction when selecting multiple images.
* hideOverlay: (boolean, defaults to *false*) When true, hides the background overlay
* hideQonDelete: (boolean, defaults to *false*) When true, hides the Dialog asking 'are you sure' when you have clicked on any 'delete file/directory' button
* zIndex: (integer, defaults to *1000*) The z-index at which the file manager will be placed (CSS attribute). The overlay will be placed at (zIndex - 1), the highest used z-index offset is (zIndex + 3000)
* styles: (object, defaults to *{}*) Extra styles which will be assigned to the file manager <div>
* listPaginationSize: (integer, defaults to *100*) When non-zero, add pagination, i.e. split the view of huge directories into pages of N items each (this speeds up rendering and interaction)
* listPaginationAvgWaitTime: (integer, defaults to *2000*) When non-zero, enable adaptive pagination: strive to, on average, not to spend more than this number of milliseconds on rendering a directory view. This is a great help to adapt the view to match the power of your clients' machines.
* propagateData: (object, defaults to *empty*) Specify extra elements, all of which will be sent with every request to the backend
* verbose: (boolean, defaults to *false*) Whether the MTFM script should log developer info to the console
* standalone: (boolean, defaults to *true*) If set to false, returns the Filemanager without enclosing window / overlay.
* parentContainer: (string, defaults to *null*) ID of the parent container. If not set, FM will consider its first container parent for fitSizes()
* thumbSize4DirGallery: (integer, defaults to *120*) The thumbnail image size in pixels of any thumbnails shown in the directory gallery view (detail pane, i.e. right panel); you can reduce network traffic quite a bit by picking '48' as the thumbnail size as the file manager will pick the small (48) or large (250) thumbnails produced by the backend, depending on this configured size.
* mkServerRequestURL: (function, defaults to *null*) specify your own alternative URL/POST data constructor when you use a framework/system which requires such.   function(object: fm_obj, string: request_code, assoc.array: post_data)

Options if Uploader is included

* upload: (boolean, defaults to *true*)
* uploadAuthData: **DEPRECATED: anything you'd place here goes into 'propagateData' now!** (object, defaults to *empty*) Extra data to be send with the GET-Request of an Upload as Flash ignores authenticated clients
* resizeImages: (boolean, defaults to *true*) Whether to show the option to resize big images or not
* uploadTimeLimit: (integer, defaults to *260*) The maximum number of seconds any single upload may take. This is forwarded to the Swiff.Uploader as the 'timeLimit' setting.
* uploadFileSizeMax: (integer, defaults to *2600 * 2600 * 25*) The maximum number of bytes any single upload can be. This is forwarded to the Swiff.Uploader as the 'fileSizeMax' setting. Note that the backend option 'maxUploadSize' is the decisive factor; this is merely a user assist value.

Events

* onComplete(path, file, fm_obj): fired when a file gets selected via the "Select file" button. Note that 'path' is already HTML encoded for direct use in <img src=""> tags, etc., while the file object contains all available metadata including references to and sizes of both thumbnail sizes produced by the backend.
* onModify(file, json, mode, fm_obj): fired when a file gets renamed/deleted or modified in another way. 'mode' tells you which of these fired the event: mode = 'destroy', 'rename', 'move' or 'copy'.
* onShow(fm_obj): fired when the FileManager opens
* onHide(fm_obj): event fired when FileManager closes
* onPreview(src, fm_obj, img_el): event fired when the user clicks an image in the preview
* onHidePreview(fm_obj): event fired before a displayed detail view ('preview') is erased, to be replaced by another one
* onDetails(json, fm_obj): event fired when an item is picked form the files list, supplies object (e.g. {width: 123, height:456} )
* onHidePreview(): event fired when the preview is hidden (e.g. when uploading)
* onScroll(e, fm_obj): event fired before the file manager is resized/repositioned following a mouse scroll or viewport resize event

Backend

* See Assets/Connector/FileManager.php and Assets/Connector/FMgr4Alias.php for all available server-side options

* Note that you can configure these items by changing the related PHP define:

  - MTFM_THUMBNAIL_JPEG_QUALITY  (default: 80) the jpeg quality for the largest thumbnails (smaller ones are automatically done at increasingly higher quality to ensure they look good)

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
  
  You can also tweak/edit the already decoded request parameters in these callbacks: the backend will pick up on those changes and act accordingly. (This is for advanced usage needs.)



### Credits

Loosely based on a Script by [Yannick Croissant](http://dev.k1der.net/dev/brooser-un-browser-de-fichier-pour-mootools/)
