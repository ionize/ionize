<?php
/*
 * Script: FileManager.php
 *   MooTools FileManager - Backend for the FileManager Script
 *
 * Authors:
 *  - Christoph Pojer (http://cpojer.net) (author)
 *  - James Ehly (http://www.devtrench.com)
 *  - Fabian Vogelsteller (http://frozeman.de)
 *  - Ger Hobbelt (http://hebbut.net)
 *  - James Sleeman (http://code.gogo.co.nz)
 *
 * License:
 *   MIT-style license.
 *
 * Copyright:
 *   Copyright (c) 2009-2011 [Christoph Pojer](http://cpojer.net)
 *   Backend: FileManager & FMgr4Alias Copyright (c) 2011 [Ger Hobbelt](http://hobbelt.com)
 *
 * Dependencies:
 *   - Tooling.php
 *   - Image.class.php
 *   - getId3 Library
 *
 * Options:
 *   - directory: (string) The URI base directory to be used for the FileManager ('URI path' i.e. an absolute path here would be rooted at DocumentRoot: '/' == DocumentRoot)
 *   - assetBasePath: (string, optional) The URI path to all images and swf files used by the filemanager
 *   - thumbnailPath: (string) The URI path where the thumbnails of the pictures will be saved
 *   - mimeTypesPath: (string, optional) The filesystem path to the MimeTypes.ini file. May exist in a place outside the DocumentRoot tree.
 *   - dateFormat: (string, defaults to *j M Y - H:i*) The format in which dates should be displayed
 *   - maxUploadSize: (integer, defaults to *20280000* bytes) The maximum file size for upload in bytes
 *   - maxImageDimension: (array, defaults to *array('width' => 1024, 'height' => 768)*) The maximum number of pixels in height and width an image can have, if the user enables "resize on upload".
 *   - upload: (boolean, defaults to *false*) allow uploads, this is also set in the FileManager.js (this here is only for security protection when uploads should be deactivated)
 *   - destroy: (boolean, defaults to *false*) allow files to get deleted, this is also set in the FileManager.js (this here is only for security protection when file/directory delete operations should be deactivated)
 *   - create: (boolean, defaults to *false*) allow creating new subdirectories, this is also set in the FileManager.js (this here is only for security protection when dir creates should be deactivated)
 *   - move: (boolean, defaults to *false*) allow file and directory move/rename and copy, this is also set in the FileManager.js (this here is only for security protection when rename/move/copy should be deactivated)
 *   - download: (boolean, defaults to *false*) allow downloads, this is also set in the FileManager.js (this here is only for security protection when downloads should be deactivated)
 *   - allowExtChange: (boolean, defaults to *false*) allow the file extension to be changed when performing a rename operation.
 *   - safe: (boolean, defaults to *true*) If true, disallows 'exe', 'dll', 'php', 'php3', 'php4', 'php5', 'phps' and saves them as 'txt' instead.
 *   - chmod: (integer, default is 0777) the permissions set to the uploaded files and created thumbnails (must have a leading "0", e.g. 0777)
 *   - filter: (string, defaults to *null*) If not empty, this is a list of allowed mimetypes (overruled by the GET request 'filter' parameter: single requests can thus overrule the common setup in the constructor for this option)
 *   - showHiddenFoldersAndFiles: (boolean, defaults to *false*) whether or not to show 'dotted' directories and files -- such files are considered 'hidden' on UNIX file systems
 *   - ViewIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given directory may be viewed.
 *     The parameter $action = 'view'.
 *   - DetailIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given file may be inspected (and the details listed).
 *     The parameter $action = 'detail'.
 *   - ThumbnailIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether a thumbnail of the given file may be shown.
 *     The parameter $action = 'thumbnail'.
 *   - UploadIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given file may be uploaded.
 *     The parameter $action = 'upload'.
 *   - DownloadIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given file may be downloaded.
 *     The parameter $action = 'download'.
 *   - CreateIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given subdirectory may be created.
 *     The parameter $action = 'create'.
 *   - DestroyIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given file / subdirectory tree may be deleted.
 *     The parameter $action = 'destroy'.
 *   - MoveIsAuthorized_cb (function/reference, default is *null*) authentication + authorization callback which can be used to determine whether the given file / subdirectory may be renamed, moved or copied.
 *     Note that currently support for copying subdirectories is missing.
 *     The parameter $action = 'move'.
 *   - URIpropagateData (array, default is *null*) the data elements which will be passed along as part of the generated request URIs, i.e. the thumbnail request URIs. Use this to pass custom data elements to the
 *     handler which delivers the thumbnails to the front-end.
 *
 * Obsoleted options:
 *   - maxImageSize: (integer, default is 1024) The maximum number of pixels in both height and width an image can have, if the user enables "resize on upload". (This option is obsoleted by the 'suggestedMaxImageDimension' option.)
 *
 *
 * About the action permissions (upload|destroy|create|move|download):
 *
 *     All the option "permissions" are set to FALSE by default. Developers should always SPECIFICALLY enable a permission to have that permission, for two reasons:
 *
 *     1. Developers forget to disable permissions, they don't forget to enable them (because things don't work!)
 *
 *     2. Having open permissions by default leaves potential for security vulnerabilities where those open permissions are exploited.
 *
 *
 * For all authorization hooks (callback functions) the following applies:
 *
 *     The callback should return TRUE for yes (permission granted), FALSE for no (permission denied).
 *     Parameters sent to the callback are:
 *       ($this, $action, $fileinfo)
 *     where $fileinfo is an array containing info about the file being uploaded, $action is a (string) identifying the current operation, $this is a reference to this FileManager instance.
 *     $action was included as a redundant parameter to each callback as a simple means to allow users to hook a single callback function to all the authorization hooks, without the need to create a wrapper function for each.
 *
 *     For more info about the hook parameter $fileinfo contents and a basic implementation, see further below (section 'Hooks: Detailed Interface Specification') and the examples in
 *     Demos/FM-common.php, Demos/manager.php and Demos/selectImage.php
 *
 *
 * Notes on relative paths and safety / security:
 *
 *   If any option is specifying a relative path, e.g. '../Assets' or 'Media/Stuff/', this is assumed to be relative to the request URI path,
 *   i.e. dirname($_SERVER['SCRIPT_NAME']).
 *
 *   Requests may post/submit relative paths as arguments to their FileManager events/actions in $_GET/$_POST, and those relative paths will be
 *   regarded as relative to the request URI handling script path, i.e. dirname($_SERVER['SCRIPT_NAME']) to make the most
 *   sense from bother server and client coding perspective.
 *
 *
 *   We also assume that any of the paths may be specified from the outside, so each path is processed and filtered to prevent malicious intent
 *   from succeeding. (An example of such would be an attacker posting his own 'destroy' event request requesting the destruction of
 *   '../../../../../../../../../etc/passwd' for example. In more complex rigs, the attack may be assisted through attacks at these options' paths,
 *   so these are subjected to the same scrutiny in here.)
 *
 *   All paths, absolute or relative, as passed to the event handlers (see the onXXX methods of this class) are ENFORCED TO ABIDE THE RULE
 *   'every path resides within the options['directory'] a.k.a. BASEDIR rooted tree' without exception.
 *   Because we can do without exceptions to important rules. ;-)
 *
 *   When paths apparently don't, they are coerced into adherence to this rule; when this fails, an exception is thrown internally and an error
 *   will be reported and the action temrinated.
 *
 *  'LEGAL URL paths':
 *
 *   Paths which adhere to the aforementioned rule are so-called LEGAL URL paths; their 'root' equals BASEDIR.
 *
 *   BASEDIR equals the path pointed at by the options['directory'] setting. It is therefore imperative that you ensure this value is
 *   correctly set up; worst case, this setting will equal DocumentRoot.
 *   In other words: you'll never be able to reach any file or directory outside this site's DocumentRoot directory tree, ever.
 *
 *
 *  Path transformations:
 *
 *   To allow arbitrary directory/path mapping algorithms to be applied (e.g. when implementing Alias support such as available in the
 *   derived class FileManagerWithAliasSupport), all paths are, on every change/edit, transformed from their LEGAL URL representation to
 *   their 'absolute URI path' (which is suitable to be used in links and references in HTML output) and 'absolute physical filesystem path'
 *   equivalents.
 *   By enforcing such a unidirectional transformation we implicitly support non-reversible and hard-to-reverse path aliasing mechanisms,
 *   e.g. complex regex+context based path manipulations in the server.
 *
 *
 *   When you need your paths to be restricted to the bounds of the options['directory'] tree (which is a subtree of the DocumentRoot based
 *   tree), you may wish to use the 'legal' class of path transformation member functions:
 *
 *   - legal2abs_url_path()
 *   - rel2abs_legal_url_path()
 *   - legal_url_path2file_path()
 *
 *   When you have a 'absolute URI path' or a path relative in URI space (implicitly relative to dirname($_SERVER['SCRIPT_NAME']) ), you can
 *   transform such a path to either a guaranteed-absolute URI space path or a filesystem path:
 *
 *   - rel2abs_url_path()
 *   - url_path2file_path()
 *
 *   Any other path transformations are ILLEGAL and DANGEROUS. The only other possibly legal transformation is from absolute URI path to
 *   BASEDIR-based LEGAL URL path, as the URI path space is assumed to be linear and contiguous. However, this operation is HIGHLY discouraged
 *   as it is a very strong indicator of other faulty logic, so we do NOT offer a method for this.
 *
 *
 * Hooks: Detailed Interface Specification:
 *
 *   All 'authorization' callback hooks share a common interface specification (function parameter set). This is by design, so one callback
 *   function can be used to process any and all of these events:
 *
 *   Function prototype:
 *
 *       function CallbackFunction($mgr, $action, &$info)
 *
 *   where
 *
 *       $msg:      (object) reference to the current FileManager class instance. Can be used to invoke public FileManager methods inside
 *                  the callback.
 *
 *       $action:   (string) identifies the event being processed. Can be one of these:
 *
 *                  'create'          create new directory
 *                  'move'            move or copy a file or directory
 *                  'destroy'         delete a file or directory
 *                  'upload'          upload a single file (when performing a bulk upload, each file will be uploaded individually)
 *                  'download'        download a file
 *                  'view'            show a directory listing (in either 'list' or 'thumb' mode)
 *                  'detail'          show detailed information about the file and, whn possible, provide a link to a (largish) thumbnail
 *                  'thumbnail'       send the thumbnail to the client (done this way to allow JiT thumbnail creation)
 *
 *       $info      (array) carries all the details. Some of which can even be manipulated if your callbac is more than just an
 *                  authentication / authorization checker. ;-)
 *                  For more detail, see the next major section.
 *
 *   The callback should return a boolean, where TRUE means the session/client is authorized to execute the action, while FALSE
 *   will cause the backend to report an authentication error and abort the action.
 *
 *  Exceptions throwing from the callback:
 *
 *   Note that you may choose to throw exceptions from inside the callback; those will be caught and transformed to proper error reports.
 *
 *   You may either throw any exceptions based on either the FileManagerException or Exception classes. When you format the exception
 *   message as "XYZ:data", where 'XYZ' is a alphanumeric-only word, this will be transformed to a i18n-support string, where
 *   'backend.XYZ' must map to a translation string (e.g. 'backend.nofile', see also the Language/Language.XX.js files) and the optional
 *   'data' tail will be appended to the translated message.
 *
 *
 * $info: the details:
 *
 *   Here is the list of $info members per $action event code:
 *
 *   'upload':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the directory where the file is being uploaded. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given directory.
 *
 *               'dir'                   (string) physical filesystem path to the directory where the file is being uploaded.
 *
 *               'raw_filename'          (string) the raw, unprocessed filename of the file being being uploaded, as specified by the client.
 *
 *                                       WARNING: 'raw_filename' may contain anything illegal, such as directory paths instead of just a filename,
 *                                                filesystem-illegal characters and what-not. Use 'name'+'extension' instead if you want to know
 *                                                where the upload will end up.
 *
 *               'name'                  (string) the filename, sans extension, of the file being uploaded; this filename is ensured
 *                                       to be both filesystem-legal, unique and not yet existing in the given directory.
 *
 *               'extension'             (string) the filename extension of the file being uploaded; this extension is ensured
 *                                       to be filesystem-legal.
 *
 *                                       Note that the file name extension has already been cleaned, including 'safe' mode processing,
 *                                       i.e. any uploaded binary executable will have been assigned the extension '.txt' already, when
 *                                       FileManager's options['safe'] is enabled.
 *
 *               'tmp_filepath'          (string) filesystem path pointing at the temporary storage location of the uploaded file: you can
 *                                       access the file data available here to optionally validate the uploaded content.
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime'                  (string) the mime type as sniffed from the file
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *               'size'                  (integer) number of bytes of the uploaded file
 *
 *               'maxsize'               (integer) the configured maximum number of bytes for any single upload
 *
 *               'overwrite'             (boolean) FALSE: the uploaded file will not overwrite any existing file, it will fail instead.
 *
 *                                       Set to TRUE (and adjust the 'name' and 'extension' entries as you desire) when you wish to overwrite
 *                                       an existing file.
 *
 *               'chmod'                 (integer) UNIX access rights (default: 0666) for the file-to-be-created (RW for user,group,world).
 *
 *                                       Note that the eXecutable bits have already been stripped before the callback was invoked.
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular upload
 *                                       operation, when the ['status']==0, we are performing a defective upload operation.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *
 *         Note that this request originates from a Macromedia Flash client: hence you'll need to use the
 *         $_POST[session_name()] value to manually set the PHP session_id() before you start your your session
 *         again.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *         The frontend-specified options.uploadAuthData items will be available as $_POST[] items.
 *
 *
 *  'download':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the file to be downloaded. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'file' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given file.
 *
 *               'file'                  (string) physical filesystem path to the file being downloaded.
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime'                  (string) the mime type as sniffed from the file
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'create': // create directory
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the parent directory of the directory being created. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for this parent directory.
 *
 *               'dir'                   (string) physical filesystem path to the parent directory of the directory being created.
 *
 *               'raw_name'              (string) the name of the directory to be created, as specified by the client (unfiltered!)
 *
 *               'uniq_name'             (string) the name of the directory to be created, filtered and ensured to be both unique and
 *                                       not-yet-existing in the filesystem.
 *
 *               'newdir'                (string) the filesystem absolute path to the directory to be created; identical to:
 *                                           $newdir = $mgr->legal_url_path2file_path($legal_url . $uniq_name);
 *                                       Note the above: all paths are transformed from URI space to physical disk every time a change occurs;
 *                                       this allows us to map even not-existing 'directories' to possibly disparate filesystem locations.
 *
 *               'chmod'                 (integer) UNIX access rights (default: 0777) for the directory-to-be-created (RWX for user,group,world)
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular 'create'
 *                                       operation, when the ['status']==0, we are performing a defective 'create' operation.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'destroy':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the file/directory to be deleted. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'file' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given file/directory.
 *
 *               'file'                  (string) physical filesystem path to the file/directory being deleted.
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime'                  (string) the mime type as sniffed from the file / directory (directories are mime type: 'text/directory')
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *                                       Note that the 'mime_filters', if any, are applied to the 'delete' operation in a special way: only
 *                                       files matching one of the mime types in this list will be deleted; anything else will remain intact.
 *                                       This can be used to selectively clean a directory tree.
 *
 *                                       The design idea behind this approach is that you are only allowed what you can see ('view'), so
 *                                       all 'view' restrictions should equally to the 'delete' operation.
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular 'destroy'
 *                                       operation, when the ['status']==0, we are performing a defective 'destroy' operation.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'move':  // move or copy!
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the source parent directory of the file/directory being moved/copied. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given directory.
 *
 *               'dir'                   (string) physical filesystem path to the source parent directory of the file/directory being moved/copied.
 *
 *               'path'                  (string) physical filesystem path to the file/directory being moved/copied itself; this is the full source path.
 *
 *               'name'                  (string) the name itself of the file/directory being moved/copied; this is the source name.
 *
 *               'legal_newurl'          (string) LEGAL URI path to the target parent directory of the file/directory being moved/copied. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given directory.
 *
 *               'newdir'                (string) physical filesystem path to the target parent directory of the file/directory being moved/copied;
 *                                       this is the full path of the directory where the file/directory will be moved/copied to. (filesystem absolute)
 *
 *               'newpath'               (string) physical filesystem path to the target file/directory being moved/copied itself; this is the full destination path,
 *                                       i.e. the full path of where the file/directory should be renamed/moved to. (filesystem absolute)
 *
 *               'newname'               (string) the target name itself of the file/directory being moved/copied; this is the destination name.
 *
 *                                       This filename is ensured to be both filesystem-legal, unique and not yet existing in the given target directory.
 *
 *               'rename'                (boolean) TRUE when a file/directory RENAME operation is requested (name change, staying within the same
 *                                       parent directory). FALSE otherwise.
 *
 *               'is_dir'                (boolean) TRUE when the subject is a directory itself, FALSE when it is a regular file.
 *
 *               'function'              (string) PHP call which will perform the operation. ('rename' or 'copy')
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular 'move'
 *                                       operation, when the ['status']==0, we are performing a defective 'move' operation.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'view':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the directory being viewed/scanned. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the scanned directory.
 *
 *               'dir'                   (string) physical filesystem path to the directory being viewed/scanned.
 *
 *               'collection'            (dual array of strings) arrays of files and directories (including '..' entry at the top when this is a
 *                                       subdirectory of the FM-managed tree): only names, not full paths. The files array is located at the
 *                                       ['files'] index, while the directories are available at the ['dirs'] index.
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *               'guess_mime'            (boolean) TRUE when the mime type for each file in this directory will be determined using filename
 *                                       extension sniffing only; FALSE means the mime type will be determined using content sniffing, which
 *                                       is slower.
 *
 *               'list_type'             (string) the type of view requested: 'list' or 'thumb'.
 *
 *               'file_preselect'        (optional, string) filename of a file in this directory which should be located and selected.
 *                                       When found, the backend will provide an index number pointing at the corresponding JSON files[]
 *                                       entry to assist the front-end in jumping to that particular item in the view.
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular view
 *                                       operation (possibly as the second half of a copy/move/delete operation), when the ['status']==0,
 *                                       we are performing a view operation as the second part of another otherwise failed action, e.g. a
 *                                       failed 'create directory'.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'detail':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the file/directory being inspected. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'file' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given file.
 *
 *               'file'                  (string) physical filesystem path to the file being inspected.
 *
 *               'filename'              (string) the filename of the file being inspected. (Identical to 'basename($legal_url)')
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime'                  (string) the mime type as sniffed from the file
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular 'detail'
 *                                       operation, when the ['status']==0, we are performing a defective 'detail' operation.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *  'thumbnail':
 *
 *           $info[] contains:
 *
 *               'legal_url'             (string) LEGAL URI path to the file/directory being thumbnailed. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_url);
 *                                       to obtain the physical filesystem path (also available in the 'file' $info entry, by the way!), or
 *                                           $url = $mgr->legal2abs_url_path($legal_url);
 *                                       to obtain the absolute URI path for the given file.
 *
 *               'file'                  (string) physical filesystem path to the file being inspected.
 *
 *               'filename'              (string) the filename of the file being inspected. (Identical to 'basename($legal_url)')
 *
 *               'meta_data'             (array) the content sniffed infor as produced by getID3
 *
 *               'mime'                  (string) the mime type as sniffed from the file
 *
 *               'mime_filter'           (optional, string) mime filter as specified by the client: a comma-separated string containing
 *                                       full or partial mime types, where a 'partial' mime types is the part of a mime type before
 *                                       and including the slash, e.g. 'image/'
 *
 *               'mime_filters'          (optional, array of strings) the set of allowed mime types, derived from the 'mime_filter' setting.
 *
 *               'requested_size'        (integer) the size (maximum width and height) in pixels of the thumbnail to be produced.
 *
 *               'mode'                  (string) 'image' (default): produce the thumbnail binary image data itself. 'json': return a JSON
 *                                       response listing the URL to the actual thumbnail image.
 *
 *               'preliminary_json'      (array) the JSON data collected so far; when ['status']==1, then we're performing a regular 'thumbnail'
 *                                       operation, when the ['status']==0, we are performing a defective 'thumbnail' operation.
 *                                       Note that it is pretty useless to edit this entry when $info['mode'] != 'json', i.e. when this call
 *                                       is required to produce a binary image.
 *
 *               'validation_failure'    (string) NULL: no validation error has been detected before the callback was invoked; non-NULL, e.g.
 *                                       "nofile": the string passed as message parameter of the FileManagerException, which will be thrown
 *                                       after the callback has returned. (You may alter the 'validation_failure' string value to change the
 *                                       reported error, or set it to NULL to turn off the validation error report entirely -- we assume you
 *                                       will have corrected the other fileinfo[] items as well, when resetting the validation error.
 *
 *         The frontend-specified options.propagateData items will be available as $_GET[] or $_POST[] items, depending on the frontend
 *         options.propagateType setting.
 *
 *
 *
 * Developer Notes:
 *
 * - member functions which have a commented out 'static' keyword have it removed by design: it makes for easier overloading through
 *   inheritance that way and meanwhile there's no pressing need to have those (public) member functions acccessible from the outside world
 *   without having an instance of the FileManager class itself round at the same time.
 */

// ----------- compatibility checks ----------------------------------------------------------------------------
if (version_compare(PHP_VERSION, '5.2.0') < 0)
{
	// die horribly: server does not match our requirements!
	header('HTTP/1.0 500 FileManager requires PHP 5.2.0 or later', true, 500); // Internal server error
	throw Exception('FileManager requires PHP 5.2.0 or later');   // this exception will most probably not be caught; that's our intent!
}

if (function_exists('UploadIsAuthenticated'))
{
	// die horribly: user has not upgraded his callback hook(s)!
	header('HTTP/1.0 500 FileManager callback has not been upgraded!', true, 500); // Internal server error
	throw Exception('FileManager callback has not been upgraded!');   // this exception will most probably not be caught; that's our intent!
}

//-------------------------------------------------------------------------------------------------------------

if (!defined('DEVELOPMENT')) define('DEVELOPMENT', 0);   // make sure this #define is always known to us


/*
require(strtr(dirname(__FILE__), '\\', '/') . '/Tooling.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/Image.class.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/Assets/getid3/getid3.php');
*/
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Tooling.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/Image.class.php');
require(strtr(dirname(__FILE__), '\\', '/') . '/getid3/getid3.php');



// the jpeg quality for the largest thumbnails (smaller ones are automatically done at increasingly higher quality)
define('MTFM_THUMBNAIL_JPEG_QUALITY', 80);

// the number of directory levels in the thumbnail cache; set to 2 when you expect to handle huge image collections.
//
// Note that each directory level distributes the files evenly across 256 directories; hence, you may set this
// level count to 2 when you expect to handle more than 32K images in total -- as each image will have two thumbnails:
// a 48px small one and a 250px large one.
define('MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE', 1);

// minimum number of cached getID3 results; cache is automatically pruned
define('MTFM_MIN_GETID3_CACHESIZE', 16);





// flags for clean_ID3info_results()
define('MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES', 0x0001);




class FileManager
{
	protected $options;
	protected $getid3;
	protected $getid3_cache;
	protected $getid3_cache_lru_ts;
	protected $icon_cache;

	public function __construct($options)
	{
		$this->options = array_merge(array(
			/*
			 * Note that all default paths as listed below are transformed to DocumentRoot-based paths
			 * through the getRealPath() invocations further below:
			 */
			'directory' => null,                                       // MUST be in the DocumentRoot tree
			'assetBasePath' => null,                                   // may sit outside options['directory'] but MUST be in the DocumentRoot tree
			'thumbnailPath' => null,                                   // may sit outside options['directory'] but MUST be in the DocumentRoot tree
			'mimeTypesPath' => strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/MimeTypes.ini',   // an absolute filesystem path anywhere; when relative, it will be assumed to be against SERVER['SCRIPT_NAME']
			'dateFormat' => 'j M Y - H:i',
			'maxUploadSize' => 2600 * 2600 * 3,
			// 'maxImageSize' => 99999,                                 // obsoleted, replaced by 'suggestedMaxImageDimension'
			// Xinha: Allow to specify the "Resize Large Images" tolerance level.
			'maxImageDimension' => array('width' => 1024, 'height' => 768),
			'upload' => false,
			'destroy' => false,
			'create' => false,
			'move' => false,
			'download' => false,
			/* ^^^ this last one is easily circumnavigated if it's about images: when you can view 'em, you can 'download' them anyway.
			 *     However, for other mime types which are not previewable / viewable 'in their full bluntal nugity' ;-) , this will
			 *     be a strong deterent.
			 *
			 *     Think Springer Verlag and PDFs, for instance. You can have 'em, but only /after/ you've ...
			 */
			'allowExtChange' => false,
			'safe' => true,
			'filter' => null,
			'chmod' => 0777,
			'ViewIsAuthorized_cb' => null,
			'DetailIsAuthorized_cb' => null,
			'ThumbnailIsAuthorized_cb' => null,
			'UploadIsAuthorized_cb' => null,
			'DownloadIsAuthorized_cb' => null,
			'CreateIsAuthorized_cb' => null,
			'DestroyIsAuthorized_cb' => null,
			'MoveIsAuthorized_cb' => null,
			'showHiddenFoldersAndFiles' => false,      // Hide dot dirs/files ?
			'URIpropagateData' => null
		), (is_array($options) ? $options : array()));

		// transform the obsoleted/deprecated options:
		if (!empty($this->options['maxImageSize']) && $this->options['maxImageSize'] != 1024 && $this->options['maxImageDimension']['width'] == 1024 && $this->options['maxImageDimension']['height'] == 768)
		{
			$this->options['maxImageDimension'] = array('width' => $this->options['maxImageSize'], 'height' => $this->options['maxImageSize']);
		}

		$assumed_root = @realpath($_SERVER['DOCUMENT_ROOT']);
		$assumed_root = strtr($assumed_root, '\\', '/');
		$assumed_root = rtrim($assumed_root, '/');
		$this->options['assumed_root_filepath'] = $assumed_root;

		// only calculate the guestimated defaults when they are indeed required:
		if ($this->options['directory'] == null || $this->options['assetBasePath'] == null || $this->options['thumbnailPath'] == null)
		{
			$my_path = @realpath(dirname(__FILE__));
			$my_path = strtr($my_path, '\\', '/');
			if (!FileManagerUtility::endsWith($my_path, '/'))
			{
				$my_path .= '/';
			}
			$my_assumed_url_path = str_replace($assumed_root, '', $my_path);

			// we throw an Exception here because when these do not apply, the user should have specified all three these entries!
			if (empty($assumed_root) || empty($my_path) || !FileManagerUtility::startsWith($my_path, $assumed_root))
			{
				//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);
				throw new FileManagerException('nofile');
			}

			if ($this->options['directory'] == null)
			{
				$this->options['directory'] = $my_assumed_url_path . '../../Demos/Files/';
			}
			if ($this->options['assetBasePath'] == null)
			{
				$this->options['assetBasePath'] = $my_assumed_url_path . '../../Demos/Files/../../Assets/';
			}
			if ($this->options['thumbnailPath'] == null)
			{
				$this->options['thumbnailPath'] = $my_assumed_url_path . '../../Demos/Files/../../Assets/Thumbs/';
			}
		}

		/*
		 * make sure we start with a very predictable and LEGAL options['directory'] setting, so that the checks applied to the
		 * (possibly) user specified value for this bugger acvtually can check out okay AS LONG AS IT'S INSIDE the DocumentRoot-based
		 * directory tree:
		 */
		$new_root = $this->options['directory'];
		$this->options['directory'] = '/';      // use DocumentRoot temporarily as THE root for this optional transform
		$this->options['directory'] = self::enforceTrailingSlash($this->rel2abs_url_path($new_root));

		$this->options['assumed_base_filepath'] = $this->url_path2file_path($this->options['directory']);

		// now that the correct options['directory'] has been set up, go and check/clean the other paths in the options[]:

		$this->options['thumbnailPath'] = self::enforceTrailingSlash($this->rel2abs_url_path($this->options['thumbnailPath']));
		$this->options['assetBasePath'] = self::enforceTrailingSlash($this->rel2abs_url_path($this->options['assetBasePath']));

		$this->options['mimeTypesPath'] = @realpath($this->options['mimeTypesPath']);
		if (empty($this->options['mimeTypesPath']))
		{
			//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);
			throw new FileManagerException('nofile');
		}
		$this->options['mimeTypesPath'] = strtr($this->options['mimeTypesPath'], '\\', '/');

		// getID3 is slower as it *copies* the image to the temp dir before processing: see GetDataImageSize().
		// This is done as getID3 can also analyze *embedded* images, for which this approach is required.
		$this->getid3 = new getID3();
		$this->getid3->setOption(array('encoding' => 'UTF-8'));
		//$this->getid3->encoding = 'UTF-8';

		// getid3_cache stores the info arrays; gitid3_cache_lru_ts stores a 'timestamp' counter to track LRU: 'timestamps' older than threshold are discarded when cache is full
		$this->getid3_cache = array();
		$this->getid3_cache_lru_ts = 0;

		$this->icon_cache = array(array(), array());

		if (!headers_sent())
		{
			header('Expires: Fri, 01 Jan 1990 00:00:00 GMT');
			header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		}
	}

	/**
	 * @return array the FileManager options and settings.
	 */
	public function getSettings()
	{
		return array_merge(array(
				'basedir' => $this->url_path2file_path($this->options['directory'])
		), $this->options);
	}




	/**
	 * Central entry point for any client side request.
	 */
	public function fireEvent($event = null)
	{
		$event = (!empty($event) ? 'on' . ucfirst($event) : null);
		if (!$event || !method_exists($this, $event)) $event = 'onView';

		$this->{$event}();
	}






	/**
	 * Generalized 'view' handler, which produces a directory listing.
	 *
	 * Return the directory listing in a nested array, suitable for JSON encoding.
	 */
	protected function _onView($legal_url, $json, $mime_filter, $list_type, $file_preselect_arg = null, $filemask = '*')
	{
		$v_ex_code = 'nofile';

		$dir = $this->legal_url_path2file_path($legal_url);
		$doubledot = null;
		$coll = null;
		if (is_dir($dir))
		{
			$coll = $this->scandir($dir, $filemask, false, 0, ($this->options['showHiddenFoldersAndFiles'] ? ~GLOB_NOHIDDEN : ~0));
			if ($coll !== false)
			{
				/*
				 * To ensure '..' ends up at the very top of the view, no matter what the other entries in $coll['dirs'][] are made of,
				 * we pop the last element off the array, check whether it's the double-dot, and if so, keep it out while we
				 * let the sort run.
				 */
				$doubledot = array_pop($coll['dirs']);
				if ($doubledot !== null && $doubledot !== '..')
				{
					$coll['dirs'][] = $doubledot;
					$doubledot = null;
				}
				natcasesort($coll['dirs']);
				natcasesort($coll['files']);

				$v_ex_code = null;
			}
		}
		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);

		$mime_filters = $this->getAllowedMimeTypes($mime_filter);

		// remove the imageinfo() call overhead per file for very large directories; just guess at the mimetye from the filename alone.
		// The real mimetype will show up in the 'details' view anyway! This is only for the 'filter' function:
		$just_guess_mime = true; // (count($coll['files']) + count($coll['dirs']) > 100);

		$fileinfo = array(
				'legal_url' => $legal_url,
				'dir' => $dir,
				'collection' => $coll,
				'mime_filter' => $mime_filter,
				'mime_filters' => $mime_filters,
				'guess_mime' => $just_guess_mime,
				'list_type' => $list_type,
				'file_preselect' => $file_preselect_arg,
				'preliminary_json' => $json,
				'validation_failure' => $v_ex_code
			);

		if (!empty($this->options['ViewIsAuthorized_cb']) && function_exists($this->options['ViewIsAuthorized_cb']) && !$this->options['ViewIsAuthorized_cb']($this, 'view', $fileinfo))
		{
			$v_ex_code = $fileinfo['validation_failure'];
			if (empty($v_ex_code)) $v_ex_code = 'authorized';
		}
		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__, $fileinfo);
		if (!empty($v_ex_code))
			throw new FileManagerException($v_ex_code);

		$legal_url = $fileinfo['legal_url'];
		$dir = $fileinfo['dir'];
		$coll = $fileinfo['collection'];
		$mime_filter = $fileinfo['mime_filter'];
		$mime_filters = $fileinfo['mime_filters'];
		$just_guess_mime = $fileinfo['guess_mime'];
		$list_type = $fileinfo['list_type'];
		$file_preselect_arg = $fileinfo['file_preselect'];
		$json = $fileinfo['preliminary_json'];

		$file_preselect_index = -1;
		$out = array(array(), array());

		$mime = 'text/directory';
		$iconspec = false;
		$thumb = null;
		$thumb48 = null;
		$icon = null;

		if ($doubledot !== null)
		{
			$filename = '..';

			$url = $legal_url . $filename;

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$file = $this->legal_url_path2file_path($url);

			$iconspec = 'is.dir_up';

			$thumb48 = $this->getIcon($iconspec, false);
			$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);

			$icon = $this->getIcon($iconspec, true);
			$icon_e = FileManagerUtility::rawurlencode_path($icon);

			if ($list_type === 'thumb')
			{
				$thumb_e = $thumb48_e;
			}
			else
			{
				$thumb_e = $icon_e;
			}

			$url_p = FileManagerUtility::rawurlencode_path($url);

			$out[1][] = array(
					'path' => $url_p,
					'name' => $filename,
					//'date' => date($this->options['dateFormat'], @filemtime($file)),
					'mime' => $mime,
					'thumbnail' => $thumb_e,
					'thumb48' => $thumb48_e,
					//'size' => @filesize($file),
					'icon' => $icon_e
				);
		}

		// now precalc the directory-common items (a.k.a. invariant computation / common subexpression hoisting)
		$iconspec_d = 'is.dir';

		$thumb48_d = $this->getIcon($iconspec_d, false);
		$thumb48_de = FileManagerUtility::rawurlencode_path($thumb48_d);

		$icon_d = $this->getIcon($iconspec_d, true);
		$icon_de = FileManagerUtility::rawurlencode_path($icon_d);

		if ($list_type === 'thumb')
		{
			$thumb_de = $thumb48_de;
		}
		else
		{
			$thumb_de = $icon_de;
		}

		foreach ($coll['dirs'] as $filename)
		{
			$url = $legal_url . $filename;

			$url_p = FileManagerUtility::rawurlencode_path($url);

			$out[1][] = array(
					'path' => $url_p,
					'name' => $filename,
					//'date' => date($this->options['dateFormat'], @filemtime($file)),
					'mime' => $mime,
					'thumbnail' => $thumb_de,
					'thumb48' => $thumb48_de,
					//'size' => @filesize($file),
					'icon' => $icon_de
				);
		}

		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);

		/*
		 * ... and another bit of invariant computation: this time it's a bit more complex, but the mkEventHandlerURL() call is rather costly,
		 * so we do that one as a 'template' and str_replace() -- which is fast -- the template variables in there:
		 */
		$thumb_tpl = $this->mkEventHandlerURL(array(
				'event' => 'thumbnail',
				// directory and filename of the ORIGINAL image should follow next:
				'directory' => $legal_url,
				'file' => '..F..',
				'size' => '..S..',          // thumbnail suitable for 'view/type=thumb' list views
				'filter' => $mime_filter
			));
		$thumb_tpl48 = str_replace('..S..', '48', $thumb_tpl);

		$idx = 0;
		//$next_reqd_mapping_idx = array_pop($coll['special_indir_mappings'][1]);

		foreach ($coll['files'] as $filename)
		{
			$url = $legal_url . $filename;

			// no need to transform URL to FILE path as the filename will remain intact (unless we've got some really contrived aliasing in FileManagerWithAliasSupport: we don't care too much here about such wicked mappings, as speed is paramount)
			if (!$just_guess_mime)
			{
				$file = $this->legal_url_path2file_path($url);

				$mime = $this->getMimeType($file, false, $url);
				$iconspec = basename($file);
			}
			else
			{
				$mime = $this->getMimeType($filename, true);
				$iconspec = $filename;
			}
			if (!$this->IsAllowedMimeType($mime, $mime_filters))
				continue;

			if ($filename === $file_preselect_arg)
			{
				$file_preselect_index = $idx;
			}

			if (FileManagerUtility::startsWith($mime, 'image/'))
			{
				/*
				 * offload the thumbnailing process to another event ('event=thumbnail') to be fired by the client
				 * when it's time to render the thumbnail: the offloading helps us tremendously in coping with large
				 * directories:
				 * WE simply assume the thumbnail will be there, so we don't even need to check for its existence
				 * (which saves us one more file_exists() per item at the very least). And when it doesn't, that's
				 * for the event=thumbnail handler to worry about (creating the thumbnail on demand or serving
				 * a generic icon image instead).
				 */

				if (0)
				{
					/*
					 * DISABLED PERMANENTLY. This *may* look like smart code, but it is not. The dirscan result is
					 * long-lived on the client side (particularly for large directories, where you can browse multiple
					 * pages' worth of directory view: all that data originates from a single request and is cached
					 * client-side.
					 * Hence any thumbnails being generated during the browsing of that directory do not get to
					 * 'short circuit' anyway, as the client-side cached dirscan output still lists the PHP-based
					 * requests.
					 *
					 * Besides, there's another issue with large lists: the server is bombarded with thumbnail
					 * requests, almost like a DoS attack. So the client should really queue the thumbnail requests
					 * for the thumb view, irrespective of the propagateType being POST or GET.
					 *
					 * Last, and minor, quible with this: when the thumbnail cache is purged while a directory is
					 * browsed, the user must hit [F5] to refresh the entire filemanger to receive an up-to-date
					 * scandir, i.e. one with PHP-based thumbnail requests. (For onDetail, on the other hand, such a
					 * short-cut is fine as a mishap there can simply be recovered by clicking on the entry in the
					 * thumb/list directory view again: that's minimal fuss. The same recovery for a dirview is
					 * non-intuitive and not recognized by users: browse to other directory and then back again. Of
					 * course that non-intuitive 'fix' only works if you actually have multiple directories to view.
					 * User tests show the only thing that makes sense at all is hitting [F5] anyway and that is
					 * regarded as a nuisance.)
					 *
					 * I don't mind this adds 'one more round trip' to the propagateType=POST approach; the shortcut
					 * is simply causing too much bother for the users. And that extra trip is hidden among the other
					 * thumbnail requests anyway: a fast image fetch, while the other thumbnails are requested/generated.
					 *
					 * And besides: this 'shortcut' reintroduced the previously optimized-out file_exist per file:
					 * this time around, it's at least one extra file_exists() check in the thumbnail cache tree, and we
					 * could do very well without it, particularly for large directories where every bit of file access
					 * is slowing this bugger down, while the user is twiddling his thumbs.
					 */
					$thumb48 = $this->getThumb($url, $file, 48, 48, true);
					if ($thumb48 !== false)
					{
						$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
					}
				}

				$thumb48_e = str_replace('..F..', FileManagerUtility::rawurlencode_path($filename), $thumb_tpl48);
			}
			else
			{
				$thumb48 = $this->getIcon($iconspec, false);
				$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
			}
			$icon = $this->getIcon($iconspec, true);
			$icon_e = FileManagerUtility::rawurlencode_path($icon);

			if ($list_type === 'thumb')
			{
				$thumb_e = $thumb48_e;
			}
			else
			{
				$thumb_e = $icon_e;
			}

			$url_p = FileManagerUtility::rawurlencode_path($url);

			$out[0][] = array(
					'path' => $url_p,
					'name' => $filename,
					//'date' => date($this->options['dateFormat'], @filemtime($file)),
					'mime' => $mime,
					'thumbnail' => $thumb_e,
					'thumb48' => $thumb48_e,
					//'size' => @filesize($file),
					'icon' => $icon_e
				);
			$idx++;

			if (0)
			{
				// help PHP when 'doing' large image directories: reset the timeout for each thumbnail / entry we produce:
				//   http://www.php.net/manual/en/info.configuration.php#ini.max-execution-time
				set_time_limit(max(30, ini_get('max_execution_time')));
			}
		}

		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__, $idx);

		return array_merge((is_array($json) ? $json : array()), array(
				'root' => substr($this->options['directory'], 1),
				'path' => $legal_url,                                  // is relative to options['directory']
				'dir' => array(
					'path' => FileManagerUtility::rawurlencode_path($legal_url),
					'name' => pathinfo($legal_url, PATHINFO_BASENAME),
					'date' => date($this->options['dateFormat'], @filemtime($dir)),
					'mime' => 'text/directory',
					'thumbnail' => $thumb_de,
					'thumb48' => $thumb48_de,
					'icon' => $icon_de
				),
				'preselect_index' => ($file_preselect_index >= 0 ? $file_preselect_index + count($out[1]) + 1 : 0),
				'preselect_name' => ($file_preselect_index >= 0 ? $file_preselect_arg : null),
				'dirs' => $out[1],
				'files' => $out[0]
			));
	}

	/**
	 * Process the 'view' event (default event fired by fireEvent() method)
	 *
	 * Returns a JSON encoded directory view list.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_POST['file_preselect']     optional filename or path:
	 *                         when a filename, this is the filename of a file in this directory
	 *                         which should be located and selected. When found, the backend will
	 *                         provide an index number pointing at the corresponding JSON files[]
	 *                         entry to assist the front-end in jumping to that particular item
	 *                         in the view.
	 *
	 *                         when a path, it is either an absolute or a relative path:
	 *                         either is assumed to be a URI URI path, i.e. rooted at
	 *                           DocumentRoot.
	 *                         The path will be transformed to a LEGAL URI path and
	 *                         will OVERRIDE the $_POST['directory'] path.
	 *                         Otherwise, this mode acts as when only a filename was specified here.
	 *                         This mode is useful to help a frontend to quickly jump to a file
	 *                         pointed at by a URI.
	 *
	 *                         N.B.: This also the only entry which accepts absolute URI paths and
	 *                               transforms them to LEGAL URI paths.
	 *
	 *                         When the specified path is illegal, i.e. does not reside inside the
	 *                         options['directory']-rooted LEGAL URI subtree, it will be discarded
	 *                         entirely (as all file paths, whether they are absolute or relative,
	 *                         must end up inside the options['directory']-rooted subtree to be
	 *                         considered manageable files) and the process will continue as if
	 *                         the $_POST['file_preselect'] entry had not been set.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * $_POST['type']          'thumb' will produce a list view including thumbnail and other
	 *                         information with each listed file; other values will produce
	 *                         a basic list view (similar to Windows Explorer 'list' view).
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 *
	 * Next to these, the JSON encoded output will, with high probability, include a
	 * list view of the parent or 'basedir' as a fast and easy fallback mechanism for client side
	 * viewing code. However, severe and repetitive errors may not produce this
	 * 'fallback view list' so proper client code should check the 'status' field in the
	 * JSON output.
	 */
	protected function onView()
	{
		// try to produce the view; if it b0rks, retry with the parent, until we've arrived at the basedir:
		// then we fail more severely.

		$emsg = null;
		$jserr = array(
				'status' => 1
			);

		$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
		$list_type = ($this->getPOSTparam('type') !== 'thumb' ? 'list' : 'thumb');
		$legal_url = null;

		try
		{
			$dir_arg = $this->getPOSTparam('directory', $this->options['directory']);
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			$file_preselect_arg = $this->getPOSTparam('file_preselect');
			try
			{
				if (!empty($file_preselect_arg))
				{
					// check if this a path instead of just a basename, then convert to legal_url and split across filename and directory.
					if (strpos($file_preselect_arg, '/') !== false)
					{
						// this will also convert a relative path to an absolute path before transforming it to a LEGAL URI path:
						$legal_presel = $this->abs2legal_url_path($file_preselect_arg);

						$prseli = pathinfo($legal_presel);
						$file_preselect_arg = $prseli['basename'];
						// override the directory!
						$legal_url = $prseli['dirname'];
						$legal_url = self::enforceTrailingSlash($legal_url);
					}
					else
					{
						$file_preselect_arg = pathinfo($file_preselect_arg, PATHINFO_BASENAME);
					}
				}
			}
			catch(FileManagerException $e)
			{
				// discard the preselect input entirely:
				$file_preselect_arg = null;
			}
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
			$legal_url = '/';
			$file_preselect_arg = null;
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything it may not be a translation keyword in the message...
			$emsg = $e->getMessage();
			$legal_url = '/';
			$file_preselect_arg = null;
		}

		// loop until we drop below the bottomdir; meanwhile getDir() above guarantees that $dir is a subdir of bottomdir, hence dir >= bottomdir.
		$original_legal_url = $legal_url;
		do
		{
			try
			{
				$rv = $this->_onView($legal_url, $jserr, $mime_filter, $list_type, $file_preselect_arg);

				if (!headers_sent()) header('Content-Type: application/json');

				echo json_encode($rv);
				return;
			}
			catch(FileManagerException $e)
			{
				if ($emsg === null)
					$emsg = $e->getMessage();
			}
			catch(Exception $e)
			{
				// catching other severe failures; since this can be anything it may not be a translation keyword in the message...
				if ($emsg === null)
					$emsg = $e->getMessage();
			}

			// step down to the parent dir and retry:
			$legal_url = self::getParentDir($legal_url);
			$file_preselect_arg = null;

			$jserr['status']++;

		} while ($legal_url !== false);

		$this->modify_json4exception($jserr, $emsg, 'path = ' . $original_legal_url);

		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}

	/**
	 * Process the 'detail' event
	 *
	 * Returns a JSON encoded HTML chunk describing the specified file (metadata such
	 * as size, format and possibly a thumbnail image as well)
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_POST['file']          filename (including extension, of course) of the file to
	 *                         be detailed.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * $_POST['mode']          'auto' or 'direct': in 'direct' mode, all thumbnails are
	 *                         forcibly generated _right_ _now_ as the client, using this
	 *                         mode, tells us PHP event trigger URLs as generated by
	 *                         mkEventHandlerURL are out of the question.
	 *                         'auto' mode will simply provide direct thumbnail image
	 *                         URLs when those are available in cache, and PHP event
	 *                         trigger URLs otherwise.
	 *
	 *                         be detailed.
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onDetail()
	{
		$emsg = null;
		$legal_url = null;
		$file_arg = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			$v_ex_code = 'nofile';

			$mode = $this->getPOSTparam('mode');

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$filename = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$filename = pathinfo($file_arg, PATHINFO_BASENAME);
				$legal_url .= $filename;
				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						if (!empty($meta['mime_type']))
							$mime = $meta['mime_type'];
						//$mime = $this->getMimeType($file);
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
							$v_ex_code = 'extension';
						else
							$v_ex_code = null;
					}
					else if (is_dir($file))
					{
						$mime = 'text/directory';
						$v_ex_code = null;
					}
				}
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'file' => $file,
					'filename' => $filename,
					'mode' => $mode,
					'meta_data' => $meta,
					'mime' => $mime,
					'mime_filter' => $mime_filter,
					'mime_filters' => $mime_filters,
					'preliminary_json' => $jserr,
					'validation_failure' => $v_ex_code
				);

			if (!empty($this->options['DetailIsAuthorized_cb']) && function_exists($this->options['DetailIsAuthorized_cb']) && !$this->options['DetailIsAuthorized_cb']($this, 'detail', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			//$file = $fileinfo['file'];
			$filename = $fileinfo['filename'];
			$mode = $fileinfo['mode'];
			$meta = $fileinfo['meta_data'];
			//$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			$jserr = $fileinfo['preliminary_json'];

			$jserr = $this->extractDetailInfo($jserr, $legal_url, $meta, $mime_filter, $mime_filters, $mode);

			if (!headers_sent()) header('Content-Type: application/json');

			echo json_encode($jserr);
			return;
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);

		$thumb48 = $this->getIconForError($emsg, 'is.default-error', false);
		$icon = $this->getIconForError($emsg, 'is.default-error', true);
		$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
		$icon_e = FileManagerUtility::rawurlencode_path($icon);
		$jserr['thumb250'] = $jserr['thumb48'] = $thumb48_e;
		$jserr['icon'] = $icon_e;

		$content_classes = "margin preview_err_report";
		$postdiag_err_HTML = '<p class="err_info">' . $emsg . '</p>';
		$postdiag_dump_HTML = '';
		$preview_HTML = '${nopreview}';
		$content = '<h3>${preview}</h3>
						<div class="filemanager-preview-content">' . $preview_HTML . '</div>';
		$content .= '<h3>Diagnostics</h3>
					 <div class="filemanager-detail-diag">
						<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>
					 </div>';

		$json['content'] = self::compressHTML('<div class="' . $content_classes . '">' . $content . '</div>');


		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}

	/**
	 * Process the 'thumbnail' event
	 *
	 * Returns either the binary content of the requested thumbnail or the binary content of a replacement image.
	 *
	 * Technical info: this function is assumed to be fired from a <img src="..."> URI or similar and must produce
	 * the content of an image.
	 * It is used in conjection with the 'view/list=thumb' view mode of the FM client: the 'view' list, as
	 * produced by us, contains specially crafted URLs pointing back at us (the 'event=thumbnail' URLs) to
	 * enable FM to cope much better with large image collections by having the entire thumbnail checking
	 * and creation process offloaded to this Just-in-Time subevent.
	 *
	 * By not loading the 'view' event with the thumbnail precreation/checking effort, it can respond
	 * much faster or at least not timeout in the backend for larger image sets in any directory.
	 * ('view' simply assumes the thumbnail will be there, hence reducing its own workload with at least
	 * 1 file_exists() plus worst-case one GD imageinfo + imageresample + extras per image in the 'view' list!)
	 *
	 * Expected parameters:
	 *
	 * $_GET['directory']      path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_GET['file']           filename (including extension, of course) of the file to
	 *                         be thumbnailed.
	 *
	 * $_GET['size']           the requested thumbnail maximum width / height (the bounding box is square).
	 *                         Must be one of our 'authorized' sizes: 48, 250.
	 *
	 * $_GET['filter']         optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * $_GET['asJSON']        return some JSON {status: 1, thumbnail: 'path/to/thumbnail.png' }
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 *
	 * Next to these, the JSON encoded output will, with high probability, include a
	 * list view of the parent or 'basedir' as a fast and easy fallback mechanism for client side
	 * viewing code. However, severe and repetitive errors may not produce this
	 * 'fallback view list' so proper client code should check the 'status' field in the
	 * JSON output.
	 */
	protected function onThumbnail()
	{
		// try to produce the view; if it b0rks, retry with the parent, until we've arrived at the basedir:
		// then we fail more severely.

		$emsg = null;
		$img_filepath = null;
		$reqd_size = 48;
		$filename = null;
		$file_arg = null;
		$legal_url = null;
		$as_JSON = false;
		$jserr = array(
				'status' => 1
			);

		try
		{
			$v_ex_code = 'disabled';

			$as_JSON = $this->getGETParam('asJSON', 0);

			$reqd_size = intval($this->getGETparam('size'));
			if (!empty($reqd_size))
			{
				// and when not requesting one of our 'authorized' thumbnail sizes, you're gonna burn as well!
				if (in_array($reqd_size, array(48, 250)))
					$v_ex_code = null;
			}

			$file_arg = $this->getGETparam('file');

			$dir_arg = $this->getGETparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			$mime_filter = $this->getGETparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$filename = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg) && empty($v_ex_code))
			{
				$v_ex_code = 'nofile';

				$filename = pathinfo($file_arg, PATHINFO_BASENAME);
				$legal_url .= $filename;
				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						if (!empty($meta['mime_type']))
							$mime = $meta['mime_type'];
						//$mime = $this->getMimeType($file);
						if ($this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = null;
						}
					}
					else
					{
						$mime = 'text/directory';
					}
				}
			}
			else if (empty($v_ex_code))
			{
				$v_ex_code = 'nofile';
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'file' => $file,
					'filename' => $filename,
					'meta_data' => $meta,
					'mime' => $mime,
					'mime_filter' => $mime_filter,
					'mime_filters' => $mime_filters,
					'requested_size' => $reqd_size,
					'mode' => ($as_JSON ? 'json' : 'image'),
					'preliminary_json' => $jserr,
					'validation_failure' => $v_ex_code
				);

			if (!empty($this->options['ThumbnailIsAuthorized_cb']) && function_exists($this->options['ThumbnailIsAuthorized_cb']) && !$this->options['ThumbnailIsAuthorized_cb']($this, 'thumbnail', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$file = $fileinfo['file'];
			$filename = $fileinfo['filename'];
			$meta = $fileinfo['meta_data'];
			$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			$reqd_size = $fileinfo['requested_size'];
			$as_JSON = ($fileinfo['mode'] === 'json');
			$jserr = $fileinfo['preliminary_json'];

			/*
			 * each image we inspect may throw an exception due to an out of memory warning
			 * (which is far better than without those: a silent fatal abort!)
			 *
			 * However, now that we do have a way to check most memory failures occurring in here (due to large images
			 * and too little available RAM) we /still/ want to see that happen: for broken and overlarge images, we
			 * produce some alternative graphics instead!
			 */

			// access the image and create a thumbnail image; this can fail dramatically.
			//
			// Note that 'onThumbnail' ASSUMES 'onDetail' has been called before, for this same file. (Otherwise, thumbnails for
			// non-images won't work!)
			//
			// Use the 250px thumbnail as a source when it already exists AND we are looking for a smaller thumbnail right now.
			// Otherwise, use the original file.
			$thumb250 = false;
			if ($reqd_size < $this->options['thumbnailSize'])
			{
				$thumb250 = $this->getThumb($legal_url, $file, $this->options['thumbnailSize'], $this->options['thumbnailSize'], true);
			}
			$thumb_path = null;
			// only try to /generate/ a thumbnail when we are looking at a image SOURCE, be it the original file or the 250px thumbnail file:
			if (FileManagerUtility::startsWith($mime, 'image/') || $thumb250 !== false)
			{
				$thumb_path = $this->getThumb($legal_url, ($thumb250 !== false ? $this->url_path2file_path($thumb250) : $file), $reqd_size, $reqd_size);
			}
			else
			{
				// 'abuse' the info extraction to produce any embedded images, which can be used for thumbnail production.
				// This is really using a side effect of this call...
				$jserr = $this->extractDetailInfo($jserr, $legal_url, $meta, $mime_filter, $mime_filters, 'direct');
				if (!empty($jserr['thumb' . $reqd_size]))
				{
					$thumb_path = $jserr['thumb' . $reqd_size];
				}
			}

			$img_filepath = (!empty($thumb_path) ? $thumb_path : $this->getIcon($filename, $reqd_size <= 16));
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
		}

		// now go and serve the content of the thumbnail / icon image file (which we still need to determine /exactly/):
		try
		{
			if (!empty($emsg))
			{
				$img_filepath = $this->getIconForError($emsg, $filename, $reqd_size <= 16);
			}

			$file = $this->url_path2file_path($img_filepath);
			if (!$as_JSON)
			{
				$fd = fopen($file, 'rb');
				if (!$fd)
				{
					// when the icon / thumbnail cannot be opened for whatever reason, fall back to the default error image:
					$file = $this->url_path2file_path($this->getIcon('is.default-error', $reqd_size <= 16));
					$fd = fopen($file, 'rb');
					if (!$fd)
						throw new Exception('panic');
				}
				$mime = $this->getMimeType($file, true);  // take the fast track for determining the image mime type; we can be certain our thumbnails have correct file extensions!
				$fsize = filesize($file);
				if (!empty($mime))
				{
					header('Content-Type: ' . $mime);
				}
				header('Content-Length: ' . $fsize);

				header("Cache-Control: private"); //use this to open files directly

				fpassthru($fd);
				fclose($fd);
				exit();
			}
			else if (file_exists($file))
			{
				$jserr['thumbnail'] = $img_filepath;
			}
		}
		catch(Exception $e)
		{
			if (!$as_JSON)
			{
				send_response_status_header(500);
				echo 'Cannot produce thumbnail: ' . $emsg . ' :: ' . $img_filepath;
			}
			$emsg = $e->getMessage();

			$thumb = $this->getIconForError($emsg, $filename, $reqd_size <= 16);
			$thumb_e = FileManagerUtility::rawurlencode_path($thumb);
			$jserr['thumbnail'] = $thumb_e;
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);

		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}


	/**
	 * Process the 'destroy' event
	 *
	 * Delete the specified file or directory and return a JSON encoded status of success
	 * or failure.
	 *
	 * Note that when images are deleted, so are their thumbnails.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_POST['file']          filename (including extension, of course) of the file to
	 *                         be detailed.
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onDestroy()
	{
		$emsg = null;
		$file_arg = null;
		$legal_url = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			if (!$this->options['destroy'])
				throw new FileManagerException('disabled');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$filename = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$filename = pathinfo($file_arg, PATHINFO_BASENAME);
				$legal_url .= $filename;
				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (file_exists($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						if (!empty($meta['mime_type']))
							$mime = $meta['mime_type'];
						//$mime = $this->getMimeType($file);
						if ($this->IsAllowedMimeType($mime, $mime_filters))
							$v_ex_code = null;
					}
					else if (is_dir($file))
					{
						$mime = 'text/directory';
						$v_ex_code = null;
					}
				}
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'file' => $file,
					'mime' => $mime,
					'meta_data' => $meta,
					'mime_filter' => $mime_filter,
					'mime_filters' => $mime_filters,
					'preliminary_json' => $jserr,
					'validation_failure' => $v_ex_code
				);

			if (!empty($this->options['DestroyIsAuthorized_cb']) && function_exists($this->options['DestroyIsAuthorized_cb']) && !$this->options['DestroyIsAuthorized_cb']($this, 'destroy', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$file = $fileinfo['file'];
			$meta = $fileinfo['meta_data'];
			$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			$jserr = $fileinfo['preliminary_json'];

			if (!$this->unlink($legal_url, $mime_filters))
				throw new FileManagerException('unlink_failed:' . $legal_url);

			if (!headers_sent()) header('Content-Type: application/json');

			echo json_encode(array(
					'status' => 1,
					'content' => 'destroyed'
				));
			return;
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);

		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}

	/**
	 * Process the 'create' event
	 *
	 * Create the specified subdirectory and give it the configured permissions
	 * (options['chmod'], default 0777) and return a JSON encoded status of success
	 * or failure.
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_POST['file']          name of the subdirectory to be created
	 *
	 * Extra input parameters considered while producing the JSON encoded directory view.
	 * These may not seem relevant for an empty directory, but these parameters are also
	 * considered when providing the fallback directory view in case an error occurred
	 * and then the listed directory (either the parent or the basedir itself) may very
	 * likely not be empty!
	 *
	 * $_POST['filter']        optional mimetype filter string, amy be the part up to and
	 *                         including the slash '/' or the full mimetype. Only files
	 *                         matching this (set of) mimetypes will be listed.
	 *                         Examples: 'image/' or 'application/zip'
	 *
	 * $_POST['type']          'thumb' will produce a list view including thumbnail and other
	 *                         information with each listed file; other values will produce
	 *                         a basic list view (similar to Windows Explorer 'list' view).
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                  0 for error; nonzero for success
	 *
	 * error                   error message
	 */
	protected function onCreate()
	{
		$emsg = null;
		$jserr = array(
				'status' => 1
			);

		$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
		$list_type = ($this->getPOSTparam('type') !== 'thumb' ? 'list' : 'thumb');

		$file_arg = null;
		$legal_url = null;

		try
		{
			if (!$this->options['create'])
				throw new FileManagerException('disabled');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$dir = $this->legal_url_path2file_path($legal_url);

			$filename = null;
			$file = null;
			$newdir = null;
			if (!empty($file_arg))
			{
				$filename = pathinfo($file_arg, PATHINFO_BASENAME);

				if (!$this->IsHiddenNameAllowed($file_arg))
				{
					$v_ex_code = 'authorized';
				}
				else
				{
					if (is_dir($dir))
					{
						$file = $this->getUniqueName(array('filename' => $filename), $dir);  // a directory has no 'extension'!
						if ($file)
						{
							$newdir = $this->legal_url_path2file_path($legal_url . $file);
							$v_ex_code = null;
						}
					}
				}
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'dir' => $dir,
					'raw_name' => $filename,
					'uniq_name' => $file,
					'newdir' => $newdir,
					'chmod' => $this->options['chmod'],
					'preliminary_json' => $jserr,
					'validation_failure' => $v_ex_code
				);
			if (!empty($this->options['CreateIsAuthorized_cb']) && function_exists($this->options['CreateIsAuthorized_cb']) && !$this->options['CreateIsAuthorized_cb']($this, 'create', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$dir = $fileinfo['dir'];
			$filename = $fileinfo['raw_name'];
			$file = $fileinfo['uniq_name'];
			$newdir = $fileinfo['newdir'];
			$jserr = $fileinfo['preliminary_json'];

			if (!@mkdir($newdir, $fileinfo['chmod'], true))
				throw new FileManagerException('mkdir_failed:' . $this->legal2abs_url_path($legal_url) . $file);

			if (!headers_sent()) header('Content-Type: application/json');

			// success, now show the new directory as a list view:
			$rv = $this->_onView($legal_url . $file . '/', $jserr, $mime_filter, $list_type);
			echo json_encode($rv);
			return;
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();

			$jserr['status'] = 0;

			// and fall back to showing the PARENT directory
			try
			{
				$jserr = $this->_onView($legal_url, $jserr, $mime_filter, $list_type);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['directory'];
					$jserr = $this->_onView($legal_url, $jserr, $mime_filter, $list_type);
				}
				catch (Exception $e)
				{
					// when we fail here, it's pretty darn bad and nothing to it.
					// just push the error JSON as go.
				}
			}
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();

			$jserr['status'] = 0;

			// and fall back to showing the PARENT directory
			try
			{
				$jserr = $this->_onView($legal_url, $jserr, $mime_filter, $list_type);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['directory'];
					$jserr = $this->_onView($legal_url, $jserr, $mime_filter, $list_type);
				}
				catch (Exception $e)
				{
					// when we fail here, it's pretty darn bad and nothing to it.
					// just push the error JSON as go.
				}
			}
		}

		$this->modify_json4exception($jserr, $emsg, 'directory = ' . $file_arg . ', path = ' . $legal_url);

		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}

	/**
	 * Process the 'download' event
	 *
	 * Send the file content of the specified file for download by the client.
	 * Only files residing within the directory tree rooted by the
	 * 'basedir' (options['directory']) will be allowed to be downloaded.
	 *
	 * Expected parameters:
	 *
	 * $_GET['file']          filepath of the file to be downloaded
	 *
	 * $_GET['filter']        optional mimetype filter string, amy be the part up to and
	 *                        including the slash '/' or the full mimetype. Only files
	 *                        matching this (set of) mimetypes will be listed.
	 *                        Examples: 'image/' or 'application/zip'
	 *
	 * On errors a HTTP 403 error response will be sent instead.
	 */
	protected function onDownload()
	{
		try
		{
			if (!$this->options['download'])
				throw new FileManagerException('disabled');

			$v_ex_code = 'nofile';

			$file_arg = $this->getGETparam('file');

			$mime_filter = $this->getGETparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$legal_url = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$legal_url = $this->rel2abs_legal_url_path($file_arg);
				//$legal_url = self::enforceTrailingSlash($legal_url);

				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						if (!empty($meta['mime_type']))
							$mime = $meta['mime_type'];
						//$mime = $this->getMimeType($file);
						if ($this->IsAllowedMimeType($mime, $mime_filters))
							$v_ex_code = null;
					}
					else
					{
						$mime = 'text/directory';
					}
				}
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'file' => $file,
					'mime' => $mime,
					'meta_data' => $meta,
					'mime_filter' => $mime_filter,
					'mime_filters' => $mime_filters,
					'validation_failure' => $v_ex_code
				);
			if (!empty($this->options['DownloadIsAuthorized_cb']) && function_exists($this->options['DownloadIsAuthorized_cb']) && !$this->options['DownloadIsAuthorized_cb']($this, 'download', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$file = $fileinfo['file'];
			$meta = $fileinfo['meta_data'];
			$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];

			if ($fd = fopen($file, 'rb'))
			{
				$fsize = filesize($file);
				$path_parts = pathinfo($legal_url);
				$ext = strtolower($path_parts["extension"]);
				// see also: http://www.boutell.com/newfaq/creating/forcedownload.html
				switch ($ext)
				{
				case "pdf":
					header('Content-Type: application/pdf');
					break;

				// add here more headers for diff. extensions

				default:
					header('Content-Type: application/octet-stream');
					break;
				}
				header('Content-Disposition: attachment; filename="' . $path_parts["basename"] . '"'); // use 'attachment' to force a download
				header("Content-length: $fsize");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private", false); // use this to open files directly

				fpassthru($fd);
				fclose($fd);
			}
		}
		catch(FileManagerException $e)
		{
			// we don't care whether it's a 404, a 403 or something else entirely: we feed 'em a 403 and that's final!
			send_response_status_header(403);
			echo $e->getMessage();
		}
		catch(Exception $e)
		{
			// we don't care whether it's a 404, a 403 or something else entirely: we feed 'em a 403 and that's final!
			send_response_status_header(403);
			echo $e->getMessage();
		}
	}

	/**
	 * Process the 'upload' event
	 *
	 * Process and store the uploaded file in the designated location.
	 * Images will be resized when possible and applicable. A thumbnail image will also
	 * be preproduced when possible.
	 * Return a JSON encoded status of success or failure.
	 *
	 * Expected parameters:
	 *
	 * $_GET['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 * $_GET['resize']        nonzero value indicates any uploaded image should be resized to the configured options['maxImageDimension'] width and height whenever possible
	 *
	 * $_GET['filter']        optional mimetype filter string, amy be the part up to and
	 *                        including the slash '/' or the full mimetype. Only files
	 *                        matching this (set of) mimetypes will be listed.
	 *                        Examples: 'image/' or 'application/zip'
	 *
	 * $_FILES[]              the metadata for the uploaded file
	 *
	 * $_GET['reportContentType'] if you want a specific content type header set on our response, put it here.
	 *                        This is needed for when we are posting an upload response to a hidden iframe, the
	 *                        default application/json mimetype breaks down in that case at least for Firefox 3.X
	 *                        as the browser will pop up a save/view dialog before JS can access the transmitted data.
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                 0 for error; nonzero for success
	 *
	 * error                  error message
	 */
	protected function onUpload()
	{
		$emsg = null;
		$file_arg = null;
		$legal_url = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			if (!$this->options['upload'])
				throw new FileManagerException('disabled');

			// MAY upload zero length files!
			if (!isset($_FILES) || empty($_FILES['Filedata']) || empty($_FILES['Filedata']['name']))
				throw new FileManagerException('nofile');

			$v_ex_code = 'nofile';

			$file_size = (empty($_FILES['Filedata']['size']) ? 0 : $_FILES['Filedata']['size']);

			$file_arg = $_FILES['Filedata']['name'];

			$dir_arg = $this->getGETparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);
			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$dir = $this->legal_url_path2file_path($legal_url);

			$mime_filter = $this->getGETparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$tmppath = $_FILES['Filedata']['tmp_name'];

			$filename = null;
			$fi = array('filename' => null, 'extension' => null);
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				if (!$this->IsHiddenNameAllowed($file_arg))
				{
					$v_ex_code = 'fmt_not_allowed';
				}
				else
				{
					$filename = $this->getUniqueName($file_arg, $dir);
					if (!empty($filename))
					{
						$fi = pathinfo($filename);

						// UPLOAD delivers files in temporary storage with extensions NOT matching the mime type, so we don't
						// filter on extension; we just let getID3 go ahead and content-sniff the mime type.
						// Since getID3::analyze() is a quite costly operation, we like to do it only ONCE per file,
						// so we cache the last entries.
						$meta = $this->getFileInfo($tmppath, null);
						if (!empty($meta['mime_type']))
							$mime = $meta['mime_type'];
						//$mime = $this->getMimeType($file);
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = 'extension';
						}
						else
						{
							/*
							 * Security:
							 *
							 * Upload::move() processes the unfiltered version of $_FILES[]['name'], at least to get the extension,
							 * unless we ALWAYS override the filename and extension in the options array below. That's why we
							 * calculate the extension at all times here.
							 */
							if ($this->options['safe'])
							{
								$fi['extension'] = $this->getSafeExtension(isset($fi['extension']) ? $fi['extension'] : '');
							}
							$v_ex_code = null;
						}
					}
				}
			}

			$fileinfo = array(
				'legal_url' => $legal_url,
				'dir' => $dir,
				'raw_filename' => $file_arg,
				'name' => $fi['filename'],
				'extension' => (isset($fi['extension']) ? $fi['extension'] : ''),
				'meta_data' => $meta,
				'mime' => $mime,
				'mime_filter' => $mime_filter,
				'mime_filters' => $mime_filters,
				'tmp_filepath' => $tmppath,
				'size' => $file_size,
				'maxsize' => $this->options['maxUploadSize'],
				'overwrite' => false,
				'chmod' => $this->options['chmod'] & 0666,   // security: never make those files 'executable'!
				'preliminary_json' => $jserr,
				'validation_failure' => $v_ex_code
			);
			if (!empty($this->options['UploadIsAuthorized_cb']) && function_exists($this->options['UploadIsAuthorized_cb']) && !$this->options['UploadIsAuthorized_cb']($this, 'upload', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$dir = $fileinfo['dir'];
			$file_arg = $fileinfo['raw_filename'];
			$filename = $fileinfo['name'] . ((isset($fileinfo['extension']) && strlen($fileinfo['extension']) > 0) ? '.' . $fileinfo['extension'] : '');
			$meta = $fileinfo['meta_data'];
			$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			//$tmppath = $fileinfo['tmp_filepath'];
			$jserr = $fileinfo['preliminary_json'];

			if($fileinfo['maxsize'] && $fileinfo['size'] > $fileinfo['maxsize'])
				throw new FileManagerException('size');

			//if(!isset($fileinfo['extension']))
			//  throw new FileManagerException('extension');

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$file = $this->legal_url_path2file_path($legal_url . $filename);

			if(!$fileinfo['overwrite'] && file_exists($file))
				throw new FileManagerException('exists');

			if(!@move_uploaded_file($_FILES['Filedata']['tmp_name'], $file))
			{
				$emsg = 'path';
				switch ($_FILES['Filedata']['error'])
				{
				case 1:
				case 2:
					$emsg = 'size';
					break;

				case 3:
					$emsg = 'partial';
					break;

				default:
					$dir = $this->legal_url_path2file_path($legal_url);
					if (!is_dir($dir))
					{
						$emsg = 'path';
					}
					else if (!is_writable($dir))
					{
						$emsg = 'path_not_writable';
					}
					else
					{
						$emsg = 'filename_maybe_too_large';
					}

					$emsg_add = 'file = ' . $this->mkSafe4Display($file_arg . ', destination path = ' . $file);
					if (!empty($_FILES['Filedata']['error']))
					{
						$emsg_add = 'error code = ' . strtolower($_FILES['Filedata']['error']) . ', ' . $emsg_add;
					}
					$emsg .= ':' . $emsg_add;
					break;
				}
				throw new FileManagerException($emsg);
			}

			@chmod($file, $fileinfo['chmod']);


			/*
			 * NOTE: you /can/ (and should be able to, IMHO) upload 'overly large' image files to your site, but the resizing process step
			 *       happening here will fail; we have memory usage estimators in place to make the fatal crash a non-silent one, i,e, one
			 *       where we still have a very high probability of NOT fatally crashing the PHP iunterpreter but catching a suitable exception
			 *       instead.
			 *       Having uploaded such huge images, a developer/somebody can always go in later and up the memory limit if the site admins
			 *       feel it is deserved. Until then, no thumbnails of such images (though you /should/ be able to milkbox-view the real thing!)
			 */
			if (FileManagerUtility::startsWith($mime, 'image/') && $this->getGETparam('resize', 0))
			{
				$img = new Image($file);
				$size = $img->getSize();
				// Image::resize() takes care to maintain the proper aspect ratio, so this is easy
				// (default quality is 100% for JPEG so we get the cleanest resized images here)
				$img->resize($this->options['maxImageDimension']['width'], $this->options['maxImageDimension']['height'])->save();
				unset($img);
			}

			if (!headers_sent()) header('Content-Type: ' . $this->getGetparam('reportContentType', 'application/json'));

			echo json_encode(array(
					'status' => 1,
					'name' => pathinfo($file, PATHINFO_BASENAME)
				));
			return;
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url);

		if (!headers_sent()) header('Content-Type: ' . $this->getGetparam('reportContentType', 'application/json'));

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode(array_merge($jserr, $_FILES));
	}

	/**
	 * Process the 'move' event (with is used by both move/copy and rename client side actions)
	 *
	 * Copy or move/rename a given file or directory and return a JSON encoded status of success
	 * or failure.
	 *
	 * Expected parameters:
	 *
	 * $_POST['copy']            nonzero value means copy, zero or nil for move/rename
	 *
	 * Source filespec:
	 *
	 *   $_POST['directory']     path relative to basedir a.k.a. options['directory'] root
	 *
	 *   $_POST['file']          original name of the file/subdirectory to be renamed/copied
	 *
	 * Destination filespec:
	 *
	 *   $_POST['newDirectory']  path relative to basedir a.k.a. options['directory'] root;
	 *                           target directory where the file must be moved / copied
	 *
	 *   $_POST['name']          target name of the file/subdirectory to be renamed
	 *
	 * Errors will produce a JSON encoded error report, including at least two fields:
	 *
	 * status                    0 for error; nonzero for success
	 *
	 * error                     error message
	 */
	protected function onMove()
	{
		$emsg = null;
		$file_arg = null;
		$legal_url = null;
		$newpath = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			if (!$this->options['move'])
				throw new FileManagerException('disabled');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg);
			$legal_url = self::enforceTrailingSlash($legal_url);

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$dir = $this->legal_url_path2file_path($legal_url);

			$newdir_arg = $this->getPOSTparam('newDirectory');
			$newname_arg = $this->getPOSTparam('name');
			$rename = (empty($newdir_arg) && !empty($newname_arg));

			$is_copy = !!$this->getPOSTparam('copy');

			$filename = null;
			$path = null;
			$fn = null;
			$legal_newurl = null;
			$newdir = null;
			$newname = null;
			$newpath = null;
			$is_dir = false;
			if (!$this->IsHiddenPathAllowed($newdir_arg) || !$this->IsHiddenNameAllowed($newname_arg))
			{
				$v_ex_code = 'authorized';
			}
			else
			{
				if (!empty($file_arg))
				{
					$filename = pathinfo($file_arg, PATHINFO_BASENAME);
					$path = $this->legal_url_path2file_path($legal_url . $filename);

					if (file_exists($path))
					{
						$is_dir = is_dir($path);

						// note: we do not support copying entire directories, though directory rename/move is okay
						if ($is_copy && $is_dir)
						{
							$v_ex_code = 'disabled';
						}
						else if ($rename)
						{
							$fn = 'rename';
							$legal_newurl = $legal_url;
							$newdir = $dir;

							$newname = pathinfo($newname_arg, PATHINFO_BASENAME);
							if ($is_dir)
								$newname = $this->getUniqueName(array('filename' => $newname), $newdir);  // a directory has no 'extension'
							else
								$newname = $this->getUniqueName($newname, $newdir);

							if (!$newname)
							{
								$v_ex_code = 'nonewfile';
							}
							else
							{
								// when the new name seems to have a different extension, make sure the extension doesn't change after all:
								// Note: - if it's only 'case' we're changing here, then exchange the extension instead of appending it.
								//       - directories do not have extensions
								$extOld = pathinfo($filename, PATHINFO_EXTENSION);
								$extNew = pathinfo($newname, PATHINFO_EXTENSION);
								if ((!$this->options['allowExtChange'] || (!$is_dir && empty($extNew))) && !empty($extOld) && strtolower($extOld) != strtolower($extNew))
								{
									$newname .= '.' . $extOld;
								}
								$v_ex_code = null;
							}
						}
						else
						{
							$fn = ($is_copy ? 'copy' : 'rename' /* 'move' */);
							$legal_newurl = $this->rel2abs_legal_url_path($newdir_arg);
							$legal_newurl = self::enforceTrailingSlash($legal_newurl);
							$newdir = $this->legal_url_path2file_path($legal_newurl);

							if ($is_dir)
								$newname = $this->getUniqueName(array('filename' => $filename), $newdir);  // a directory has no 'extension'
							else
								$newname = $this->getUniqueName($filename, $newdir);

							if (!$newname)
								$v_ex_code = 'nonewfile';
							else
								$v_ex_code = null;
						}

						if (empty($v_ex_code))
						{
							$newpath = $this->legal_url_path2file_path($legal_newurl . $newname);
						}
					}
				}
			}

			$fileinfo = array(
					'legal_url' => $legal_url,
					'dir' => $dir,
					'path' => $path,
					'name' => $filename,
					'legal_newurl' => $legal_newurl,
					'newdir' => $newdir,
					'newpath' => $newpath,
					'newname' => $newname,
					'rename' => $rename,
					'is_dir' => $is_dir,
					'function' => $fn,
					'preliminary_json' => $jserr,
					'validation_failure' => $v_ex_code
				);

			if (!empty($this->options['MoveIsAuthorized_cb']) && function_exists($this->options['MoveIsAuthorized_cb']) && !$this->options['MoveIsAuthorized_cb']($this, 'move', $fileinfo))
			{
				$v_ex_code = $fileinfo['validation_failure'];
				if (empty($v_ex_code)) $v_ex_code = 'authorized';
			}
			if (!empty($v_ex_code))
				throw new FileManagerException($v_ex_code);

			$legal_url = $fileinfo['legal_url'];
			$dir = $fileinfo['dir'];
			$path = $fileinfo['path'];
			$filename = $fileinfo['name'];
			$legal_newurl = $fileinfo['legal_newurl'];
			$newdir = $fileinfo['newdir'];
			$newpath = $fileinfo['newpath'];
			$newname = $fileinfo['newname'];
			$rename = $fileinfo['rename'];
			$is_dir = $fileinfo['is_dir'];
			$fn = $fileinfo['function'];
			$jserr = $fileinfo['preliminary_json'];

			if($rename)
			{
				// try to remove the thumbnail related to the original file; don't mind if it doesn't exist
				if(!$is_dir)
				{
					if (!$this->deleteThumb($legal_url . $filename))
						throw new FileManagerException('delete_thumbnail_failed');
				}
			}

			if (!function_exists($fn))
				throw new FileManagerException((empty($fn) ? 'rename' : $fn) . '_failed:' . $legal_newurl . ':' . $newname);
			if (!@$fn($path, $newpath))
				throw new FileManagerException($fn . '_failed:' . $legal_newurl . ':' . $newname);

			if (!headers_sent()) header('Content-Type: application/json');

			// jserr['status'] == 1
			$jserr['name'] = $newname;
			echo json_encode($jserr);
			return;
		}
		catch(FileManagerException $e)
		{
			$emsg = $e->getMessage();
		}
		catch(Exception $e)
		{
			// catching other severe failures; since this can be anything and should only happen in the direst of circumstances, we don't bother translating
			$emsg = $e->getMessage();
		}

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $file_arg . ', path = ' . $legal_url . ', destination path = ' . $newpath);

		if (!headers_sent()) header('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON as go.
		echo json_encode($jserr);
	}







	/**
	 * Convert a given file spec into a URL pointing at our JiT thumbnail creation/delivery event handler.
	 *
	 * The spec must be an array with these elements:
	 *   'event':       'thumbnail'
	 *   'directory':   URI path to directory of the ORIGINAL file
	 *   'file':        filename of the ORIGINAL file
	 *   'size':        requested thumbnail size (e.g. 48)
	 *   'filter':      optional mime_filter as originally specified by the client
	 *   'type':        'thumb' or 'list': the current type of directory view at the client
	 *
	 * Return the URL string.
	 */
	public function mkEventHandlerURL($spec)
	{
		// first determine how the client can reach us; assume that's the same URI as he went to right now.
		$our_handler_url = $this->getRequestScriptURI();

		if (is_array($this->options['URIpropagateData']))
		{
			// the items in 'spec' always win over any entries in 'URIpropagateData':
			$spec = array_merge(array(), $this->options['URIpropagateData'], $spec);
		}

		// next, construct the query part of the URI:
		$qstr = http_build_query_ex($spec, null, '&', null, PHP_QUERY_RFC3986);

		return $our_handler_url . (strpos($our_handler_url, '?') === false ? '?' : '&') . $qstr;
	}



	/**
	 * Produce a HTML snippet detailing the given file in the JSON 'content' element; place additional info
	 * in the JSON elements 'thumbnail', 'thumb48', 'thumb250', 'width', 'height', ...
	 *
	 * Return an augmented JSON array.
	 *
	 * Throw an exception on error.
	 */
	public function extractDetailInfo($json_in, $legal_url, $fi, $mime_filter, $mime_filters, $thumbnail_gen_mode)
	{
		$auto_thumb_gen_mode = ($thumbnail_gen_mode !== 'direct');

		$url = $this->legal2abs_url_path($legal_url);
		$filename = pathinfo($url, PATHINFO_BASENAME);

		// must transform here so alias/etc. expansions inside url_path2file_path() get a chance:
		$file = $this->url_path2file_path($url);

		$isdir = !is_file($file);
		$bad_ext = false;
		$mime = null;
		// only perform the (costly) getID3 scan when it hasn't been done before, i.e. can we re-use previously obtained data or not?
		if (!is_array($fi))
		{
			$fi = $this->getFileInfo($file, $legal_url);
		}
		if (!$isdir)
		{
			if (!empty($fi['mime_type']))
				$mime = $fi['mime_type'];
			if (empty($mime))
				$mime = $this->getMimeType($file, true);

			$mime2 = $this->getMimeType($file, true);
			$fi['mime_type from file extension'] = $mime2;
			$bad_ext = ($mime2 != $mime);
			if ($bad_ext)
			{
				$iconspec = 'is.' + $this->getExtFromMime($mime);
			}
			else
			{
				$iconspec = $filename;
			}

			if (!$this->IsAllowedMimeType($mime, $mime_filters))
				throw new FileManagerException('extension');
		}
		else if (is_dir($file))
		{
			$mime = 'text/directory';
			$iconspec = 'is.dir';

			$fi['mime_type'] = $mime;
		}
		else
		{
			// simply do NOT list anything that we cannot cope with.
			// That includes clearly inaccessible files (and paths) with non-ASCII characters:
			// PHP5 and below are a real mess when it comes to handling Unicode filesystems
			// (see the php.net site too: readdir / glob / etc. user comments and the official
			// notice that PHP will support filesystem UTF-8/Unicode only when PHP6 is released.
			//
			// Big, fat bummer!
			throw new FileManagerException('nofile');
		}

		// as all the work below is quite costly, we check whether the already loaded cache entry got our number:
		$json = false;
		$hash = false;
		$cachefile = false;
		$cache_dir = false;
		$cache_json_entry = false;
		if (!empty($fi['cache_hash']))
		{
			$hash = $fi['cache_hash'];
			$cachefile = $fi['cache_file'];
			$cache_dir = $fi['cache_dir'];

			$cache_json_entry = ($auto_thumb_gen_mode ? 'direct_json' : 'auto_json');
			if (array_key_exists($hash, $this->getid3_cache) && !empty($this->getid3_cache[$hash][$cache_json_entry]))
			{
				$json = $this->getid3_cache[$hash][$cache_json_entry];
				//$json['content'] .= '<p>full info retrieved from RAM/file</p>';
			}
		}

		if ($json === false)
		{
			$thumbnail = $this->getIcon($iconspec, false);
			$thumb48_e = FileManagerUtility::rawurlencode_path($thumbnail);
			$thumb250_e = $thumb48_e;
			$icon = $this->getIcon($iconspec, true);
			$icon_e = FileManagerUtility::rawurlencode_path($icon);

			$json = array_merge(array(
					//'status' => 1,
					//'mimetype' => $mime,
					'content' => self::compressHTML('<div class="margin">
						${nopreview}
					</div>')
				),
				array(
					'path' => FileManagerUtility::rawurlencode_path($url),
					'name' => $filename,
					'date' => date($this->options['dateFormat'], @filemtime($file)),
					'mime' => $mime,
					'thumb48' => $thumb48_e,
					'thumb250' => $thumb250_e,
					'icon' => $icon_e,
					'size' => @filesize($file)
				));


			$content_classes = "margin" . ($bad_ext ? ' preview_err_report' : '');
			$content = '';
			$preview_HTML = null;
			$postdiag_err_HTML = '';
			$postdiag_dump_HTML = '';

			$mime_els = explode('/', $mime);
			for(;;) // bogus loop; only meant to assist the [mime remapping] state machine in here
			{
				$thumb250   = false;
				$thumb250_e = false;
				$thumb48    = false;
				$thumb48_e  = false;

				switch ($mime_els[0])
				{
				case 'image':
					// generates a random number to put on the end of the image, to prevent caching
					//$randomImage = '?'.md5(uniqid(rand(),1));

					//$size = @getimagesize($file);
					//// check for badly formatted image files (corruption); we'll handle the overly large ones next
					//if (!$size)
					//  throw new FileManagerException('corrupt_img:' . $url);

					/*
					 * thumbnail_gen_mode === 'auto':
					 *
					 * offload the thumbnailing process to another event ('event=thumbnail') to be fired by the client
					 * when it's time to render the thumbnail:
					 * WE simply assume the thumbnail will be there, and when it doesn't, that's
					 * for the event=thumbnail handler to worry about (creating the thumbnail on demand or serving
					 * a generic icon image instead). Meanwhile, we are able to speed up the response process here quite
					 * a bit (rendering thumbnails from very large images can take a lot of time!)
					 *
					 * To further improve matters, we first generate the 250px thumbnail and then generate the 48px
					 * thumbnail from that one (if it doesn't already exist). That saves us one more time processing
					 * the (possibly huge) original image; downscaling the 250px file is quite fast, relatively speaking.
					 *
					 * That bit of code ASSUMES that the thumbnail will be generated from the file argument, while
					 * the url argument is used to determine the thumbnail name/path.
					 */
					$meta = null;
					$emsg = null;
					try
					{
						$thumb250 = $this->getThumb($url, $file, $this->options['thumbnailSize'], $this->options['thumbnailSize'], $auto_thumb_gen_mode);
						$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
						$thumb48  = $this->getThumb($url, (($auto_thumb_gen_mode && $thumb250 !== false) ? $this->url_path2file_path($thumb250) : $file), 48, 48, $auto_thumb_gen_mode);
						$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);

						if ($thumb48 === false || $thumb250 === false)
						{
							/*
							 * do NOT generate the thumbnail itself yet (it takes too much time!) but do check whether it CAN be generated
							 * at all: THAT is a (relatively speaking) fast operation!
							 */
							$meta = Image::checkFileForProcessing($file);
						}
						if ($thumb48 === false)
						{
							$thumb48 = null;
							$thumb48_e = $this->mkEventHandlerURL(array(
									'event' => 'thumbnail',
									// directory and filename of the ORIGINAL image should follow next:
									'directory' => pathinfo($legal_url, PATHINFO_DIRNAME),
									'file' => pathinfo($legal_url, PATHINFO_BASENAME),
									'size' => 48,          // thumbnail suitable for 'view/type=thumb' list views
									'filter' => $mime_filter
								));
						}
						if ($thumb250 === false)
						{
							$thumb250 = null;
							$thumb250_e = $this->mkEventHandlerURL(array(
									'event' => 'thumbnail',
									// directory and filename of the ORIGINAL image should follow next:
									'directory' => pathinfo($legal_url, PATHINFO_DIRNAME),
									'file' => pathinfo($legal_url, PATHINFO_BASENAME),
									'size' => 250,         // thumbnail suitable for 'view/type=thumb' list views
									'filter' => $mime_filter
								));
						}
					}
					catch (Exception $e)
					{
						$emsg = $e->getMessage();
						$thumb48 = $this->getIconForError($emsg, $legal_url, false);
						$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
						$thumb250 = $thumb48;
						$thumb250_e = $thumb48_e;
					}

					$json['thumb48'] = $thumb48_e;
					$json['thumb250'] = $thumb250_e;

					$sw_make = $this->mkSafeUTF8($this->getID3infoItem($fi, null, 'jpg', 'exif', 'IFD0', 'Software'));
					$time_make = $this->mkSafeUTF8($this->getID3infoItem($fi, null, 'jpg', 'exif', 'IFD0', 'DateTime'));

					$width = round($this->getID3infoItem($fi, 0, 'video', 'resolution_x'));
					$height = round($this->getID3infoItem($fi, 0, 'video', 'resolution_y'));
					$json['width'] = $width;
					$json['height'] = $height;

					$content = '<dl>
							<dt>${width}</dt><dd>' . $width . 'px</dd>
							<dt>${height}</dt><dd>' . $height . 'px</dd>
						</dl>';
					if (!empty($sw_make) || !empty($time_make))
					{
						$content .= '<p>Made with ' . (empty($sw_make) ? '???' : $sw_make) . ' @ ' . (empty($time_make) ? '???' : $time_make) . '</p>';
					}

					// are we delaying the thumbnail generation? When yes, then we need to infer th thumbnail dimensions *anyway*!
					if (empty($thumb250))
					{
						// to show the loader.gif in the preview <img> tag, we MUST set a width+height there, so we guestimate the thumbnail250 size as accurately as possible
						//
						// derive size from original:
						$dims = $this->predictThumbDimensions($width, $height, 250, 250);
					
						$preview_HTML = '<a href="' . FileManagerUtility::rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $thumb250_e . '" class="preview" alt="preview" style="width: ' . $dims['width'] . 'px; height: ' . $dims['height'] . 'px;" />
									 </a>';
					}
					// else: defer the $preview_HTML production until we're at the end of this and have fetched the actual thumbnail dimensions
					
					if (!empty($emsg))
					{
						// use the abilities of modify_json4exception() to munge/format the exception message:
						$jsa = array('error' => '');
						$this->modify_json4exception($jsa, $emsg, 'path = ' . $url);
						$postdiag_err_HTML .= "\n" . '<p class="err_info">' . $jsa['error'] . '</p>';
					
						if (strpos($emsg, 'img_will_not_fit') !== false)
						{
							$earr = explode(':', $emsg, 2);
							$postdiag_err_HTML .= "\n" . '<p class="tech_info">Estimated minimum memory requirements to create thumbnails for this image: ' . $earr[1] . '</p>';
						}
					}

					if (0)
					{
						$exif_data = $this->getID3infoItem($fi, null, 'jpg', 'exif');
						try
						{
							if (!empty($exif_data))
							{
								/*
								 * before dumping the EXIF data array (which may carry binary content and MAY CRASH the json_encode()r >:-((
								 * we filter it to prevent such crashes and oddly looking (diagnostic) presentation of values.
								 */
								$dump = FileManagerUtility::table_var_dump($exif_data, false);

								self::clean_EXIF_results($exif_data);
								$dump .= var_dump_ex($exif_data, 0, 0, false);
								$postdiag_dump_HTML .= $dump;
							}
						}
						catch (Exception $e)
						{
							// use the abilities of modify_json4exception() to munge/format the exception message:
							$jsa = array('error' => '');
							$this->modify_json4exception($jsa, $e->getMessage());
							$postdiag_err_HTML .= "\n" . '<p class="err_info">' . $jsa['error'] . '</p>';
						}
					}
					break;

				case 'text':
					switch ($mime_els[1])
					{
					case 'directory':
						$preview_HTML = '';
						break;

					default:
						// text preview:
						$filecontent = @file_get_contents($file, false, null, 0);
						if ($filecontent === false)
							throw new FileManagerException('nofile');

						if (!FileManagerUtility::isBinary($filecontent))
						{
							$content_classes .= ' textpreview';
							$preview_HTML = '<pre>' . str_replace(array('$', "\t"), array('&#36;', '&nbsp;&nbsp;'), htmlentities($filecontent, ENT_NOQUOTES, 'UTF-8')) . '</pre>';
						}
						else
						{
							// else: fall back to 'no preview available' (if getID3 didn't deliver instead...)
							$mime_els[0] = 'unknown'; // remap!
							continue 3;
						}
						break;
					}
					break;

				case 'application':
					switch ($mime_els[1])
					{
					case 'x-javascript':
						$mime_els[0] = 'text'; // remap!
						continue 3;

					case 'zip':
						$out = array(array(), array());
						$info = $this->getID3infoItem($fi, null, 'zip', 'files');
						if (is_array($info))
						{
							foreach ($info as $name => $size)
							{
								$name = $this->mkSafeUTF8($name);
								$isdir = is_array($size);
								$out[$isdir ? 0 : 1][$name] = '<li><a><img src="' . FileManagerUtility::rawurlencode_path($this->getIcon($name, true)) . '" alt="" /> ' . $name . '</a></li>';
							}
							natcasesort($out[0]);
							natcasesort($out[1]);
							$preview_HTML = '<ul>' . implode(array_merge($out[0], $out[1])) . '</ul>';
						}
						break;

					case 'x-shockwave-flash':
						$info = $this->getID3infoItem($fi, null, 'swf', 'header');
						if (is_array($info))
						{
							// Note: preview data= urls were formatted like this in CCMS:
							// $this->options['assetBasePath'] . 'dewplayer.swf?mp3=' . rawurlencode($url) . '&volume=30'

							$width = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_width') / 10);
							$height = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_height') / 10);
							$json['width'] = $width;
							$json['height'] = $height;

							$content = '<dl>
									<dt>${width}</dt><dd>' . $width . 'px</dd>
									<dt>${height}</dt><dd>' . $height . 'px</dd>
									<dt>${length}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'swf', 'header', 'length') / $this->getID3infoItem($fi, 25, 'swf', 'header', 'frame_count')) . 's</dd>
								</dl>';
						}
						break;

					default:
						// else: fall back to 'no preview available' (if getID3 didn't deliver instead...)
						$mime_els[0] = 'unknown'; // remap!
						continue 3;
					}
					break;

				case 'audio':
					$dewplayer = FileManagerUtility::rawurlencode_path($this->options['assetBasePath'] . 'dewplayer.swf');


					$title = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'title', 0), 'tags', 'id3v2', 'title', 0));
					$artist = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'artist', 0), 'tags', 'id3v2', 'artist', 0));
					$album = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'album', 0), 'tags', 'id3v2', 'album', 0));

					/*
					<h2>${preview}</h2>
					<div class="object">
					  <object type="application/x-shockwave-flash" data="' . $this->options['assetBasePath'] . '/dewplayer.swf" width="200" height="20" id="dewplayer" name="dewplayer">
						<param name="wmode" value="transparent" />
						<param name="movie" value="' . $this->options['assetBasePath'] . '/dewplayer.swf" />
						<param name="flashvars" value="mp3=' . rawurlencode($url) . '&amp;volume=50&amp;showtime=1" />
					  </object>
					</div>';
					*/
					$content = '<dl>
							<dt>${title}</dt><dd>' . $title . '</dd>
							<dt>${artist}</dt><dd>' . $artist . '</dd>
							<dt>${album}</dt><dd>' . $album . '</dd>
							<dt>${length}</dt><dd>' . $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'playtime_string')) . '</dd>
							<dt>${bitrate}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'bitrate') / 1000) . 'kbps</dd>
						</dl>';
					break;

				case 'video':
					$dewplayer = FileManagerUtility::rawurlencode_path($this->options['assetBasePath'] . 'dewplayer.swf');

					$a_fmt = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'audio', 'dataformat'));
					$a_samplerate = round($this->getID3infoItem($fi, 0, 'audio', 'sample_rate') / 1000, 1);
					$a_bitrate = round($this->getID3infoItem($fi, 0, 'audio', 'bitrate') / 1000, 1);
					$a_bitrate_mode = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'audio', 'bitrate_mode'));
					$a_channels = round($this->getID3infoItem($fi, 0, 'audio', 'channels'));
					$a_codec = $this->mkSafeUTF8($this->getID3infoItem($fi, '', 'audio', 'codec'));
					$a_streams = $this->getID3infoItem($fi, '???', 'audio', 'streams');
					$a_streamcount = (is_array($a_streams) ? count($a_streams) : 0);

					$v_fmt = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'video', 'dataformat'));
					$v_bitrate = round($this->getID3infoItem($fi, 0, 'video', 'bitrate') / 1000, 1);
					$v_bitrate_mode = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'video', 'bitrate_mode'));
					$v_framerate = round($this->getID3infoItem($fi, 0, 'video', 'frame_rate'), 5);
					$v_width = round($this->getID3infoItem($fi, '???', 'video', 'resolution_x'));
					$v_height = round($this->getID3infoItem($fi, '???', 'video', 'resolution_y'));
					$v_par = round($this->getID3infoItem($fi, 1.0, 'video', 'pixel_aspect_ratio'), 7);
					$v_codec = $this->mkSafeUTF8($this->getID3infoItem($fi, '', 'video', 'codec'));

					$g_bitrate = round($this->getID3infoItem($fi, 0, 'bitrate') / 1000, 1);
					$g_playtime_str = $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'playtime_string'));

					$content = '<dl>
							<dt>Audio</dt><dd>';
					if ($a_fmt === '???' && $a_samplerate == 0 && $a_bitrate == 0 && $a_bitrate_mode === '???' && $a_channels == 0 && empty($a_codec) && $a_streams === '???' && $a_streamcount == 0)
					{
						$content .= '-';
					}
					else
					{
						$content .= $a_fmt . (!empty($a_codec) ? ' (' . $a_codec . ')' : '') .
									(!empty($a_channels) ? ($a_channels === 1 ? ' (mono)' : ($a_channels === 2 ? ' (stereo)' : ' (' . $a_channels . ' channels)')) : '') .
									': ' . $a_samplerate . ' kHz @ ' . $a_bitrate . ' kbps (' . strtoupper($a_bitrate_mode) . ')' .
									($a_streamcount > 1 ? ' (' . $a_streamcount . ' streams)' : '');
					}
					$content .= '</dd>
							<dt>Video</dt><dd>' . $v_fmt . (!empty($v_codec) ? ' (' . $v_codec . ')' : '') .  ': ' . $v_framerate . ' fps @ ' . $v_bitrate . ' kbps (' . strtoupper($v_bitrate_mode) . ')' .
												($v_par != 1.0 ? ', PAR: ' . $v_par : '') .
										'</dd>
							<dt>${width}</dt><dd>' . $v_width . 'px</dd>
							<dt>${height}</dt><dd>' . $v_height . 'px</dd>
							<dt>${length}</dt><dd>' . $g_playtime_str . '</dd>
							<dt>${bitrate}</dt><dd>' . $g_bitrate . 'kbps</dd>
						</dl>';
					break;

				default:
					// fall back to 'no preview available' (if getID3 didn't deliver instead...)
					break;
				}

				if (!empty($fi['error']))
				{
					$postdiag_err_HTML .= '<p class="err_info">' . $this->mkSafeUTF8(implode(', ', $fi['error'])) . '</p>';
				}

				try
				{
					$emsgX = null;
					if ($thumb250_e === false)
					{
						// when the ID3 info scanner can dig up an EMBEDDED thumbnail, when we don't have anything else, we're happy with that one!
						$thumb250 = $this->getThumb($url, $file, 250, 250, true);
						if ($thumb250 === false)
						{
							/*
							 * No thumbnail available yet, so find me one!
							 *
							 * When we find a thumbnail during the 'cleanup' scan, we don't know up front if it's suitable to be used directly,
							 * so we treat it as an alternative 'original' file and generate a 250px/48px thumbnail set from it.
							 *
							 * When the embedded thumbnail is small enough, the thumbnail creation process will be simply a copy action, so relatively
							 * low cost.
							 */
							$embed = $this->extract_ID3info_embedded_image($fi);
							//@file_put_contents(dirname(__FILE__) . '/extract_embedded_img.log', print_r(array('html' => $preview_HTML, 'json' => $json, 'thumb250_e' => $thumb250_e, 'thumb250' => $thumb250, 'embed' => $embed, 'fileinfo' => $fi), true));
							if (is_object($embed))
							{
								$thumbX = $this->options['thumbnailPath'] . $this->generateThumbName($url, 'embed');
								$tfi = pathinfo($thumbX);
								$tfi['extension'] = image_type_to_extension($embed->metadata[2]);
								$thumbX = $tfi['dirname'] . '/' . $tfi['filename'] . '.' . $tfi['extension'];
								$thumbX = $this->normalize($thumbX);
								$thumbX_f = $this->url_path2file_path($thumbX);
								// as we've spent some effort to dig out the embedded thumbnail, and 'knowing' (assuming) that generally
								// embedded thumbnails are not too large, we don't concern ourselves with delaying the thumbnail generation (the
								// source file mapping is not bidirectional, either!) and go straight ahead and produce the 250px thumbnail at least.
								$thumb250 = false;
								$thumb48  = false;
								if (false === file_put_contents($thumbX_f, $embed->imagedata))
								{
									@unlink($thumbX_f);
									$emsgX = 'Cannot save embedded image data to cache.';
									$thumb48 = $this->getIcon('is.default-error', false);
									$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
									$thumb250 = $thumb48;
									$thumb250_e = $thumb48_e;
								}
								else
								{
									try
									{
										$thumb250 = $this->getThumb($url, $thumbX_f, 250, 250, false);
										$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
										$thumb48  = $this->getThumb($url, ($thumb250 !== false ? $this->url_path2file_path($thumb250) : $thumbX_f), 48, 48, false);
										$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
									}
									catch (Exception $e)
									{
										$emsgX = $e->getMessage();
										$thumb48 = $this->getIconForError($emsgX, $url, false);
										$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
										$thumb250 = $thumb48;
										$thumb250_e = $thumb48_e;
									}
								}

								if ($thumb250 !== false)
								{
									$json['thumb250'] = $thumb250_e;
								}
								if ($thumb48 !== false)
								{
									$json['thumb48'] = $thumb48_e;
								}
							}
						}
						else
						{
							// $thumb250 !== false
							$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
							try
							{
								$thumb48  = $this->getThumb($url, $this->url_path2file_path($thumb250), 48, 48, false);
								$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
							}
							catch (Exception $e)
							{
								$emsgX = $e->getMessage();
								$thumb48 = $this->getIconForError($emsgX, $url, false);
								$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
							}

							$json['thumb250'] = $thumb250_e;
							if ($thumb48 !== false)
							{
								$json['thumb48'] = $thumb48_e;
							}
						}
					}

					// also provide X/Y size info with each direct-access thumbnail file:
					if (!empty($thumb48) || !empty($thumb250))
					{
						if (!empty($thumb250))
						{
							$tnsize = getimagesize($this->url_path2file_path($thumb250));
							if (is_array($tnsize))
							{
								$json['thumb250-width'] = $tnsize[0];
								$json['thumb250-height'] = $tnsize[1];
								
								if (empty($preview_HTML))
								{
									$preview_HTML = '<a href="' . FileManagerUtility::rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
												   <img src="' . $thumb250_e . '" class="preview" alt="' . (!empty($emsgX) ? $this->mkSafe4HTMLattr($emsgX) : 'preview') . '" 
												        style="width: ' . $tnsize[0] . 'px; height: ' . $tnsize[1] . 'px;" />
											 </a>';
								}
							}

							if ($thumb48 === $thumb250)
							{
								$json['thumb48-width'] = $tnsize[0];
								$json['thumb48-height'] = $tnsize[1];
							}
						}
						if (!empty($thumb48) && $thumb48 !== $thumb250)
						{
							$tnsize = getimagesize($this->url_path2file_path($thumb48));
							if (is_array($tnsize))
							{
								$json['thumb48-width'] = $tnsize[0];
								$json['thumb48-height'] = $tnsize[1];
							}
						}
					}

					$fi4dump = array_merge(array(), $fi); // clone $fi
					$this->clean_ID3info_results($fi4dump);

					$dump = FileManagerUtility::table_var_dump($fi4dump, false);

					if (0)
					{
						self::clean_EXIF_results($fi4dump);
						$dump .= var_dump_ex($fi4dump, 0, 0, false);
					}

					$postdiag_dump_HTML .= "\n" . $dump . "\n";
					//@file_put_contents(dirname(__FILE__) . '/getid3.log', print_r(array('html' => $preview_HTML, 'json' => $json, 'thumb250_e' => $thumb250_e, 'thumb250' => $thumb250, 'embed' => $embed, 'fileinfo' => $fi), true));
				}
				catch(Exception $e)
				{
					$postdiag_err_HTML .= '<p class="err_info">' . $e->getMessage() . '</p>';
				}
				break;
			}

			if ($preview_HTML === null)
			{
				$preview_HTML = '${nopreview}';
			}

			if (!empty($preview_HTML))
			{
				$content .= '<h3>${preview}</h3><div class="filemanager-preview-content">' . $preview_HTML . '</div>';
			}
			if (!empty($postdiag_err_HTML) || !empty($postdiag_dump_HTML))
			{
				$content .= '<h3>Diagnostics</h3><div class="filemanager-detail-diag">';
				if (!empty($postdiag_err_HTML))
				{
					$content .= '<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>';
				}
				if (!empty($postdiag_dump_HTML))
				{
					$content .= '<div class="filemanager-diag-dump">' . $postdiag_dump_HTML . '</div>';
				}
				$content .= '</div>';
			}

			$json['content'] = self::compressHTML('<div class="' . $content_classes . '">' . $content . '</div>');


			// and now store the generated JSON in the RAM+FILE cache:
			if ($hash !== false)
			{
				$this->getid3_cache[$hash][$cache_json_entry] = $json;

				// and save the new entry to file cache as well, so we can reuse it in a future request
				if ($cachefile !== false)
				{
					$data = serialize($this->getid3_cache[$hash]);
					if (!file_exists($cache_dir))
					{
						@mkdir($cache_dir);
					}
					if (false === @file_put_contents($cachefile, $data))
					{
						// destroy failed cache attempt
						@unlink($cachefile);
					}
					//$json['content'] .= '<p>full info</p>';
				}
			}
			//$json['content'] .= '<p>generated</p>';
		}

		return array_merge((is_array($json_in) ? $json_in : array()), $json);
	}

	/**
	 * Traverse the getID3 info[] array tree and fetch the item pointed at by the variable number of indices specified
	 * as additional parameters to this function.
	 *
	 * Return the default value when the indicated element does not exist in the info[] set; otherwise return the located item.
	 *
	 * The purpose of this method is to act as a safe go-in-between for the fileManager to collect arbitrary getID3 data and
	 * not get a PHP error when some item in there does not exist.
	 */
	public /* static */ function getID3infoItem($getid3_info_obj, $default_value /* , ... */ )
	{
		$rv = false;
		$argc = func_num_args();

		for ($i = 2; $i < $argc; $i++)
		{
			if (!is_array($getid3_info_obj))
			{
				return $default_value;
			}

			$index = func_get_arg($i);
			if (array_key_exists($index, $getid3_info_obj))
			{
				$getid3_info_obj = $getid3_info_obj[$index];
			}
			else
			{
				return $default_value;
			}
		}
		// WARNING: convert '$' to the HTML entity to prevent the JS/client side from 'seeing' the $ and start ${xyz} template variable replacement erroneously
		return str_replace('$', '&#36;', $getid3_info_obj);
	}

	// helper function for clean_EXIF_results() as PHP < 5.3 lacks lambda functions
	protected static function __clean_EXIF_results(&$value, $key)
	{
		if (is_string($value))
		{
			//  // $dump may dump object IDs and other binary stuff, which will completely b0rk json_encode: make it palatable:
			//  //$dump = html_entity_encode($value, ENT_NOQUOTES, 'UTF-8');
			//  // strip the NULs out:
			//  $dump = str_replace('&#0;', '?', $dump);
			//  //$dump = html_entity_decode(strip_tags($dump), ENT_QUOTES, 'UTF-8');
			//  // since the regex matcher leaves NUL bytes alone, we do those above in undecoded form; the rest is treated here
			//  $dump = preg_replace("/[^ -~\n\r\t]/", '?', $dump); // remove everything outside ASCII range; some of the high byte values seem to crash json_encode()!
			//  // and reduce long sequences of unknown charcodes:
			//  $dump = preg_replace('/\?{8,}/', '???????', $dump);
			//  //$dump = html_entity_encode(strip_tags($dump), ENT_NOQUOTES, 'UTF-8');

			if (FileManagerUtility::isBinary($value))
			{
				$value = '(binary data... length = ' . strlen($value) . ')';
			}
		}
	}

	protected static function clean_EXIF_results(&$arr)
	{
		// see http://nl2.php.net/manual/en/function.array-walk-recursive.php#81835
		// --> we don't mind about it because we're not worried about the references occurring in here, now or later.
		// Indeed, that does assume we (as in 'we' being this particular function!) know about how the
		// data we process will be used. Risky, but fine with me. Hence the 'protected'.
		array_walk_recursive($arr, 'self::__clean_EXIF_results');
	}


	/**
	 * Extract an embedded image from the getID3 info data.
	 *
	 * Return FALSE when no embedded image was found, otherwise return an array of the metadata and the binary image data itself.
	 */
	protected function extract_ID3info_embedded_image(&$arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $key => &$value)
			{
				if ($key === 'data' && isset($arr['image_mime']))
				{
					// first make sure this is a valid image
					$imageinfo = array();
					$imagechunkcheck = getid3_lib::GetDataImageSize($value, $imageinfo);
					if (is_array($imagechunkcheck) && isset($imagechunkcheck[2]) && in_array($imagechunkcheck[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))
					{
						return new EmbeddedImageContainer($imagechunkcheck, $value);
					}
				}
				else if (is_array($value))
				{
					$rv = $this->extract_ID3info_embedded_image($value);
					if ($rv !== false)
						return $rv;
				}
			}
		}
		return false;
	}

	protected function fold_quicktime_subatoms(&$arr, &$inject, $key_prefix)
	{
		$satms = false;
		if (!empty($arr['subatoms']) && is_array($arr['subatoms']) && !empty($arr['hierarchy']) && !empty($arr['name']) && $arr['hierarchy'] == $arr['name'])
		{
			// fold these up all the way to the root:
			$key_prefix .= '.' . $arr['hierarchy'];

			$satms = $arr['subatoms'];
			unset($arr['subatoms']);
			$inject[$key_prefix] = $satms;
		}

		foreach ($arr as $key => &$value)
		{
			if (is_array($value))
			{
				$this->fold_quicktime_subatoms($value, $inject, $key_prefix);
			}
		}

		if ($satms !== false)
		{
			$this->fold_quicktime_subatoms($inject[$key_prefix], $inject, $key_prefix);
		}
	}

	// assumes an input array with the keys in CamelCase semi-Hungarian Notation. Convert those keys to something humanly grokkable after it is processed by table_var_dump().
	protected function clean_AVI_Hungarian(&$arr)
	{
		$dst = array();
		foreach($arr as $key => &$value)
		{
			$nk = explode('_', preg_replace('/([A-Z])/', '_\\1', $key));
			switch ($nk[0])
			{
			case 'dw':
			case 'n':
			case 'w':
			case 'bi':
				unset($nk[0]);
				break;
			}
			$dst[strtolower(implode('_', $nk))] = $value;
		}
		$arr = $dst;
	}

	// another round of scanning to rewrite the keys to human legibility: as this changes the keys, we'll need to rewrite all entries to keep order intact
	protected function clean_ID3info_keys(&$arr)
	{
		$dst = array();
		foreach($arr as $key => &$value)
		{
			$key = strtr($key, "_\x00", '  ');

			// custom transformations: (hopefully switch/case/case/... is faster than str_replace/strtr here)
			switch ((string)$key)
			{
			default:
				//$key = $this->mkSafeUTF8($key);
				if (preg_match('/[^ -~]/', $key))
				{
					// non-ASCII values in the key: hexdump those characters!
					$klen = strlen($key);
					$nk = '';
					for ($i = 0; $i < $klen; ++$i)
					{
						$c = ord($key[$i]);
						
						if ($c >= 32 && $c <= 127)
						{
							$nk .= chr($c);
						}
						else
						{
							$nk .= sprintf('$%02x', $c);
						}
					}
					$key = $nk;
				}
				break;

			case 'avdataend':
				$key = 'AV data end';
				break;

			case 'avdataoffset':
				$key = 'AV data offset';
				break;

			case 'channelmode':
				$key = 'channel mode';
				break;

			case 'dataformat':
				$key = 'data format';
				break;

			case 'fileformat':
				$key = 'file format';
				break;

			case 'modeextension':
				$key = 'mode extension';
				break;
			}

			$dst[$key] = $value;
			if (is_array($value))
			{
				$this->clean_ID3info_keys($dst[$key]);
			}
		}
		$arr = $dst;
	}

	protected function clean_ID3info_results_r(&$arr, $flags)
	{
		if (is_array($arr))
		{
			// heuristic #1: fold all the quickatoms subatoms using their hierarchy and name fields; this is a tree rewrite
			if (array_key_exists('quicktime', $arr) && is_array($arr['quicktime']))
			{
				$inject = array();
				$this->fold_quicktime_subatoms($arr['quicktime'], $inject, 'quicktime');

				// can't use array_splice on associative arrays, so we rewrite $arr now:
				$newarr = array();
				foreach ($arr as $key => &$value)
				{
					$newarr[$key] = $value;
					if ($key === 'quicktime')
					{
						foreach ($inject as $ik => &$iv)
						{
							$newarr[$ik] = $iv;
						}
					}
				}
				$arr = $newarr;
				unset($inject);
				unset($newarr);
			}

			$activity = true;
			while ($activity)
			{
				$activity = false; // assume there's nothing to do anymore. Prove us wrong now...

				// heuristic #2: when the number of items in the array which are themselves arrays is 80%, contract the set
				$todo = array();
				foreach ($arr as $key => &$value)
				{
					if (is_array($value))
					{
						$acnt = 0;
						foreach ($value as $sk => &$sv)
						{
							if (is_array($sv))
							{
								$acnt++;
							}
						}

						// the floor() here helps to fold single-element arrays alongside! :-)
						if (floor(0.5 * count($value)) <= $acnt)
						{
							$todo[] = $key;
						}
					}
				}
				if (count($todo) > 0)
				{
					$inject = array();
					foreach ($arr as $key => &$value)
					{
						if (is_array($value) && in_array($key, $todo))
						{
							unset($todo[$key]);

							foreach ($value as $sk => &$sv)
							{
								$nk = $key . '.' . $sk;

								// pull up single entry subsubarrays at the same time!
								if (is_array($sv) && count($sv) == 1)
								{
									foreach ($sv as $sk2 => &$sv2)
									{
										$nk .= '.' . $sk2;
										$inject[$nk] = $sv2;
									}
								}
								else
								{
									$inject[$nk] = $sv;
								}
							}
						}
						else
						{
							$inject[$key] = $value;
						}
					}
					$arr = $inject;
					$activity = true;
				}
			}

			foreach ($arr as $key => &$value)
			{
				if ($key === 'data' && isset($arr['image_mime']))
				{
					// when this is a valid image, it's already available as a thumbnail, most probably
					$imageinfo = array();
					$imagechunkcheck = getid3_lib::GetDataImageSize($value, $imageinfo);
					if (is_array($imagechunkcheck) && isset($imagechunkcheck[2]) && in_array($imagechunkcheck[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)))
					{
						if ($flags & MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES)
						{
							$value = new BinaryDataContainer('(embedded image ' . image_type_to_extension($imagechunkcheck[2]) . ' data...)');
						}
						else
						{
							$value = new EmbeddedImageContainer($imagechunkcheck, $value);
						}
					}
					else
					{
						if ($flags & MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES)
						{
							$value = new BinaryDataContainer('(unidentified image data ... ' . (is_string($value) ? 'length = ' . strlen($value) : '') . ' -- ' . print_r($imagechunkcheck) . ')');
						}
					}
				}
				else if (isset($arr[$key . '_guid']))
				{
					// convert guid raw binary data to hex:
					$temp = unpack('H*', $value);
					$value = new BinaryDataContainer($temp[1]);
				}
				else if (  $key === 'non_intra_quant'                                                                       // MPEG quantization matrix
						|| $key === 'error_correct_type'
						)
				{
					// convert raw binary data to hex in 32 bit chunks:
					$temp = unpack('H*', $value);
					$temp = str_split($temp[1], 8);
					$value = new BinaryDataContainer(implode(' ', $temp));
				}
				else if ($key === 'data' && is_string($value) && isset($arr['frame_name']) && isset($arr['encoding']) && isset($arr['datalength'])) // MP3 tag chunk
				{
					$str = $this->mkSafeUTF8(trim(strtr(getid3_lib::iconv_fallback($arr['encoding'], 'UTF-8', $value), "\x00", ' ')));
					$temp = unpack('H*', $value);
					$temp = str_split($temp[1], 8);
					$value = new BinaryDataContainer(implode(' ', $temp) . (!empty($str) ? ' (' . $str . ')' : ''));
				}
				else if (  ($key === 'data' && is_string($value) && isset($arr['offset']) && isset($arr['size']))           // AVI offset/size/data items: data = binary
						|| ($key === 'type_specific_data' && is_string($value) /* && isset($arr['type_specific_len']) */ )  // munch WMV/RM 'type specific data': binary   ('type specific len' will occur alongside, but not in WMV)
						)
				{
					// a bit like UNIX strings tool: strip out anything which isn't at least possibly legible
					$str = ' ' . preg_replace('/[^ !#-~]/', ' ', strtr($value, "\x00", ' ')) . ' ';     // convert non-ASCII and double quote " to space
					do
					{
						$repl_count = 0;
						$str = preg_replace(array('/ [^ ] /',
												  '/ [^A-Za-z0-9\\(.]/',
												  '/ [A-Za-z0-9][^A-Za-z0-9\\(. ] /'
												 ), ' ', $str, -1, $repl_count);
					} while ($repl_count > 0);
					$str = trim($str);

					if (strlen($value) <= 256)
					{
						$temp = unpack('H*', $value);
						$temp = str_split($temp[1], 8);
						$temp = implode(' ', $temp);
						$value = new BinaryDataContainer($temp . (!empty($str) > 0 ? ' (' . $str . ')' : ''));
					}
					else
					{
						$value = new BinaryDataContainer('binary data... (length = ' . strlen($value) . ' bytes)');
					}
				}
				else if (is_scalar($value) && preg_match('/^(dw[A-Z]|n[A-Z]|w[A-Z]|bi[A-Z])[a-zA-Z]+$/', $key))
				{
					// AVI sections which use Hungarian notation, at least partially
					$this->clean_AVI_Hungarian($arr);
					// and rescan the transformed key set...
					$this->clean_ID3info_results($arr);
					break; // exit this loop
				}
				else if (is_array($value))
				{
					// heuristic #3: when the value is an array of integers, implode them to a comma-separated list (string) instead:
					$is_all_ints = true;
					for ($sk = count($value) - 1; $sk >= 0; --$sk)
					{
						if (!array_key_exists($sk, $value) || !is_int($value[$sk]))
						{
							$is_all_ints = false;
							break;
						}
					}
					if ($is_all_ints)
					{
						$s = implode(', ', $value);
						$value = $s;
					}
					else
					{
						$this->clean_ID3info_results_r($value, $flags);
					}
				}
				else
				{
					$this->clean_ID3info_results_r($value, $flags);
				}
			}
		}
		else if (is_string($arr))
		{
			// is this a cleaned up item? Yes, then there's a full-ASCII string here, sans newlines, etc.
			$len = strlen($arr);
			$value = rtrim($arr, "\x00");
			$value = strtr($value, "\x00", ' ');    // because preg_match() doesn't 'see' NUL bytes...
			if (preg_match("/[^ -~\n\r\t]/", $value))
			{
				if ($len > 0 && $len < 256)
				{
					// check if we can turn it into something UTF8-LEGAL; when not, we hexdump!
					$im = str_replace('?', '&QMaRK;', $value);
					$dst = $this->mkSafeUTF8($im);
					if (strpos($dst, '?') === false)
					{
						// it's a UTF-8 legal string now!
						$arr = str_replace('&QMaRK;', '?', $dst);
					}
					else
					{
						// convert raw binary data to hex in 32 bit chunks:
						$temp = unpack('H*', $arr);
						$temp = str_split($temp[1], 8);
						$arr = new BinaryDataContainer(implode(' ', $temp));
					}
				}
				else
				{
					$arr = new BinaryDataContainer('(unidentified binary data ... length = ' . strlen($arr) . ')');
				}
			}
			else
			{
				$arr = $value;   // don't store as a 'processed' item (shortcut)
			}
		}
		else if (is_bool($arr) ||
				 is_int($arr) ||
				 is_float($arr) ||
				 is_null($arr))
		{
		}
		else if (is_object($arr) && !isset($arr->id3_procsupport_obj))
		{
			$arr = new BinaryDataContainer('(object) ' . $this->mkSafeUTF8(print_r($arr, true)));
		}
		else if (is_resource($arr))
		{
			$arr = new BinaryDataContainer('(resource) ' . $this->mkSafeUTF8(print_r($arr, true)));
		}
		else
		{
			$arr = new BinaryDataContainer('(unidentified type: ' . gettype($arr) . ') ' . $this->mkSafeUTF8(print_r($arr, true)));
		}
	}

	protected function clean_ID3info_results(&$arr, $flags = 0)
	{
		unset($arr['GETID3_VERSION']);
		unset($arr['filepath']);
		unset($arr['filename']);
		unset($arr['filenamepath']);
		unset($arr['cache_hash']);
		unset($arr['cache_file']);
		unset($arr['cache_dir']);

		$this->clean_ID3info_results_r($arr, $flags);

		// heuristic #4: convert keys to something legible:
		if (is_array($arr))
		{
			$this->clean_ID3info_keys($arr);
		}
	}








	/**
	 * Delete a file or directory, inclusing subdirectories and files.
	 *
	 * Return TRUE on success, FALSE when an error occurred.
	 *
	 * Note that the routine will try to persevere and keep deleting other subdirectories
	 * and files, even when an error occurred for one or more of the subitems: this is
	 * a best effort policy.
	 */
	protected function unlink($legal_url, $mime_filters)
	{
		$rv = true;

		// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
		$file = $this->legal_url_path2file_path($legal_url);

		if(is_dir($file))
		{
			$dir = self::enforceTrailingSlash($file);
			$url = self::enforceTrailingSlash($legal_url);
			$coll = $this->scandir($dir, '*', false, 0, ~GLOB_NOHIDDEN);
			if ($coll !== false)
			{
				foreach ($coll['dirs'] as $f)
				{
					if($f === '.' || $f === '..')
						continue;

					$rv &= $this->unlink($url . $f, $mime_filters);
				}
				foreach ($coll['files'] as $f)
				{
					$rv &= $this->unlink($url . $f, $mime_filters);
				}
			}
			else
			{
				$rv = false;
			}

			$rv &= @rmdir($file);
		}
		else if (file_exists($file))
		{
			if (is_file($file))
			{
				$mime = $this->getMimeType($file, true);   // take the fast track to mime type sniffing; we'll live with the (rather low) risk of being inacurate due to accidental/intentional misnaming of the files
				if (!$this->IsAllowedMimeType($mime, $mime_filters))
					return false;
			}

			$rv2 = @unlink($file);
			if ($rv2)
				$rv &= $this->deleteThumb($legal_url);
			else
				$rv = false;
		}
		return $rv;
	}

	/**
	 * glob() wrapper: accepts the same options as Tooling.php::safe_glob()
	 *
	 * However, this method will also ensure the '..' directory entry is only returned,
	 * even while asked for, when the parent directory can be legally traversed by the FileManager.
	 *
	 * Return a dual array (possibly empty) of directories and files, or FALSE on error.
	 *
	 * IMPORTANT: this function GUARANTEES that, when present at all, the double-dot '..'
	 *            entry is the very last entry in the array.
	 *            This guarantee is important as onView() et al depend on it.
	 */
	public function scandir($dir, $filemask, $see_thumbnail_dir, $glob_flags_or, $glob_flags_and)
	{
		// list files, except the thumbnail folder itself or any file in it:
		$dir = self::enforceTrailingSlash($dir);

		$just_below_thumbnail_dir = false;
		if (!$see_thumbnail_dir)
		{
			$tnpath = $this->url_path2file_path($this->options['thumbnailPath']);
			if (FileManagerUtility::startswith($dir, $tnpath))
				return false;

			$tnparent = $this->url_path2file_path(self::getParentDir($this->options['thumbnailPath']));
			$just_below_thumbnail_dir = ($dir == $tnparent);

			$tndir = basename(substr($this->options['thumbnailPath'], 0, -1));
		}

		$at_basedir = ($this->options['assumed_base_filepath'] == $dir);

		$flags = GLOB_NODOTS | GLOB_NOHIDDEN | GLOB_NOSORT;
		$flags &= $glob_flags_and;
		$flags |= $glob_flags_or;
		$coll = safe_glob($dir . $filemask, $flags);

		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);

		if ($coll !== false)
		{
			if ($just_below_thumbnail_dir)
			{
				foreach($coll['dirs'] as $k => $dir)
				{
					if ($dir === $tndir)
					{
						unset($coll['dirs'][$k]);
						break;
					}
				}
			}

			if (!$at_basedir)
			{
				$coll['dirs'][] = '..';
			}

			//$coll['special_indir_mappings'] = array(array(), array());
		}

		//FM_vardumper($this, __FUNCTION__ . ' @ ' . __LINE__);

		return $coll;
	}



	/**
	 * Check the $extension argument and replace it with a suitable 'safe' extension.
	 */
	public function getSafeExtension($extension, $safe_extension = 'txt', $mandatory_extension = 'txt')
	{
		if (!is_string($extension) || $extension === '') // can't use 'empty($extension)' as "0" is a valid extension itself.
		{
			//enforce a mandatory extension, even when there isn't one (due to filtering or original input producing none)
			return (!empty($mandatory_extension) ? $mandatory_extension : (!empty($safe_extension) ? $safe_extension : $extension));
		}
		$extension = strtolower($extension);
		switch ($extension)
		{
		case 'exe':
		case 'dll':
		case 'com':
		case 'sys':
		case 'bat':
		case 'pl':
		case 'sh':
		case 'php':
		case 'php3':
		case 'php4':
		case 'php5':
		case 'phps':
			return (!empty($safe_extension) ? $safe_extension : $extension);

		default:
			return $extension;
		}
	}

	/**
	 * Only allow a 'dotted', i.e. UNIX hidden filename when options['safe'] == FALSE
	 */
	public function IsHiddenNameAllowed($file)
	{
		if ($this->options['safe'] && !empty($file))
		{
			if ($file !== '.' && $file !== '..' && $file[0] === '.')
			{
				return false;
			}
		}
		return true;
	}

	public function IsHiddenPathAllowed($path)
	{
		if ($this->options['safe'] && !empty($path))
		{
			$path = strtr($path, '\\', '/');
			$segs = explode('/', $path);
			foreach($segs as $file)
			{
				if (!$this->IsHiddenNameAllowed($file))
				{
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * Make a cleaned-up, unique filename
	 *
	 * Return the file (dir + name + ext), or a unique, yet non-existing, variant thereof, where the filename
	 * is appended with a '_' and a number, e.g. '_1', when the file itself already exists in the given
	 * directory. The directory part of the returned value equals $dir.
	 *
	 * Return NULL when $file is empty or when the specified directory does not reside within the
	 * directory tree rooted by options['directory']
	 *
	 * Note that the given filename will be converted to a legal filename, containing a filesystem-legal
	 * subset of ASCII characters only, before being used and returned by this function.
	 *
	 * @param mixed $fileinfo     either a string containing a filename+ext or an array as produced by pathinfo().
	 * @daram string $dir         path pointing at where the given file may exist.
	 *
	 * @return a filepath consisting of $dir and the cleaned up and possibly sequenced filename and file extension
	 *         as provided by $fileinfo.
	 */
	public function getUniqueName($fileinfo, $dir)
	{
		$dir = self::enforceTrailingSlash($dir);

		if (is_string($fileinfo))
		{
			$fileinfo = pathinfo($fileinfo);
		}

		if (!is_array($fileinfo))
		{
			return null;
		}
		$dotfile = (strlen($fileinfo['filename']) == 0);

		/*
		 * since 'pagetitle()' is used to produce a unique, non-existing filename, we can forego the dirscan
		 * and simply check whether the constructed filename/path exists or not and bump the suffix number
		 * by 1 until it does not, thus quickly producing a unique filename.
		 *
		 * This is faster than using a dirscan to collect a set of existing filenames and feeding them as
		 * an option array to pagetitle(), particularly for large directories.
		 */
		$filename = FileManagerUtility::pagetitle($fileinfo['filename'], null, '-_., []()~!@+' /* . '#&' */, '-_,~@+#&');
		if (!$filename && !$dotfile)
			return null;

		// also clean up the extension: only allow alphanumerics in there!
		$ext = FileManagerUtility::pagetitle(isset($fileinfo['extension']) ? $fileinfo['extension'] : null);
		$ext = (strlen($ext) > 0 ? '.' . $ext : null);
		// make sure the generated filename is SAFE:
		$fname = $filename . $ext;
		$file = $dir . $fname;
		if (file_exists($file))
		{
			if ($dotfile)
			{
				$filename = $fname;
				$ext = '';
			}

			/*
			 * make a unique name. Do this by postfixing the filename with '_X' where X is a sequential number.
			 *
			 * Note that when the input name is already such a 'sequenced' name, the sequence number is
			 * extracted from it and sequencing continues from there, hence input 'file_5' would, if it already
			 * existed, thus be bumped up to become 'file_6' and so on, until a filename is found which
			 * does not yet exist in the designated directory.
			 */
			$i = 1;
			if (preg_match('/^(.*)_([1-9][0-9]*)$/', $filename, $matches))
			{
				$i = intval($matches[2]);
				if ('P'.$i !== 'P'.$matches[2] || $i > 100000)
				{
					// very large number: not a sequence number!
					$i = 1;
				}
				else
				{
					$filename = $matches[1];
				}
			}
			do
			{
				$fname = $filename . ($i ? '_' . $i : '') . $ext;
				$file = $dir . $fname;
				$i++;
			} while (file_exists($file));
		}

		// $fname is now guaranteed to NOT exist in the given directory
		return $fname;
	}

	
	
	/**
	 * Predict the actual width/height dimensions of the thumbnail, given the original image's dimensions and the given size limits.
	 *
	 * Note: exists as a method in this class, so you can override it when you override getThumb().
	 */
	public function predictThumbDimensions($orig_x, $orig_y, $max_x = null, $max_y = null, $ratio = true, $resizeWhenSmaller = false)
	{
		return Image::calculate_resize_dimensions($orig_x, $orig_y, $max_x, $max_y, $ratio, $resizeWhenSmaller);
	}
	
	
	/**
	 * Returns the URI path to the apropriate icon image for the given file / directory.
	 *
	 * NOTES:
	 *
	 * 1) any $path with an 'extension' of '.dir' is assumed to be a directory.
	 *
	 * 2) This method specifically does NOT check whether the given path exists or not: it just looks at
	 *    the filename extension passed to it, that's all.
	 *
	 * Note #2 is important as this enables this function to also serve as icon fetcher for ZIP content viewer, etc.:
	 * after all, those files do not exist physically on disk themselves!
	 */
	public function getIcon($file, $smallIcon)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		if (array_key_exists($ext, $this->icon_cache[!$smallIcon]))
		{
			return $this->icon_cache[!$smallIcon][$ext];
		}

		$largeDir = (!$smallIcon ? 'Large/' : '');
		$url_path = $this->options['assetBasePath'] . 'Images/Icons/' . $largeDir . $ext . '.png';
		$path = (is_file($this->url_path2file_path($url_path)))
			? $url_path
			: $this->options['assetBasePath'] . 'Images/Icons/' . $largeDir . 'default.png';

		$this->icon_cache[!$smallIcon][$ext] = $path;

		return $path;
	}

	/**
	 * Return the path to the thumbnail of the specified image, the thumbnail having its
	 * width and height limited to $width pixels.
	 *
	 * When the thumbnail image does not exist yet, it will created on the fly.
	 *
	 * @param string $legal_url    the LEGAL URL path to the original image. Is used solely
	 *                             to generate a suitable thumbnail filename.
	 *
	 * @param string $path         filesystem path to the original image. Is used to derive
	 *                             the thumbnail content from.
	 *
	 * @param integer $width       the maximum number of pixels for width of the
	 *                             thumbnail.
	 *
	 * @param integer $height      the maximum number of pixels for height of the
	 *                             thumbnail.
	 */
	public function getThumb($legal_url, $path, $width, $height, $onlyIfExistsInCache = false)
	{
		$thumb = $this->generateThumbName($legal_url, $width);
		$thumbPath = $this->url_path2file_path($this->options['thumbnailPath'] . $thumb);
		if (!is_file($thumbPath))
		{
			if ($onlyIfExistsInCache)
				return false;

			if (!file_exists(dirname($thumbPath)))
			{
				@mkdir(dirname($thumbPath), $this->options['chmod'], true);
			}
			$img = new Image($path);
			// generally save as lossy / lower-Q jpeg to reduce filesize, unless orig is PNG/GIF, higher quality for smaller thumbnails:
			$img->resize($width, $height)->save($thumbPath, min(98, max(MTFM_THUMBNAIL_JPEG_QUALITY, MTFM_THUMBNAIL_JPEG_QUALITY + 0.15 * (250 - min($width, $height)))), true);

			if (DEVELOPMENT)
			{
				$meta = $img->getMetaInfo();

				$meta['mem_usage'] = array(
					'memory used' => number_format(memory_get_peak_usage() / 1E6, 1) . ' MB',
					'memory estimated' => number_format(@$meta['fileinfo']['usage_guestimate'] / 1E6, 1) . ' MB',
					'memory suggested' => number_format(@$meta['fileinfo']['usage_min_advised'] / 1E6, 1) . ' MB'
				);

				//FM_vardumper($this, 'getThumb', $meta);
			}

			unset($img);
		}
		return $this->options['thumbnailPath'] . $thumb;
	}

	/**
	 * Assistant function which produces the best possible icon image path for the given error/exception message.
	 */
	public function getIconForError($emsg, $original_filename, $small_icon)
	{
		if (empty($emsg))
		{
			// just go and pick the extension-related icon for this one; nothing is wrong today, it seems.
			$thumb_path = (!empty($original_filename) ? $original_filename : 'is.default-missing');
		}
		else
		{
			$thumb_path = 'is.default-error';

			if (strpos($emsg, 'img_will_not_fit') !== false)
			{
				$thumb_path = 'is.oversized_img';
			}
			else if (strpos($emsg, 'nofile') !== false)
			{
				$thumb_path = 'is.default-missing';
			}
			else if (strpos($emsg, 'unsupported_imgfmt') !== false)
			{
				// just go and pick the extension-related icon for this one; nothing seriously wrong here.
				$thumb_path = (!empty($original_filename) ? $original_filename : $thumb_path);
			}
			else if (strpos($emsg, 'image') !== false)
			{
				$thumb_path = 'badly.broken_img';
			}
		}

		$img_filepath = $this->getIcon($thumb_path, $small_icon);

		return $img_filepath;
	}

	/**
	 * Make sure the generated thumbpath is unique for each file. To prevent
	 * reduced performance for large file sets: all thumbnails derived from any files in the entire
	 * FileManager-managed directory tree, rooted by options['directory'], can become a huge collection,
	 * so we distribute them across a directory tree, which is created on demand.
	 *
	 * The thumbnails directory tree is determined by the MD5 of the full path to the image,
	 * using the first two characters of the MD5, making for a span of 256.
	 *
	 * Note: when you expect to manage a really HUGE file collection from FM, you may dial up the
	 *       $number_of_dir_levels to 2 here.
	 */
	protected function generateThumbName($legal_url, $width = 250, $number_of_dir_levels = MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE)
	{
		$fi = pathinfo($legal_url);
		$ext = strtolower((isset($fi['extension']) && strlen($fi['extension']) > 0) ? $fi['extension'] : '');
		switch ($ext)
		{
		case 'gif':
		case 'png':
		case 'jpg':
		case 'jpeg':
			break;

		case 'mp3':
		case 'mp3':
			// default to JPG, as embedded images don't contain transparency info:
			$ext = 'jpg';
			break;

		default:
			//$ext = preg_replace('/[^A-Za-z0-9]+/', '_', $ext);

			// default to PNG, as it'll handle transparancy and full color both:
			$ext = 'png';
			break;
		}

		// as the Thumbnail is generated, but NOT guaranteed from a safe filepath (FM may be visiting unsafe
		// image files when they exist in a preloaded directory tree!) we do the full safe-filename transform
		// on the name itself.
		// The MD5 is taken from the untrammeled original, though:
		$dircode = md5($legal_url);

		$rv = '';
		for ($i = 0; $i < $number_of_dir_levels; $i++)
		{
			$rv .= substr($dircode, 0, 2) . '/';
			$dircode = substr($dircode, 2);
		}

		$fn = '_' . $fi['filename'];
		$fn = substr($dircode, 0, 4) . preg_replace('/[^A-Za-z0-9]+/', '_', $fn);
		$fn = substr($fn . $dircode, 0, 38);

		$rv .= $fn . '-' . $width . '.' . $ext;
		return $rv;
	}

	protected function deleteThumb($legal_url)
	{
		// generate a thumbnail name with embedded wildcard for the size parameter:
		$thumb = $this->generateThumbName($legal_url, '*');
		$tfi = pathinfo($thumb);
		$thumbnail_subdir = $tfi['dirname'];
		$thumbPath = $this->url_path2file_path($this->options['thumbnailPath'] . $thumbnail_subdir);
		$thumbPath = self::enforceTrailingSlash($thumbPath);

		// remove thumbnails (any size) and any other related cached files (TODO: future version should cache getID3 metadata as well -- and delete it here!)
		$coll = $this->scandir($thumbPath, $tfi['filename'] . '.*', true, 0, ~GLOB_NOHIDDEN);

		$rv = true;
		if ($coll !== false)
		{
			foreach($coll['files'] as $filename)
			{
				$file = $thumbPath . $filename;
				$rv &= @unlink($file);
			}
		}

		// as the thumbnail subdirectory may now be entirely empty, try to remove it as well,
		// but do NOT yack when we don't succeed: there may be other thumbnails, etc. in there still!

		while ($thumbnail_subdir > '/')
		{
			// try to NOT delete the thumbnails base directory itself; we MAY not be able to recreate it later on demand!
			$thumbPath = $this->url_path2file_path($this->options['thumbnailPath'] . $thumbnail_subdir);
			@rmdir($thumbPath);

			$thumbnail_subdir = self::getParentDir($thumbnail_subdir);
		}

		return $rv;   // when thumbnail does not exist, say it is succesfully removed: all that counts is it doesn't exist anymore when we're done here.
	}








	/**
	 * Convert the given content to something that is safe to copy straight to HTML
	 */
	public function mkSafe4Display($str)
	{
		// only allow ASCII to pass:
		$str = preg_replace("/[^ -~\t\r\n]/", '?', $str);
		$str = str_replace('%3C', '?', $str);             // in case someone want's to get really fancy: nuke the URLencoded '<'

		// anything that's more complex than a simple <TAG> is nuked:
		$str = preg_replace('/<([\/]?script\s*[\/]?)>/', '?', $str);               // but first make sure '<script>' doesn't make it through the next regex!
		$str = preg_replace('/<([\/]?[a-zA-Z]+\s*[\/]?)>/', "\x04\\1\x05", $str);
		$str = strip_tags($str);
		$str = strtr($str, "\x04", '<');
		$str = strtr($str, "\x05", '>');
		return $str;
	}
	

	/**
 	 * Make data suitable for inclusion in a HTML tag attribute value: strip all tags and encode quotes! 
	 */
	public function mkSafe4HTMLattr($str)
	{
		$str = str_replace('%3C', '?', $str);             // in case someone want's to get really fancy: nuke the URLencoded '<'
		$str = strip_tags($str);
		return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}
	
	/**
	 * inspired by http://nl3.php.net/manual/en/function.utf8-encode.php#102382; mix & mash to make sure the result is LEGAL UTF-8
	 *
	 * Introduced after the JSON encoder kept spitting out 'null' instead of a string value for a few choice French JPEGs with very non-UTF EXIF content. :-(
	 */
	public function mkSafeUTF8($str) 
	{
		// kill NUL bytes: they don't belong in here!
		$str = strtr($str, "\x00", ' ');
		
		if (!mb_check_encoding($str, 'UTF-8') || $str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')) 
		{
			$encoding = mb_detect_encoding($str, 'auto, ISO-8859-1', true);
			$im = str_replace('?', '&qmark;', $str);
			if ($encoding !== false)
			{
				$dst = mb_convert_encoding($im, 'UTF-8', $encoding);
			}
			else
			{
				$dst = mb_convert_encoding($im, 'UTF-8');
			}
			//$dst = utf8_encode($im);
			//$dst = getid3_lib::iconv_fallback('ISO-8859-1', 'UTF-8', $im);

			if (!mb_check_encoding($dst, 'UTF-8') || $dst !== mb_convert_encoding(mb_convert_encoding($dst, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') || strpos($dst, '?') !== false)
			{
				// not UTF8 yet... try them all
				$encs = mb_list_encodings();
				foreach ($encs as $encoding)
				{
					$dst = mb_convert_encoding($im, 'UTF-8', $encoding);
					if (mb_check_encoding($dst, 'UTF-8') && $dst === mb_convert_encoding(mb_convert_encoding($dst, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') && strpos($dst, '?') === false)
					{
						return str_replace('&qmark;', '?', $dst);
					}
				}
				
				// when we get here, it's pretty hopeless. Strip ANYTHING that's non-ASCII:
				return preg_replace("/[^ -~\t\r\n]/", '?', $str);
			}

			// UTF8 cannot contain low-ASCII values; at least WE do not allow that!
			if (preg_match("/[^ -\xFF\n\r\t]/", $dst))
			{
				// weird output that's not legible anyhow, so strip ANYTHING that's non-ASCII:
				return preg_replace("/[^ -~\t\r\n]/", '?', $str);
			}
			return str_replace('&qmark;', '?', $dst);
		}
		return $str;
	}

	

	/**
	 * Safe replacement of dirname(); does not care whether the input has a trailing slash or not.
	 *
	 * Return FALSE when the path is attempting to get the parent of '/'
	 */
	public static function getParentDir($path)
	{
		/*
		 * on Windows, you get:
		 *
		 * dirname("/") = "\"
		 * dirname("y/") = "."
		 * dirname("/x") = "\"
		 *
		 * so we'd rather not use dirname()   :-(
		 */
		if (!is_string($path))
			return false;
		$path = rtrim($path, '/');
		// empty directory or a path with only 1 character in it cannot be a parent+child: that would be 2 at the very least when it's '/a': parent is root '/' then:
		if (strlen($path) <= 1)
			return false;

		$p2 = strrpos($path, '/' /* , -1 */ );  // -1 as extra offset is not good enough? Nope. At least not for my Win32 PHP 5.3.1. Yeah, sounds like a PHP bug to me. So we rtrim() now...
		if ($p2 === false)
		{
			return false; // tampering!
		}
		$prev = substr($path, 0, $p2 + 1);
		return $prev;
	}

	/**
	 * Return the URI absolute path to the script pointed at by the current URI request.
	 * For example, if the request was 'http://site.org/dir1/dir2/script.php', then this method will
	 * return '/dir1/dir2/script.php'.
	 *
	 * This is equivalent to $_SERVER['SCRIPT_NAME']
	 */
	public /* static */ function getRequestScriptURI()
	{
		// see also: http://php.about.com/od/learnphp/qt/_SERVER_PHP.htm
		$path = strtr($_SERVER['SCRIPT_NAME'], '\\', '/');

		return $path;
	}

	/**
	 * Return the URI absolute path to the directory pointed at by the current URI request.
	 * For example, if the request was 'http://site.org/dir1/dir2/script', then this method will
	 * return '/dir1/dir2/'.
	 *
	 * Note that the path is returned WITH a trailing slash '/'.
	 */
	public /* static */ function getRequestPath()
	{
		// see also: http://php.about.com/od/learnphp/qt/_SERVER_PHP.htm
		$path = self::getParentDir($this->getRequestScriptURI());
		$path = self::enforceTrailingSlash($path);

		return $path;
	}

	/**
	 * Normalize an absolute path by converting all slashes '/' and/or backslashes '\' and any mix thereof in the
	 * specified path to UNIX/MAC/Win compatible single forward slashes '/'.
	 *
	 * Also roll up any ./ and ../ directory elements in there.
	 *
	 * Throw an exception when the operation failed to produce a legal path.
	 */
	public /* static */ function normalize($path)
	{
		$path = preg_replace('/(\\\|\/)+/', '/', $path);

		/*
		 * fold '../' directory parts to prevent malicious paths such as 'a/../../../../../../../../../etc/'
		 * from succeeding
		 *
		 * to prevent screwups in the folding code, we FIRST clean out the './' directories, to prevent
		 * 'a/./.././.././.././.././.././.././.././.././../etc/' from succeeding:
		 */
		$path = preg_replace('#/(\./)+#', '/', $path);

		// now temporarily strip off the leading part up to the colon to prevent entries like '../d:/dir' to succeed when the site root is 'c:/', for example:
		$lead = '';
		// the leading part may NOT contain any directory separators, as it's for drive letters only.
		// So we must check in order to prevent malice like /../../../../../../../c:/dir from making it through.
		if (preg_match('#^([A-Za-z]:)?/(.*)$#', $path, $matches))
		{
			$lead = $matches[1];
			$path = '/' . $matches[2];
		}

		while (($pos = strpos($path, '/..')) !== false)
		{
			$prev = substr($path, 0, $pos);
			/*
			 * on Windows, you get:
			 *
			 * dirname("/") = "\"
			 * dirname("y/") = "."
			 * dirname("/x") = "\"
			 *
			 * so we'd rather not use dirname()   :-(
			 */
			$p2 = strrpos($prev, '/');
			if ($p2 === false)
			{
				throw new FileManagerException('path_tampering:' . $path);
			}
			$prev = substr($prev, 0, $p2);
			$next = substr($path, $pos + 3);
			if ($next && $next[0] !== '/')
			{
				throw new FileManagerException('path_tampering:' . $path);
			}
			$path = $prev . $next;
		}

		$path = $lead . $path;

		/*
		 * iff there was such a '../../../etc/' attempt, we'll know because there'd be an exception thrown in the loop above.
		 */

		return $path;
	}


	/**
	 * Accept a URI relative or absolute path and transform it to an absolute URI path, i.e. rooted against DocumentRoot.
	 *
	 * Relative paths are assumed to be relative to the current request path, i.e. the getRequestPath() produced path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized URI absolute path.
	 */
	public function rel2abs_url_path($path)
	{
		$path = strtr($path, '\\', '/');
		if (!FileManagerUtility::startsWith($path, '/'))
		{
			$based = $this->getRequestPath();
			$path = $based . $path;
		}
		return $this->normalize($path);
	}

	/**
	 * Accept an absolute URI path, i.e. rooted against DocumentRoot, and transform it to a LEGAL URI absolute path, i.e. rooted against options['directory'].
	 *
	 * Relative paths are assumed to be relative to the current request path, i.e. the getRequestPath() produced path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw a FileManagerException
	 *
	 * Returns a fully normalized LEGAL URI path.
	 *
	 * Throws a FileManagerException when the given path cannot be converted to a LEGAL URL, i.e. when it resides outside the options['directory'] subtree.
	 */
	public function abs2legal_url_path($path)
	{
		$root = $this->options['directory'];

		$path = $this->rel2abs_url_path($path);

		// but we MUST make sure the path is still a LEGAL URI, i.e. sitting inside options['directory']:
		if (strlen($path) < strlen($root))
			$path = self::enforceTrailingSlash($path);

		if (!FileManagerUtility::startsWith($path, $root))
		{
			throw new FileManagerException('path_tampering:' . $path);
		}

		$path = str_replace($root, '/', $path);

		return $path;
	}

	/**
	 * Accept a URI relative or absolute LEGAL URI path and transform it to an absolute URI path, i.e. rooted against DocumentRoot.
	 *
	 * Relative paths are assumed to be relative to the current request path, i.e. the getRequestPath() produced path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw a FileManagerException
	 *
	 * Returns a fully normalized URI absolute path.
	 */
	public function legal2abs_url_path($path)
	{
		$root = $this->options['directory'];

		$path = strtr($path, '\\', '/');
		if (FileManagerUtility::startsWith($path, '/'))
		{
			// clip the trailing '/' off the $root path as $path has a leading '/' already:
			$path = substr($root, 0, -1) . $path;
		}

		$path = $this->rel2abs_url_path($path);

		// but we MUST make sure the path is still a LEGAL URI, i.e. sutting inside options['directory']:
		if (strlen($path) < strlen($root))
			$path = self::enforceTrailingSlash($path);

		if (!FileManagerUtility::startsWith($path, $root))
		{
			throw new FileManagerException('path_tampering:' . $path);
		}
		return $path;
	}

	/**
	 * Accept a URI relative or absolute LEGAL URI path and transform it to an absolute LEGAL URI path, i.e. rooted against options['directory'].
	 *
	 * Relative paths are assumed to be relative to the options['directory'] directory. This makes them equivalent to absolute paths within
	 * the LEGAL URI tree and this fact may seem odd. Alas, the FM frontend sends requests without the leading slash and it's those that
	 * we wish to resolve here, after all. So, yes, this deviates from the general principle applied elesewhere in the code. :-(
	 * Nevertheless, it's easier than scanning and tweaking the FM frontend everywhere.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized LEGAL URI absolute path.
	 */
	public function rel2abs_legal_url_path($path)
	{
		if (0) // TODO: remove the 'relative is based on options['directory']' hack when the frontend has been fixed...
		{
			$path = $this->legal2abs_url_path($path);

			$root = $this->options['directory'];

			// clip the trailing '/' off the $root path before reduction:
			$path = str_replace(substr($root, 0, -1), '', $path);
		}
		else
		{
			$path = strtr($path, '\\', '/');
			if (!FileManagerUtility::startsWith($path, '/'))
			{
				$path = '/' . $path;
			}

			$path = $this->normalize($path);
		}

		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative or absolute URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function url_path2file_path($url_path)
	{
		$url_path = $this->rel2abs_url_path($url_path);

		$path = $this->options['assumed_root_filepath'] . $url_path;
		//$path = $this->normalize($path);    -- taken care of by rel2abs_url_path already
		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative URI path or absolute LEGAL URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function legal_url_path2file_path($url_path)
	{
		$path = $this->rel2abs_legal_url_path($url_path);

		$path = substr($this->options['assumed_base_filepath'], 0, -1) . $path;

		return $path;
	}

	public static function enforceTrailingSlash($string)
	{
		return (strrpos($string, '/') === strlen($string) - 1 ? $string : $string . '/');
	}





	/**
	 * Produce minimized HTML output; used to cut don't on the content fed
	 * to JSON_encode() and make it more readable in raw debug view.
	 */
	public static function compressHTML($str)
	{
		// brute force: replace tabs by spaces and reduce whitespace series to a single space.
		//$str = preg_replace('/\s+/', ' ', $str);

		return $str;
	}


	protected /* static */ function modify_json4exception(&$jserr, $emsg, $target_info = null)
	{
		if (empty($emsg))
			return;

		// only set up the new json error report array when this is the first exception we got:
		if (empty($jserr['error']))
		{
			// check the error message and see if it is a translation code word (with or without parameters) or just a generic error report string
			$e = explode(':', $emsg, 2);
			if (preg_match('/[^A-Za-z0-9_-]/', $e[0]))
			{
				// generic message. ouch.
				$jserr['error'] = $emsg;
			}
			else
			{
				// WARNING: braces in here are MANDATORY as PHP doesn't evaluate the nested ?: as you'd expect: (C1 ? A : C2 ? B : C) will deliver B when both C! and C2 are TRUE!
				$jserr['error'] = $emsg = '${backend.' . $e[0] . '}' . (!empty($e[1]) ? $e[1] : (!empty($target_info) ? ' (' . $this->mkSafe4Display($target_info) . ')' : ''));
			}
			$jserr['status'] = 0;
		}
	}






	public function getAllowedMimeTypes($mime_filter = null)
	{
		$mimeTypes = array();

		if (empty($mime_filter)) return null;
		$mset = explode(',', $mime_filter);
		for($i = count($mset) - 1; $i >= 0; $i--)
		{
			if (strpos($mset[$i], '/') === false)
				$mset[$i] .= '/';
		}

		$mimes = $this->getMimeTypeDefinitions();

		foreach ($mimes as $k => $mime)
		{
			if ($k === '.')
				continue;

			foreach($mset as $filter)
			{
				if (FileManagerUtility::startsWith($mime, $filter))
					$mimeTypes[] = $mime;
			}
		}

		return $mimeTypes;
	}

	public function getMimeTypeDefinitions()
	{
		static $mimes;

		$pref_ext = array();

		if (!$mimes)
		{
			$mimes = parse_ini_file($this->options['mimeTypesPath']);

			//FM_vardumper($this, 'getMimeTypeDefinitions', $mimes);

			if (is_array($mimes))
			{
				foreach($mimes as $k => $v)
				{
					$m = explode(',', (string)$v);
					$mimes[$k] = $m[0];
					$p = null;
					if (!empty($m[1]))
					{
						$p = trim($m[1]);
					}
					// is this the preferred extension for this mime type? Or is this the first known extension for the given mime type?
					if ($p === '*' || !array_key_exists($m[0], $pref_ext))
					{
						$pref_ext[$m[0]] = $k;
					}
				}

				// stick the mime-to-extension map into an 'illegal' index:
				$mimes['.'] = $pref_ext;
			}
			else
			{
				$mimes = false;
			}
		}

		if (!is_array($mimes)) $mimes = array(); // prevent faulty mimetype ini file from b0rking other code sections.

		return $mimes;
	}

	public function IsAllowedMimeType($mime_type, $mime_filters)
	{
		if (empty($mime_type))
			return false;
		if (!is_array($mime_filters))
			return true;

		return in_array($mime_type, $mime_filters);
	}

	/**
	 * Returns (if possible) the mimetype of the given file
	 *
	 * @param string $file        physical filesystem path of the file for which we wish to know the mime type.
	 *
	 * @param boolean $just_guess when TRUE, files are not 'sniffed' to derive their actual mimetype
	 *                            but instead only the swift (and blunt) process of guestimating
	 *                            the mime type from the file extension is performed.
	 *
	 * @param string $legal_url   when not NULL, this should be the legal url path to the given file.
	 *                            It is used by the caching system inside getFileInfo(), which is invoked
	 *                            when $just_guess is FALSE. This parameter is therefore only relevant
	 *                            when $just_guess is FALSE. (default: NULL)
	 */
	public function getMimeType($file, $just_guess = false, $legal_url = null)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$mime = null;
		$ini = error_reporting(0);
		if ($just_guess && function_exists('finfo_open') && $f = finfo_open(FILEINFO_MIME, getenv('MAGIC')))
		{
			$mime = finfo_file($f, $file);
			// some systems also produce the character encoding with the mime type; strip if off:
			$ma = explode(';', $mime);
			$mime = $ma[0];
			finfo_close($f);
		}
		error_reporting($ini);

		// UPLOAD delivers files in temporary storage with extensions NOT matching the mime type, so we don't
		// filter on extension; we just let getID3 go ahead and content-sniff the mime type.
		// Since getID3::analyze() is a quite costly operation, we like to do it only ONCE per file,
		// so we cache the last entries.
		if (empty($mime) && !$just_guess)
		{
			$fi = $this->getFileInfo($file, $legal_url);
			if (!empty($fi['mime_type']))
				$mime = $fi['mime_type'];
		}

		if ((empty($mime) || $mime === 'application/octet-stream') && strlen($ext) > 0)
		{
			$ext2mimetype_arr = $this->getMimeTypeDefinitions();

			if (array_key_exists($ext, $ext2mimetype_arr))
				$mime = $ext2mimetype_arr[$ext];
		}

		if (empty($mime))
			$mime = 'application/octet-stream';

		return $mime;
	}

	/**
	 * Return the first known extension for the given mime type.
	 *
	 * Return NULL when no known extension is found.
	 */
	public function getExtFromMime($mime)
	{
		$ext2mimetype_arr = $this->getMimeTypeDefinitions();
		$mime2ext_arr = $ext2mimetype_arr['.'];

		if (array_key_exists($mime, $mime2ext_arr))
			return $mime2ext_arr[$mime];

		return null;
	}

	/**
	 * Returns (if possible) all info about the given file, mimetype, dimensions, the works
	 *
	 * @param string $file       physical filesystem path to the file we want to know all about
	 *
	 * @param string $legal_url  legal url path to the file; used as the file/path basis for
	 *                           the caching system inside: getFileInfo() will cache the
	 *                           extracted info alongside the thumbnails in a cache file with
	 *                           '.nfo' extension.
	 *
	 * @return the info array as produced by getID3::analyze()
	 */
	public function getFileInfo($file, $legal_url)
	{
		$filetime = @filemtime($file);
		$hash = md5($file . ':' . $filetime);
		$cache_dir = false;
		$cachefile = false;

		$age_limit = $this->getid3_cache_lru_ts - MTFM_MIN_GETID3_CACHESIZE;

		// when hash exists in cache, return that one:
		if (array_key_exists($hash, $this->getid3_cache))
		{
			$rv = $this->getid3_cache[$hash]['fileinfo'];

			// mark as LRU entry; only update the timestamp when it's rather old (age/2) to prevent
			// cache flushing due to hammering of a few entries:
			if ($this->getid3_cache[$hash]['cache_timestamp'] < $age_limit + MTFM_MIN_GETID3_CACHESIZE / 2)
			{
				$this->getid3_cache[$hash]['cache_timestamp'] = $this->getid3_cache_lru_ts++;
			}

			//$rv['cache_from'] = 'RAM';
		}
		else
		{
			$rv = false;

			/*
			 * next: check file cache
			 *
			 * We only store the 'most recent' version of the file info per file, so we need to load the cache file and
			 * verify the timestamp in there to decide whether we can us it as-is or should replace it with the updated
			 * data obtained from analyze().
			 */
			$cachefile = false;
			$cache_dir = false;
			if (!empty($legal_url))
			{
				$cachefile = $this->generateThumbName($legal_url, 'info');
				$tfi = pathinfo($cachefile);
				$cf_subdir = $tfi['dirname'];
				$cache_dir = $this->url_path2file_path($this->options['thumbnailPath'] . $cf_subdir);
				$cache_dir = self::enforceTrailingSlash($cache_dir);
				$cachefile = $cache_dir . $tfi['filename'] . '.nfo';

				if (is_readable($cachefile))
				{
					$data = file_get_contents($cachefile);
					$data = @unserialize($data);
					if (is_array($data) && $data['filetime'] == $filetime && !empty($data['fileinfo']))
					{
						// we're good to go: use the cached data!
						$this->getid3_cache[$hash] = array_merge($data, array('cache_timestamp' => $this->getid3_cache_lru_ts++));
						$rv = $data['fileinfo'];
						//$rv['cache_from'] = 'file';
					}
					else
					{
						// destroy outdated cache file
						@unlink($cachefile);
					}
				}
			}

			if ($rv === false)
			{
				if (is_dir($file))
				{
					$rv = array(
							'mime_type' => 'text/directory'
						);
				}
				else
				{
					$this->getid3->analyze($file);

					$rv = $this->getid3->info;
					if (empty($rv['mime_type']))
					{
						// guarantee to produce a mime type, at least!
						$rv['mime_type'] = $this->getMimeType($file, true);     // guestimate mimetype when content sniffing didn't work
					}
				}

				// store it in the cache; mark as LRU entry
				$this->getid3_cache[$hash] = array(
					'cache_timestamp' => $this->getid3_cache_lru_ts++,
					'fileinfo' => $rv,
					'filetime' => $filetime
				);

				// and save the new entry to file cache as well, so we can reuse it in a future request
				if ($cachefile !== false)
				{
					$data = serialize($this->getid3_cache[$hash]);
					if (!file_exists($cache_dir))
					{
						@mkdir($cache_dir);
					}
					if (false === @file_put_contents($cachefile, $data))
					{
						// destroy failed cache attempt
						@unlink($cachefile);
					}
				}
			}

			/*
			 * Cleanup/cache size restriction algorithm:
			 *
			 * Randomly probe the cache and check whether the probe has a 'timestamp' older than the configured
			 * minimum required lifetime. When the probe is older, it is discarded from the cache.
			 *
			 * As the probe is assumed to be perfectly random, further assuming we've got a cache size of N,
			 * then the chance we pick a probe older then age A is (N - A) / N  -- picking any age X has a
			 * chance of 1/N as random implies flat distribution. Hitting any of the most recent A entries
			 * is A * 1/N, hence picking any older item is 1 - A/N == (N - A) / N
			 *
			 * This means the growth of the cache beyond the given age limit A is a logarithmic curve, but
			 * we like to have a guaranteed upper limit significantly below N = +Inf, so we probe the cache
			 * TWICE for each addition: given a cache size of 2N, one of these probes should, on average,
			 * be successful, thus removing one cache entry on average for a cache size of 2N. As we only
			 * add 1 item at the same time, the statistically expected bound of the cache will be 2N.
			 * As chances increase for both probes to be successful when cache size increases, the risk
			 * of a (very) large cache size at any point in time is dwindingly small, while cost is constant
			 * per cache transaction (insert + dual probe).
			 *
			 * This scheme is expected to be faster (thanks to log growth curve and linear insert/prune costs)
			 * than the usual where one keeps meticulous track of the entries and their age and entries are
			 * discarded in order, oldest first.
			 */
			$probe_index = array_rand($this->getid3_cache);
			$probe = &$this->getid3_cache[$probe_index];
			if ($probe['cache_timestamp'] < $age_limit)
			{
				// discard antiquated entry:
				unset($this->getid3_cache[$probe_index]);
			}
			$probe_index = array_rand($this->getid3_cache);
			$probe = &$this->getid3_cache[$probe_index];
			if ($probe['cache_timestamp'] < $age_limit)
			{
				// discard antiquated entry:
				unset($this->getid3_cache[$probe_index]);
			}
		}

		$rv['cache_hash'] = $hash;
		if ($cachefile !== false)
		{
			$rv['cache_file'] = $cachefile;
			$rv['cache_dir'] = $cache_dir;
		}

		return $rv;
	}





	protected /* static */ function getGETparam($name, $default_value = null)
	{
		if (is_array($_GET) && !empty($_GET[$name]))
		{
			$rv = $_GET[$name];

			// see if there's any stuff in there which we don't like
			if (!preg_match('/[^A-Za-z0-9\/~!@#$%^&*()_+{}[]\'",.?]/', $rv))
			{
				return $rv;
			}
		}
		return $default_value;
	}

	protected /* static */ function getPOSTparam($name, $default_value = null)
	{
		if (is_array($_POST) && !empty($_POST[$name]))
		{
			$rv = $_POST[$name];

			// see if there's any stuff in there which we don't like
			if (!preg_match('/[^A-Za-z0-9\/~!@#$%^&*()_+{}[]\'",.?]/', $rv))
			{
				return $rv;
			}
		}
		return $default_value;
	}
}






class FileManagerException extends Exception {}





/* Stripped-down version of some Styx PHP Framework-Functionality bundled with this FileBrowser. Styx is located at: http://styx.og5.net */
class FileManagerUtility
{
	public static function endsWith($string, $look)
	{
		return strrpos($string, $look) === strlen($string) - strlen($look);
	}

	public static function startsWith($string, $look)
	{
		return strpos($string, $look) === 0;
	}


	/**
	 * Cleanup and check against 'already known names' in optional $options array.
	 * Return a uniquified name equal to or derived from the original ($data).
	 *
	 * First clean up the given name ($data): by default all characters not part of the
	 * set [A-Za-z0-9_] are converted to an underscore '_'; series of these underscores
	 * are reduced to a single one, and characters in the set [_.,&+ ] are stripped from
	 * the lead and tail of the given name, e.g. '__name' would therefor be reduced to
	 * 'name'.
	 *
	 * Next, check the now cleaned-up name $data against an optional set of names ($options array)
	 * and return the name itself when it does not exist in the set,
	 * otherwise return an augmented name such that it does not exist in the set
	 * while having been constructed as name plus '_' plus an integer number,
	 * starting at 1.
	 *
	 * Example:
	 * If the set is {'file', 'file_1', 'file_3'} then $data = 'file' will return
	 * the string 'file_2' instead, while $data = 'fileX' will return that same
	 * value: 'fileX'.
	 *
	 * @param string $data     the name to be cleaned and checked/uniquified
	 * @param array $options   an optional array of strings to check the given name $data against
	 * @param string $extra_allowed_chars     optional set of additional characters which should pass
	 *                                        unaltered through the cleanup stage. a dash '-' can be
	 *                                        used to denote a character range, while the literal
	 *                                        dash '-' itself, when included, should be positioned
	 *                                        at the very start or end of the string.
	 *
	 *                                        Note that ] must NOT need to be escaped; we do this
	 *                                        ourselves.
	 * @param string $trim_chars              optional set of additional characters which are trimmed off the
	 *                                        start and end of the name ($data); note that de dash
	 *                                        '-' is always treated as a literal dash here; no
	 *                                        range feature!
	 *                                        The basic set of characters trimmed off the name is
	 *                                        [. ]; this set cannot be reduced, only extended.
	 *
	 * @return cleaned-up and uniquified name derived from ($data).
	 */
	public static function pagetitle($data, $options = null, $extra_allowed_chars = null, $trim_chars = null)
	{
		static $regex;
		if (!$regex){
			$regex = array(
				explode(' ', 'Æ æ Œ œ ß Ü ü Ö ö Ä ä À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;'),
				explode(' ', 'Ae ae Oe oe ss Ue ue Oe oe Ae ae A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z'),
			);
		}

		if (empty($data))
		{
			return (string)$data;
		}

		// fixup $extra_allowed_chars to ensure it's suitable as a character sequence for a set in a regex:
		//
		// Note:
		//   caller must ensure a dash '-', when to be treated as a separate character, is at the very end of the string
		if (is_string($extra_allowed_chars))
		{
			$extra_allowed_chars = str_replace(']', '\]', $extra_allowed_chars);
			if (strpos($extra_allowed_chars, '-') === 0)
			{
				$extra_allowed_chars = substr($extra_allowed_chars, 1) . (strpos($extra_allowed_chars, '-') != strlen($extra_allowed_chars) - 1 ? '-' : '');
			}
		}
		else
		{
			$extra_allowed_chars = '';
		}
		// accepts dots and several other characters, but do NOT tolerate dots or underscores at the start or end, i.e. no 'hidden file names' accepted, for example!
		$data = preg_replace('/[^A-Za-z0-9' . $extra_allowed_chars . ']+/', '_', str_replace($regex[0], $regex[1], $data));
		$data = trim($data, '_. ' . $trim_chars);

		//$data = trim(substr(preg_replace('/(?:[^A-z0-9]|_|\^)+/i', '_', str_replace($regex[0], $regex[1], $data)), 0, 64), '_');
		return !empty($options) ? self::checkTitle($data, $options) : $data;
	}

	protected static function checkTitle($data, $options = array(), $i = 0)
	{
		if (!is_array($options)) return $data;

		$lwr_data = strtolower($data);

		foreach ($options as $content)
			if ($content && strtolower($content) == $lwr_data . ($i ? '_' . $i : ''))
				return self::checkTitle($data, $options, ++$i);

		return $data.($i ? '_' . $i : '');
	}

	public static function isBinary($str)
	{
		for($i = 0; $i < strlen($str); $i++)
		{
			$c = ord($str[$i]);
			// do not accept ANY codes below SPACE, except TAB, CR and LF.
			if ($c == 255 || ($c < 32 /* SPACE */ && $c != 9 && $c != 10 && $c != 13)) return true;
		}

		return false;
	}

	/**
	 * Apply rawurlencode() to each of the elements of the given path
	 *
	 * @note
	 *   this method is provided as rawurlencode() itself also encodes the '/' separators in a path/string
	 *   and we do NOT want to 'revert' such change with the risk of also picking up other %2F bits in
	 *   the string (this assumes crafted paths can be fed to us).
	 */
	public static function rawurlencode_path($path)
	{
		return str_replace('%2F', '/', rawurlencode($path));
	}

	/**
	 * Convert a number (representing number of bytes) to a formatted string representing GB .. bytes,
	 * depending on the size of the value.
	 */
	public static function fmt_bytecount($val, $precision = 1)
	{
		$unit = array('TB', 'GB', 'MB', 'KB', 'bytes');
		for ($x = count($unit) - 1; $val >= 1024 && $x > 0; $x--)
		{
			$val /= 1024.0;
		}
		$val = round($val, ($x > 0 ? $precision : 0));
		return $val . '&#160;' . $unit[$x];
	}




	/*
	 * Derived from getID3 demo_browse.php sample code.
	 *
	 * Attempts some 'intelligent' conversions for better readability and information compacting.
	 */
	public static function table_var_dump(&$variable, $wrap_in_td = false, $show_types = false)
	{
		$returnstring = '';
		if (is_array($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td>' : '');
			$returnstring .= '<table class="dump_array" cellspacing="0" cellpadding="2">';
			foreach ($variable as $key => &$value)
			{
				$returnstring .= '<tr><td valign="top"><b>'.$key.'</b></td>';
				if ($show_types)
				{
					$returnstring .= '<td valign="top">'.gettype($value);
					if (is_array($value))
					{
						$returnstring .= '&nbsp;('.count($value).')';
					}
					elseif (is_string($value))
					{
						$returnstring .= '&nbsp;('.strlen($value).')';
					}
					$returnstring .= '</td>';
				}

				switch ((string)$key)
				{
				case 'filesize':
					$returnstring .= '<td class="dump_seconds">' . self::fmt_bytecount($value) . ($value >= 1024 ? ' (' . $value . ' bytes)' : '') . '</td>';
					continue 2;

				case 'playtime seconds':
					$returnstring .= '<td class="dump_seconds">' . number_format($value, 1) . ' s</td>';
					continue 2;

				case 'compression ratio':
					$returnstring .= '<td class="dump_compression_ratio">' . number_format($value * 100, 1) . '%</td>';
					continue 2;

				case 'bitrate':
				case 'bit rate':
				case 'avg bit rate':
				case 'max bit rate':
				case 'max bitrate':
				case 'sample rate':
				case 'sample rate2':
				case 'samples per sec':
				case 'avg bytes per sec':
					$returnstring .= '<td class="dump_rate">' . self::fmt_bytecount($value) . '/s</td>';
					continue 2;

				case 'bytes per minute':
					$returnstring .= '<td class="dump_rate">' . self::fmt_bytecount($value) . '/min</td>';
					continue 2;
				}
				$returnstring .= FileManagerUtility::table_var_dump($value, true, $show_types) . '</tr>';
			}
			$returnstring .= '</table>';
			$returnstring .= ($wrap_in_td ? '</td>' : '');
		}
		else if (is_bool($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td class="dump_boolean">' : '').($variable ? 'TRUE' : 'FALSE').($wrap_in_td ? '</td>' : '');
		}
		else if (is_int($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td class="dump_integer">' : '').$variable.($wrap_in_td ? '</td>' : '');
		}
		else if (is_float($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td class="dump_double">' : '').$variable.($wrap_in_td ? '</td>' : '');
		}
		else if (is_object($variable) && isset($variable->id3_procsupport_obj))
		{
			if (isset($variable->metadata) && isset($variable->imagedata))
			{
				// an embedded image (MP3 et al)
				$returnstring .= ($wrap_in_td ? '<td class="dump_embedded_image">' : '');
				$returnstring .= '<table class="dump_image" cellspacing="0" cellpadding="2">';
				$returnstring .= '<tr><td><b>type</b></td><td>'.getid3_lib::ImageTypesLookup($variable->metadata[2]).'</td></tr>';
				$returnstring .= '<tr><td><b>width</b></td><td>'.number_format($variable->metadata[0]).' px</td></tr>';
				$returnstring .= '<tr><td><b>height</b></td><td>'.number_format($variable->metadata[1]).' px</td></tr>';
				$returnstring .= '<tr><td><b>size</b></td><td>'.number_format(strlen($variable->imagedata)).' bytes</td></tr></table>';
				$returnstring .= '<img src="data:'.$variable->metadata['mime'].';base64,'.base64_encode($variable->imagedata).'" width="'.$variable->metadata[0].'" height="'.$variable->metadata[1].'">';
				$returnstring .= ($wrap_in_td ? '</td>' : '');
			}
			else if (isset($variable->binarydata_mode))
			{
				$returnstring .= ($wrap_in_td ? '<td class="dump_binary_data">' : '');
				if ($variable->binarydata_mode == 'procd')
				{
					$returnstring .= '<i>' . self::table_var_dump($variable->binarydata, false, false) . '</i>';
				}
				else
				{
					$temp = unpack('H*', $variable->binarydata);
					$temp = str_split($temp[1], 8);
					$returnstring .= '<i>' . self::table_var_dump(implode(' ', $temp), false, false) . '</i>';
				}
				$returnstring .= ($wrap_in_td ? '</td>' : '');
			}
			else
			{
				$returnstring .= ($wrap_in_td ? '<td class="dump_object">' : '').print_r($variable, true).($wrap_in_td ? '</td>' : '');
			}
		}
		else if (is_object($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td class="dump_object">' : '').print_r($variable, true).($wrap_in_td ? '</td>' : '');
		}
		else if (is_null($variable))
		{
			$returnstring .= ($wrap_in_td ? '<td class="dump_null">' : '').'(null)'.($wrap_in_td ? '</td>' : '');
		}
		else if (is_string($variable))
		{
			$variable = strtr($variable, "\x00", ' ');
			$varlen = strlen($variable);
			for ($i = 0; $i < $varlen; $i++)
			{
				$returnstring .= htmlentities($variable{$i}, ENT_QUOTES, 'UTF-8');
			}
			$returnstring = ($wrap_in_td ? '<td class="dump_string">' : '').nl2br($returnstring).($wrap_in_td ? '</td>' : '');
		}
		else
		{
			$returnstring .= ($wrap_in_td ? '<td>' : '').nl2br(htmlspecialchars(strtr($variable, "\x00", ' '))).($wrap_in_td ? '</td>' : '');
		}
		return $returnstring;
	}
}



// support class for the getID3 info and embedded image extraction:
class EmbeddedImageContainer
{
	public $metadata;
	public $imagedata;
	public $id3_procsupport_obj;

	public function __construct($meta, $img)
	{
		$this->metadata = $meta;
		$this->imagedata = $img;
		$this->id3_procsupport_obj = true;
	}
}

class BinaryDataContainer
{
	public $binarydata;
	public $binarydata_mode;
	public $id3_procsupport_obj;

	public function __construct($data, $mode = 'procd')
	{
		$this->binarydata_mode = $mode;
		$this->binarydata = $data;
		$this->id3_procsupport_obj = true;
	}
}

