<?php
/**
 * Ionize
 *
 * @package		Ionize
 * @author		Ionize Dev Team
 * @license		http://doc.ionizecms.com/en/basic-infos/license-agreement
 * @link		http://ionizecms.com
 * @since		Version 1.0.6
 *
 */

/**
 * Media Controller
 */
class Media extends Base_Controller
{
    /** @var  Media_model */
    public $media_model;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        // Models
        $this->load->model(
            array(
                'media_model'
            ),
            '',
            TRUE
        );

        $this->load->library('medias');

    }

    public function index()
    {
        echo('');
        die();
    }

    /**
     * Download media with given ID
     *
     * @param   int     $id_media
     * @param   string  $hash       SHA-1 hash to verify a valid (intentionally public) download link
     */
    public function download($id_media, $hash)
    {
        $id_media = (int) $id_media;

        if( $hash !== sha1($id_media.config_item('encryption_key')) ) {
            show_error('Access Denied');
        }

        $media  = $id_media ? $this->media_model->get($id_media) : FALSE;
        $filePath = substr(BASEPATH, 0, -7) . $media['path'];

        if( $id_media === 0 || ! file_exists($filePath) ) {
            show_404();
        }

        header('Content-length: ' . filesize($filePath));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $media['file_name'] . '"');
        die( file_get_contents($filePath) );
    }

}