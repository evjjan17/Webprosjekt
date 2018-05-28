<?php

namespace com\cminds\mapsroutesmanager\controller;

use com\cminds\mapsroutesmanager\model\Attachment;

use com\cminds\mapsroutesmanager\model\Location;

use com\cminds\mapsroutesmanager\model\Route;

class AttachmentController extends Controller {
	
	static $ajax = array('cmmrm_get_image_id');
	
	static $actions = array(
		'cmmrm_route_editor_before_map' => array('args' => 1, 'priority' => 50),
		'cmmrm_route_after_save' => array('args' => 1),
		'cmmrm_route_editor_location_bottom' => array('args' => 1),
		'cmmrm_location_after_save' => array('args' => 2),
	);
	
	
	static function cmmrm_route_editor_before_map(Route $route) {
		echo self::loadFrontendView('editor-route-images', compact('route'));
	}
	
	
	static function cmmrm_route_editor_location_bottom(Route $route) {
		echo self::loadFrontendView('editor-location-images', compact('route'));
	}
	
	
	static function cmmrm_route_after_save(Route $route) {
		if (!empty($_POST['images'])) {
			$images = $_POST['images'];
		} else {
			$images = array();
		}
		$route->setImages($images);
	}
	
	
	static function cmmrm_location_after_save(Location $location, $i) {
		if( isset($_POST['locations']['images'][$i]) ) {
			$location->setImages($_POST['locations']['images'][$i]);
		} else {
			$location->setImages(array());
		}
		if( isset($_POST['locations']['icon'][$i]) ) {
			$location->setIcon($_POST['locations']['icon'][$i]);
		} else {
			$location->setIcon('');
		}
		if( isset($_POST['locations']['icon_size'][$i]) ) {
			$location->setIconSize($_POST['locations']['icon_size'][$i]);
		} else {
			$location->setIconSize(Location::ICON_SIZE_NORMAL);
		}
	}
	

	static function cmmrm_get_image_id() {
		$response = array('success' => 0, 'msg' => 'Error');
		if (!empty($_POST['url'])) {
			if (Attachment::isYouTubeUrl($_POST['url'])) { // YouTube
				$attachment = Attachment::createYouTube(0, $_POST['url']);
			} else {
				$url = $_POST['url'];
				$attachment = Attachment::getByUrl($url);
				if (empty($attachment)) {
					$url = preg_replace('~(\-[0-9]+x[0-9]+)(\.\w+)~', '$2', $url);
					$attachment = Attachment::getByUrl($url);
				}
			}
			
			if (!empty($attachment)) {
				$response = array(
					'success' => 1,
					'id' => $attachment->getId(),
					'url' => $attachment->isImage() ? $attachment->getImageUrl(Attachment::IMAGE_SIZE_FULL) : $attachment->getUrl(),
					'thumb' => $attachment->getImageUrl(Attachment::IMAGE_SIZE_THUMB),
				);
			} else {
				$response['msg'] = 'Attachment not found.';
			}
		}
	
		header('Content-type: application/json');
		echo json_encode($response);
		exit;
	
	}
	
	
}
