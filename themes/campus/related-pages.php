<?php
wp_reset_query();
$parent_id = $post->post_parent;

if ($parent_id == 0) {
	$child_of = $post->ID;
	$widget_title = the_title('','',false);
} // if no parent
else {
	$child_of = $parent_id;
	$widget_title = get_the_title($parent_id);
	$pagelink = get_page_link( $parent_id );
}

$children_pages = get_pages( array( 'child_of' => $child_of, 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );

if (count($children_pages) > 1) {
	echo '<div class="widget">';
	echo '<p class="title-widget title-s">';
	if (isset($pagelink)) {
		echo '<a href="'.$pagelink.'">'.$widget_title.'</a>';
	} else {
		echo $widget_title;
	}
	echo '</p>';
	echo '<ul class="academia-related-pages">';
	
	foreach ($children_pages as $page) {
		echo'<li class="academia-related-page';
		if ($page->ID == $post->ID) {echo ' current-page';}
		echo'"><a href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a></li>';
	} // foreach
	
	echo '</ul><div class="cleaner">&nbsp;</div></div>';
}

wp_reset_query();
?>