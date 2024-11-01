<?php
/*
Plugin Name: WordPress Places
Plugin URI: http://travelllll.com/wordpress-places/
Description: Allows you to categorise posts with locations
Author: Travelllll.com
Version: 0.1
Author URI: http://travelllll.com
*/
$wpdb->taxonomymeta = "{$wpdb->prefix}taxonomymeta";

function wp_places_activation() {
	global $wpdb;
	$charset_collate = '';  
	if(!empty($wpdb->charset)) {
    	$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	}
    if(!empty($wpdb->collate)) {
    	$charset_collate .= " COLLATE $wpdb->collate";
    }
  
	$tables = $wpdb->get_results("show tables like '{$wpdb->prefix}taxonomymeta'");
	if(!count($tables)) {
    	$wpdb->query("CREATE TABLE {$wpdb->prefix}taxonomymeta (
			meta_id bigint(20) unsigned NOT NULL auto_increment,
			taxonomy_id bigint(20) unsigned NOT NULL default '0',
			meta_key varchar(255) default NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY taxonomy_id (taxonomy_id),
			KEY meta_key (meta_key)
    	) $charset_collate;");
	}
	wp_places_register_taxonomy();
	wp_places_register_settings();
	update_option('cat-width', '100');
	update_option('cat-height', '100');
	update_option('map-width', '100');
	update_option('map-height', '100');
    flush_rewrite_rules(); 
}

function wp_places_register_taxonomy() {
    register_taxonomy(
    	'places', 
    	'post',
    	array(
	    	'labels' 			=> array( 
		        'name' 							=> _x('Places', 'places'),
		        'singular_name' 				=> _x('Place', 'places'),
		        'search_items' 					=> _x('Search Places', 'places'),
		        'popular_items' 				=> _x('Popular Places', 'places'),
		        'all_items' 					=> _x('All Places', 'places'),
		        'parent_item' 					=> _x('Parent Place', 'places'),
		        'parent_item_colon' 			=> _x('Parent Place:', 'places'),
		        'edit_item' 					=> _x('Edit Place', 'places'),
		        'update_item' 					=> _x('Update Place', 'places'),
		        'add_new_item' 					=> _x('Add New Place', 'places'),
		        'new_item_name' 				=> _x('New Place Name', 'places'),
		        'separate_items_with_commas'	=> _x('Separate places with commas', 'places'),
		        'add_or_remove_items' 			=> _x('Add or remove places', 'places'),
		        'choose_from_most_used' 		=> _x('Choose from the most used places', 'places'),
		        'menu_name' 					=> _x('Places', 'place'),
		    ),
	        'public' 			=> true,
	        'show_in_nav_menus' => true,
	        'show_ui' 			=> true,
	        'show_tagcloud' 	=> true,
	        'hierarchical' 		=> true,
	        'rewrite' 			=> true,
        	'query_var' 		=> true
	    )
    );
}
 
function wp_places_meta_add($tag) {
	?>
	<div class="form-field">
		<label for="google-map"><?php _e('Latitude, Longitude') ?></label>
		<input name="google-map" id="google-map" type="text" value="" size="40" aria-required="true" />
		<p class="description"><?php _e('Latitude, Longitude for this place.'); ?></p>
	</div>
	<div class="form-field">
		<label for="image-url"><?php _e('Image URL') ?></label>
		<input name="image-url" id="image-url" type="text" value="" size="40" aria-required="true" />
		<p class="description"><?php _e('This image will be the thumbnail shown on the category page.'); ?></p>
	</div>
	<?php
} 	
 
function wp_places_meta_edit($tag) {
	?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="google-map"><?php _e('Latitude, Longitude') ?></label>
		</th>
		<td>
			<input name="google-map" id="google-map" type="text" value="<?=get_metadata('taxonomy', $tag->term_id, 'google-map', true);?>" size="40" aria-required="true" />
			<p class="description"><?php _e('Latitude, Longitude for this place.'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="image-url"><?php _e('Image URL'); ?></label>
		</th>
		<td>
			<input name="image-url" id="image-url" type="text" value="<?=get_metadata('taxonomy', $tag->term_id, 'image-url', true);?>" size="40" aria-required="true" />
			<p class="description"><?php _e('This image will be the thumbnail shown on the category page.'); ?></p>
		</td>
	</tr>
	<?php
}

function wp_places_created($term_id) {
	if(isset($_POST['google-map']) && !empty($_POST['google-map'])) {
		update_metadata('taxonomy', $term_id, 'google-map', $_POST['google-map']);
	}
	if(isset($_POST['image-url']) && !empty($_POST['image-url'])) {
		update_metadata('taxonomy', $term_id, 'image-url', $_POST['image-url']);
	}
}

function wp_places_init() {
	wp_places_register_taxonomy();
	if(get_option('places-apikey') != '') {
		wp_register_script(
			'google-maps', 
			'http://maps.google.com/maps?file=api&v=2&sensor=false&key='.get_option('places-apikey')
		);
		wp_register_script(
			'wp-places',
			WP_PLUGIN_URL.'/wp-places/res/wp-places.js'
		);
		wp_enqueue_script('google-maps');
		wp_enqueue_script('wp-places');
	}
}

function wp_places_admin_init() {
	wp_places_register_settings();
}

function wp_places_admin_menu() {
	add_options_page('WP Places', 'WP Places', 'manage_options', 'places-settings', 'wp_places_settings_page');
}

function wp_places_settings_page() {
	include 'settings.php';
}

function wp_places_register_settings() {
	register_setting('wp-places', 'places-apikey');
	register_setting('wp-places', 'autolinking');
	register_setting('wp-places', 'cat-width');
	register_setting('wp-places', 'cat-height');
	register_setting('wp-places', 'map-width');
	register_setting('wp-places', 'map-height');
}

if(is_admin()) {
	add_action('admin_init', 'wp_places_admin_init');
	add_action('places_add_form_fields', 'wp_places_meta_add', 10, 1);
	add_action('places_edit_form_fields', 'wp_places_meta_edit', 10, 1);
	add_action('created_places', 'wp_places_created', 10, 1);
	add_action('edited_places', 'wp_places_created', 10, 1);
	add_action('admin_menu', 'wp_places_admin_menu');
} else {
	add_filter('the_content', 'wp_places_autolinking');
}

add_action('init', 'wp_places_init');
register_activation_hook(__FILE__, 'wp_places_activation');
?>