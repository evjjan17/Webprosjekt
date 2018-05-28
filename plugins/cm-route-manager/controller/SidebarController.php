<?php

namespace com\cminds\mapsroutesmanager\controller;

use com\cminds\mapsroutesmanager\helper\AdminNotice;

use com\cminds\mapsroutesmanager\widget\MenuWidget;

use com\cminds\mapsroutesmanager\helper\Sidebar;

class SidebarController extends Controller {
	
	
	protected static $actions = array(
		'admin_notices',
	);
	
	
	static function admin_notices() {
		$widgetId = MenuWidget::getWidgetId();
		$sidebars = Sidebar::getWidgetSidebars($widgetId);
		if (empty($sidebars)) {
			echo new AdminNotice(
				$id = AdminNotice::method2Id(__METHOD__) . '_menu_widget',
				$type = 'error',
				$msg = sprintf('You didn\'t add the menu widget to your sidebar. <a href="%s" class="button">Add menu widget</a>', esc_attr(admin_url('widgets.php'))),
				$dismiss = true
			);
		}
	}
	
	
}
