<?php /*

/*
Plugin Name:  Advanced Drafts & Reviews Dashboard Widget
Plugin URI:   http://wordpress.org/extend/plugins/advanced-drafts-and-reviews-dashboard-widget/
Description:  Widget for your Dashboard. Depending your selection the Widget shows a list of pending drafts and/or reviews of posts (and pages). Other settings: show Author, Date (with custom settings) and how many pending entrys. With next update: exerpt and some user-rights for displaying.

Version:      0.9.8.5
Author:       WP-Henne
Author URI:   http://wordpress.org/extend/plugins/advanced-drafts-and-reviews-dashboard-widget/
Min WP Version: 2.7.*
Max WP Version: 3.7.*
*/

/*  Copyright 2013 WP-Henne  (email : wp@petzold.it)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('AvDashboard_DraftReview_DOMAIN', 'av-dashboard-widget-draft-review');
define('AvDashboard_DraftReview_OPTION_NAME', 'AvDashboardDraftReview');
define('AvDashboard_DraftReview_WidgetID', 'AvDashboardDraftReview');

function AvDashboardDraftReviewInitLanguageFiles() {
	// load language file
	if (function_exists('load_plugin_textdomain')) {
		if ( !defined('WP_PLUGIN_DIR') ) {
			load_plugin_textdomain(AvDashboard_DraftReview_DOMAIN, str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');
		} else {
			load_plugin_textdomain(AvDashboard_DraftReview_DOMAIN, false, dirname( plugin_basename(__FILE__) ) . '/languages');
		}
	}
}

/** Main Widget function */
function AvDashboardDraftReview_Main() {
	// Add the widget to the dashboard
	global $wpdb;
	$widget_options = AvDashboardDraftReview_Options();

	$request = "SELECT $wpdb->posts.*, display_name as name, $wpdb->posts.ID as post_ID FROM $wpdb->posts LEFT JOIN $wpdb->users ON $wpdb->posts.post_author=$wpdb->users.ID ";

        switch ($widget_options['showwhat']) {
	    case 'Draft':
		$what = "draft";
	        break;
	    case 'Review':
		$what = "pending";
	        break;
	    default:
		$what = "draft', 'pending";
        }

        switch ($widget_options['sortorder']) {
	    case 'Date':
		$sortby = "post_date";
	        break;
	    case 'ID':
		$sortby = "$wpdb->posts.ID";
	        break;
	    case 'AuthorDate':
		$sortby = "post_author, post_date";
	        break;
	    default:
		$sortby = "post_author, $wpdb->posts.ID";
        }

        switch ($widget_options['showpages']) {
	    case 1:
        	$request .= "WHERE post_status IN ('".$what."') AND post_type IN ('post') AND $wpdb->posts.ID NOT IN ('".$widget_options['itemexclude']."')";
	        break;
	    default:
       		$request .= "WHERE post_status IN ('".$what."') AND post_type IN ('post') AND $wpdb->posts.ID NOT IN ('".$widget_options['itemexclude']."')";
        }

	$request .= "ORDER BY ".$sortby." ".$widget_options['ascdesc']." LIMIT ".$widget_options['items_view_count'];
	$posts = $wpdb->get_results($request);

	if ( $posts ) {
		echo "<ul id='av-widget-draft-review-list'>\n";

		foreach ( $posts as $post ) {
			if (current_user_can( 'edit_post', $post->ID )) {
				$post_meta = sprintf('%s', '<a href="post.php?action=edit&amp;post=' . $post->ID . '">' . get_the_title($post->ID) . '</a> ' );
			} else {
				$post_meta = sprintf('%s', '<span style="text-decoration:underline">' . get_the_title() . '</span>' );
			}

			if($widget_options['showstatus']) {
				$post_meta.= sprintf('%s %s', __('(', AvDashboard_DraftReview_DOMAIN), '<i>'. $post->post_type .' '.$post->post_status.'</i> ) ' );
			}

			if($widget_options['showauthor']) {
				$user_role = '';
				$user_id = $post->post_author;
				$user = new WP_User( $user_id );
				if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
					foreach ( $user->roles as $role )
					$user_role.= $role ." ";
				} else {
					$user_role.= "couldn't find role";
				}
					$post_meta.= sprintf('%s %s', __('by', AvDashboard_DraftReview_DOMAIN),'<strong><abbr title="' . $user_role . '">' . $post->name . '</abbr></strong> ' );
			}

			if($widget_options['showtime']) {
				$time = get_post_time('G', true);

				if ( ( abs(time() - $time) ) < 86400 ) {
					$h_time = sprintf( __('%s ago', AvDashboard_DraftReview_DOMAIN), human_time_diff( $time ) );
				} else {
					$h_time = mysql2date(__('Y/m/d G:i'), $post->post_date);
				}

				$ltime =  get_the_modified_date('G', true);
				if ( ( abs(time() - $ltime) ) < 86400 ) {
					$l_time = sprintf( __('%s ago', AvDashboard_DraftReview_DOMAIN), human_time_diff( $ltime ) );
				} else {
					$l_time = mysql2date(__('Y/m/d G:i'), $post->post_modified);
				}
			
				$post_meta.= sprintf( __('&#8212; %s', AvDashboard_DraftReview_DOMAIN),'<abbr title="last modified: ' . date($widget_options['fhovertime'], strtotime($l_time)) . '">' . date($widget_options['formattime'], strtotime($h_time)) . '</abbr>' );

				}

			if($widget_options['showexcerpt']) {
				$excerpt_count = $widget_options['excerpt_count'] + 1 ;
				if ( $content = preg_split( '#\s#', strip_tags( $post->post_excerpt ), $excerpt_count, PREG_SPLIT_NO_EMPTY ) ) {
				        switch ($widget_options['fullpostexcerpt']) {
					    case 1:
						$item = '<p><i>Excerpt:</i> ' . join( ' ', array_slice( $content, 0, count( $content ) ) ) . ( $widget_options['excerpt_count'] < count( $content ) ? '&hellip;' : '' ) . '</p>';	
					        break;
					    default:
						$item = '<p><i>Excerpt:</i> ' . join( ' ', array_slice( $content, 0, $widget_options['excerpt_count'] ) ) . ( $widget_options['excerpt_count'] < count( $content ) ? '&hellip;' : '' ) . '</p>';	
        				}
				} else {
				if ( $content = preg_split( '#\s#', strip_tags( $post->post_content ), $excerpt_count, PREG_SPLIT_NO_EMPTY ) )

					$item = '<p><i>Post:</i> ' . join( ' ', array_slice( $content, 0, $widget_options['excerpt_count'] ) ) . ( $widget_options['excerpt_count'] < count( $content ) ? '&hellip;' : '' ) . '</p>';
				}

				$post_meta.= $item;
			}

			echo "<li class='post-meta'>" . $post_meta . "</li>";

		}

		echo "</ul>\n";
	} else {
		echo '<p>' . _e( "No entrys found.", AvDashboard_DraftReview_DOMAIN ) . "</p>\n";
	}

}

/**
 * Setup the widget.
 * - reads the saved options from the database
 */
function AvDashboardDraftReview_Setup() {
	$options = AvDashboardDraftReview_Options();

	if ( 'post' == strtolower($_SERVER['REQUEST_METHOD']) && isset( $_POST['widget_id'] ) && AvDashboard_DraftReview_WidgetID == $_POST['widget_id'] ) {
		foreach ( array( 'items_view_count', 'showpages', 'showwhat', 'showstatus', 'showtime', 'formattime', 'fhovertime', 'showauthor', 'showexcerpt', 'fullpostexcerpt', 'excerpt_count', 'sortorder', 'ascdesc', 'itemexclude' ) as $key )
		$options[$key] = $_POST[$key];
		update_option( AvDashboard_DraftReview_OPTION_NAME, $options );
	}

	?>

<p><label for="items_view_count"><?php _e('How many entrys would you like to display?', AvDashboard_DraftReview_DOMAIN ); ?>
	<select id="items_view_count" name="items_view_count">
	<?php for ( $i = 5; $i <= 20; $i = $i + 1 )
	echo "<option value='$i'" . ( $options['items_view_count'] == $i ? " selected='selected'" : '' ) . ">$i</option>";
	?> </select> </label></p>

<p><label for="showwhat"> 
	<?php _e('Show what?<br />', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="radio" id="showwhat1" name="showwhat" value="Draft"
	<?php if ($options['showwhat'] ==  'Draft') {echo 'checked="checked" ';} ?>/> Draft only <br />
	<input type="radio" id="showwhat2" name="showwhat" value="Review" 
	<?php if ($options['showwhat'] ==  'Review') {echo 'checked="checked" ';} ?>/> Review only <br />
	<input type="radio" id="showwhat3" name="showwhat" value="both"
	<?php if ($options['showwhat'] ==  'both') {echo 'checked="checked" ';} ?>/> both (Draft & Review)<br />
	</label></p>

<p><label for="showpages"> <input id="showpages" name="showpages"
	<input id="showpages" name="showpages" type="checkbox" value="1"
	<?php if ( $options['showpages'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show Pages?', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>

<p><label for="showauthor"> 
	<input id="showauthor" name="showauthor" type="checkbox" value="1"
	<?php if ( $options['showauthor'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show author?', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>

<p><label for="showstatus"> 
	<input id="showstatus" name="showstatus" type="checkbox" value="1"
	<?php if ( $options['showstatus'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show Status and Type (Post/Page)?', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>

<p><label for="showexcerpt"> 
	<input id="showexcerpt" name="showexcerpt" type="checkbox" value="1"
	<?php if ( $options['showexcerpt'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show exerpt?<br />If have no excerpt, first words of post/page will displayed.', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>

<p><label for="fullpostexcerpt"> 
	<input id="fullpostexcerpt" name="fullpostexcerpt" type="checkbox" value="1"
	<?php if ( $options['fullpostexcerpt'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show full exerpt of Post if any, but limit lengt of first words of post/page fix as well.', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>


<p><label for="excerpt_count"><?php _e('How many words would you like to display?', AvDashboard_DraftReview_DOMAIN ); ?>
	<select id="excerpt_count" name="excerpt_count">
	<?php for ( $i = 10; $i <= 20; $i = $i + 2 )
	echo "<option value='$i'" . ( $options['excerpt_count'] == $i ? " selected='selected'" : '' ) . ">$i</option>"; 
	?> </select> </label></p>

<p><label for="showtime"> <input id="showtime" name="showtime"
	<input id="showtime" name="showtime" type="checkbox" value="1"
	<?php if ( $options['showtime'] == 1 ) echo ' checked="checked"'; ?> />
	<?php _e('Show date?', AvDashboard_DraftReview_DOMAIN ); ?> </label></p>

<p><label for="formattime"> 
	<?php _e('Date format (see <a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">date and time formatting</a> )<br />', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="radio" id="formattime1" name="formattime1" onClick="document.getElementById('formattime3').value='<?php echo get_option( 'date_format' ); ?>'"
	<?php if ($options['formattime'] ==  get_option( 'date_format' )) {echo 'checked="checked" ';} ?>/> Date global <a href="options-general.php">WP-Setting</a> (
	<?php echo get_option( 'date_format' ) ?> )<br />
	<input type="radio" id="formattime2" name="formattime1" onClick="document.getElementById('formattime3').value='<?php echo $options['formattime']; ?>'"
	<?php if ($options['formattime'] !=  get_option( 'date_format' )) {echo 'checked="checked" ';} ?>/> custom 
	<input type="text" id="formattime3" name="formattime" value="<? echo $options['formattime']; ?>">
	</label></p>

<p><label for="fhovertime"> 
	<?php _e('Show Date hover as: ', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="text" name="fhovertime" value="<? echo $options['fhovertime']; ?>">
	</label></p>

<p><label for="sortorder"> 
	<?php _e('Sort by?<br />', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="radio" id="sortorder1" name="sortorder" value="Date" 
	<?php if ($options['sortorder'] ==  'Date') {echo 'checked="checked" ';} ?>/> Date<br />
	<input type="radio" id="sortorder2" name="sortorder" value="ID"
	<?php if ($options['sortorder'] ==  'ID') {echo 'checked="checked" ';} ?>/> ID (ignore Date, sort by created order)<br />
	<input type="radio" id="sortorder3" name="sortorder" value="AuthorDate"
	<?php if ($options['sortorder'] ==  'AuthorDate') {echo 'checked="checked" ';} ?>/> Author and then Date<br />
	<input type="radio" id="sortorder3" name="sortorder" value="AuthorID"
	<?php if ($options['sortorder'] ==  'AuthorID') {echo 'checked="checked" ';} ?>/> Author and then ID<br />
	</label></p>

<p><label for="ascdesc"> 
	<?php _e('Sort by?<br />', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="radio" id="ascdesc1" name="ascdesc" value="ASC"
	<?php if ($options['ascdesc'] ==  'ASC') {echo 'checked="checked" ';} ?>/> ASC<br />
	<input type="radio" id="ascdesc2" name="ascdesc" value="DESC"
	<?php if ($options['ascdesc'] ==  'DESC') {echo 'checked="checked" ';} ?>/> DESC <br />
	</label></p>

<p><label for="itemexclude"> 
	<?php _e('Exclude Items by ID: (List, separated by comma, post or page ID)', AvDashboard_DraftReview_DOMAIN ); ?> 
	<input type="text" name="itemexclude" value="<? echo $options['itemexclude']; ?>">
	</label></p>

	<?php
} //end function

/** Options */

/** Configuration Options of the widget */
function AvDashboardDraftReview_Options() {
	$defaults = array( 'items_view_count' => 5, 'showpages' => 1, 'showwhat' => 'Draft', 'showstatus' => 1, 'showtime' => 1, 'formattime' => 'D, j.M Y', 'fhovertime' => 'l, j. F Y G:i', 'showauthor' => 1, 'showexcerpt' => 0, 'fullpostexcerpt' => 0, 'excerpt_count'  => 10, 'sortorder' => 'Date', 'ascdesc' => 'DESC',  'itemexclude' => '');
	if ( ( !$options = get_option( AvDashboard_DraftReview_OPTION_NAME ) ) || !is_array($options) )
	$options = array();
	return array_merge( $defaults, $options );
}

/** initial the widget */
function AvDashboardDraftReview_Init() {
	wp_add_dashboard_widget( AvDashboard_DraftReview_WidgetID, __('Drafts & Pending Reviews', AvDashboard_DraftReview_DOMAIN), 'AvDashboardDraftReview_Main', 'AvDashboardDraftReview_Setup');
}


//*******************************************************************
// Start main
//*******************************************************************
{
	//Check WP Content Url
	// Pre-2.6 compatibility
	if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

	//Init the language files
	AvDashboardDraftReviewInitLanguageFiles();

	/** use hook, to integrate the widget */
	add_action('wp_dashboard_setup', 'AvDashboardDraftReview_Init');

} // end main


?>