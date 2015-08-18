<?php
/*
Plugin Name: Laughing Squid Web Hosting News & Status WordPress Dashboard Widget
Plugin URI: https://www.laughingsquid.us
Description: The Laughing Squid Web Hosting News & Status WordPress Dashboard Widget provides status information within your WordPress dashboard pulled directly from the <a href="http://laughingsquidhosting.wordpress.com/">Laughing Squid Web Hosting News & Status blog</a>.
Version: 1.7
Author: Shelby DeNike
Author URI: http://www.sd3labs.com
*/

//Function to display the RSS feed.
function ls_rss_dashboard_widget_function() {
	$rss = fetch_feed( "http://laughingsquidhosting.wordpress.com/" );

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') ) {
			echo '<p>';
			printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
			echo '</p>';
		}
		return;
	}

	if ( !$rss->get_item_quantity() ) {
		echo '<p>There is currently no news available.</p>';
		$rss->__destruct();
		unset($rss);
		return;
	}

	echo "<ul>\n";

	if ( !isset($items) )
		$items = 5;

	foreach ( $rss->get_items(0, $items) as $item ) {
		$publisher = '';
		$site_link = '';
		$link = '';
		$content = '';
		$date = $item->get_date('F j, Y');
		$link = esc_url( strip_tags( $item->get_link() ) );
		$rss_title = $item->get_title();

		$content = $item->get_content();
		$content = wp_html_excerpt($content, 250) . ' ...';

		echo "\t<li><a href='$link'>$rss_title</a> - ($date)<br>$content</li><hr>\n";
	}

	echo "</ul>\n";
	$rss->__destruct();
	unset($rss);
};

//Function to add the rss feed to the dashboard.
function ls_rss_add_dashboard_widget() {
	wp_add_dashboard_widget('ls_rss_dashboard_widget', 'Laughing Squid Web Hosting Status', 'ls_rss_dashboard_widget_function');
}

//Action that calls the function that adds the widget to the dashboard.
add_action('wp_dashboard_setup', 'ls_rss_add_dashboard_widget');

# Custom Laughing Squid Menu
class LSHostingMenu {
	function LSHostingMenu() {
		add_action( 'admin_bar_menu', array( $this, "lshosting_links" ), 31 );
	}
	function add_root_menu($name, $id, $href = FALSE) {
		global $wp_admin_bar;
		if ( !is_super_admin() || !is_admin_bar_showing() )
		return;

		$wp_admin_bar->add_menu( array(
		'id'   => $id,
		'meta' => array(),
		'title' => $name,
		'href' => $href ) );
	}

	function add_sub_menu($name, $link, $root_menu, $id, $meta = FALSE) {
		global $wp_admin_bar;
		if ( ! is_super_admin() || ! is_admin_bar_showing() )
		return;

		$wp_admin_bar->add_menu( array(
		'parent' => $root_menu,
		'id' => $id,
		'title' => $name,
		'href' => $link,
		'meta' => $meta
		) );
	}

	function lshosting_links() {
		$this->add_root_menu( 'Laughing Squid', "lshostingl");
		$this->add_sub_menu( 'Support', "https://laughingsquid.zendesk.com", "lshostingl", "lshostingls" );
		$this->add_sub_menu( 'Billing', "https://laughingsquid.freshbooks.com", "lshostingl", "lshostinglb" );
		$this->add_sub_menu( 'Cloud Control Panel', "https://websitesettings.com", "lshostingl", "lshostinglc" );
		$this->add_sub_menu( 'Email Admin', "https://admin.emailsrvr.com", "lshostingl", "lshostingle" );
	}
}
add_action( "init", "LSHostingMenuInit" );
function LSHostingMenuInit() {
	global $LSHostingMenu;
	$LSHostingMenu = new LSHostingMenu();
}
?>