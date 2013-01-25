/*
Script: Language.tr.js
	MooTools Filemanager - Language Strings in Turkish

Translation:
	[Christoph Pojer](http://cpojer.net)
*/

Filemanager.Language.tr = {
	more: 'Detaylar',
	width: 'Genişlik:',
	height: 'Yükseklik:',

	ok: 'Tamam',
	open: 'Dosya Seç',
	upload: 'Yükle',
	create: 'Klasör Oluştur',
	createdir: 'Lütfen Bir Klasör Adı Belirtin:',
	cancel: 'İptal',
	error: 'Hata',

	information: 'Bilgi',
	type: 'Tip:',
	size: 'Boyut:',
	dir: 'Yol:',
	modified: 'Son düzenleme:',
	preview: 'Önizleme',
	close: 'Kapat',
	destroy: 'Sil',
	destroyfile: 'Dosyayı silmek istediğinizden eminmisiniz?',

	rename: 'Yeniden Adlandır',
	renamefile: 'Lütfen yeni bir dosya adı girin:',
	rn_mv_cp: 'Yeniden Adlandır/Taşı/Kopyala',

	download: 'İndir',
	nopreview: '<i>Önizleme mevcut değil</i>',

	title: 'Başlık:',
	artist: 'Artist:',
	album: 'Albüm:',
	length: 'Uzunluk:',
	bitrate: 'Kalite:',

	deselect: 'Kaldır',

	nodestroy: 'Serverda dosya silme engellenmiş.',

	toggle_side_boxes: 'Küçük Resim Görünümü',
	toggle_side_list: 'Liste Görünümü',
	show_dir_thumb_gallery: 'Önizleme panelinde doyaları küçük resim olarak görüntüle',
	drag_n_drop: 'Taşı & Bırak bu dizin için aktiv edildi',
	drag_n_drop_disabled: 'Taşı & Bırak bu dizin için geçici olarak devre dışı bırakıldı',
	goto_page: 'Sayfaya Git',

	'backend.disabled': 'Bu işlem serverda devre dışı bırakılmıştır.',
	'backend.authorized': 'Bu işlemi gerçekleştirmek için yeterli yetkiye sahip deilsiniz.',
	'backend.path': 'Belirtilen klasör mevcut değil. Site yöneticisine başvurun.',
	'backend.exists': 'Belirtilen yer mevcut. Lütfen site yöneticinizle iletişim kurun',
	'backend.mime': 'Belirtilen dosya türüne izin verilmiyor',
	'backend.extension': 'Yüklemeye çalıştığınız dosya bilinmiyor yada yasak bir dosya uzantısına sahip.',
	'backend.size': 'Yüklemek istediğiniz dosya boyutu sunucuda işlemek için çok büyük. Lütfen daha küçük boyutlara sahip bir dosya yüklemeyi deneyin.',
	
	'backend.partial': 'Yüklemeye çalıştığınız dosya teknik nedenlerden dolayı yüklenemedi, lütfen tekrar yüklemeyi deneyin.',
	'backend.nofile': 'Belirtilen dosya yok yada dosya mevcut değil.',
	'backend.default': 'Dosya yüklenirken sorun çıktı.',
	'backend.path_not_writable': 'Belirtilen yükleme klasöründe yazma izniniz yok.',
	'backend.filename_maybe_too_large': 'Dosya ismi yada yolu sunucu dosya sistemi için çok uzun. Daha kısa bir dosya adıyla yeniden deneyin.',
	'backend.fmt_not_allowed': 'Yükleme için bu dosya biçimi yada adında izin verilmiyor.',
	'backend.read_error': 'Belirtilen dosya okunamıyot / indirilemiyor.',
	'backend.unidentified_error': '(Sunucuda) Yönetim paneliyle iletişimde bilinmeyen bir hata oluştu.',

	'backend.nonewfile': 'Dosya için yeni bir isimle taşınacak veya kopyalanacak',
	'backend.corrupt_img': 'Bu dosya bir resim yada görüntülenebilir bir dosya değil: ', // path
	'backend.resize_inerr': 'Bu dosya bir iç hata nedeniyle yeniden boyutlandırılamadı.',
	'backend.copy_failed': 'Dosya yada Dizini kopyalama sırasında bir hata oluştu: ', // oldlocalpath : newlocalpath
	'backend.delete_cache_entries_failed': '(Küçük Resim, Meta Bilgisi) Ön bellekten silmeye çalışırken bir hata meydana geldi.',
	'backend.mkdir_failed': 'Dizin oluşturulurken bir hata meydana geldi: ', // path
	'backend.move_failed': 'Dosya yada klasör Taşınırken yada Yeniden adlandırılırken bir hata meydana geldi: ', // oldlocalpath : newlocalpath
	'backend.path_tampering': 'Yolun değiştiği tespit edildi.',
	'backend.realpath_failed': 'Verilen dosya varsayılan saklama yoluna çevrilemedi: ', // $path
	'backend.unlink_failed': 'Dosya veya klasörü silmeye çalışırken bir hata meydana geldi: ',  // path

	// Image.class.php:
	'backend.process_nofile': 'Görüntü işleme birimi üzerinde çalışmak için geçerli bir dosya konumu alamadı.',
	'backend.imagecreatetruecolor_failed': 'Görüntü işleme birimi başarısız : GD imagecreatetruecolor() başarısız.',
	'backend.imagealphablending_failed': 'Görüntü işleme birimi başarısız : image alpha blending gerçekleştirilemiyor.',
	'backend.imageallocalpha50pctgrey_failed': 'Görüntü işleme birimi başarısız : alfa kanalı ve % 50 arka plan için yer ayıramıyor.',
	'backend.imagecolorallocatealpha_failed': 'Görüntü işleme birimi başarısız : Bu renkli görüntü için alfa kanalı yer ayıramıyor..',
	'backend.imagerotate_failed': 'Görüntü işleme birimi başarısız : GD imagerotate() başarısız.',
	'backend.imagecopyresampled_failed': 'Görüntü işleme birimi başarısız : GD imagecopyresampled() başarısız.',
	'backend.imagecopy_failed': 'Görüntü işleme birimi başarısız : GD imagecopy() başarısız.',
	'backend.imageflip_failed': 'Görüntü işleme birimi başarısız : görüntü döndürülemedi.',
	'backend.imagejpeg_failed': 'Görüntü işleme birimi başarısız : GD imagejpeg() başarısız.',
	'backend.imagepng_failed': 'Görüntü işleme birimi başarısız : GD imagepng() başarısız.',
	'backend.imagegif_failed': 'Görüntü işleme birimi başarısız : GD imagegif() başarısız.',
	'backend.imagecreate_failed': 'Görüntü işleme birimi başarısız : GD imagecreate() başarısız.',
	'backend.cvt2truecolor_failed': 'True Color dönüşümü başarısız oldu. Görüntü çözünürlüğü: ', /* x * y */
	'backend.no_imageinfo': 'Bozuk görüntü ya da resim dosyası yok.',
	'backend.img_will_not_fit': 'Görüntü mevcut RAM ile uyuşmuyor; gereken asgari (tahmini): ', /* XXX MBytes */
	'backend.unsupported_imgfmt': 'Desteklenmeyen resim formatı: ',    /* jpeg/png/gif/... */
	
	/* FU */
	uploader: {
		unknown: 'Bilinmeyen hata',
		sizeLimitMin: 'Eklenemez "<em>${name}</em>" (${size}), Minimun dosya boyutu <strong>${size_min}</strong>!',
		sizeLimitMax: 'Eklenemez "<em>${name}</em>" (${size}), dosya boyutu limiti <strong>${size_max}</strong>!',
		mod_security: 'No response was given from the uploader, this may mean that "mod_security" is active on the server and one of the rules in mod_security has cancelled this request.  If you can not disable mod_security, you may need to use the NoFlash Uploader.'
	},
	
	flash: {
		hidden: 'Flash Medya yükleyiciyi etkinleştirmek için, internet tarayıcınızın açılır pencere engelleyicisini kapatın ve sayfayı yenileyin.',
		disabled: 'Flash Medya yükleyiciyi etkinleştirmek için, engellenen flash videoyu aktive edin ve sayfayı yenileyin.',
		flash: 'Dosya yükleyebilmek için <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">Adobe Flash</a> yüklemeniz gerekiyor.'
	},
	
	resizeImages: 'Büyük resimleri yüklerken yeniden boyutlandır',

	serialize: 'Galeriyi Kaydet',
	gallery: {
		text: 'Resim Açıklaması',
		save: 'Kaydet',
		remove: 'Galeriden kaldır',
		drag: 'Elementleri buraya taşıyıp bir galeri oluşturun...'
	}
};