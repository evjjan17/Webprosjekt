<?php

namespace com\cminds\mapsroutesmanager\model;

use com\cminds\mapsroutesmanager\controller\ImportController;

use com\cminds\mapsroutesmanager\helper\KmlHelper;

use com\cminds\mapsroutesmanager\helper\GpxHelper;

use com\cminds\mapsroutesmanager\App;

class Attachment extends PostType {
	
	const POST_TYPE = 'attachment';
	
	const IMAGE_SIZE_THUMB = 'thumbnail';
	const IMAGE_SIZE_MEDIUM = 'medium';
	const IMAGE_SIZE_LARGE = 'large';
	const IMAGE_SIZE_FULL = 'full';
	
	const META_WP_ATTACHED_FILE = '_wp_attached_file';
	
	const UPLOAD_DIR = 'cm-maps-routes-manager';
	const UPLOAD_DIR_IMPORTS = 'imports';
	
	
	static function bootstrap() {
		parent::bootstrap();
		add_filter('wp_get_attachment_url', function($url, $postId) {
			if ($post = get_post($postId) AND $post->post_type == Attachment::POST_TYPE AND $post->post_mime_type == 'video/youtube') {
				$url = get_post_meta($postId, Attachment::META_WP_ATTACHED_FILE, true);
			}
			return $url;
		}, 10, 2);
	}
	

	/**
	 * Get instance
	 *
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\mapsroutesmanager\model\Attachment
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	
	static function registerPostType() {
		// do not register
	}
	
	
	static function getForPost($postId) {
		$posts = get_posts(array(
			'posts_per_page' => -1,
			'post_type' => Attachment::POST_TYPE,
			'post_status' => 'any',
			'post_parent' => $postId,
			'orderby' => 'menu_order',
			'order' => 'asc',
		));
		return array_filter(array_map(array(__CLASS__, 'getInstance'), $posts));
	}
	
	
	/**
	 * 
	 * @param unknown $url
	 * @return \com\cminds\mapsroutesmanager\model\Attachment
	 */
	static function getByUrl($url) {
		global $wpdb;
		if ($path = parse_url($url, PHP_URL_PATH)) {
			$dir = wp_upload_dir();
			$path = substr($path, strlen(parse_url($dir['baseurl'], PHP_URL_PATH))+1, 9999);
			$sql = $wpdb->prepare("SELECT p.* FROM $wpdb->postmeta m
				JOIN $wpdb->posts p ON p.ID = m.post_id
				WHERE m.meta_key = %s
				AND m.meta_value LIKE %s
				AND p.post_type = %s",
				self::META_WP_ATTACHED_FILE,
				$path,
				static::POST_TYPE
			);
			$post = $wpdb->get_row($sql);
			if ($post) {
				return static::getInstance($post);
			}
		}
	}
	
	
	function getImageInfo($size = self::IMAGE_SIZE_FULL, $icon = false) {
		if ($this->isImage()) {
			return wp_get_attachment_image_src($this->getId(), $size, $icon);
		}
	}
	
	
	function getImageUrl($size = self::IMAGE_SIZE_FULL, $icon = false) {
		if ($this->isImage()) {
			$result = wp_get_attachment_image_src($this->getId(), $size, $icon);
			if (!empty($result[0])) {
				return $result[0];
			}
		}
		else if ($this->isVideo()) {
			return $this->getYoutubeThumbUrl();
// 			return App::url('asset/img/play-video.png');
		}
	}
	
	
	function getYoutubeThumbUrl($number = 3) {
		return 'https://img.youtube.com/vi/'. $this->getYoutubeId() .'/3.jpg';
	}
	
	
	function getYoutubeId($number = 3) {
		$url = parse_url($this->getYoutubeUrl());
		if (in_array($url['host'], array('www.youtube.com', 'youtube.com')) AND !empty($url['query'])) {
			parse_str($url['query'], $query);
			if (isset($query['v'])) {
				return $query['v'];
			}
		}
		else if ($url['host'] == 'youtu.be' AND !empty($url['path'])) {
			return $url['path'];
		}
	}
	
	
	function getYoutubeUrl() {
		return $this->getPostMeta(static::META_WP_ATTACHED_FILE);
	}
	
	
	function getPostMetaKey($name) {
		return $name;
	}
	
	
	function getFilePath() {
		return get_attached_file($this->getId());
	}
	

	function getUrl() {
		return wp_get_attachment_url($this->getId());
	}
	
	
	function isImage() {
		return (strpos($this->post->post_mime_type, 'image') !== false);
	}
	
	function isVideo() {
		return (strpos($this->post->post_mime_type, 'video') !== false);
	}
	
	
	function isKml() {
		return ($this->post->post_mime_type == KmlHelper::MIME_TYPE);
	}
	
	
	function isKmz() {
		return ($this->post->post_mime_type == KmlHelper::KMZ_MIME_TYPE);
	}
	
	
	function isGpx() {
		return ($this->post->post_mime_type == GpxHelper::MIME_TYPE);
	}

	
	static function createYouTube($parentPostId, $url) {
		$attachment = new static(array(
			'post_parent' => $parentPostId,
			'post_author' => get_current_user_id(),
			'post_type' => static::POST_TYPE,
			'post_status' => 'inherit',
			'ping_status' => 'closed',
			'comment_status' => 'closed',
			'post_mime_type' => 'video/youtube',
		));
		$attachment->save();
		update_post_meta($attachment->getId(), static::META_WP_ATTACHED_FILE, $url);
		return $attachment;
	}
	
	
	static function isYouTubeUrl($url) {
		return preg_match('/https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//', $url);
	}
	
	
	static function keepOriginalImportFile(Route $route, $file) {
		
		$format = substr($file['name'], -3, 3);
		if (!in_array($format, ImportController::$supportedFormats)) {
			throw new \Exception('Format not supported.');
		}
		
		$fileName = static::sanitizeFileName($file['name']);
		$filePath = static::getUploadDir(self::UPLOAD_DIR_IMPORTS) . $fileName;
		if( move_uploaded_file($file['tmp_name'], $filePath) ) {
			return static::createOriginalImportFile($route, $filePath);
		} else {
			throw new \Exception('Failed to move uploaded file.');
		}
		
	}
	
	
	function delete() {
		return wp_delete_post($this->getId(), true);
	}
	
	
	static function createOriginalImportFile(Route $route, $filePath) {
		
		// Delete old file stored for this route
		if ($oldFile = $route->getOriginalImportFile()) {
			$oldFile->delete();
		}
		
		$format = substr($filePath, -3, 3);
		
		$attachment = array(
			'post_title' => static::sanitizeFileName(basename($filePath), false),
			'post_parent' => $route->getId(),
			'post_author' => $route->getAuthorId(),
			'post_type' => static::POST_TYPE,
			'post_status' => 'inherit',
			'ping_status' => 'closed',
			'comment_status' => 'closed',
			'post_mime_type' => ($format == 'gpx' ? GpxHelper::MIME_TYPE : KmlHelper::MIME_TYPE),
		);
		
		$attach_id = wp_insert_attachment($attachment, $filePath, $route->getId());
		if ($attach_id) {
			static::updateMetaData($attach_id, $filePath);
			return new static(get_post($attach_id));
		} else {
			throw new \Exception('Error when creating the attachment record.');
		}
		
	}
	
	
	public static function updateMetaData($postId, $filePath) {
		// you must first include the image.php file
		// for the function wp_generate_attachment_metadata() to work
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');
		$attach_data = wp_generate_attachment_metadata($postId, $filePath);
		return wp_update_attachment_metadata($postId, $attach_data);
	}
	
	
	
	public static function sanitizeFileName($title, $unique = true) {
		return ($unique ? floor(microtime(true)*1000) . '-' : '') . sanitize_file_name($title);
	}
	
	
	public static function getUploadDir($name) {
		$uploadDir = wp_upload_dir();
		if ($uploadDir['error']) {
			throw new \Exception(__('Error while getting wp_upload_dir():' . $uploadDir['error']));
		} else {
			$dir = $uploadDir['basedir'] . '/' . static::UPLOAD_DIR . '/' . $name . '/';
			if(!is_dir($dir)) {
				if(!wp_mkdir_p($dir)) {
					throw new \Exception(__('Script couldn\'t create the upload folder:' . $dir));
				}
			}
			return $dir;
		}
	}
	
	
	function getPostMimeType() {
		return $this->post_mime_type;
	}
	
	
	
}
