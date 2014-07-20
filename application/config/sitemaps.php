<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| Engines pinged with a location of your sitemap
|--------------------------------------------------------------------------
| 
| Google is used by default, see link
| for a list of supported search engines:
| http://www.sitemapwriter.com/notify.php
| 
| Make sure "url" ends with the sitemap/url parameter,
| as the location of the sitemap will be appended.
|
*/
$config['sitemaps_search_engines'] = array (
   'http://www.google.com/webmasters/tools/ping?sitemap='
);

/*
|--------------------------------------------------------------------------
| Compress the finished sitemap using gzencode,
| and save it
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_gzip'] = false;
$config['sitemaps_gzip_path'] = '{file_name}.gz';

/*
|--------------------------------------------------------------------------
| Auto update sitemaps after each page or article change
| Can make page and article save process longer
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_auto_create'] = false;


/*
|--------------------------------------------------------------------------
| Same as the above two, but for sitemap indexes
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_index_gzip'] = true;
$config['sitemaps_index_gzip_path'] = '{file_name}.gz';

/*
|--------------------------------------------------------------------------
| Debugging mode and various errors
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_debug'] = false;
$config['sitemaps_filesize_error'] = true;
$config['sitemaps_log_http_responses'] = true;

/*
|--------------------------------------------------------------------------
| XML header and footer for sitemaps
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_header'] = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n" .
	"<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n\t" .
	"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n\t" .
	"xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n\t\t\t    " .
	"http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">";

$config['sitemaps_footer'] = "</urlset>\n";

/*
|--------------------------------------------------------------------------
| ...and indexes
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_index_header'] = "<\x3Fxml version=\"1.0\" encoding=\"UTF-8\"\x3F>\n" .
    "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
$config['sitemaps_index_footer'] = "</sitemapindex>\n";

/*
|--------------------------------------------------------------------------
| User agent that is sent during ping
|--------------------------------------------------------------------------
|
*/
$config['sitemaps_user_agent'] = "User-Agent: Mozilla/5.0 (compatible; " . PHP_OS . ") PHP/" . PHP_VERSION . "\r\n";


/* End of file sitemaps.php */
/* Location: ./application/config/sitemaps.php */