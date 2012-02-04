<?php
/*
 * Script: Filemanager.php
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
 *   Backend: FileManager & FileManagerWithAliasSupport Copyright (c) 2011 [Ger Hobbelt](http://hobbelt.com)
 *
 * Dependencies:
 *   - Tooling.php
 *   - Image.class.php
 *   - getId3 Library
 *
 * Options:
 *   - URLpath4FileManagedDirTree: (string) The URI base directory to be used for the FileManager ('URI path' i.e. an absolute path here would be rooted at DocumentRoot: '/' == DocumentRoot)
 *   - URLpath4assets: (string, optional) The URI path to all images and swf files used by the filemanager
 *   - URLpath4thumbnails: (string) The URI path where the thumbnails of the pictures will be saved
 *   - thumbSmallSize: (integer) The (maximum) width / height in pixels of the thumb48 'small' thumbnails produced by this backend
 *   - thumbBigSize: (integer) The (maximum) width / height in pixels of the thumb250 'big' thumbnails produced by this backend
 *   - FileSystemPath4mimeTypesMapFile: (string, optional) The filesystem path to the MimeTypes.ini file. May exist in a place outside the DocumentRoot tree.
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
 *   'every path resides within the options['URLpath4FileManagedDirTree'] a.k.a. BASEDIR rooted tree' without exception.
 *   Because we can do without exceptions to important rules. ;-)
 *
 *   When paths apparently don't, they are coerced into adherence to this rule; when this fails, an exception is thrown internally and an error
 *   will be reported and the action temrinated.
 *
 *  'LEGAL URL paths':
 *
 *   Paths which adhere to the aforementioned rule are so-called LEGAL URL paths; their 'root' equals BASEDIR.
 *
 *   BASEDIR equals the path pointed at by the options['URLpath4FileManagedDirTree'] setting. It is therefore imperative that you ensure this value is
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
 *   When you need your paths to be restricted to the bounds of the options['URLpath4FileManagedDirTree'] tree (which is a subtree of the DocumentRoot based
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
 *               'legal_dir_url'         (string) LEGAL URI path to the directory where the file is being uploaded. You may invoke
 *                                           $dir = $mgr->legal_url_path2file_path($legal_dir_url);
 *                                       to obtain the physical filesystem path (also available in the 'dir' $info entry, by the way!), or
 *                                           $dir_url = $mgr->legal2abs_url_path($legal_dir_url);
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
 *               'filename'              (string) the filename, plus extension, of the file being uploaded; this filename is ensured
 *                                       to be both filesystem-legal, unique and not yet existing in the given directory.
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
 *               'resize'                (boolean) TRUE: any uploaded images are resized to the configured maximum dimensions before they
 *                                       are stored on disk.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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
 *         The frontend-specified options.propagateData items will be available as $_POST[] items.
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

// allow MTFM to use finfo_open() to help us produce mime types for files. This is slower than the basic file extension to mimetype mapping
define('MTFM_USE_FINFO_OPEN', false);






// flags for clean_ID3info_results()
define('MTFM_CLEAN_ID3_STRIP_EMBEDDED_IMAGES',      0x0001);






/**
 * Cache element class custom-tailored for the MTFM: includes the code to construct a unique
 * (thumbnail) cache filename and derive suitable cache filenames from the same template with
 * minimal effort.
 *
 * Makes sure the generated (thumbpath) template is unique for each source file ('$legal_url'). We prevent
 * reduced performance for large file sets: all thumbnails/templates derived from any files in the entire
 * FileManager-managed directory tree, rooted by options['URLpath4FileManagedDirTree'], can become a huge collection,
 * so we distribute them across a (thumbnail/cache) directory tree, which is created on demand.
 *
 * The thumbnails cache directory tree is determined by the MD5 of the full path to the source file ($legal_url),
 * using the first two characters of the MD5, making for a span of 256 (directories).
 *
 * Note: when you expect to manage a really HUGE file collection from FM, you may dial up the
 *       MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE define to 2 here.
 */
class MTFMCacheItem
{
	protected $store;

	protected $legal_url;
	protected $file;
	protected $dirty;
	protected $persistent_edits;
	protected $loaded;
	protected $fstat;

	protected $cache_dir;
	protected $cache_dir_mode;  // UNIX access bits: UGA:RWX
	protected $cache_dir_url;
	protected $cache_base;      // cache filename template base
	protected $cache_tnext;     // thumbnail extension

	protected $cache_file;

	public function __construct($fm_obj, $legal_url, $prefetch = false, $persistent_edits = true)
	{
		$this->init($fm_obj, $legal_url, $prefetch, $persistent_edits);
	}

	public function init($fm_obj, $legal_url, $prefetch = false, $persistent_edits)
	{
		$this->dirty = false;
		$this->persistent_edits = $persistent_edits;
		$this->loaded = false;
		$this->store = array();

		$fmopts = $fm_obj->getSettings();

		$this->legal_url = $legal_url;
		$this->file = $fm_obj->legal_url_path2file_path($legal_url);
		$this->fstat = null;

		$fi = pathinfo($legal_url);
		if (is_dir($this->file))
		{
			$filename = $fi['basename'];
			unset($fi['extension']);
			$ext = '';
		}
		else
		{
			$filename = $fi['filename'];
			$ext = strtolower((isset($fi['extension']) && strlen($fi['extension']) > 0) ? '.' . $fi['extension'] : '');
			switch ($ext)
			{
			case '.gif':
			case '.png':
			case '.jpg':
			case '.jpeg':
				break;

			case '.mp3':
				// default to JPG, as embedded images don't contain transparency info:
				$ext = '.jpg';
				break;

			default:
				//$ext = preg_replace('/[^A-Za-z0-9.]+/', '_', $ext);

				// default to PNG, as it'll handle transparancy and full color both:
				$ext = '.png';
				break;
			}
		}

		// as the cache file is generated, but NOT guaranteed from a safe filepath (FM may be visiting unsafe
		// image files when they exist in a preloaded directory tree!) we do the full safe-filename transform
		// on the name itself.
		// The MD5 is taken from the untrammeled original, though:
		$dircode = md5($legal_url);

		$dir = '';
		for ($i = 0; $i < MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE; $i++)
		{
			$dir .= substr($dircode, 0, 2) . '/';
			$dircode = substr($dircode, 2);
		}

		$fn = substr($dircode, 0, 4) . '_' . preg_replace('/[^A-Za-z0-9]+/', '_', $filename);
		$dircode = substr($dircode, 4);
		$fn = substr($fn . $dircode, 0, 38);

		$this->cache_dir_url = $fmopts['URLpath4thumbnails'] . $dir;
		$this->cache_dir = $fmopts['thumbnailCacheDir'] . $dir;
		$this->cache_dir_mode = $fmopts['chmod'];
		$this->cache_base = $fn;
		$this->cache_tnext = $ext;

		$cache_url = $fn . '-meta.nfo';
		$this->cache_file = $this->cache_dir . $cache_url;

		if ($prefetch)
		{
			$this->load();
		}
	}

	public function load()
	{
		if (!$this->loaded)
		{
			$this->loaded = true; // always mark as loaded, even when the load fails

			if (!is_array($this->fstat) && file_exists($this->file))
			{
				$this->fstat = @stat($this->file);
			}
			if (file_exists($this->cache_file))
			{
				include($this->cache_file);  // unserialize();

				if (   isset($statdata) && isset($data) && is_array($data) && is_array($this->fstat) && is_Array($statdata)
					&& $statdata[10] == $this->fstat[10] // ctime
					&& $statdata[9]  == $this->fstat[9]   // mtime
					&& $statdata[7]  == $this->fstat[7]   // size
				   )
				{
					if (!DEVELOPMENT)
					{
						// mix disk cache data with items already existing in RAM cache: we use a delayed-load scheme which necessitates this.
						$this->store = array_merge($data, $this->store);
					}
				}
				else
				{
					// nuke disk cache!
					@unlink($this->cache_file);
				}
			}
		}
	}

	public function delete($every_ting_baby = false)
	{
		$rv = true;
		$dir = $this->cache_dir;
		$dir_exists = file_exists($dir);

		// What do I get for ten dollars?
		if ($every_ting_baby)
		{
			if ($dir_exists)
			{
				$dir_and_mask = $dir . $this->cache_base . '*';
				$coll = safe_glob($dir_and_mask, GLOB_NODOTS | GLOB_NOSORT);

				if ($coll !== false)
				{
					foreach($coll['files'] as $filename)
					{
						$file = $dir . $filename;
						$rv &= @unlink($file);
					}
				}
			}
		}
		else if (file_exists($this->cache_file))
		{
			// nuke cache!
			$rv &= @unlink($this->cache_file);
		}

		// as the thumbnail subdirectory may now be entirely empty, try to remove it as well,
		// but do NOT yack when we don't succeed: there may be other thumbnails, etc. in there still!
		if ($dir_exists)
		{
			for ($i = 0; $i < MTFM_NUMBER_OF_DIRLEVELS_FOR_CACHE; $i++)
			{
				@rmdir($dir);
				$dir = dirname($dir);
			}
		}

		// also clear the data cached in RAM:
		$this->dirty = false;
		$this->loaded = true;  // we know the cache file doesn't exist any longer, so don't bother trying to load it again later on!
		$this->store = array();

		return $rv;
	}

	public function __destruct()
	{
		if ($this->dirty && $this->persistent_edits)
		{
			// store data to persistent storage:
			if (!$this->mkCacheDir() && !$this->loaded)
			{
				// fetch from disk before saving in order to ensure RAM cache is mixed with _existing_ _valid_ disk cache (RAM wins on individual items).
				$this->load();
			}

			if (!is_array($this->fstat) && file_exists($this->file))
			{
				$this->fstat = @stat($this->file);
			}

			$data = '<?php

// legal URL: ' . $this->legal_url . '

$statdata = ' . var_export($this->fstat, true) . ';

$data = ' . var_export($this->store, true) . ';' . PHP_EOL;

			@file_put_contents($this->cache_file, $data);
		}
	}

	/*
	 * @param boolean $persistent    (default: TRUE) TRUE when we should also check the persistent cache storage for this item/key
	 */
	public function fetch($key, $persistent = true)
	{
		if (isset($this->store[$key]))
		{
			return $this->store[$key];
		}
		else if ($persistent && !$this->loaded)
		{
			// only fetch from disk when we ask for items which haven't been stored yet.
			$this->load();
			if (isset($this->store[$key]))
			{
				return $this->store[$key];
			}
		}

		return null;
	}

	/*
	 * @param boolean $persistent    (default: TRUE) TRUE when we should also store this item/key in the persistent cache storage
	 */
	public function store($key, $value, $persistent = true)
	{
		if (isset($this->store[$key]))
		{
			$persistent &= ($this->store[$key] !== $value); // only mark cache as dirty when we actully CHANGE the value stored in here!
		}
		$this->dirty |= ($persistent && $this->persistent_edits);
		$this->store[$key] = $value;
	}


	public function getThumbPath($dimensions)
	{
		assert(!empty($dimensions));
		return $this->cache_dir . $this->cache_base . '-' . $dimensions . $this->cache_tnext;
	}

	public function getThumbURL($dimensions)
	{
		assert(!empty($dimensions));
		return $this->cache_dir_url . $this->cache_base . '-' . $dimensions . $this->cache_tnext;
	}

	public function mkCacheDir()
	{
		if (!is_dir($this->cache_dir))
		{
			@mkdir($this->cache_dir, $this->cache_dir_mode, true);
			return true;
		}
		return false;
	}

	public function getMimeType()
	{
		if (!empty($this->store['mime_type']))
		{
			return $this->store['mime_type'];
		}
		//$mime = $fm_obj->getMimeFromExt($file);
		return null;
	}
}







class MTFMCache
{
	protected $store;           // assoc. array: stores cached data
	protected $store_ts;        // assoc. array: stores corresponding 'cache timestamps' for use by the LRU algorithm
	protected $store_lru_ts;    // integer: current 'cache timestamp'
	protected $min_cache_size;  // integer: minimum cache size limit (maximum is a statistical derivate of this one, about twice as large)

	public function __construct($min_cache_size)
	{
		$this->store = array();
		$this->store_ts = array();
		// store_lru_ts stores a 'timestamp' counter to track LRU: 'timestamps' older than threshold are discarded when cache is full
		$this->store_lru_ts = 0;
		$this->min_cache_size = $min_cache_size;
	}

	/*
	 * Return a reference to the cache slot. When the cache slot did not exist before, it will be created, and
	 * the value stored in the slot will be NULL.
	 *
	 * You can store any arbitrary data in a cache slot: it doesn't have to be a MTFMCacheItem instance.
	 */
	public function &pick($key, $fm_obj = null, $create_if_not_exist = true)
	{
		assert(!empty($key));

		$age_limit = $this->store_lru_ts - $this->min_cache_size;

		if (isset($this->store[$key]))
		{
			// mark as LRU entry; only update the timestamp when it's rather old (age/2) to prevent
			// cache flushing due to hammering of a few entries:
			if ($this->store_ts[$key] < $age_limit + $this->min_cache_size / 2)
			{
				$this->store_ts[$key] = $this->store_lru_ts++;
			}
		}
		else if ($create_if_not_exist)
		{
			// only start pruning when we run the risk of overflow. Heuristic: when we're at 50% fill rate, we can expect more requests to come in, so we start pruning already
			if (count($this->store_ts) >= $this->min_cache_size / 2)
			{
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
				$probe_index = array_rand($this->store_ts);
				if ($this->store_ts[$probe_index] < $age_limit)
				{
					// discard antiquated entry:
					unset($this->store_ts[$probe_index]);
					unset($this->store[$probe_index]);
				}
				$probe_index = array_rand($this->store_ts);
				if ($this->store_ts[$probe_index] < $age_limit)
				{
					// discard antiquated entry:
					unset($this->store_ts[$probe_index]);
					unset($this->store[$probe_index]);
				}
			}

			/*
			 * add this slot (empty for now) to the cache. Only do this AFTER the pruning, so it won't risk being
			 * picked by the random process in there. We _need_ this one right now. ;-)
			 */
			$this->store[$key] = (!empty($fm_obj) ? new MTFMCacheItem($fm_obj, $key) : null);
			$this->store_ts[$key] = $this->store_lru_ts++;
		}
		else
		{
			// do not clutter the cache; all we're probably after this time is the assistance of a MTFMCacheItem:
			// provide a dummy cache entry, nulled and all; we won't be saving the stored data, if any, anyhow.
			if (isset($this->store['!']) && !empty($fm_obj))
			{
				$this->store['!']->init($fm_obj, $key, false, false);
			}
			else
			{
				$this->store['!'] = (!empty($fm_obj) ? new MTFMCacheItem($fm_obj, $key, false, false) : null);
			}
			$this->store_ts['!'] = 0;
			$key = '!';
		}

		return $this->store[$key];
	}
}







class FileManager
{
	protected $options;
	protected $getid3;
	protected $getid3_cache;
	protected $icon_cache;              // cache the icon paths per size (large/small) and file extension

	protected $thumbnailCacheDir;
	protected $thumbnailCacheParentDir;  // assistant precalculated value for scandir/view
	protected $managedBaseDir;           // precalculated filesystem path eqv. of options['URLpath4FileManagedDirTree']

	public function __construct($options)
	{
		$this->options = array_merge(array(
			

			/*
			 * Note that all default paths as listed below are transformed to DocumentRoot-based paths
			 * through the getRealPath() invocations further below:
			 */
			'URLpath4FileManagedDirTree' => null,                                       // the root of the 'legal URI' directory tree, to be managed by MTFM. MUST be in the DocumentRoot tree.
			'URLpath4assets' => null,                                                   // may sit outside options['URLpath4FileManagedDirTree'] but MUST be in the DocumentRoot tree
			'URLpath4thumbnails' => null,                                               // may sit outside options['URLpath4FileManagedDirTree'] but MUST be in the DocumentRoot tree
			
			'thumbSmallSize' => 48,                                                     // Used for thumb48 creation
			'thumbBigSize' => 250,                                                      // Used for thumb250 creation
			'FileSystemPath4mimeTypesMapFile' => strtr(dirname(__FILE__), '\\', '/') . '/Filemanager/MimeTypes.ini',  // an absolute filesystem path anywhere; when relative, it will be assumed to be against options['URIpath4RequestScript']
			'FileSystemPath4SiteDocumentRoot' => null,                                  // an absolute filesystem path pointing at URI path '/'. Default: SERVER['DOCUMENT_ROOT']
			'URIpath4RequestScript' => null,                                            // default is $_SERVER['SCRIPT_NAME']
			'dateFormat' => 'j M Y - H:i',
			'maxUploadSize' => 2600 * 2600 * 3,
			// 'maxImageSize' => 99999,                                                 // OBSOLETED, replaced by 'suggestedMaxImageDimension'
			'maxImageDimension' => array('width' => 1024, 'height' => 768),             // Allow to specify the "Resize Large Images" tolerance level.
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
			'cleanFileName' => TRUE,
			'ViewIsAuthorized_cb' => null,
			'DetailIsAuthorized_cb' => null,
			'UploadIsAuthorized_cb' => null,
			'DownloadIsAuthorized_cb' => null,
			'CreateIsAuthorized_cb' => null,
			'DestroyIsAuthorized_cb' => null,
			'MoveIsAuthorized_cb' => null,
			'showHiddenFoldersAndFiles' => false      // Hide dot dirs/files ?
		), (is_array($options) ? $options : array()));

		$document_root_fspath = null;
		if (!empty($this->options['FileSystemPath4SiteDocumentRoot']))
		{
			$document_root_fspath = realpath($this->options['FileSystemPath4SiteDocumentRoot']);
		}
		if (empty($document_root_fspath))
		{
			$document_root_fspath = realpath($_SERVER['DOCUMENT_ROOT']);
		}
		$document_root_fspath = strtr($document_root_fspath, '\\', '/');
		$document_root_fspath = rtrim($document_root_fspath, '/');
		$this->options['FileSystemPath4SiteDocumentRoot'] = $document_root_fspath;

		// apply default to URIpath4RequestScript:
		if (empty($this->options['URIpath4RequestScript']))
		{
			$this->options['URIpath4RequestScript'] = $this->getURIpath4RequestScript();
		}
		// only calculate the guestimated defaults when they are indeed required:
		if ($this->options['URLpath4FileManagedDirTree'] == null || $this->options['URLpath4assets'] == null || $this->options['URLpath4thumbnails'] == null)
		{
			$my_path = @realpath(dirname(__FILE__));
			$my_path = strtr($my_path, '\\', '/');
			$my_path = self::enforceTrailingSlash($my_path);

			// we throw an Exception here because when these do not apply, the user should have specified all three these entries!
			if (!FileManagerUtility::startsWith($my_path, $document_root_fspath))
			{
				throw new FileManagerException('nofile');
			}

			$my_url_path = str_replace($document_root_fspath, '', $my_path);

			if ($this->options['URLpath4FileManagedDirTree'] == null)
			{
				$this->options['URLpath4FileManagedDirTree'] = $my_url_path . '../../Demos/Files/';
			}
			if ($this->options['URLpath4assets'] == null)
			{
				$this->options['URLpath4assets'] = $my_url_path . '../../Assets/';
			}
			if ($this->options['URLpath4thumbnails'] == null)
			{
				$this->options['URLpath4thumbnails'] = $my_url_path . '../../Assets/Thumbs/';
			}
		}

		/*
		 * make sure we start with a very predictable and LEGAL options['URLpath4FileManagedDirTree'] setting, so that the checks applied to the
		 * (possibly) user specified value for this bugger actually can check out okay AS LONG AS IT'S INSIDE the DocumentRoot-based
		 * directory tree:
		 */
		$this->options['URLpath4FileManagedDirTree'] = $this->rel2abs_url_path($this->options['URLpath4FileManagedDirTree'] . '/');

		$this->managedBaseDir = $this->url_path2file_path($this->options['URLpath4FileManagedDirTree']);

		// now that the correct options['URLpath4FileManagedDirTree'] has been set up, go and check/clean the other paths in the options[]:

		$this->options['URLpath4thumbnails'] = $this->rel2abs_url_path($this->options['URLpath4thumbnails'] . '/');
		
		$this->thumbnailCacheDir = $this->url_path2file_path($this->options['URLpath4thumbnails']);  // precalculate this value; safe as we can assume the entire cache dirtree maps 1:1 to filesystem.
		$this->thumbnailCacheParentDir = $this->url_path2file_path(self::getParentDir($this->options['URLpath4thumbnails']));    // precalculate this value as well; used by scandir/view

		$this->options['URLpath4assets'] = $this->rel2abs_url_path($this->options['URLpath4assets'] . '/');

		$this->options['FileSystemPath4mimeTypesMapFile'] = @realpath($this->options['FileSystemPath4mimeTypesMapFile']);
		if (empty($this->options['FileSystemPath4mimeTypesMapFile']))
		{
			throw new FileManagerException('nofile');
		}
		$this->options['FileSystemPath4mimeTypesMapFile'] = strtr($this->options['FileSystemPath4mimeTypesMapFile'], '\\', '/');

		// getID3 is slower as it *copies* the image to the temp dir before processing: see GetDataImageSize().
		// This is done as getID3 can also analyze *embedded* images, for which this approach is required.
		$this->getid3 = new getID3();
		$this->getid3->setOption(array('encoding' => 'UTF-8'));
		//$this->getid3->encoding = 'UTF-8';

		$this->getid3_cache = new MTFMCache(MTFM_MIN_GETID3_CACHESIZE);

		$this->icon_cache = array(array(), array());
	}

	/**
	 * @return array the FileManager options and settings.
	 */
	public function getSettings()
	{
		return array_merge(array(
				'thumbnailCacheDir' => $this->thumbnailCacheDir,
				'thumbnailCacheParentDir' => $this->thumbnailCacheParentDir,
				'managedBaseDir' => $this->managedBaseDir
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
	protected function _onView($legal_url, $json, $mime_filter, $file_preselect_arg = null, $filemask = '*')
	{
		$v_ex_code = 'nofile';

		$dir = $this->legal_url_path2file_path($legal_url);
		$doubledot = null;
		$coll = null;

// log_message('error', '_onView : ' .  $dir);

		if (is_dir($dir))
		{
			/*
			 * Caching notice:
			 *
			 * Testing on Win7/64 has revealed that at least on that platform, directories' 'last modified' timestamp does NOT change when
			 * the contents of the directory are altered (e.g. when a file was added), hence filemtime() cannot be used for directories
			 * to detect any change and thus steer the cache access.
			 *
			 * When one assumes that all file access in the managed directory tree is performed through an MTFM entity, then we can use a
			 * different tactic (which, due to this risky assumption is dupped part of the group of 'aggressive caching' actions) where
			 * we check for the existence of a cache file for the given directory; when it does exist, we can use it.
			 * Also, when any editing activity occurs in a directory, we can either choose to update the dir-cache file (costly, tough,
			 * rather complex) or simply delete the dir-cache file to signal the next occurrence of the 'view' a fresh dirscan is
			 * required.
			 *
			 * Also, we can keep track of the completed thumbnail generation per file in this dir-cache file. However, the argument against
			 * such relative sophitication (to prevent a double round-trip per thumbnail in 'thumb' list view) is the heavy cost of
			 * loading + saving the (edited) dir-cache file for each thumbnail production. The question here is: are those costs significantly
			 * less then the cost of dirscan + round trips (or 'direct' mode thumbnail file tests) for each 'view' request? How many 'view's
			 * do you expect compared to the number of directory edits? 'Usually' that ratio should be rather high (few edits, many views),
			 * thus suggesting a benefit to this aggressive caching and cache updating for thumbnail production.    The 'cheaper for the
			 * thumbnail production' approach would be to consider it a 'directory edit' and thus nuke the dir-cache for every thumbnail (48px)
			 * produced. This is /probably/ slower than the cahce updating, as the latter requires only a single file access per 'view'
			 * operation; all we need to store are a flag (Y/N) per file in the directory, so the store size would be small, even for large
			 * directories.
			 *
			 * What to do? We haven't come to a decision yet.
			 *
			 * Code: TODO
			 */

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

		$mime_filters = $this->getAllowedMimeTypes($mime_filter);

		$fileinfo = array(
				'legal_url' => $legal_url,
				'dir' => $dir,
				'collection' => $coll,
				'mime_filter' => $mime_filter,
				'mime_filters' => $mime_filters,
				'file_preselect' => $file_preselect_arg,
				'preliminary_json' => $json,
				'validation_failure' => $v_ex_code
			);

		if (!empty($this->options['ViewIsAuthorized_cb']) && function_exists($this->options['ViewIsAuthorized_cb']) && !$this->options['ViewIsAuthorized_cb']($this, 'view', $fileinfo))
		{
			$v_ex_code = $fileinfo['validation_failure'];
			if (empty($v_ex_code)) $v_ex_code = 'authorized';
		}
		if (!empty($v_ex_code))
			throw new FileManagerException($v_ex_code);

		$legal_url = $fileinfo['legal_url'];
		$dir = $fileinfo['dir'];
		$coll = $fileinfo['collection'];
		$mime_filter = $fileinfo['mime_filter'];
		$mime_filters = $fileinfo['mime_filters'];
		$file_preselect_arg = $fileinfo['file_preselect'];
		$json = $fileinfo['preliminary_json'];

		$file_preselect_index = -1;
		$out = array(array(), array());

		$mime = 'text/directory';
		$iconspec = false;

		if ($doubledot !== null)
		{
			$filename = '..';

			$l_url = $legal_url . $filename;

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$file = $this->legal_url_path2file_path($l_url);

			$iconspec = 'is.directory_up';

			$icon48 = $this->getIcon($iconspec, false);
			$icon48_e = FileManagerUtility::rawurlencode_path($icon48);

			$icon = $this->getIcon($iconspec, true);
			$icon_e = FileManagerUtility::rawurlencode_path($icon);

			$out[1][] = array(
					'path' => $l_url,
					'name' => $filename,
					'mime' => $mime,
					'icon48' => $icon48_e,
					'icon' => $icon_e
				);
		}

		// now precalc the directory-common items (a.k.a. invariant computation / common subexpression hoisting)
		$iconspec_d = 'is.directory';

		$icon48_d = $this->getIcon($iconspec_d, false);
		$icon48_de = FileManagerUtility::rawurlencode_path($icon48_d);

		$icon_d = $this->getIcon($iconspec_d, true);
		$icon_de = FileManagerUtility::rawurlencode_path($icon_d);

		foreach ($coll['dirs'] as $filename)
		{
			$l_url = $legal_url . $filename;

			$out[1][] = array(
					'path' => $l_url,
					'name' => $filename,
					'mime' => $mime,
					'icon48' => $icon48_de,
					'icon' => $icon_de
				);
		}

		// and now list the files in the directory
		$idx = 0;
		foreach ($coll['files'] as $filename)
		{
			$l_url = $legal_url . $filename;

			// Do not allow the getFileInfo()/imageinfo() overhead per file for very large directories; just guess the mimetype from the filename alone.
			// The real mimetype will show up in the 'details' view anyway as we'll have called getFileInfo() by then!
			$mime = $this->getMimeFromExt($filename);
			$iconspec = $filename;

			if (!$this->IsAllowedMimeType($mime, $mime_filters))
				continue;

			if ($filename === $file_preselect_arg)
			{
				$file_preselect_index = $idx;
			}

			/*
			 * offload the thumbnailing process to another event ('event=detail / mode=direct') to be fired by the client
			 * when it's time to render the thumbnail: the offloading helps us tremendously in coping with large
			 * directories:
			 * WE simply assume the thumbnail will be there, so we don't even need to check for its existence
			 * (which saves us one more file_exists() per item at the very least). And when it doesn't, that's
			 * for the event=thumbnail handler to worry about (creating the thumbnail on demand or serving
			 * a generic icon image instead).
			 *
			 * For now, simply assign a basic icon to any and all; the 'detail' event will replace this item in the frontend
			 * when the time has arrives when that 'detail' request has been answered.
			 */
			$icon48 = $this->getIcon($iconspec, false);
			$icon48_e = FileManagerUtility::rawurlencode_path($icon48);

			$icon = $this->getIcon($iconspec, true);
			$icon_e = FileManagerUtility::rawurlencode_path($icon);

			$out[0][] = array(
					'path' => $l_url,
					'name' => $filename,
					'mime' => $mime,
					// we don't know the thumbnail paths yet --> this will trigger deferred requests: (event=detail, mode=direct)
					'thumbs_deferred' => true,
					'icon48' => $icon48_e,
					'icon' => $icon_e
				);
			$idx++;
		}

		return array_merge((is_array($json) ? $json : array()), array(
				'root' => substr($this->options['URLpath4FileManagedDirTree'], 1),
				'this_dir' => array(
					'path' => $legal_url,
					'name' => basename($legal_url),
					'date' => date($this->options['dateFormat'], @filemtime($dir)),
					'mime' => 'text/directory',
					'icon48' => $icon48_de,
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
	 * $_POST['directory']     path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
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
	 *                         options['URLpath4FileManagedDirTree']-rooted LEGAL URI subtree, it will be discarded
	 *                         entirely (as all file paths, whether they are absolute or relative,
	 *                         must end up inside the options['URLpath4FileManagedDirTree']-rooted subtree to be
	 *                         considered manageable files) and the process will continue as if
	 *                         the $_POST['file_preselect'] entry had not been set.
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
	 *
	 * Next to these, the JSON encoded output will, with high probability, include a
	 * list view of a valid parent or 'basedir' as a fast and easy fallback mechanism for client side
	 * viewing code, jumping back to a existing directory. However, severe and repetitive errors may not produce this
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
		$legal_url = null;

		try
		{
			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg . '/');

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
						$file_preselect_arg = basename($file_preselect_arg);
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
				$rv = $this->_onView($legal_url, $jserr, $mime_filter, $file_preselect_arg);

				$this->sendHttpHeaders('Content-Type: application/json');
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

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
		echo json_encode($jserr);
		die();
	}

	/**
	 * Process the 'detail' event
	 *
	 * Returns a JSON encoded HTML chunk describing the specified file (metadata such
	 * as size, format and possibly a thumbnail image as well)
	 *
	 * Expected parameters:
	 *
	 * $_POST['directory']     path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
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
	 *                         mode, tells us delayed generating and loading of the
	 *                         thumbnail image(s) is out of the question.
	 *                         'auto' mode will simply provide direct thumbnail image
	 *                         URLs when those are available in cache, while 'auto' mode
	 *                         will neglect to provide those, expecting the frontend to
	 *                         delay-load them through another 'event=detail / mode=direct'
	 *                         request later on.
	 *                         'metaHTML': show the metadata as extra HTML content in
	 *                         the preview pane (you can also turn that off using CSS:
	 *                             div.filemanager div.filemanager-diag-dump
	 *                             {
	 *                                 display: none;
	 *                             }
	 *                         'metaJSON': deliver the extra getID3 metadata in JSON format
	 *                         in the json['metadata'] field.
	 *
	 *                         Modes can be mixed by adding a '+' between them.
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
			$mode = explode('+', $mode);
			if (empty($mode))
			{
				$mode = array();
			}

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg . '/');

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$filename = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$filename = basename($file_arg);
				// must normalize the combo as the user CAN legitimally request filename == '.' (directory detail view) for this event!
				$path = $this->rel2abs_legal_url_path($legal_url . $filename);
				//echo " path = $path, ($legal_url . $filename);\n";
				$legal_url = $path;
				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						$mime = $meta->getMimeType();
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = 'extension';
						}
						else
						{
							$v_ex_code = null;
						}
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
			$mode = $fileinfo['mode'];
			$meta = $fileinfo['meta_data'];
			//$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			$jserr = $fileinfo['preliminary_json'];

			$jserr = $this->extractDetailInfo($jserr, $legal_url, $meta, $mime_filter, $mime_filters, $mode);

			$this->sendHttpHeaders('Content-Type: application/json');

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

		$icon48 = $this->getIconForError($emsg, 'is.default-error', false);
		$icon48_e = FileManagerUtility::rawurlencode_path($icon48);
		$icon = $this->getIconForError($emsg, 'is.default-error', true);
		$icon_e = FileManagerUtility::rawurlencode_path($icon);
		$jserr['thumb250'] = null;
		$jserr['thumb48'] = null;
		$jserr['icon48'] = $icon48_e;
		$jserr['icon'] = $icon_e;

		$postdiag_err_HTML = '<p class="err_info">' . $emsg . '</p>';
		$preview_HTML = '${nopreview}';
		$content = '';
		//$content .= '<h3>${preview}</h3>';
		$content .= '<div class="filemanager-preview-content">' . $preview_HTML . '</div>';
		//$content .= '<h3>Diagnostics</h3>';
		//$content .= '<div class="filemanager-detail-diag">;
		$content .= '<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>';
		//$content .= '</div>';

		$json['content'] = self::compressHTML($content);

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
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
	 * $_POST['directory']     path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
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
				throw new FileManagerException('disabled:destroy');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg . '/');

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$filename = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$filename = basename($file_arg);
				$legal_url .= $filename;
				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (file_exists($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						$mime = $meta->getMimeType();
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = 'extension';
						}
						else
						{
							$v_ex_code = null;
						}
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
			{
				throw new FileManagerException('unlink_failed:' . $legal_url);
			}

			$this->sendHttpHeaders('Content-Type: application/json');

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

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
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
	 * $_POST['directory']     path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
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

		$file_arg = null;
		$legal_url = null;

		try
		{
			if (!$this->options['create'])
				throw new FileManagerException('disabled:create');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg . '/');

			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$dir = $this->legal_url_path2file_path($legal_url);

			$filename = null;
			$file = null;
			$newdir = null;
			if (!empty($file_arg))
			{
				$filename = basename($file_arg);
				$filename = FileManagerUtility::cleanUrl($filename, array(), '_');

				if (!$this->IsHiddenNameAllowed($file_arg))
				{
					$v_ex_code = 'authorized';
				}
				else
				{
					if (is_dir($dir))
					{
						$file = $this->getUniqueName(array('filename' => $filename), $dir);  // a directory has no 'extension'!
						if ($file !== null)
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
			{
				throw new FileManagerException('mkdir_failed:' . $this->legal2abs_url_path($legal_url) . $file);
			}
			@chmod($newdir, $fileinfo['chmod']);
			
			$this->sendHttpHeaders('Content-Type: application/json');

			// success, now show the new directory as a list view:
			$rv = $this->_onView($legal_url . $file . '/', $jserr, $mime_filter);

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
				$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['URLpath4FileManagedDirTree'];
					$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
				}
				catch (Exception $e)
				{
					// when we fail here, it's pretty darn bad and nothing to it.
					// just push the error JSON and go.
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
				$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
			}
			catch (Exception $e)
			{
				// and fall back to showing the BASEDIR directory
				try
				{
					$legal_url = $this->options['URLpath4FileManagedDirTree'];
					$jserr = $this->_onView($legal_url, $jserr, $mime_filter);
				}
				catch (Exception $e)
				{
					// when we fail here, it's pretty darn bad and nothing to it.
					// just push the error JSON and go.
				}
			}
		}

		$this->modify_json4exception($jserr, $emsg, 'directory = ' . $file_arg . ', path = ' . $legal_url);

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
		echo json_encode($jserr);
	}

	/**
	 * Process the 'download' event
	 *
	 * Send the file content of the specified file for download by the client.
	 * Only files residing within the directory tree rooted by the
	 * 'basedir' (options['URLpath4FileManagedDirTree']) will be allowed to be downloaded.
	 *
	 * Expected parameters:
	 *
	 * $_POST['file']         filepath of the file to be downloaded
	 *
	 * $_POST['filter']       optional mimetype filter string, amy be the part up to and
	 *                        including the slash '/' or the full mimetype. Only files
	 *                        matching this (set of) mimetypes will be listed.
	 *                        Examples: 'image/' or 'application/zip'
	 *
	 * On errors a HTTP 403 error response will be sent instead.
	 */
	protected function onDownload()
	{
		$emsg = null;
		$file_arg = null;
		$file = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			if (!$this->options['download'])
				throw new FileManagerException('disabled:download');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$legal_url = null;
			$file = null;
			$mime = null;
			$meta = null;
			if (!empty($file_arg))
			{
				$legal_url = $this->rel2abs_legal_url_path($file_arg);

				// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
				$file = $this->legal_url_path2file_path($legal_url);

				if (is_readable($file))
				{
					if (is_file($file))
					{
						$meta = $this->getFileInfo($file, $legal_url);
						$mime = $meta->getMimeType();
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = 'extension';
						}
						else
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
				$fi = pathinfo($legal_url);

				$hdrs = array();
				// see also: http://www.boutell.com/newfaq/creating/forcedownload.html
				switch ($mime)
				{
				// add here more mime types for different file types and special handling by the client on download
				case 'application/pdf':
					$hdrs[] = 'Content-Type: ' . $mime;
					break;

				default:
					$hdrs[] = 'Content-Type: application/octet-stream';
					break;
				}
				$hdrs[] = 'Content-Disposition: attachment; filename="' . $fi['basename'] . '"'; // use 'attachment' to force a download
				$hdrs[] = 'Content-length: ' . $fsize;
				$hdrs[] = 'Expires: 0';
				$hdrs[] = 'Cache-Control: must-revalidate, post-check=0, pre-check=0';
				$hdrs[] = '!Cache-Control: private'; // flag as FORCED APPEND; use this to open files directly

				$this->sendHttpHeaders($hdrs);

				fpassthru($fd);
				fclose($fd);
				return;
			}

			$emsg = 'read_error';
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

		// we don't care whether it's a 404, a 403 or something else entirely: we feed 'em a 403 and that's final!
		send_response_status_header(403);

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $this->mkSafe4Display($file_arg . ', destination path = ' . $file));

		$this->sendHttpHeaders('Content-Type: text/plain');        // Safer for iframes: the 'application/json' mime type would cause FF3.X to pop up a save/view dialog when transmitting these error reports!

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
		echo json_encode($jserr);
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
	 * $_POST['directory']    path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
	 *
	 * $_POST['resize']       nonzero value indicates any uploaded image should be resized to the configured
	 *                        options['maxImageDimension'] width and height whenever possible
	 *
	 * $_POST['filter']       optional mimetype filter string, amy be the part up to and
	 *                        including the slash '/' or the full mimetype. Only files
	 *                        matching this (set of) mimetypes will be listed.
	 *                        Examples: 'image/' or 'application/zip'
	 *
	 * $_FILES[]              the metadata for the uploaded file
	 *
	 * $_POST['reportContentType']
	 *                        if you want a specific content type header set on our response, put it here.
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
		$file = null;
		$legal_dir_url = null;
		$jserr = array(
				'status' => 1
			);

		try
		{
			if (!$this->options['upload'])
				throw new FileManagerException('disabled:upload');

			// MAY upload zero length files!
			if (!isset($_FILES) || empty($_FILES['Filedata']) || empty($_FILES['Filedata']['name']))
				throw new FileManagerException('nofile');

			$v_ex_code = 'nofile';

			$file_size = (empty($_FILES['Filedata']['size']) ? 0 : $_FILES['Filedata']['size']);
			$file_arg = $_FILES['Filedata']['name'];

			$dir_arg = $this->getPOSTparam('directory');
			$legal_dir_url = $this->rel2abs_legal_url_path($dir_arg . '/');
			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$dir = $this->legal_url_path2file_path($legal_dir_url);

			$mime_filter = $this->getPOSTparam('filter', $this->options['filter']);
			$mime_filters = $this->getAllowedMimeTypes($mime_filter);

			$tmppath = $_FILES['Filedata']['tmp_name'];

			$resize_imgs = $this->getPOSTparam('resize', 0);

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
					if ($filename !== null)
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
							$fi = pathinfo($filename);
							$fi['extension'] = $this->getSafeExtension(isset($fi['extension']) ? $fi['extension'] : '');
							$filename = $fi['filename'] . ((isset($fi['extension']) && strlen($fi['extension']) > 0) ? '.' . $fi['extension'] : '');
						}

						$legal_url = $legal_dir_url . $filename;

						// UPLOAD delivers files in temporary storage with extensions NOT matching the mime type, so we don't
						// filter on extension; we just let getID3 go ahead and content-sniff the mime type.
						// Since getID3::analyze() is a quite costly operation, we like to do it only ONCE per file,
						// so we cache the last entries.
						$meta = $this->getFileInfo($tmppath, $legal_url);
						$mime = $meta->getMimeType();
						if (!$this->IsAllowedMimeType($mime, $mime_filters))
						{
							$v_ex_code = 'extension';
						}
						else
						{
							$v_ex_code = null;
						}
					}
				}
			}

			$fileinfo = array(
				'legal_dir_url' => $legal_dir_url,
				'dir' => $dir,
				'raw_filename' => $file_arg,
				'filename' => $filename,
				'meta_data' => $meta,
				'mime' => $mime,
				'mime_filter' => $mime_filter,
				'mime_filters' => $mime_filters,
				'tmp_filepath' => $tmppath,
				'size' => $file_size,
				'maxsize' => $this->options['maxUploadSize'],
				'overwrite' => false,
				'resize' => $resize_imgs,
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

			$legal_dir_url = $fileinfo['legal_dir_url'];
			$dir = $fileinfo['dir'];
			$file_arg = $fileinfo['raw_filename'];
			$filename = $fileinfo['filename'];
			$meta = $fileinfo['meta_data'];
			$mime = $fileinfo['mime'];
			$mime_filter = $fileinfo['mime_filter'];
			$mime_filters = $fileinfo['mime_filters'];
			//$tmppath = $fileinfo['tmp_filepath'];
			$resize_imgs = $fileinfo['resize'];
			$jserr = $fileinfo['preliminary_json'];

			if ($fileinfo['maxsize'] && $fileinfo['size'] > $fileinfo['maxsize'])
				throw new FileManagerException('size');

			//if (!isset($fileinfo['extension']))
			//  throw new FileManagerException('extension');
			
			// Creates safe file names
			if ($this->options['cleanFileName'])
			{
				$filename = FileManagerUtility::cleanUrl($filename, array(), '_');
			}
			
			// must transform here so alias/etc. expansions inside legal_url_path2file_path() get a chance:
			$legal_url = $legal_dir_url . $filename;
			$file = $this->legal_url_path2file_path($legal_url);

			if (!$fileinfo['overwrite'] && file_exists($file))
				throw new FileManagerException('exists');

			if (!@move_uploaded_file($_FILES['Filedata']['tmp_name'], $file))
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
					$dir = $this->legal_url_path2file_path($legal_dir_url);
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

					if (!empty($_FILES['Filedata']['error']))
					{
						$emsg .= ': error code = ' . strtolower($_FILES['Filedata']['error']) . ', ' . $emsg_add;
					}
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
			$thumb250   = false;
			$thumb250_e = false;
			$thumb48    = false;
			$thumb48_e  = false;
			if (FileManagerUtility::startsWith($mime, 'image/'))
			{
				if (!empty($resize_imgs))
				{
					$img = new Image($file);
					$size = $img->getSize();
					// Image::resize() takes care to maintain the proper aspect ratio, so this is easy
					// (default quality is 100% for JPEG so we get the cleanest resized images here)
					$img->resize($this->options['maxImageDimension']['width'], $this->options['maxImageDimension']['height'])->save();
					unset($img);

					// source image has changed: nuke the cached metadata and then refetch the metadata = forced refetch
					$meta = $this->getFileInfo($file, $legal_url, true);
				}
			}

			/*
			 * 'abuse' the info extraction process to generate the thumbnails. Besides, doing it this way will also prime the metadata cache for this item,
			 * so we'll have a very fast performance viewing this file's details and thumbnails both from this point forward!
			 */
			$jsbogus = array('status' => 1);
			$jsbogus = $this->extractDetailInfo($jsbogus, $legal_url, $meta, $mime_filter, $mime_filters, array('direct'));

			$this->sendHttpHeaders('Content-Type: ' . $this->getPOSTparam('reportContentType', 'application/json'));

			echo json_encode(array(
					'status' => 1,
					'name' => basename($file)
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

		$this->modify_json4exception($jserr, $emsg, 'file = ' . $this->mkSafe4Display($file_arg . ', destination path = ' . $file . ', target directory (URI path) = ' . $legal_dir_url));

		$this->sendHttpHeaders('Content-Type: ' . $this->getPOSTparam('reportContentType', 'application/json'));

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
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
	 *   $_POST['copy']          nonzero value means copy, zero or nil for move/rename
	 *
	 * Source filespec:
	 *
	 *   $_POST['directory']     path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root
	 *
	 *   $_POST['file']          original name of the file/subdirectory to be renamed/copied
	 *
	 * Destination filespec:
	 *
	 *   $_POST['newDirectory']  path relative to basedir a.k.a. options['URLpath4FileManagedDirTree'] root;
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
				throw new FileManagerException('disabled:rn_mv_cp');

			$v_ex_code = 'nofile';

			$file_arg = $this->getPOSTparam('file');

			$dir_arg = $this->getPOSTparam('directory');
			$legal_url = $this->rel2abs_legal_url_path($dir_arg . '/');

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
					$filename = basename($file_arg);
					$path = $this->legal_url_path2file_path($legal_url . $filename);

					if (file_exists($path))
					{
						$is_dir = is_dir($path);

						// note: we do not support copying entire directories, though directory rename/move is okay
						if ($is_copy && $is_dir)
						{
							$v_ex_code = 'disabled:rn_mv_cp';
						}
						else if ($rename)
						{
							$fn = 'rename';
							$legal_newurl = $legal_url;
							$newdir = $dir;

							$newname = basename($newname_arg);
							if ($is_dir)
								$newname = $this->getUniqueName(array('filename' => $newname), $newdir);  // a directory has no 'extension'
							else
								$newname = $this->getUniqueName($newname, $newdir);

							if ($newname === null)
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
							$legal_newurl = $this->rel2abs_legal_url_path($newdir_arg . '/');
							$newdir = $this->legal_url_path2file_path($legal_newurl);

							if ($is_dir)
								$newname = $this->getUniqueName(array('filename' => $filename), $newdir);  // a directory has no 'extension'
							else
								$newname = $this->getUniqueName($filename, $newdir);

							if ($newname === null)
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

			if ($rename)
			{
				// try to remove the thumbnail & other cache entries related to the original file; don't mind if it doesn't exist
				$flurl = $legal_url . $filename;
				$meta = &$this->getid3_cache->pick($flurl, $this, false);
				assert($meta != null);
				if (!$meta->delete(true))
				{
					throw new FileManagerException('delete_cache_entries_failed');
				}
				unset($meta);
			}

			if (!function_exists($fn))
				throw new FileManagerException((empty($fn) ? 'rename' : $fn) . '_failed');
			if (!@$fn($path, $newpath))
				throw new FileManagerException($fn . '_failed');

			$this->sendHttpHeaders('Content-Type: application/json');

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

		$this->sendHttpHeaders('Content-Type: application/json');

		// when we fail here, it's pretty darn bad and nothing to it.
		// just push the error JSON and go.
		echo json_encode($jserr);
	}










	/**
	 * Send the listed headers when possible; the input parameter is an array of header strings or a single header string.
	 *
	 * NOTE: when a header string starts with the '!' character, it means that header is required
	 * to be appended to the header output set and not overwrite any existing equal header.
	 */
	public function sendHttpHeaders($headers)
	{
		if (!headers_sent())
		{
			$headers = array_merge(array(
				'Expires: Fri, 01 Jan 1990 00:00:00 GMT',
				'Cache-Control: no-cache, no-store, max-age=0, must-revalidate'
			), (is_array($headers) ? $headers : array($headers)));

			foreach($headers as $h)
			{
				$append_flag = ($h[0] == '!');
				$h = ltrim($h, '!');
				header($h, $append_flag);
			}
		}
	}




	// derived from   http://www.php.net/manual/en/function.filesize.php#100097
	public function format_bytes($bytes)
	{
		if ($bytes < 1024)
			return $bytes . ' Bytes';
		elseif ($bytes < 1048576)
			return round($bytes / 1024, 2) . ' KB (' . $bytes . ' Bytes)';
		elseif ($bytes < 1073741824)
			return round($bytes / 1048576, 2) . ' MB (' . $bytes . ' Bytes)';
		else
			return round($bytes / 1073741824, 2) . ' GB (' . $bytes . ' Bytes)';
	}

	/**
	 * Produce a HTML snippet detailing the given file in the JSON 'content' element; place additional info
	 * in the JSON elements 'thumbnail', 'thumb48', 'thumb250', 'width', 'height', ...
	 *
	 * Return an augmented JSON array.
	 *
	 * Throw an exception on error.
	 */
	public function extractDetailInfo($json_in, $legal_url, &$meta, $mime_filter, $mime_filters, $mode)
	{
		$auto_thumb_gen_mode = !in_array('direct', $mode, true);
		$metaHTML_mode = in_array('metaHTML', $mode, true);
		$metaJSON_mode = in_array('metaJSON', $mode, true);

		$url = $this->legal2abs_url_path($legal_url);
		$filename = basename($url);
log_message('error', '$url : ' . $url);
		// must transform here so alias/etc. expansions inside url_path2file_path() get a chance:
		$file = $this->url_path2file_path($url);

		$isdir = !is_file($file);
		$bad_ext = false;
		$mime = null;
		// only perform the (costly) getID3 scan when it hasn't been done before, i.e. can we re-use previously obtained data or not?
		if (!is_object($meta))
		{
			$meta = $this->getFileInfo($file, $legal_url);
		}
		if (!$isdir)
		{
			$mime = $meta->getMimeType();

			$mime2 = $this->getMimeFromExt($file);
			$meta->store('mime_type from file extension', $mime2);

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
			$mime = $meta->getMimeType();
			// $mime = 'text/directory';
			$iconspec = 'is.directory';
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
		// several chunks of work below may have been cached and when they have been, use the cached data.

		// it's an internal error when this entry do not exist in the cache store by now!
		$fi = $meta->fetch('analysis');
		//assert(!empty($fi));

		$icon48 = $this->getIcon($iconspec, false);
		$icon = $this->getIcon($iconspec, true);

		$thumb250 = $meta->fetch('thumb250_direct');
		$thumb48 = $meta->fetch('thumb48_direct');
		$thumb250_e = false;
		$thumb48_e  = false;

		$tstamp_str = date($this->options['dateFormat'], @filemtime($file));
		$fsize = @filesize($file);

		$json = array_merge(array(
				//'status' => 1,
				//'mimetype' => $mime,
				'content' => self::compressHTML('<div class="margin">
					${nopreview}
				</div>')
			),
			array(
				'path' => $legal_url,
				'name' => $filename,
				'date' => $tstamp_str,
				'mime' => $mime,
				'size' => $fsize
			));

		if (empty($fsize))
		{
			$fsize_str = '-';
		}
		else
		{
			// convert to T/G/M/K-bytes:
			$fsize_str = $this->format_bytes($fsize);
		}

		$content = '<dl>
						<dt>${modified}</dt>
						<dd class="filemanager-modified">' . $tstamp_str . '</dd>
						<dt>${type}</dt>
						<dd class="filemanager-type">' . $mime . '</dd>
						<dt>${size}</dt>
						<dd class="filemanager-size">' . $fsize_str . '</dd>';
		$content_dl_term = false;

		$preview_HTML = null;
		$postdiag_err_HTML = '';
		$postdiag_dump_HTML = '';
		$thumbnails_done_or_deferred = false;   // TRUE: mark our thumbnail work as 'done'; any NULL thumbnails represent deferred generation entries!
		$check_for_embedded_img = false;

		$mime_els = explode('/', $mime);
		for(;;) // bogus loop; only meant to assist the [mime remapping] state machine in here
		{
			switch ($mime_els[0])
			{
			case 'image':
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
				$emsg = null;
				try
				{
					if (empty($thumb250))
					{
						$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], $auto_thumb_gen_mode);
					}
					if (!empty($thumb250))
					{
						$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
					}
					if (empty($thumb48))
					{
						$thumb48 = $this->getThumb($meta, (!empty($thumb250) ? $this->url_path2file_path($thumb250) : $file), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], $auto_thumb_gen_mode);
					}
					if (!empty($thumb48))
					{
						$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
					}

					if (empty($thumb48) || empty($thumb250))
					{
						/*
						 * do NOT generate the thumbnail itself yet (it takes too much time!) but do check whether it CAN be generated
						 * at all: THAT is a (relatively speaking) fast operation!
						 */
						$imginfo = Image::checkFileForProcessing($file);
					}
					$thumbnails_done_or_deferred = true;
				}
				catch (Exception $e)
				{
					$emsg = $e->getMessage();
					$icon48 = $this->getIconForError($emsg, $legal_url, false);
					$icon = $this->getIconForError($emsg, $legal_url, true);
					// even cache the fail: that means next time around we don't suffer the failure but immediately serve the error icon instead.
				}

				$width = round($this->getID3infoItem($fi, 0, 'video', 'resolution_x'));
				$height = round($this->getID3infoItem($fi, 0, 'video', 'resolution_y'));
				$json['width'] = $width;
				$json['height'] = $height;

				$content .= '
						<dt>${width}</dt><dd>' . $width . 'px</dd>
						<dt>${height}</dt><dd>' . $height . 'px</dd>
					</dl>';
				$content_dl_term = true;

				$sw_make = $this->mkSafeUTF8($this->getID3infoItem($fi, null, 'jpg', 'exif', 'IFD0', 'Software'));
				$time_make = $this->mkSafeUTF8($this->getID3infoItem($fi, null, 'jpg', 'exif', 'IFD0', 'DateTime'));

				if (!empty($sw_make) || !empty($time_make))
				{
					$content .= '<p>Made with ' . (empty($sw_make) ? '???' : $sw_make) . ' @ ' . (empty($time_make) ? '???' : $time_make) . '</p>';
				}

				// are we delaying the thumbnail generation? When yes, then we need to infer the thumbnail dimensions *anyway*!
				if (empty($thumb48) && $thumbnails_done_or_deferred)
				{
					$dims = $this->predictThumbDimensions($width, $height, $this->options['thumbSmallSize'], $this->options['thumbSmallSize']);

					$json['thumb48_width'] = $dims['width'];
					$json['thumb48_height'] = $dims['height'];
				}
				if (empty($thumb250))
				{
					if ($thumbnails_done_or_deferred)
					{
						// to show the loader.gif in the preview <img> tag, we MUST set a width+height there, so we guestimate the thumbnail250 size as accurately as possible
						//
						// derive size from original:
						$dims = $this->predictThumbDimensions($width, $height, $this->options['thumbBigSize'], $this->options['thumbBigSize']);

						$preview_HTML = '<a href="' . FileManagerUtility::rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $this->options['URLpath4assets'] . 'Images/transparent.gif" class="preview" alt="preview" style="width: ' . $dims['width'] . 'px; height: ' . $dims['height'] . 'px;" />
									 </a>';

						$json['thumb250_width'] = $dims['width'];
						$json['thumb250_height'] = $dims['height'];
					}
					else
					{
						// when we get here, a failure occurred before, so we only will have the icons. So we use those:
						$preview_HTML = '<a href="' . FileManagerUtility::rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . FileManagerUtility::rawurlencode_path($icon48) . '" class="preview" alt="preview" />
									 </a>';
					}
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
				break;

			case 'text':
				switch ($mime_els[1])
				{
				case 'directory':
					$content = '<dl>';
					
					$preview_HTML = '';
					break;

				default:
					// text preview:
					$filecontent = @file_get_contents($file, false, null, 0);
					if ($filecontent === false)
						throw new FileManagerException('nofile');

					if (!FileManagerUtility::isBinary($filecontent))
					{
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
					$check_for_embedded_img = true;

					$info = $this->getID3infoItem($fi, null, 'swf', 'header');
					if (is_array($info))
					{
						$width = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_width') / 10);
						$height = round($this->getID3infoItem($fi, 0, 'swf', 'header', 'frame_height') / 10);
						$json['width'] = $width;
						$json['height'] = $height;

						$content .= '
								<dt>${width}</dt><dd>' . $width . 'px</dd>
								<dt>${height}</dt><dd>' . $height . 'px</dd>
								<dt>${length}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'swf', 'header', 'length') / $this->getID3infoItem($fi, 25, 'swf', 'header', 'frame_count')) . 's</dd>
							</dl>';
						$content_dl_term = true;
					}
					break;

				default:
					// else: fall back to 'no preview available' (if getID3 didn't deliver instead...)
					$mime_els[0] = 'unknown'; // remap!
					continue 3;
				}
				break;

			case 'audio':
				$check_for_embedded_img = true;

				$title = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'title', 0), 'tags', 'id3v2', 'title', 0));
				$artist = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'artist', 0), 'tags', 'id3v2', 'artist', 0));
				$album = $this->mkSafeUTF8($this->getID3infoItem($fi, $this->getID3infoItem($fi, '???', 'tags', 'id3v1', 'album', 0), 'tags', 'id3v2', 'album', 0));

				$content .= '
						<dt>${title}</dt><dd>' . $title . '</dd>
						<dt>${artist}</dt><dd>' . $artist . '</dd>
						<dt>${album}</dt><dd>' . $album . '</dd>
						<dt>${length}</dt><dd>' . $this->mkSafeUTF8($this->getID3infoItem($fi, '???', 'playtime_string')) . '</dd>
						<dt>${bitrate}</dt><dd>' . round($this->getID3infoItem($fi, 0, 'bitrate') / 1000) . 'kbps</dd>
					</dl>';
				$content_dl_term = true;
				break;

			case 'video':
				$check_for_embedded_img = true;

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

				$content .= '
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
				$content_dl_term = true;
				break;

			default:
				// fall back to 'no preview available' (if getID3 didn't deliver instead...)
				break;
			}
			break;
		}

		if (!$content_dl_term)
		{
			$content .= '</dl>';
		}

		if (!empty($fi['error']))
		{
			$postdiag_err_HTML .= '<p class="err_info">' . $this->mkSafeUTF8(implode(', ', $fi['error'])) . '</p>';
		}

		$emsgX = null;
		if (empty($thumb250))
		{
			if (!$thumbnails_done_or_deferred)
			{
				// check if we have stored a thumbnail for this file anyhow:
				$thumb250 = $this->getThumb($meta, $file, $this->options['thumbBigSize'], $this->options['thumbBigSize'], true);
				if (empty($thumb250))
				{
					if (!empty($fi) && $check_for_embedded_img)
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
							$thumbX = $meta->getThumbURL('embed');
							$tfi = pathinfo($thumbX);
							$tfi['extension'] = image_type_to_extension($embed->metadata[2]);
							$thumbX = $tfi['dirname'] . '/' . $tfi['filename'] . '.' . $tfi['extension'];
							$thumbX = $this->normalize($thumbX);
							$thumbX_f = $this->url_path2file_path($thumbX);
							// as we've spent some effort to dig out the embedded thumbnail, and 'knowing' (assuming) that generally
							// embedded thumbnails are not too large, we don't concern ourselves with delaying the thumbnail generation (the
							// source file mapping is not bidirectional, either!) and go straight ahead and produce the 250px thumbnail at least.
							$thumb250   = false;
							$thumb250_e = false;
							$thumb48    = false;
							$thumb48_e  = false;
							$meta->mkCacheDir();
							if (false === file_put_contents($thumbX_f, $embed->imagedata))
							{
								@unlink($thumbX_f);
								$emsgX = 'Cannot save embedded image data to cache.';
								$icon48 = $this->getIcon('is.default-error', false);
								$icon = $this->getIcon('is.default-error', true);
							}
							else
							{
								try
								{
									$thumb250 = $this->getThumb($meta, $thumbX_f, $this->options['thumbBigSize'], $this->options['thumbBigSize'], false);
									if (!empty($thumb250))
									{
										$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
									}
									$thumb48 = $this->getThumb($meta, (!empty($thumb250) ? $this->url_path2file_path($thumb250) : $thumbX_f), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], false);
									if (!empty($thumb48))
									{
										$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
									}
								}
								catch (Exception $e)
								{
									$emsgX = $e->getMessage();
									$icon48 = $this->getIconForError($emsgX, $legal_url, false);
									$icon = $this->getIconForError($emsgX, $legal_url, true);
								}
							}
						}
					}
				}
				else
				{
					// !empty($thumb250)
					$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
					try
					{
						$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], false);
						assert(!empty($thumb48));
						$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
					}
					catch (Exception $e)
					{
						$emsgX = $e->getMessage();
						$icon48 = $this->getIconForError($emsgX, $legal_url, false);
						$icon = $this->getIconForError($emsgX, $legal_url, true);
						$thumb48 = false;
						$thumb48_e = false;
					}
				}
			}
		}
		else // if (!empty($thumb250))
		{
			if (empty($thumb250_e))
			{
				$thumb250_e = FileManagerUtility::rawurlencode_path($thumb250);
			}
			if (empty($thumb48))
			{
				try
				{
					$thumb48 = $this->getThumb($meta, $this->url_path2file_path($thumb250), $this->options['thumbSmallSize'], $this->options['thumbSmallSize'], false);
					assert(!empty($thumb48));
					$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
				}
				catch (Exception $e)
				{
					$emsgX = $e->getMessage();
					$icon48 = $this->getIconForError($emsgX, $legal_url, false);
					$icon = $this->getIconForError($emsgX, $legal_url, true);
					$thumb48 = false;
					$thumb48_e = false;
				}
			}
			if (empty($thumb48_e))
			{
				$thumb48_e = FileManagerUtility::rawurlencode_path($thumb48);
			}
		}

		// also provide X/Y size info with each direct-access thumbnail file:
		if (!empty($thumb250))
		{
			$json['thumb250'] = $thumb250_e;
			$meta->store('thumb250_direct', $thumb250);

			$tnsize = $meta->fetch('thumb250_info');
			if (empty($tnsize))
			{
				$tnsize = getimagesize($this->url_path2file_path($thumb250));
				$meta->store('thumb250_info', $tnsize);
			}
			if (is_array($tnsize))
			{
				$json['thumb250_width'] = $tnsize[0];
				$json['thumb250_height'] = $tnsize[1];

				if (empty($preview_HTML))
				{
					$preview_HTML = '<a href="' . FileManagerUtility::rawurlencode_path($url) . '" data-milkbox="single" title="' . htmlentities($filename, ENT_QUOTES, 'UTF-8') . '">
									   <img src="' . $thumb250_e . '" class="preview" alt="' . (!empty($emsgX) ? $this->mkSafe4HTMLattr($emsgX) : 'preview') . '"
											style="width: ' . $tnsize[0] . 'px; height: ' . $tnsize[1] . 'px;" />
									 </a>';
				}
			}
		}
		if (!empty($thumb48))
		{
			$json['thumb48'] = $thumb48_e;
			$meta->store('thumb48_direct', $thumb48);

			$tnsize = $meta->fetch('thumb48_info');
			if (empty($tnsize))
			{
				$tnsize = getimagesize($this->url_path2file_path($thumb48));
				$meta->store('thumb48_info', $tnsize);
			}
			if (is_array($tnsize))
			{
				$json['thumb48_width'] = $tnsize[0];
				$json['thumb48_height'] = $tnsize[1];
			}
		}
		if ($thumbnails_done_or_deferred && (empty($thumbs250) || empty($thumbs48)))
		{
			$json['thumbs_deferred'] = true;
		}
		else
		{
			$json['thumbs_deferred'] = false;
		}

		if (!empty($icon48))
		{
			$icon48_e = FileManagerUtility::rawurlencode_path($icon48);
			$json['icon48'] = $icon48_e;
		}
		if (!empty($icon))
		{
			$icon_e = FileManagerUtility::rawurlencode_path($icon);
			$json['icon'] = $icon_e;
		}

		$fi4dump = null;
		if (!empty($fi))
		{
			try
			{
				$fi4dump = $meta->fetch('file_info_dump');
				if (empty($fi4dump))
				{
					$fi4dump = array_merge(array(), $fi); // clone $fi
					$this->clean_ID3info_results($fi4dump);
					$meta->store('file_info_dump', $fi4dump);
				}

				$dump = FileManagerUtility::table_var_dump($fi4dump, false);

				$postdiag_dump_HTML .= "\n" . $dump . "\n";
				//@file_put_contents(dirname(__FILE__) . '/getid3.log', print_r(array('html' => $preview_HTML, 'json' => $json, 'thumb250_e' => $thumb250_e, 'thumb250' => $thumb250, 'embed' => $embed, 'fileinfo' => $fi), true));
			}
			catch(Exception $e)
			{
				$postdiag_err_HTML .= '<p class="err_info">' . $e->getMessage() . '</p>';
			}
		}

		if ($preview_HTML === null)
		{
			$preview_HTML = '${nopreview}';
		}

		if (!empty($preview_HTML))
		{
			//$content .= '<h3>${preview}</h3>';
			$content .= '<div class="filemanager-preview-content">' . $preview_HTML . '</div>';
		}
		if (!empty($postdiag_err_HTML))
		{
			$content .= '<div class="filemanager-errors">' . $postdiag_err_HTML . '</div>';
		}
		if (!empty($postdiag_dump_HTML) && $metaHTML_mode)
		{
			$content .= '<div class="filemanager-diag-dump">' . $postdiag_dump_HTML . '</div>';
		}

		$json['content'] = self::compressHTML($content);
		$json['metadata'] = ($metaJSON_mode ? $fi4dump : null);

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
						else
						{
							$this->clean_ID3info_results_r($value, $flags);
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

		if (is_dir($file))
		{
			$dir = self::enforceTrailingSlash($file);
			$url = self::enforceTrailingSlash($legal_url);
			$coll = $this->scandir($dir, '*', false, 0, ~GLOB_NOHIDDEN);
			if ($coll !== false)
			{
				foreach ($coll['dirs'] as $f)
				{
					if ($f === '.' || $f === '..')
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
				$mime = $this->getMimeFromExt($file);   // take the fast track to mime type sniffing; we'll live with the (rather low) risk of being inacurate due to accidental/intentional misnaming of the files
				if (!$this->IsAllowedMimeType($mime, $mime_filters))
					return false;
			}

			$meta = &$this->getid3_cache->pick($legal_url, $this, false);
			assert($meta != null);

			$rv &= @unlink($file);
			$rv &= $meta->delete(true);

			unset($meta);
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
			$tnpath = $this->thumbnailCacheDir;
			if (FileManagerUtility::startswith($dir, $tnpath))
				return false;

			$tnparent = $this->thumbnailCacheParentDir;
			$just_below_thumbnail_dir = ($dir == $tnparent);

			$tndir = basename(substr($this->options['URLpath4thumbnails'], 0, -1));
		}

		$at_basedir = ($this->managedBaseDir == $dir);

		$flags = GLOB_NODOTS | GLOB_NOHIDDEN | GLOB_NOSORT;
		$flags &= $glob_flags_and;
		$flags |= $glob_flags_or;
		$coll = safe_glob($dir . $filemask, $flags);

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
		}

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
	 * directory tree rooted by options['URLpath4FileManagedDirTree']
	 *
	 * Note that the given filename will be converted to a legal filename, containing a filesystem-legal
	 * subset of ASCII characters only, before being used and returned by this function.
	 *
	 * @param mixed $fileinfo     either a string containing a filename+ext or an array as produced by pathinfo().
	 * @daram string $dir         path pointing at where the given file may exist.
	 *
	 * @return a filepath consisting of $dir and the cleaned up and possibly sequenced filename and file extension
	 *         as provided by $fileinfo, or NULL on error.
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
	 * 1) any $path with an 'extension' of '.directory' is assumed to be a directory.
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
		$url_path = $this->options['URLpath4assets'] . 'Images/Icons/' . $largeDir . $ext . '.png';
//log_message('error', 'getIcon : ' . ($url_path));
		$path = (is_file($this->url_path2file_path($url_path)))
			? $url_path
			: $this->options['URLpath4assets'] . 'Images/Icons/' . $largeDir . 'default.png';

		$this->icon_cache[!$smallIcon][$ext] = $path;

		return $path;
	}

	/**
	 * Return the path to the thumbnail of the specified image, the thumbnail having its
	 * width and height limited to $width pixels.
	 *
	 * When the thumbnail image does not exist yet, it will created on the fly.
	 *
	 * @param object $meta
	 *                             the cache record instance related to the original image. Is used
	 *                             to access the cache and generate a suitable thumbnail filename.
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
	public function getThumb($meta, $path, $width, $height, $onlyIfExistsInCache = false)
	{
		$thumbPath = $meta->getThumbPath($width . 'x' . $height);
		if (!is_file($thumbPath))
		{
			if ($onlyIfExistsInCache)
				return false;

			// make sure the cache subdirectory exists where we are going to store the thumbnail:
			$meta->mkCacheDir();

			$img = new Image($path);
			// generally save as lossy / lower-Q jpeg to reduce filesize, unless orig is PNG/GIF, higher quality for smaller thumbnails:
			$img->resize($width, $height)->save($thumbPath, min(98, max(MTFM_THUMBNAIL_JPEG_QUALITY, MTFM_THUMBNAIL_JPEG_QUALITY + 0.15 * (250 - min($width, $height)))), true);

			if (DEVELOPMENT)
			{
				$imginfo = $img->getMetaInfo();
				$meta->store('img_info', $imginfo);

				$meta->store('memory used', number_format(memory_get_peak_usage() / 1E6, 1) . ' MB');
				$meta->store('memory estimated', number_format(@$imginfo['fileinfo']['usage_guestimate'] / 1E6, 1) . ' MB');
				$meta->store('memory suggested', number_format(@$imginfo['fileinfo']['usage_min_advised'] / 1E6, 1) . ' MB');
			}

			unset($img);
		}
		return $meta->getThumbURL($width . 'x' . $height);
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
	 * By default, this is equivalent to $_SERVER['SCRIPT_NAME'].
	 *
	 * This default can be overridden by specifying the options['URIpath4RequestScript'] when invoking the constructor.
	 */
	public function getURIpath4RequestScript()
	{
		if (!empty($this->options['URIpath4RequestScript']))
		{
			return $this->options['URIpath4RequestScript'];
		}

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
	public function getRequestPath()
	{
		$path = self::getParentDir($this->getURIpath4RequestScript());
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
	public function normalize($path)
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
		// special fix: now strip trailing '/.' section; MUST replace by '/' (trailing) or path won't be accepted as legal when this is the '.' requested for root '/'
		$path = preg_replace('#/\.$#', '/', $path);

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
	 * Accept an absolute URI path, i.e. rooted against DocumentRoot, and transform it to a LEGAL URI absolute path, i.e. rooted against options['URLpath4FileManagedDirTree'].
	 *
	 * Relative paths are assumed to be relative to the current request path, i.e. the getRequestPath() produced path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw a FileManagerException
	 *
	 * Returns a fully normalized LEGAL URI path.
	 *
	 * Throws a FileManagerException when the given path cannot be converted to a LEGAL URL, i.e. when it resides outside the options['URLpath4FileManagedDirTree'] subtree.
	 */
	public function abs2legal_url_path($path)
	{
		$root = $this->options['URLpath4FileManagedDirTree'];

		$path = $this->rel2abs_url_path($path);

		// but we MUST make sure the path is still a LEGAL URI, i.e. sitting inside options['URLpath4FileManagedDirTree']:
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
	 * Accept a relative or absolute LEGAL URI path and transform it to an absolute URI path, i.e. rooted against DocumentRoot.
	 *
	 * Relative paths are assumed to be relative to the options['URLpath4FileManagedDirTree'] directory. This makes them equivalent to absolute paths within
	 * the LEGAL URI tree and this fact may seem odd. Alas, the FM frontend sends requests without the leading slash and it's those that
	 * we wish to resolve here, after all. So, yes, this deviates from the general principle applied elesewhere in the code. :-(
	 * Nevertheless, it's easier than scanning and tweaking the FM frontend everywhere.
	 *
	 * Note: as it uses normalize(), any illegal path will throw a FileManagerException
	 *
	 * Returns a fully normalized URI absolute path.
	 */
	public function legal2abs_url_path($path)
	{
		$path = $this->rel2abs_legal_url_path($path);

		$root = $this->options['URLpath4FileManagedDirTree'];

		// clip the trailing '/' off the $root path as $path has a leading '/' already:
		$path = substr($root, 0, -1) . $path;

		return $path;
	}

	/**
	 * Accept a relative or absolute LEGAL URI path and transform it to an absolute LEGAL URI path, i.e. rooted against options['URLpath4FileManagedDirTree'].
	 *
	 * Relative paths are assumed to be relative to the options['URLpath4FileManagedDirTree'] directory. This makes them equivalent to absolute paths within
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
		$path = strtr($path, '\\', '/');
		if (!FileManagerUtility::startsWith($path, '/'))
		{
			$path = '/' . $path;
		}

		$path = $this->normalize($path);

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
//log_message('error', 'url_path2file_path :' . $url_path);
//log_message('error', 'FileSystemPath4SiteDocumentRoot :' . $this->options['FileSystemPath4SiteDocumentRoot']);
		$path = $this->options['FileSystemPath4SiteDocumentRoot'] . $url_path;

		return $path;
	}

	/**
	 * Return the filesystem absolute path for the relative or absolute LEGAL URI path.
	 *
	 * Note: as it uses normalize(), any illegal path will throw an FileManagerException
	 *
	 * Returns a fully normalized filesystem absolute path.
	 */
	public function legal_url_path2file_path($url_path)
	{
		$path = $this->rel2abs_legal_url_path($url_path);

		$path = substr($this->managedBaseDir, 0, -1) . $path;

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
				$extra1 = (!empty($e[1]) ? $this->mkSafe4Display($e[1]) : '');
				$extra2 = (!empty($target_info) ? ' (' . $this->mkSafe4Display($target_info) . ')' : '');
				$jserr['error'] = $emsg = '${backend.' . $e[0] . '}';
				if ($e[0] != 'disabled')
				{
					// only append the extra data when it's NOT the 'disabled on this server' message!
					$jserr['error'] .=  $extra1 . $extra2;
				}
				else
				{
					$jserr['error'] .=  ' (${' . $extra1 . '})';
				}
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
			$mimes = parse_ini_file($this->options['FileSystemPath4mimeTypesMapFile']);

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
	 */
	public function getMimeFromExt($file)
	{
		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		$mime = null;
		if (MTFM_USE_FINFO_OPEN)
		{
			$ini = error_reporting(0);
			if (function_exists('finfo_open') && $f = finfo_open(FILEINFO_MIME, getenv('MAGIC')))
			{
				$mime = finfo_file($f, $file);
				// some systems also produce the character encoding with the mime type; strip if off:
				$ma = explode(';', $mime);
				$mime = $ma[0];
				finfo_close($f);
			}
			error_reporting($ini);
		}

		if ((empty($mime) || $mime === 'application/octet-stream') && strlen($ext) > 0)
		{
			$ext2mimetype_arr = $this->getMimeTypeDefinitions();

			if (array_key_exists($ext, $ext2mimetype_arr))
				$mime = $ext2mimetype_arr[$ext];
		}

		if (empty($mime))
		{
			$mime = 'application/octet-stream';
		}

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
	 * @param string $legal_url
	 *                           'legal URL path' to the file; used as the key to the corresponding
	 *                           cache storage record: getFileInfo() will cache the
	 *                           extracted info alongside the thumbnails in a cache file with
	 *                           '.nfo' extension.
	 *
	 * @return the info array as produced by getID3::analyze(), as part of a MTFMCacheEntry reference
	 */
	public function getFileInfo($file, $legal_url, $force_recheck = false)
	{
		// when hash exists in cache, return that one:
		$meta = &$this->getid3_cache->pick($legal_url, $this);
		assert($meta != null);
		$mime_check = $meta->fetch('mime_type');
		if (empty($mime_check) || $force_recheck)
		{
			// cache entry is not yet filled: we'll have to do the hard work now and store it.
			if (is_dir($file))
			{
				$meta->store('mime_type', 'text/directory', false);
				$meta->store('analysis', null, false);
			}
			else
			{
				$this->getid3->analyze($file);

				$rv = $this->getid3->info;
				if (empty($rv['mime_type']))
				{
					// guarantee to produce a mime type, at least!
					$meta->store('mime_type', $this->getMimeFromExt($file));     // guestimate mimetype when content sniffing didn't work
				}
				else
				{
					$meta->store('mime_type', $rv['mime_type']);
				}
				$meta->store('analysis', $rv);
			}
		}

		return $meta;
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


	public static function cleanUrl($str, $replace=array(), $delimiter='-')
	{
		setlocale(LC_ALL, 'en_US.UTF8');

		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
	
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_.|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-. '));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return $clean;
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
	public static function table_var_dump(&$variable, $wrap_in_td = false, $show_types = false, $level = 0)
	{
		$returnstring = '';
		if (is_array($variable))
		{
			$returnstring .= ($wrap_in_td ? '' : '');
			$returnstring .= '<ul class="dump_array dump_level_' . sprintf('%02u', $level) . '">';
			foreach ($variable as $key => &$value)
			{
				// Assign an extra class representing the (rounded) width in number of characters 'or more':
				// You can use this as a width approximation in pixels to style (very) wide items. It saves
				// a long run through all the nodes in JS, just to measure the actual width and correct any
				// overlap occurring in there.
				$keylen = strlen($key);
				$threshold = 10;
				$overlarge_key_class = '';
				while ($keylen >= $threshold)
				{
					$overlarge_key_class .= ' overlarger' . sprintf('%04d', $threshold);
					$threshold *= 1.6;
				}

				$returnstring .= '<li><span class="key' . $overlarge_key_class . '">' . $key . '</span>';
				$tstring = '';
				if ($show_types)
				{
					$tstring = '<span class="type">'.gettype($value);
					if (is_array($value))
					{
						$tstring .= '&nbsp;('.count($value).')';
					}
					elseif (is_string($value))
					{
						$tstring .= '&nbsp;('.strlen($value).')';
					}
					$tstring = '</span>';
				}

				switch ((string)$key)
				{
				case 'filesize':
					$returnstring .= '<span class="dump_seconds">' . $tstring . self::fmt_bytecount($value) . ($value >= 1024 ? ' (' . $value . ' bytes)' : '') . '</span></li>';
					continue 2;

				case 'playtime seconds':
					$returnstring .= '<span class="dump_seconds">' . $tstring . number_format($value, 1) . ' s</span></li>';
					continue 2;

				case 'compression ratio':
					$returnstring .= '<span class="dump_compression_ratio">' . $tstring . number_format($value * 100, 1) . '%</span></li>';
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
					$returnstring .= '<span class="dump_rate">' . $tstring . self::fmt_bytecount($value) . '/s</span></li>';
					continue 2;

				case 'bytes per minute':
					$returnstring .= '<span class="dump_rate">' . $tstring . self::fmt_bytecount($value) . '/min</span></li>';
					continue 2;
				}
				$returnstring .= FileManagerUtility::table_var_dump($value, true, $show_types, $level + 1) . '</li>';
			}
			$returnstring .= '</ul>';
			$returnstring .= ($wrap_in_td ? '' : '');
		}
		else if (is_bool($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_boolean">' : '').($variable ? 'TRUE' : 'FALSE').($wrap_in_td ? '</span>' : '');
		}
		else if (is_int($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_integer">' : '').$variable.($wrap_in_td ? '</span>' : '');
		}
		else if (is_float($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_double">' : '').$variable.($wrap_in_td ? '</span>' : '');
		}
		else if (is_object($variable) && isset($variable->id3_procsupport_obj))
		{
			if (isset($variable->metadata) && isset($variable->imagedata))
			{
				// an embedded image (MP3 et al)
				$returnstring .= ($wrap_in_td ? '<div class="dump_embedded_image">' : '');
				$returnstring .= '<table class="dump_image">';
				$returnstring .= '<tr><td><b>type</b></td><td>'.getid3_lib::ImageTypesLookup($variable->metadata[2]).'</td></tr>';
				$returnstring .= '<tr><td><b>width</b></td><td>'.number_format($variable->metadata[0]).' px</td></tr>';
				$returnstring .= '<tr><td><b>height</b></td><td>'.number_format($variable->metadata[1]).' px</td></tr>';
				$returnstring .= '<tr><td><b>size</b></td><td>'.number_format(strlen($variable->imagedata)).' bytes</td></tr></table>';
				$returnstring .= '<img src="data:'.$variable->metadata['mime'].';base64,'.base64_encode($variable->imagedata).'" width="'.$variable->metadata[0].'" height="'.$variable->metadata[1].'">';
				$returnstring .= ($wrap_in_td ? '</div>' : '');
			}
			else if (isset($variable->binarydata_mode))
			{
				$returnstring .= ($wrap_in_td ? '<span class="dump_binary_data">' : '');
				if ($variable->binarydata_mode == 'procd')
				{
					$returnstring .= '<i>' . self::table_var_dump($variable->binarydata, false, false, $level + 1) . '</i>';
				}
				else
				{
					$temp = unpack('H*', $variable->binarydata);
					$temp = str_split($temp[1], 8);
					$returnstring .= '<i>' . self::table_var_dump(implode(' ', $temp), false, false, $level + 1) . '</i>';
				}
				$returnstring .= ($wrap_in_td ? '</span>' : '');
			}
			else
			{
				$returnstring .= ($wrap_in_td ? '<span class="dump_object">' : '').print_r($variable, true).($wrap_in_td ? '</span>' : '');
			}
		}
		else if (is_object($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_object">' : '').print_r($variable, true).($wrap_in_td ? '</span>' : '');
		}
		else if (is_null($variable))
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_null">' : '').'(null)'.($wrap_in_td ? '</span>' : '');
		}
		else if (is_string($variable))
		{
			$variable = strtr($variable, "\x00", ' ');
			$varlen = strlen($variable);
			for ($i = 0; $i < $varlen; $i++)
			{
				$returnstring .= htmlentities($variable{$i}, ENT_QUOTES, 'UTF-8');
			}
			$returnstring = ($wrap_in_td ? '<span class="dump_string">' : '').nl2br($returnstring).($wrap_in_td ? '</span>' : '');
		}
		else
		{
			$returnstring .= ($wrap_in_td ? '<span class="dump_other">' : '').nl2br(htmlspecialchars(strtr($variable, "\x00", ' '))).($wrap_in_td ? '</span>' : '');
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

	public static function __set_state($arr)
	{
		$obj = new EmbeddedImageContainer($arr['metadata'], $arr['imagedata']);
		return $obj;
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

	public static function __set_state($arr)
	{
		$obj = new BinaryDataContainer($arr['binarydata'], $arr['binarydata_mode']);
		return $obj;
	}
}

