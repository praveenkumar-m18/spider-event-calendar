<?php
/*
Plugin Name: Spider Event Calendar
Plugin URI: https://web-dorado.com/products/wordpress-calendar.html
Description: Spider Event Calendar is a highly configurable product which allows you to have multiple organized events. Spider Event Calendar is an extraordinary user friendly extension.
Version: 1.5.57
Author: WebDorado
Author URI: https://web-dorado.com/wordpress-plugins-bundle.html
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
error_reporting(0);
$wd_spider_calendar_version="1.5.57";
// LANGUAGE localization.
function sp_calendar_language_load() {
  load_plugin_textdomain('sp_calendar', FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'sp_calendar_language_load');

add_action('init', 'sp_cal_registr_some_scripts');
	
function	sp_cal_registr_some_scripts(){
  global $wd_spider_calendar_version;
  wp_register_script("Canlendar_upcoming", plugins_url("elements/calendar.js", __FILE__), array(), $wd_spider_calendar_version);
  wp_register_script("calendnar-setup_upcoming", plugins_url("elements/calendar-setup.js", __FILE__), array(), $wd_spider_calendar_version);
  wp_register_script("calenndar_function_upcoming", plugins_url("elements/calendar_function.js", __FILE__), array(), $wd_spider_calendar_version);
  
  if( isset($_GET['page']) && $_GET['page'] == "Uninstall_sp_calendar" ) {
	wp_enqueue_script("sp_calendar-deactivate-popup", plugins_url('wd/assets/js/deactivate_popup.js', __FILE__), array(), $wd_spider_calendar_version);
	   $admin_data = wp_get_current_user();
	   
		wp_localize_script( 'sp_calendar-deactivate-popup', 'sp_calendarWDDeactivateVars', array(
			"prefix" => "sp_calendar" ,
			"deactivate_class" =>  'sp_calendar_deactivate_link',
			"email" => $admin_data->data->user_email,
			"plugin_wd_url" => "https://web-dorado.com/products/wordpress-calendar.html",
		));
	}
  
}

// Include widget.
require_once("widget_spider_calendar.php");
require_once("spidercalendar_upcoming_events_widget.php");
function current_page_url_sc() {
  if (is_home()) {
    $pageURL = site_url();
  }
  else {
    $pageURL = get_permalink();
  }
  return $pageURL;
}

function resolv_js_prob() {
  ?>
  <script>
    var xx_cal_xx = '&';
  </script>
  <?php
}
add_action('wp_head', 'resolv_js_prob');

function spider_calendar_scripts() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('thickbox', NULL, array('jquery'));
  wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', NULL, '1.0');
  wp_enqueue_style('thickbox');
}
add_action('wp_enqueue_scripts', 'spider_calendar_scripts');

$many_sp_calendar = 1;
function spider_calendar_big($atts) {
  if (!isset($atts['default'])) {
    $atts['theme'] = 30;
    $atts['default'] = 'month';
  }
  extract(shortcode_atts(array(
    'id' => 'no Spider catalog',
    'theme' => '30',
    'default' => 'month',
    'select' => 'month,list,day,week,',
  ), $atts));
  if (!isset($atts['select'])) {
    $atts['select'] = 'month,list,day,week,';
  }
  return spider_calendar_big_front_end($id, $theme, $default, $select);
}
add_shortcode('Spider_Calendar', 'spider_calendar_big');

function spider_calendar_big_front_end($id, $theme, $default, $select, $widget = 0) {
  require_once("front_end/frontend_functions.php");  
  ob_start();
  global $many_sp_calendar;
  global $wpdb;
  
  if ($widget === 1) {
$themes = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'spidercalendar_widget_theme WHERE id=%d', $theme));
}
else{
$themes = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'spidercalendar_theme WHERE id=%d', $theme));
}
  $cal_width = $themes->width; ?>
  <input type="hidden" id="cal_width<?php echo $many_sp_calendar ?>" value="<?php echo $cal_width ?>" /> 
  <div id='bigcalendar<?php echo $many_sp_calendar ?>' class="wdc_calendar"></div>
  <script> 
    var tb_pathToImage = "<?php echo plugins_url('images/loadingAnimation.gif', __FILE__) ?>";
    var tb_closeImage = "<?php echo plugins_url('images/tb-close.png', __FILE__) ?>"
	var randi;
    if (typeof showbigcalendar != 'function') {	
      function showbigcalendar(id, calendarlink, randi, widget) {
        var xmlHttp;
        try {
          xmlHttp = new XMLHttpRequest();// Firefox, Opera 8.0+, Safari
        }
        catch (e) {
          try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer
          }
          catch (e) {
            try {
              xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e) {
              alert("No AJAX!?");
              return false;
            }
          }
        }
        xmlHttp.onreadystatechange = function () {
          if (xmlHttp.readyState == 4) {
            // document.getElementById(id).innerHTML = xmlHttp.responseText;
            jQuery('#' + id).html(xmlHttp.responseText);
          }
        }
        xmlHttp.open("GET", calendarlink, false);
        xmlHttp.send();
	 jQuery(document).ready(function (){
	  jQuery('#views_select').toggle(function () {	
		jQuery('#drop_down_views').stop(true, true).delay(200).slideDown(500);
		jQuery('#views_select .arrow-down').addClass("show_arrow");
		jQuery('#views_select .arrow-right').removeClass("show_arrow");
	  }, function () { 
		jQuery('#drop_down_views').stop(true, true).slideUp(500);
		jQuery('#views_select .arrow-down').removeClass("show_arrow");
		jQuery('#views_select .arrow-right').addClass("show_arrow");		
	  });
	});
if(widget!=1)
{
	jQuery('drop_down_views').hide();
	var parent_width = document.getElementById('bigcalendar'+randi).parentNode.clientWidth;
	var calwidth=  document.getElementById('cal_width'+randi).value;
	var responsive_width = (calwidth)/parent_width*100;
	document.getElementById('bigcalendar'+randi).setAttribute('style','width:'+responsive_width+'%;');
	jQuery('pop_table').css('height','100%');
}
        var thickDims, tbWidth, tbHeight;
        jQuery(document).ready(function ($) {	
		if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) { jQuery('body').addClass('ios_device'); } 			
		 setInterval(function(){	
				if(jQuery("body").hasClass("modal-open")) jQuery("html").addClass("thickbox_open");
				else jQuery("html").removeClass("thickbox_open");
			},500);			
          thickDims = function () {		
             var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;
            if (tbWidth) {
              if (tbWidth < (W - 90)) w = tbWidth; else  w = W - 200;
            } else w = W - 200;
            if (tbHeight) {
              if (tbHeight < (H - 90)) h = tbHeight; else  h = H - 200;
            } else h = H - 200;			
            if (tbWindow.size()) {
              tbWindow.width(w).height(h);
              jQuery('#TB_iframeContent').width(w).height(h - 27);
              tbWindow.css({'margin-left':'-' + parseInt((w / 2), 10) + 'px'});
              if (typeof document.body.style.maxWidth != 'undefined')
                tbWindow.css({'top':(H - h) / 2, 'margin-top':'0'});
            }
			 if(jQuery(window).width() < 768 ){
			  var tb_left = parseInt((w / 2), 10) + 20;		  
				jQuery('#TB_window').css({"left": tb_left+ "px", "width": "90%", "margin-top": "-13%","height": "100%"})
				jQuery('#TB_window iframe').css({'height':'100%', 'width':'100%'});
			}
			 else jQuery('#TB_window').css('left','50%');
		if (typeof popup_width_from_src != "undefined") {
				popup_width_from_src=jQuery('.thickbox-previewbigcalendar'+randi).attr('href').indexOf('tbWidth=');
				str=jQuery('.thickbox-previewbigcalendar'+randi).attr('href').substr(popup_width_from_src+8,150)
				find_amp=str.indexOf('&');
				width_orig=str.substr(0,find_amp);				
				find_eq=str.indexOf('=');
				height_orig=str.substr(find_eq+1,5);
			jQuery('#TB_window').css({'max-width':width_orig+'px', 'max-height':height_orig+'px'});
			jQuery('#TB_window iframe').css('max-width',width_orig+'px');
			}	
          };
          thickDims();
          jQuery(window).resize(function () {
            thickDims();			
		  });		  
          jQuery('a.thickbox-preview' + id).click(function () {
            tb_click.call(this);
            var alink = jQuery(this).parents('.available-theme').find('.activatelink'), link = '', href = jQuery(this).attr('href'), url, text;
            var reg_with = new RegExp(xx_cal_xx + "tbWidth=[0-9]+");	
            if (tbWidth = href.match(reg_with))
              tbWidth = parseInt(tbWidth[0].replace(/[^0-9]+/g, ''), 10);
            else
              tbWidth = jQuery(window).width() - 90;			  
            var reg_heght = new RegExp(xx_cal_xx + "tbHeight=[0-9]+");
            if (tbHeight = href.match(reg_heght))
              tbHeight = parseInt(tbHeight[0].replace(/[^0-9]+/g, ''), 10);
            else
              tbHeight = jQuery(window).height() - 60;
            jQuery('#TB_ajaxWindowTitle').css({'float':'right'}).html(link);			
            thickDims();			
            return false;			
          });
		  
        });
      }
    }	
    document.onkeydown = function (evt) {
      evt = evt || window.event;
      if (evt.keyCode == 27) {
        document.getElementById('sbox-window').close();
      }
    };
    <?php global $wpdb;
    $calendarr = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar WHERE id='%d'", $id));
    $year = ($calendarr->def_year ? $calendarr->def_year : date("Y"));
    $month = ($calendarr->def_month ? $calendarr->def_month : date("m"));	
	
    $date = $year . '-' . $month;
    if ($default == 'day') {
      $date .= '-' . date('d');
    }
    if ($default == 'week') {
      $date .= '-' . date('d');
      $d = new DateTime($date);
      $weekday = $d->format('w');
      $diff = ($weekday == 0 ? 6 : $weekday - 1);
      if ($widget === 1) {
        $theme_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_widget_theme WHERE id='%d'", $theme));
      }
      else {
        $theme_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_theme WHERE id='%d'", $theme));
      }
      $weekstart = $theme_row->week_start_day;
      if ($weekstart == "su") {
        $diff = $diff + 1;
      }
      $d->modify("-$diff day");
      $d->modify("-1 day");
      $prev_date = $d->format('Y-m-d');
      $prev_month = add_0((int) substr($prev_date, 5, 2) - 1);
      $this_month = add_0((int) substr($prev_date, 5, 2));
      $next_month = add_0((int) substr($prev_date, 5, 2) + 1);
      if ($next_month == '13') {
        $next_month = '01';
      }
      if ($prev_month == '00') {
        $prev_month = '12';
      }
    }
    if ($widget === 1) {
      $default .= '_widget';
    }
    else {
    }
    ?> showbigcalendar('bigcalendar<?php echo $many_sp_calendar; ?>', '<?php echo add_query_arg(array(
      'action' => 'spiderbigcalendar_' . $default,
      'theme_id' => $theme,
      'calendar' => $id,
      'select' => $select,
      'date' => $date,
      'months' => (($default == 'week' || $default == 'week_widget') ? $prev_month . ',' . $this_month . ',' . $next_month : ''),
      'many_sp_calendar' => $many_sp_calendar,
      'widget' => $widget,
	  'rand' => $many_sp_calendar,
      ), admin_url('admin-ajax.php'));?>','<?php echo $many_sp_calendar; ?>','<?php echo $widget; ?>');</script>
	<style>
	#TB_window iframe{
		background: <?php echo '#'.str_replace('#','',$themes->show_event_bgcolor); ?>;
	}
	</style>
  <?php
  $many_sp_calendar++;
  $calendar = ob_get_contents();
  ob_end_clean();
  return $calendar;
}

function convert_time($calendar_format, $old_time){
	if($calendar_format==0){	
		if (strpos($old_time, 'AM') !== false || strpos($old_time, 'PM') !== false) {
			$row_time_12  = explode('-',$old_time);
			$row_time_24 = "";
			for($i=0; $i<count($row_time_12); $i++){
				$row_time_24 .= date("H:i", strtotime($row_time_12[$i])). "-";
			}
			if(substr($row_time_24, -1)=="-") $row_time = rtrim($row_time_24,'-'); 
		}
		else $row_time = $old_time; 
	}
	else{
		if (strpos($old_time, 'AM') !== false || strpos($old_time, 'PM') !== false) $row_time = $old_time;
		else{
			$row_time_12 = "";
			$row_time_24  = explode('-',$old_time);
			for($i=0; $i<count($row_time_24); $i++){
				$row_time_12 .= date("g:iA", strtotime($row_time_24[$i])). "-";
			}
			if(substr($row_time_12, -1)=="-") $row_time = rtrim($row_time_12,'-');
		} 
	}
	return $row_time;
}

// Quick edit.
add_action('wp_ajax_spidercalendarinlineedit', 'spider_calendar_quick_edit');
add_action('wp_ajax_spidercalendarinlineupdate', 'spider_calendar_quick_update');
add_action('wp_ajax_upcoming', 'upcoming_widget');
function spider_calendar_quick_update() {
  $current_user = wp_get_current_user();
  if ($current_user->roles[0] !== 'administrator') {
    echo 'You have no permission.';
    die();
  }
  global $wpdb;
  if (isset($_POST['calendar_id']) && isset($_POST['calendar_title']) && isset($_POST['us_12_format_sp_calendar']) && isset($_POST['default_year']) && isset($_POST['default_month'])) {
    $wpdb->update($wpdb->prefix . 'spidercalendar_calendar', array(
        'title' => esc_sql(esc_html(stripslashes($_POST['calendar_title']))),
        'time_format' => esc_sql(esc_html(stripslashes($_POST['us_12_format_sp_calendar']))),
        'def_year' => esc_sql(esc_html(stripslashes($_POST['default_year']))),
        'def_month' => esc_sql(esc_html(stripslashes($_POST['default_month']))),
      ), array('id' => esc_sql(esc_html(stripslashes($_POST['calendar_id'])))), array(
        '%s',
        '%d',
        '%s',
        '%s',
      ), array('%d'));
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar WHERE id='%d'", (int) $_POST['calendar_id']));
	$calendar_format = esc_sql(esc_html(stripslashes($_POST['us_12_format_sp_calendar'])));
	
	$events_list = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_event WHERE calendar='%d'", (int) $_POST['calendar_id']));
	
	for($i = 0; $i < count($events_list); $i++){
		if($events_list[$i]->time!=''){
			$wpdb->update($wpdb->prefix . 'spidercalendar_event', array(
			  'time' => convert_time($calendar_format, $events_list[$i]->time)
			), array('id' => $events_list[$i]->id), array(
			  '%s'
			)); 
		}
	}  
	?>
  <td><?php echo $row->id; ?></td>
  <td class="post-title page-title column-title">
    <a title="Manage Events" class="row-title" href="admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=<?php echo $row->id; ?>"><?php echo $row->title; ?></a>
    <div class="row-actions"> 
      <span class="inline hide-if-no-js">
        <a href="#" class="editinline" onclick="show_calendar_inline(<?php echo $row->id; ?>)" title="Edit This Calendar Inline">Quick&nbsp;Edit</a> | </span>
      <span class="trash">
        <a class="submitdelete" title="Delete This Calendar" href="javascript:confirmation('admin.php?page=SpiderCalendar&task=remove_calendar&id=<?php echo $row->id; ?>','<?php echo $row->title; ?>')">Delete</a></span>
    </div>
  </td>
  <td><a href="admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=<?php echo $row->id; ?>">Manage events</a></td>
  <td><a href="admin.php?page=SpiderCalendar&task=edit_calendar&id=<?php echo $row->id; ?>" title="Edit This Calendar">Edit</a></td>
  <td><a <?php if (!$row->published)
    echo 'style="color:#C00"'; ?>
    href="admin.php?page=SpiderCalendar&task=published&id=<?php echo $row->id; ?>"><?php if ($row->published)
    echo "Yes";
  else echo "No"; ?></a></td>
  <?php
    die();
  }
  else {
    die();
  }
}

function spider_calendar_quick_edit() {
  $current_user = wp_get_current_user();
  if ($current_user->roles[0] !== 'administrator') {
    echo 'You have no permission.';
    die();
  }
  global $wpdb;
  if (isset($_POST['calendar_id'])) {
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar WHERE id='%d'", (int) $_POST['calendar_id']));
    ?>
  <td colspan="4" class="colspanchange">
    <fieldset class="inline-edit-col-left">
      <div style="float:left; width:100% " class="inline-edit-col">
        <h4>Quick Edit</h4>
        <label for="calendar_title"><span style="width:160px !important" class="title">Title: </span></label>
        <span class="input-text-wrap">
          <input type="text" style="width:150px !important" id="calendar_title" name="calendar_title" value="<?php echo $row->title; ?>" class="ptitle" value=""/>
        </span>
        <label for="def_year"><span class="title alignleft" style="width:160px !important">Default Year: </span></label>
        <span>
          <input type="text" name="def_year" id="def_year" style="width:150px;" value="<?php echo $row->def_year ?>"/>
        </span>
        <label for="def_month"><span class="title alignleft" style="width:160px !important">Default Month: </span></label>
        <span>
          <select id="def_month" name="def_month" style="width:150px;">
            <?php
            $month_array = array(
              '' => 'Current',
              '01' => 'January',
              '02' => 'February',
              '03' => 'March',
              '04' => 'April',
              '05' => 'May',
              '06' => 'June',
              '07' => 'July',
              '08' => 'August',
              '09' => 'September',
              '10' => 'October',
              '11' => 'November',
              '12' => 'December',
            );
            foreach ($month_array as $key => $def_month) {
              ?>
              <option <?php echo (($row->def_month == $key) ? 'selected="selected"' : '');?> value="<?php echo $key;?>"><?php echo $def_month;?></option>
              <?php
            }
            ?>
          </select>
        </span>
        <label for="time_format0"><span class="title alignleft" style="width:160px !important">Use 12 hours time format: </span></label>
        <span>
          <input style="margin-top:5px" type="radio" class="alignleft" name="time_format" id="time_format0" value="0" <?php if ($row->time_format == 0) echo 'checked="checked"'; ?> />
          <em style="margin:4px 5px 0 0" class="alignleft"> No </em>
          <input style="margin-top:5px" class="alignleft" type="radio" name="time_format" id="time_format1" value="1" <?php if ($row->time_format == 1) echo 'checked="checked"'; ?> />
          <em style="margin:4px 5px 0 0" class="alignleft"> Yes </em>
        </span>
      </div>
    </fieldset>
    <p class="submit inline-edit-save">
      <a accesskey="c" href="#" title="Cancel" onclick="cancel_qiucik_edit(<?php echo $row->id; ?>)" class="button-secondary cancel alignleft">Cancel</a>
      <input type="hidden" id="_inline_edit" name="_inline_edit" value="d8393e8662">
      <a accesskey="s" href="#" title="Update" onclick="updae_inline_sp_calendar(<?php echo  "'" . $row->id . "'" ?>)" class="button-primary save alignright">Update</a>
      <input type="hidden" name="post_view" value="list">
      <input type="hidden" name="screen" value="edit-page">
      <span class="error" style="display:none"></span>
      <br class="clear">
    </p>
  </td>
  <?php
    die();
  }
  else {
    die();
  }
}

// Add editor new mce button.
add_filter('mce_external_plugins', "sp_calendar_register");
add_filter('mce_buttons', 'sp_calendar_add_button', 0);

// Function for add new button.
function sp_calendar_add_button($buttons) {
  array_push($buttons, "sp_calendar_mce");
  return $buttons;
}

// Function for registr new button.
function sp_calendar_register($plugin_array) {
  $url = plugins_url('js/editor_plugin.js', __FILE__);
  $plugin_array["sp_calendar_mce"] = $url;
  return $plugin_array;
}

function spider_calendar_ajax_func() {
  ?>
  <script>
    var spider_calendar_ajax = '<?php echo admin_url("admin-ajax.php"); ?>';
  </script>
  <?php
}
add_action('admin_head', 'spider_calendar_ajax_func');

// Function create in menu.
function sp_calendar_options_panel() {
  if( get_option( "sp_calendar_subscribe_done" ) == 1 ){
	add_menu_page('Theme page title', 'Calendar', 'manage_options', 'SpiderCalendar', 'Manage_Spider_Calendar', plugins_url("images/calendar_menu.png", __FILE__));
  }
  $page_calendar = add_submenu_page('SpiderCalendar', 'Calendars', 'Calendars', 'manage_options', 'SpiderCalendar', 'Manage_Spider_Calendar');
  $page_event_category = add_submenu_page('SpiderCalendar', 'Event Category', 'Event Category', 'manage_options', 'spider_calendar_event_category', 'Manage_Spider_Category_Calendar');
  $page_theme = add_submenu_page('SpiderCalendar', 'Calendar Parameters', 'Calendar Themes', 'manage_options', 'spider_calendar_themes', 'spider_calendar_params');
  $page_widget_theme = add_submenu_page('SpiderCalendar', 'Calendar Parameters', 'Widget Themes', 'manage_options', 'spider_widget_calendar_themes', 'spider_widget_calendar_params');
  add_submenu_page('SpiderCalendar', 'Export', 'Export', 'manage_options', 'calendar_export', 'calendar_export'); 
  add_submenu_page('SpiderCalendar', 'Get Pro', 'Get Pro', 'manage_options', 'Spider_calendar_Licensing', 'Spider_calendar_Licensing');
  $page_uninstall = add_submenu_page('SpiderCalendar', 'Uninstall  Spider Event Calendar', 'Uninstall  Spider Event Calendar', 'manage_options', 'Uninstall_sp_calendar', 'Uninstall_sp_calendar'); // uninstall Calendar
  add_action('admin_print_styles-' . $page_theme, 'spider_calendar_themes_admin_styles_scripts');
  add_action('admin_print_styles-' . $page_event_category, 'spider_calendar_event_category_admin_styles_scripts');
  add_action('admin_print_styles-' . $page_calendar, 'spider_calendar_admin_styles_scripts');
  add_action('admin_print_styles-' . $page_uninstall, 'spider_calendar_admin_styles_scripts');
  add_action('admin_print_styles-' . $page_widget_theme, 'spider_widget_calendar_themes_admin_styles_scripts');
}

function Spider_calendar_Licensing() {
	global $wpdb;
  ?>
  <div style="width:95%">
    <p>This plugin is the non-commercial version of the Spider Event Calendar. Use of the calendar is free.<br />
    The only limitation is the use of the themes. If you want to use one of the 11 standard themes or create a new one that
    satisfies the needs of your web site, you are required to purchase a license.<br />
    Purchasing a license will add 12 standard themes and give possibility to edit the themes of the Spider Event Calendar.
    </p>
    <br /><br />
    <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" class="button-primary" target="_blank">Purchase a License</a>
    <br /><br /><br />
    <p>After the purchasing the commercial version follow this steps:</p>
    <ol>
      <li>Deactivate Spider Event Calendar Plugin</li>
      <li>Delete Spider Event Calendar Plugin</li>
      <li>Install the downloaded commercial version of the plugin</li>
  </ol>
  </div>
  <?php
}

function spider_calendar_themes_admin_styles_scripts() {
  global $wd_spider_calendar_version;
  wp_enqueue_script("jquery");
  wp_enqueue_script("standart_themes", plugins_url('elements/theme_reset.js', __FILE__), array(), $wd_spider_calendar_version);
 wp_enqueue_script('wp-color-picker');
  wp_enqueue_style( 'wp-color-picker' );
  if (isset($_GET['task'])) {
    if ($_GET['task'] == 'edit_theme' || $_GET['task'] == 'add_theme' || $_GET['task'] == 'Apply') {
      wp_enqueue_style("parsetheme_css", plugins_url('style_for_cal/style_for_tables_cal.css', __FILE__), array(), $wd_spider_calendar_version);
    }
  }

}

function spider_widget_calendar_themes_admin_styles_scripts() {
  global $wd_spider_calendar_version;
  wp_enqueue_script("jquery");
  wp_enqueue_script("standart_themes", plugins_url('elements/theme_reset_widget.js', __FILE__), array(), $wd_spider_calendar_version);
    wp_enqueue_script('wp-color-picker');
  wp_enqueue_style( 'wp-color-picker' );
  if (isset($_GET['task'])) {
    if ($_GET['task'] == 'edit_theme' || $_GET['task'] == 'add_theme' || $_GET['task'] == 'Apply') {
      wp_enqueue_style("parsetheme_css", plugins_url('style_for_cal/style_for_tables_cal.css', __FILE__), array(), $wd_spider_calendar_version);
    }
  }
}

function spider_calendar_admin_styles_scripts() {
  global $wd_spider_calendar_version;
  wp_enqueue_script("Calendar", plugins_url("elements/calendar.js", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  wp_enqueue_script("calendar-setup", plugins_url("elements/calendar-setup.js", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  wp_enqueue_script("calendar_function", plugins_url("elements/calendar_function.js", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  wp_enqueue_style("spcalendar-jos", plugins_url("elements/calendar-jos.css", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  
  if( isset($_GET['page']) && $_GET['page'] == "Uninstall_sp_calendar" ) {
	 wp_enqueue_style("sp_calendar_deactivate-css", plugins_url("wd/assets/css/deactivate_popup.css", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  }
}

function spider_calendar_event_category_admin_styles_scripts(){
  global $wd_spider_calendar_version;
  wp_enqueue_script("Calendar", plugins_url("elements/calendar.js", __FILE__), array(), $wd_spider_calendar_version, FALSE);
  wp_enqueue_script("calendar-setup", plugins_url("elements/calendar-setup.js", __FILE__), array(), $wd_spider_calendar_version, FALSE);
    wp_enqueue_script('wp-color-picker');
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_style("spcalendar-jos", plugins_url("elements/calendar-jos.css", __FILE__), array(), $wd_spider_calendar_version, FALSE);  
  }

add_filter('admin_head', 'spide_ShowTinyMCE');
function spide_ShowTinyMCE() {
  $screen = get_current_screen();
  $screen_id = $screen->id;
  if($screen_id=="toplevel_page_SpiderCalendar" || $screen_id=="calendar_page_spider_calendar_event_category" || $screen_id=="calendar_page_spider_calendar_themes" || $screen_id=="calendar_page_spider_widget_calendar_themes" || $screen_id=="calendar_page_calendar_export"|| $screen_id=="calendar_page_Uninstall_sp_calendar" || $screen_id=="calendar_page_overview_sp_calendar") {
    // conditions here
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) {
      add_thickbox();
    }
    wp_print_scripts('media-upload');
    if (version_compare(get_bloginfo('version'), 3.3) < 0) {
      if (function_exists('wp_tiny_mce')) {
        wp_tiny_mce();
      }
    }
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
  }
}

// Add menu.
add_action( 'admin_menu', 'sp_calendar_options_panel' );
add_action( 'init', "wd_spcal_init" ); 


function wd_spcal_init(){

	if( !class_exists("DoradoWeb") ){
		require_once('wd/start.php');
	}
	
	global $sp_calendar_options;
	$sp_calendar_options =  array (
		"prefix" => "sp_calendar",
		"wd_plugin_id" => 29,
		"plugin_title" => "Spider Calendar", 
		"plugin_wordpress_slug" => "spider-event-calendar", 
		"plugin_dir" => plugins_url('/', __FILE__),
		"plugin_main_file" => __FILE__,
		"description" => __('This is the best WordPress event Calendar plugin available in WordPress Directory.', 'sp_calendar'), 

	   // from web-dorado.com
		"plugin_features" => array(
				0 => array(
					"title" => __("Responsive", "sp_calendar"),
					"description" => __("Spider Calendar plugin is fully responsive and mobile-ready.  Thus a beautiful display on all types of devices and screens is guaranteed.", "sp_calendar"),
				),
				1 => array(
					"title" => __("Unlimited Calendars & Events", "sp_calendar"),
					"description" => __("The calendar plugin allows you to create as many calendars as you want and add unlimited number of events in each calendar.  Customize the design of each calendar, create events for each calendar separately and show multiple calendars on one page.", "sp_calendar"),
				),
				2 => array(
					"title" => __("Event Categories", "sp_calendar"),
					"description" => __("You can assign categories to your events by adding titles, descriptions and category colors from the website admin panel. The plugin allows you customize the calendars to show events from all or just a few categories.", "sp_calendar"),
				), 
				3 => array(
					"title" => __("Themes", "sp_calendar"),
					"description" => __("Choose among 17 different calendar themes to make sure the calendar fits perfectly with your website design. Add your own style to the themes by customizing almost everything or easily create your own theme.", "sp_calendar"),
				),
				4 => array(
					"title" => __("Repeat Events", "sp_calendar"),
					"description" => __("If you have events in your calendar that occur regularly you can choose to use the recurring events option.  You can set the events to repeat daily, weekly, monthly, yearly on specific days of the week, specific days of the month or year.", "sp_calendar"),
				)			
		   ),
		   // user guide from web-dorado.com
		 "user_guide" => array(
			0 => array(
					"main_title" => __("Creating/Editing Calendars", "sp_calendar"),
					"url" => "https://web-dorado.com/wordpress-spider-calendar/creating-editing-calendar.html",
					"titles" => array()
				),
			1 => array(
				"main_title" => __("Creating/Editing Events", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/creating-editing-events.html",
				"titles" => array()
			),
			2 => array(
				"main_title" => __("Adding Event Category", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-event-category.html",
				"titles" => array()
			),
			3 => array(
				"main_title" => __("Adding Themes", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes.html",
				"titles" => array(
					array(
						"title" => __("General Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes/general-parameters.html",
					),
					array(
						"title" => __("Header Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes/header-parameters.html",
					),
					array(
						"title" => __("Body Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes/body-parameters.html",
					),
					array(
						"title" => __("Popup Window Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes/popup-window-parameters.html",
					),
					array(
						"title" => __("Other Views Parameters of the Wordpress Calendar", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-themes/other-views-parameters.html",
					),					
				)
			), 
			4 => array(
				"main_title" => __("Adding Themes for a widget view", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-widget-view-themes.html",
				"titles" => array(
					array(
						"title" => __("General Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-widget-view-themes/general-parameters.html",
					),
					array(
						"title" => __("Popup Window Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-widget-view-themes/popup-window-parameters.html",
					),
					array(
						"title" => __("Body Parameters", "sp_calendar"),
						"url" => "https://web-dorado.com/wordpress-spider-calendar/adding-widget-view-themes/body-parameters.html",
					),
				)
			), 
			5 => array(
				"main_title" => __("Publishing the Created Calendar in a Page or a Post", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/publishing-calendar.html",				
				"titles" => array()
			), 
			6 => array(
				"main_title" => __("Publishing the Created Calendar in the Widget", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/publishing-calendar-in-widget.html",				
				"titles" => array()
			), 
			7 => array(
				"main_title" => __("Publishing the Upcoming Events widget", "sp_calendar"),
				"url" => "https://web-dorado.com/wordpress-spider-calendar/publishing-upcoming-events-widget.html",				
				"titles" => array()
			),   
			
	   ), 
	   "video_youtube_id" => "wDrMRAjhgHk",  // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
	   "overview_welcome_image" => null,
	   "plugin_wd_url" => "https://web-dorado.com/products/wordpress-calendar.html", 
	   "plugin_wd_demo_link" => "http://wpdemo.web-dorado.com/spider-calendar/?_ga=1.67418517.523103993.1473155982", 
	   "plugin_wd_addons_link" => null, 
	   "plugin_wizard_link" => null, 
	   "after_subscribe" => admin_url('admin.php?page=overview_sp_calendar'), // this can be plagin overview page or set up page
	   "plugin_menu_title" => "Calendar", 
	   "plugin_menu_icon" => plugins_url('/images/calendar_menu.png', __FILE__) , 
	   "deactivate" => true, 
	   "subscribe" => true,
	   "custom_post" => "SpiderCalendar",  // if true => edit.php?post_type=contact
	   "menu_capability" => "manage_options",
       "menu_position" => null,
					   
	);
	dorado_web_init($sp_calendar_options);
}

require_once("functions_for_xml_and_ajax.php");
require_once("front_end/bigcalendarday.php");
require_once("front_end/bigcalendarlist.php");
require_once("front_end/bigcalendarweek.php");
require_once("front_end/bigcalendarmonth.php");
require_once("front_end/bigcalendarmonth_widget.php");
require_once("front_end/bigcalendarweek_widget.php");
require_once("front_end/bigcalendarlist_widget.php");
require_once("front_end/bigcalendarday_widget.php");

// Actions for popup and xmls.
add_action('wp_ajax_spiderbigcalendar_day', 'big_calendar_day');
add_action('wp_ajax_spiderbigcalendar_list', 'big_calendar_list');
add_action('wp_ajax_spiderbigcalendar_week', 'big_calendar_week');
add_action('wp_ajax_spiderbigcalendar_month', 'big_calendar_month');
add_action('wp_ajax_spiderbigcalendar_month_widget', 'big_calendar_month_widget');
add_action('wp_ajax_spiderbigcalendar_list_widget', 'big_calendar_list_widget');
add_action('wp_ajax_spiderbigcalendar_week_widget', 'big_calendar_week_widget');
add_action('wp_ajax_spiderbigcalendar_day_widget', 'big_calendar_day_widget');
add_action('wp_ajax_spidercalendarbig', 'spiderbigcalendar');
add_action('wp_ajax_spiderseemore', 'seemore');
add_action('wp_ajax_window', 'php_window');
// Ajax for users.
add_action('wp_ajax_nopriv_spiderbigcalendar_day', 'big_calendar_day');
add_action('wp_ajax_nopriv_spiderbigcalendar_list', 'big_calendar_list');
add_action('wp_ajax_nopriv_spiderbigcalendar_week', 'big_calendar_week');
add_action('wp_ajax_nopriv_spiderbigcalendar_month', 'big_calendar_month');
add_action('wp_ajax_nopriv_spiderbigcalendar_month_widget', 'big_calendar_month_widget');
add_action('wp_ajax_nopriv_spiderbigcalendar_list_widget', 'big_calendar_list_widget');
add_action('wp_ajax_nopriv_spiderbigcalendar_week_widget', 'big_calendar_week_widget');
add_action('wp_ajax_nopriv_spiderbigcalendar_day_widget', 'big_calendar_day_widget');
add_action('wp_ajax_nopriv_spidercalendarbig', 'spiderbigcalendar');
add_action('wp_ajax_nopriv_spiderseemore', 'seemore');
add_action('wp_ajax_nopriv_window', 'php_window');

// Add style head.
function add_button_style_calendar() {
  echo '<script>var wdplugin_url = "' . plugins_url('', __FILE__) . '";</script>';
}
add_action('admin_head', 'add_button_style_calendar');

function Manage_Spider_Calendar() {
  global $wpdb;
  if (!function_exists('print_html_nav')) {
    require_once("nav_function/nav_html_func.php");
  }
  require_once("calendar_functions.php"); // add functions for Spider_Video_Player
  require_once("calendar_functions.html.php"); // add functions for vive Spider_Video_Player
  if (isset($_GET["task"])) {
    $task = esc_html($_GET["task"]);
  }
  else {
    $task = "";
  }
  if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
  }
  else {
    $id = 0;
  }
  if (isset($_GET["calendar_id"])) {
    $calendar_id = (int) $_GET["calendar_id"];
  }
  else {
    $calendar_id = 0;
  }
  switch ($task) {
    case 'calendar':
      show_spider_calendar();
      break;
    case 'add_calendar':
      add_spider_calendar();
      break;
    case 'published';
	  $nonce_sp_cal = $_REQUEST['_wpnonce'];
	  if (! wp_verify_nonce($nonce_sp_cal, 'nonce_sp_cal') )
   	    die("Are you sure you want to do this?");
      spider_calendar_published($id);
      show_spider_calendar();
      break;
    case 'Save':
      if (!$id) {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_calendar(-1);
      }
      else {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_calendar($id);
      }
      show_spider_calendar();
      break;
    case 'Apply':
      if (!$id) {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_calendar(-1);
        $id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "spidercalendar_calendar");
      }
      else {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_calendar($id);
      }
      edit_spider_calendar($id);
      break;
    case 'edit_calendar':
      edit_spider_calendar($id);
      break;
    case 'remove_calendar':
	  check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
      remove_spider_calendar($id);
      show_spider_calendar();
      break;
    // Events.
    case 'show_manage_event':
      show_spider_event($calendar_id);
      break;
    case 'add_event':
      add_spider_event($calendar_id);
      break;
    case 'save_event':
      if ($id) {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_event($calendar_id, $id);
      }
      else {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_event($calendar_id, -1);
      }
      show_spider_event($calendar_id);
      break;
    case 'apply_event':
      if ($id) {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_event($calendar_id, $id);
      }
      else {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_event($calendar_id, -1);
        $id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "spidercalendar_event");
      }
      edit_spider_event($calendar_id, $id);
      break;
    case 'edit_event':
      edit_spider_event($calendar_id, $id);
      break;
    case 'remove_event':
	  $nonce_sp_cal = $_REQUEST['_wpnonce'];
	  if (! wp_verify_nonce($nonce_sp_cal, 'nonce_sp_cal') ) 
	    die("Are you sure you want to do this?");
      remove_spider_event($calendar_id, $id);
      show_spider_event($calendar_id);
      break;
	case 'copy_event':
	  $nonce_sp_cal = $_REQUEST['_wpnonce'];
	  if (! wp_verify_nonce($nonce_sp_cal, 'nonce_sp_cal') ) 
	    die("Are you sure you want to do this?");
      copy_spider_event($calendar_id, $id);
      show_spider_event($calendar_id);
      break;
    case 'published_event';
	  $nonce_sp_cal = $_REQUEST['_wpnonce'];
	  if (! wp_verify_nonce($nonce_sp_cal, 'nonce_sp_cal') )
   	    die("Are you sure you want to do this?");
      published_spider_event($calendar_id, $id);
      show_spider_event($calendar_id);
      break;
    default:
      show_spider_calendar();
      break;
  }
}

function Manage_Spider_Category_Calendar(){
	require_once("calendar_functions.html.php");
	require_once("calendar_functions.php");
if (!function_exists('print_html_nav')) {
    require_once("nav_function/nav_html_func.php");
  }

global $wpdb;
  if (isset($_GET["task"])) {
    $task = esc_html($_GET["task"]);
  }
  else {
    $task = "";
	show_event_cat();
	return;
  }
  if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
  }
  else {
    $id = 0;
  }

switch($task){
	case 'add_category':
		edit_event_category($id);
	break;

	case 'save_category_event':
	if(!$id){
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
		save_spider_category_event();
		$id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "spidercalendar_event_category");
		}
		else
		{
		check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
		apply_spider_category_event($id);
		}
		show_event_cat();
		break;
		
	case 'apply_event_category':
	 if (!$id) {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        save_spider_category_event();
        $id = $wpdb->get_var("SELECT MAX(id) FROM " . $wpdb->prefix . "spidercalendar_event_category");
      }
      else {
	    check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
        apply_spider_category_event($id);
      }
      edit_event_category($id);
		break;
		
	case 'edit_event_category':
		//apply_spider_category_event();
		edit_event_category($id);
		break;
		
	case 'remove_event_category':	
		check_admin_referer('nonce_sp_cal', 'nonce_sp_cal');
		remove_category_event($id);
		show_event_cat();
		break;
	case 'published':
		$nonce_sp_cal = $_REQUEST['_wpnonce'];
		if (! wp_verify_nonce($nonce_sp_cal, 'nonce_sp_cal') )
	      die("Are you sure you want to do this?");
		spider_category_published($id);
		show_event_cat();
		break;
	  }

}

function upcoming_widget(){
	require_once("calendar_functions.html.php");
	require_once("spidercalendar_upcoming_events_widget.php");
	require_once("calendar_functions.php");
	if (!function_exists('print_html_nav')) {
    require_once("nav_function/nav_html_func.php");
  }
 
	  global $wpdb;
 
  spider_upcoming();
}

function spider_widget_calendar_params() {
  wp_enqueue_script('media-upload');
  wp_admin_css('thickbox');
  if (!function_exists('print_html_nav')) {
    require_once("nav_function/nav_html_func.php");
  }
  require_once("widget_Themes_function.html.php");
  global $wpdb;
  if (isset($_GET["task"])) {
    $task = esc_html($_GET["task"]);
  }
  else {
    $task = "";
  }
  switch ($task) {
    case 'theme':
      html_show_theme_calendar_widget();
      break;
    default:
      html_show_theme_calendar_widget();
  }
}

// Themes.
function spider_calendar_params() {
  wp_enqueue_script('media-upload');
  wp_admin_css('thickbox');
  if (!function_exists('print_html_nav')) {
    require_once("nav_function/nav_html_func.php");
  }
  require_once("Themes_function.html.php"); // add functions for vive Spider_Video_Player
  global $wpdb;
  if (isset($_GET["task"])) {
    $task = esc_html($_GET["task"]);
  }
  else {
    $task = "";
  }
  switch ($task) {
    case 'theme':
      html_show_theme_calendar();
      break;
    default:
      html_show_theme_calendar();
  }
}

function Uninstall_sp_calendar() {
  global $wpdb,  $sp_calendar_options;
  if(!class_exists("DoradoWebConfig")){
		require_once("wd/config.php");
  }
  $config = new DoradoWebConfig();
  $config->set_options( $sp_calendar_options );
  $deactivate_reasons = new DoradoWebDeactivate($config);	
  $deactivate_reasons->submit_and_deactivate(); 
	
  $base_name = plugin_basename('Spider_Calendar');
  $base_page = 'admin.php?page=' . $base_name;
  $mode = (isset($_GET['mode']) ? trim($_GET['mode']) : '');
 
  ?>
	<?php upgrade_pro_sp(); ?>	
	<br />
	<div class="goodbye-text">
		Before uninstalling the plugin, please Contact our <a href="https://web-dorado.com/support/contact-us.html?source=spidercalendar" target= '_blank'>support team</a>. We'll do our best to help you out with your issue. We value each and every user and value what’s right for our users in everything we do.<br>
		However, if anyway you have made a decision to uninstall the plugin, please take a minute to <a href="https://web-dorado.com/support/contact-us.html?source=spidercalendar" target= '_blank'>Contact us</a> and tell what you didn't like for our plugins further improvement and development. Thank you !!!
	</div>	
  <?php
  if (!empty($_POST['do'])) {
    if ($_POST['do'] == "UNINSTALL Spider Event Calendar") {
      check_admin_referer('Spider_Calendar uninstall');
      
        echo '<form id="message" class="updated fade">';
        echo '<p>';
        echo "Table '" . $wpdb->prefix . "spidercalendar_event' has been deleted.";
		$wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercalendar_event");
        echo '<font style="color:#000;">';
        echo '</font><br />';
        echo '</p>';
		echo '<p>';
        echo "Table '" . $wpdb->prefix . "spidercalendar_event_category' has been deleted.";
		$wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercalendar_event_category");
        echo '<font style="color:#000;">';
        echo '</font><br />';
        echo '</p>';		
        echo '<p>';
        echo "Table '" . $wpdb->prefix . "spidercalendar_calendar' has been deleted.";
		$wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercalendar_calendar");
        echo '<font style="color:#000;">';
        echo '</font><br />';
        echo '</p>';
		 echo '<p>';
        echo "Table '" . $wpdb->prefix . "spidercalendar_theme' has been deleted.";
		$wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercalendar_theme");
        echo '<font style="color:#000;">';
        echo '</font><br />';
        echo '</p>';
        echo '<p>';
        echo "Table '" . $wpdb->prefix . "spidercalendar_widget_theme' has been deleted.";
        $wpdb->query("DROP TABLE " . $wpdb->prefix . "spidercalendar_widget_theme");
        echo '<font style="color:#000;">';
        echo '</font><br />';
        echo '</p>';
        echo '</form>';
		delete_option('sp_calendar_subscribe_done');

        $mode = 'end-UNINSTALL';      
    }
  }
    
  switch ($mode) {
    case 'end-UNINSTALL':
      echo '<div class="wrap">';
      echo '<h2>Uninstall Spider Event Calendar</h2>';
      echo '<p><strong><a href="#"  class="sp_calendar_deactivate_link" data-uninstall="1">Click Here</a> To Finish The Uninstallation And Spider Event Calendar Will Be Deactivated Automatically.</strong></p>';
      echo '</div>';
      break;
    // Main Page
    default:
      ?>
      <form method="post" id="uninstall_form"  action="<?php echo admin_url('admin.php?page=Uninstall_sp_calendar'); ?>">
        <?php wp_nonce_field('Spider_Calendar uninstall'); ?>
        <div class="wrap">
          <div id="icon-Spider_Calendar" class="icon32"><br/></div>

          <p>
            <?php echo 'Deactivating Spider Event Calendar plugin does not remove any data that may have been created. To completely remove this plugin, you can uninstall it here.'; ?>
          </p>

          <p style="color: red">
            <strong><?php echo'WARNING:'; ?></strong>
            <?php echo 'Once uninstalled, this cannot be undone. You should use a Database Backup plugin of WordPress to back up all the data first.'; ?>
          </p>

          <p style="color: red">
            <strong><?php echo 'The following WordPress Options/Tables will be DELETED:'; ?></strong><br/>
          </p>
          <table class="widefat">
            <thead>
            <tr>
              <th><?php echo 'WordPress Tables'; ?></th>
            </tr>
            </thead>

            <tr>
              <td valign="top">
                <ol>
                  <?php
                  echo '<li>' . $wpdb->prefix . 'spidercalendar_event</li>' . "\n";
				  echo '<li>' . $wpdb->prefix . 'spidercalendar_event_category</li>' . "\n";
                  echo '<li>' . $wpdb->prefix . 'spidercalendar_calendar</li>' . "\n";
				  echo '<li>' . $wpdb->prefix . 'spidercalendar_theme</li>' . "\n";
                  echo '<li>' . $wpdb->prefix . 'spidercalendar_widget_theme</li>' . "\n";
                  ?>
                </ol>
              </td>
            </tr>
          </table>
		  <script>
		  function uninstall(){
		  jQuery(document).ready(function() {
				  if(jQuery('#uninstall_yes').is(':checked')){
					var answer = confirm('<?php echo 'You Are About To Uninstall Spider Event Calendar From WordPress.\nThis Action Is Not Reversible.\n\n Choose [Cancel] To Stop, [OK] To Uninstall.'; ?>');
				
					if(answer)
						jQuery("#uninstall_form").submit();
					}
				  else
					alert('To uninstall please check the box above.');

			  });
		  }
		  </script>
          <p style="text-align: center;">
              <?php echo 'Do you really want to uninstall Spider Event Calendar?'; ?><br/><br/>
            <input type="checkbox" value="yes" id="uninstall_yes" />&nbsp;<?php echo 'Yes'; ?><br/><br/>
			  <input type="hidden" name="do" value="UNINSTALL Spider Event Calendar" />
            <input type="button" name="DODO" value="<?php echo 'UNINSTALL Spider Event Calendar'; ?>"
                   class="button-primary"
                   onclick="uninstall()"/>
          </p>
        </div>
      </form>
      <?php
	   
  }
}

add_action('init', 'spider_calendar_export');
function spider_calendar_export() {
     if (isset($_POST['export_spider_calendar']) && $_POST['export_spider_calendar'] == 'Export') {
        global $wpdb;
        $tmp_folder = get_temp_dir();        
        $select_spider_categories = "SELECT * from " . $wpdb->prefix . "spidercalendar_event_category";
        $spider_cats = $wpdb->get_results($select_spider_categories);
        $cat_columns = array(
            array('id', 'title', 'published', 'color', 'description')
        );
        if ($spider_cats) {
            foreach ($spider_cats as $cat) {
                $cat_columns[] = array(
                    $cat->id,
                    $cat->title,
                    $cat->published,
                    $cat->color,
                    $cat->description
                );
            }
        }
        $cat_handle = fopen($tmp_folder . '/sc_categories.csv', 'w+');
        foreach ($cat_columns as $ar) {
            if (fputcsv($cat_handle, $ar, ',') === FALSE) {
                break;
            }
        }
        @fclose($cat_handle);        
        $select_spider_calendars = "SELECT * from " . $wpdb->prefix . "spidercalendar_calendar";
        $spider_calendars = $wpdb->get_results($select_spider_calendars);
        $cal_columns = array(
            array('id', 'title', 'published')
        );
        if ($spider_calendars) {
            foreach ($spider_calendars as $cal) {
                $cal_columns[] = array(
                    $cal->id,
                    $cal->title,
                    $cal->published
                );
            }
        }
        $cal_handle = fopen($tmp_folder . '/sc_calendars.csv', 'w+');
        foreach ($cal_columns as $ar) {
            if (fputcsv($cal_handle, $ar, ',') === FALSE) {
                break;
            }
        }
        @fclose($cal_handle);        
        $select_spider_events = "SELECT * from " . $wpdb->prefix . "spidercalendar_event";
        $spider_events = $wpdb->get_results($select_spider_events);
        $events_columns = array(
            array('id', 'cal_id', 'start_date', 'end_date', 'title', 'cat_id',
                'time', 'text_for_date', 'userID', 'repeat_method', 'repeat', 'week',
                'month', 'month_type', 'monthly_list', 'month_week', 'year_month', 'published')
        );
        if ($spider_events) {
            foreach ($spider_events as $ev) {
                $events_columns[] = array(
                    $ev->id,
                    $ev->calendar,
                    $ev->date,
                    $ev->date_end,
                    $ev->title,
                    $ev->category,
                    $ev->time,
                    $ev->text_for_date,
                    $ev->userID,
                    $ev->repeat_method,
                    $ev->repeat,
                    $ev->week,
                    $ev->month,
                    $ev->month_type,
                    $ev->monthly_list,
                    $ev->month_week,
                    $ev->year_month,
                    $ev->published
                );
            }
        }
        $ev_handle = fopen($tmp_folder . '/sc_events.csv', 'w+');
        foreach ($events_columns as $ar) {
            if (fputcsv($ev_handle, $ar, ',') === FALSE) {
                break;
            }
        }
        @fclose($ev_handle);
        $files = array(
            'sc_categories.csv',
            'sc_calendars.csv',
            'sc_events.csv'
        );
        $zip = new ZipArchive();
        $tmp_file = tempnam('.', '');
        if ($zip->open($tmp_file, ZIPARCHIVE::CREATE) === TRUE) {
            foreach ($files as $file) {
                if (file_exists($tmp_folder . $file)) {
                    $zip->addFile($tmp_folder . $file, $file);
                }
            }
            $zip->close();
            header("Content-type: application/zip; charset=utf-8");
            header("Content-Disposition: attachment; filename=spider-event-calendar-export.zip");
            header("Content-length: " . filesize($tmp_file));
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($tmp_file);
        }
        foreach ($files as $file) {
            @unlink($tmp_folder . $file);
        }
    }
}


function upgrade_pro_sp($text = false){
    $page = isset($_GET["page"]) ? $_GET["page"] : "";
?>
    <div class="sp_calendar_upgrade wd-clear" >
        <div class="wd-left">
        <?php
            switch($page){
                case "SpiderCalendar":
                ?>
                    <div style="font-size: 14px;">
                        <?php _e("This section allows you to create calendars.","sp_calendar");?>
                        <a style="color: #5CAEBD; text-decoration: none;border-bottom: 1px dotted;" target="_blank" href="https://web-dorado.com/wordpress-spider-calendar/creating-editing-calendar.html"><?php _e("Read More in User Manual.","sp_calendar");?></a>
                    </div>
                <?php      
                break;
				case "spider_calendar_event_category":
                ?>
                    <div style="font-size: 14px;">
                        <?php _e("This section allows you to create event categories.","sp_calendar");?>
                        <a style="color: #5CAEBD; text-decoration: none;border-bottom: 1px dotted;" target="_blank" href="https://web-dorado.com/wordpress-spider-calendar/adding-event-category.html"><?php _e("Read More in User Manual.","sp_calendar");?></a>
                    </div>
                <?php      
                break;
                case "calendar_export":
                ?>
                    <div style="font-size: 14px;">
                        <?php _e("This section will allow exporting Spider Calendar data for further import to Event Calendar WD.","sp_calendar");?>
                        <a style="color: #5CAEBD; text-decoration: none;border-bottom: 1px dotted;" target="_blank" href="https://web-dorado.com/products/wordpress-event-calendar-wd.html"><?php _e("Read More in User Manual.","sp_calendar");?></a>
                    </div> 
                <?php      
                break; 
				case "Uninstall_sp_calendar":
                ?>
                    <div style="font-size: 14px;">
                        <div class="page-banner uninstall-banner">
							<div class="uninstall_icon">
							</div>
							<div class="logo-title">Uninstall Spider Calendar </div>
						</div>
                    </div> 
                <?php      
                break;               
            }
        ?>
        </div>
        <div class="wd-right"> 
            <div class="wd-table">
                <div class="wd-cell wd-cell-valign-middle">
                    <a href="https://wordpress.org/support/plugin/spider-event-calendar" target="_blank">
                        <img src="<?php echo plugins_url('images/i_support.png', __FILE__); ?>" >
                        <?php _e("Support Forum", "sp_calendar"); ?>
                    </a>
                </div>            
                <div class="wd-cell wd-cell-valign-middle">
                    <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank">
                    <?php _e("UPGRADE TO PAID VERSION", "sp_calendar"); ?>
                     </a> 
                </div>
            </div>     
                            
        </div>
    </div>
    <?php if($text){
    ?>
        <div class="wd-text-right wd-row" style="color: #15699F; font-size: 20px; margin-top:10px; padding:0px 15px;">
            <?php echo sprintf(__("This is FREE version, Customizing %s is available only in the PAID version.","sp_calendar"), $text);?>
        </div>
    <?php
    }

}

function calendar_export() {
    ?>
	<?php upgrade_pro_sp(); ?>
    <form method="post" style="font-size: 14px; font-weight: bold;">
        <input type='submit' value='Export' id="export_WD" name='export_spider_calendar' />
    </form>
	<style>
	#export_div{
		background: #fff;
		border: 1px solid #e5e5e5;
		-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04);
		box-shadow: 0 1px 1px rgba(0,0,0,.04);
		border-spacing: 0;
		width: 65%;
		clear: both;
		margin: 0;
		padding: 7px 7px 8px 10px;
		margin: 20px 0 10px 0;
	}

	#export_WD{
		font-size: 13px;
		padding: 7px 25px;
	}
	</style>
    <?php
}


if (!function_exists('spcal_bp_install_notice')) {

  if(get_option('wds_bk_notice_status')==='' || get_option('wds_bk_notice_status')==='1'){
	return;
  }

  function spcal_bp_script_style() {
    $screen = get_current_screen();
    $screen_id = $screen->id;
    if($screen_id!="toplevel_page_SpiderCalendar" && $screen_id!="calendar_page_spider_calendar_event_category" && $screen_id!="calendar_page_spider_calendar_themes" && $screen_id!="calendar_page_spider_widget_calendar_themes" && $screen_id!="calendar_page_calendar_export" && $screen_id!="calendar_page_Uninstall_sp_calendar" && $screen_id!="calendar_page_overview_sp_calendar"&& $screen_id!="calendar_page_Spider_calendar_Licensing") {
      return;
    }

    $spcal_bp_plugin_url = plugins_url('', __FILE__);
    wp_enqueue_script('spcal_bck_install', $spcal_bp_plugin_url . '/js/wd_bp_install.js', array('jquery'));
    wp_enqueue_style('spcal_bck_install', $spcal_bp_plugin_url . '/style_for_cal/wd_bp_install.css');
  }
  add_action('admin_enqueue_scripts', 'spcal_bp_script_style');

  /**
   * Show notice to install backup plugin
   */
  function spcal_bp_install_notice() {
	$screen = get_current_screen(); 
	$screen_id = $screen->id;
    if($screen_id!="toplevel_page_SpiderCalendar" && $screen_id!="calendar_page_spider_calendar_event_category" && $screen_id!="calendar_page_spider_calendar_themes" && $screen_id!="calendar_page_spider_widget_calendar_themes" && $screen_id!="calendar_page_calendar_export" && $screen_id!="calendar_page_Uninstall_sp_calendar" && $screen_id!="calendar_page_overview_sp_calendar"&& $screen_id!="calendar_page_Spider_calendar_Licensing") {
      return;
    }

    $spcal_bp_plugin_url = plugins_url('', __FILE__);
    $prefix = 'sp';
    $meta_value = get_option('wd_bk_notice_status');
    if ($meta_value === '' || $meta_value === false) {
      ob_start();
      ?>
      <div class="notice notice-info" id="wd_bp_notice_cont">
        <p>
          <img id="wd_bp_logo_notice" src="<?php echo $spcal_bp_plugin_url . '/images/backup-logo.png'; ?>">
          <?php _e("Spider Event Calendar advises: Install brand new FREE", $prefix) ?>
          <a href="https://wordpress.org/plugins/backup-wd/" title="<?php _e("More details", $prefix) ?>"
             target="_blank"><?php _e("Backup WD", $prefix) ?></a>
          <?php _e("plugin to keep your data and website safe.", $prefix) ?>
          <a class="button button-primary"
             href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=backup-wd'), 'install-plugin_backup-wd')); ?>">
            <span onclick="wd_bp_notice_install()"><?php _e("Install", $prefix); ?></span>
          </a>
        </p>
        <button type="button" class="wd_bp_notice_dissmiss notice-dismiss"><span class="screen-reader-text"></span>
        </button>
      </div>
      <script>spcal_bp_url = '<?php echo add_query_arg(array('action' => 'wd_bp_dismiss',), admin_url('admin-ajax.php')); ?>'</script>
      <?php
      echo ob_get_clean();
    }
  }

  if (!is_dir(plugin_dir_path(dirname(__FILE__)) . 'backup-wd')) {
    add_action('admin_notices', 'spcal_bp_install_notice');
  }

  /**
   * Add usermeta to db
   *
   * empty: notice,
   * 1    : never show again
   */
  function spcal_bp_install_notice_status() {
    update_option('wd_bk_notice_status', '1', 'no');
  }
  add_action('wp_ajax_wd_bp_dismiss', 'spcal_bp_install_notice_status');
}


add_filter("plugin_row_meta", 'spidercal_add_plugin_meta_links', 10, 2);

function spidercal_add_plugin_meta_links($meta_fields, $file){

  if(plugin_basename(__FILE__) == $file) {

    $meta_fields[] = "<a href='https://wordpress.org/support/plugin/spider-event-calendar/' target='_blank'>Support Forum</a>";
    $meta_fields[] = "<a href='https://wordpress.org/support/plugin/spider-event-calendar/reviews#new-post' target='_blank' title='Rate'>
            <i class='spidercal-rate-stars'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";

    $stars_color = "#ffb900";

    echo "<style>"
      . ".spidercal-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
      . ".spidercal-rate-stars svg{fill:" . $stars_color . ";}"
      . ".spidercal-rate-stars svg:hover{fill:" . $stars_color . "}"
      . ".spidercal-rate-stars svg:hover ~ svg{fill:none;}"
      . "</style>";
  }

  return $meta_fields;
}

function spidercal_activate($networkwide){
	if (function_exists('is_multisite') && is_multisite()) {
		// Check if it is a network activation - if so, run the activation function for each blog id.
		if ($networkwide) {
			global $wpdb;
			// Get all blog ids.
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				SpiderCalendar_activate();
				restore_current_blog();
			}
			return;
		}
	}
	SpiderCalendar_activate();
}

// Activate plugin.
function SpiderCalendar_activate() {
  global $wpdb;
  /*$spider_event_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercalendar_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar` int(11) NOT NULL,
  `date` date NOT NULL,
  `date_end` date NOT NULL,
  `title` text NOT NULL,
  `time` varchar(20) NOT NULL,
  `text_for_date` longtext NOT NULL,
  `userID` varchar(255) NOT NULL,
  `repeat_method` varchar(255) NOT NULL,
  `repeat` varchar(255) NOT NULL,
  `week` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `month_type` varchar(255) NOT NULL,
  `monthly_list` varchar(255) NOT NULL,
  `month_week` varchar(255) NOT NULL,
  `year_month` varchar(255) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";*/

   $spider_event_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercalendar_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar` int(11) NOT NULL,
  `date` date NOT NULL,
  `date_end` date NOT NULL,
  `title` text NOT NULL,
  `category` int(11) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `partner` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `channel` text NOT NULL,
  `proposition` text NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `audience` text NOT NULL,
  `current_state` text NOT NULL,
  `activation` text NOT NULL,
  `key_message` text NOT NULL,
  `ideal_voice` varchar(255) NOT NULL,
  `analytics` text NOT NULL,
  `budget` varchar(255) NOT NULL,
  `effort` varchar(255) NOT NULL,
  `final_attachment` text NOT NULL,
  `time` varchar(20) NOT NULL,
  `text_for_date` longtext NOT NULL,
  `userID` varchar(255) NOT NULL,
  `repeat_method` varchar(255) NOT NULL,
  `repeat` varchar(255) NOT NULL,
  `week` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `month_type` varchar(255) NOT NULL,
  `monthly_list` varchar(255) NOT NULL,
  `month_week` varchar(255) NOT NULL,
  `year_month` varchar(255) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
  $spider_calendar_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercalendar_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `gid` varchar(255) NOT NULL,
  `def_zone` varchar(255) NOT NULL,
  `time_format` tinyint(1) NOT NULL,
  `allow_publish` varchar(255) NOT NULL,
  `start_month` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
$spider_category_event_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercalendar_event_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `color` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
$spider_event_content_piece_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "spidercalendar_custom_content_piece` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `tactic_name` varchar(255) NOT NULL,
  `publish_date` date NOT NULL,
  `content_channel` varchar(255) NOT NULL,
  `cta` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `content_attachment` varchar(255) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
  $wpdb->query($spider_event_table);
  $wpdb->query($spider_calendar_table);
  $wpdb->query($spider_category_event_table);
  $wpdb->query($spider_event_content_piece_table);
  require_once "spider_calendar_update.php";
  spider_calendar_chech_update();
}
register_activation_hook(__FILE__, 'spidercal_activate');

//Add fron-end form
function elegance_referal_init()
{
	if(is_page('manage-campaign')){	
		add_filter('the_content', 'func_content');
	}
	
}

function func_content(){
 global $wpdb;
 //require_once $base_dir.'Classes/PHPExcel.php';
 require_once $base_dir.'Classes/PHPExcel/IOFactory.php';
 $query25 = $wpdb->get_results("SELECT title,id FROM " . $wpdb->prefix . "spidercalendar_event_category");


  //Add/Edit Variables
	 $camp_name = htmlentities($_POST['campaign_name']);
	 $owner = htmlentities($_POST['owner']);
	 $partner = htmlentities($_POST['partner']);
	 $start = $_POST['start_date'];
	 $start_date = date('Y-m-d', strtotime($start));
	 $end = $_POST['end_date'];
	 $end_date = date('Y-m-d', strtotime($end));
	 $category = $_POST['category'];
	 $product_summary = htmlentities($_POST['product_summary']);
	 $product_channel = htmlentities($_POST['product_channel']);
	 $bus_prposition = htmlentities($_POST['bus_prposition']);
	 
	 $audi_curr_state = htmlentities($_POST['audi_curr_state']);
	 $audi_activation = htmlentities($_POST['audi_activation']);
	 $key_messages = htmlentities($_POST['key_messages']);
	 $ideal_voice = htmlentities($_POST['ideal_voice']);
	 $analytics = htmlentities($_POST['analytics']);
	 $budget = htmlentities($_POST['budget']);
	 $effort = htmlentities($_POST['effort']);
	 //print_r($_POST['owner']);
	
	//Content piece variables

	$tactic_name = $_POST['tactic'];
	$publish = $_POST['publish'];
	$cta = $_POST['cta'];
	$url = $_POST['url'];

	$file_attach = $_FILES['attachment'];
	$file_attach_name = $file_attach['name'];
	$file_attach_type = $file_attach ['type'];
	$file_attach_size = $file_attach ['size'];
	$file_attach_path = $file_attach ['tmp_name'];	
  
 if($_POST['submit'])
 {
	  
     $import_tactics = $_FILES['import_tactics'];
	 $base = dirname(__FILE__);
	 $file = $_FILES['business_attachment'];
	 $file_name = $file['name'];
	 $file_type = $file ['type'];
	 $file_size = $file ['size'];
	 $file_path = $file ['tmp_name'];
	 
	 if(count($_FILES['upload']['name']) > 0)
	 {
	    for($i=0; $i<count($_FILES['upload']['name']); $i++) 
		  {
			 $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			 if($tmpFilePath != "")
			 {
				$shortname = $_FILES['upload']['name'][$i];
                $filePath = $base."/attachments/final_docs/" .$_FILES['upload']['name'][$i];
                if(move_uploaded_file($tmpFilePath, $filePath)) 
				{
                    $files[] = $shortname;
                } 
			 }				 
		  }
		  
			//$prefix = $fileList = '';
			foreach ($files as $files_new)
			{
			$fileList .= $files_new.',';
			
			}
	 }
	 
	 if($file_name!="" && $file_size <= 614400)
	 { 
	  $upload = move_uploaded_file ($file_path,$base.'/attachments/'.$file_name);
	 }	 
	 $audience = $_POST['audience'];
	 $chk=""; 
	 foreach($audience as $chk1) 
	 { 
	 $chk.= $chk1.","; 
	 } 
     $tablename=$wpdb->prefix.'spidercalendar_event';
	 $sql = "INSERT INTO ".$tablename."(`calendar`,`date`,`date_end`,`title`,`category`,`owner`,`partner`,`summary`,`channel`,`proposition`, 
	   `attachment`,`audience`,`current_state`,`activation`,`key_message`,`ideal_voice`,`analytics`,`budget`,`effort`,`final_attachment`,`time`,`text_for_date`,`userID`,
	   	`repeat_method`,`repeat`,`week`,`month`,`month_type`,`monthly_list`,`month_week`,`year_month`,`published`) VALUES 
	   (1,'$start_date','$end_date','$camp_name','$category','$owner','$partner','$product_summary',
	   '$product_channel','$bus_prposition','$file_name','$chk','$audi_curr_state','$audi_activation',
	   '$key_messages','$ideal_voice','$analytics','$budget','$effort','$fileList','','Lorem Ipsum is simply dummy text of the printing and typesetting industry',
	    '','no_repeat',1,'','',1,'','',1,1)"; 
	  
	$query = $wpdb->query($sql);
	if ($query) {
		$lastid = $wpdb->insert_id;	
		if($import_tactics['name'] != '')
		{ 
			$uploadFilePath = $base.'/attachments/'.basename($_FILES['import_tactics']['name']);
			move_uploaded_file($_FILES['import_tactics']['tmp_name'], $uploadFilePath);
			$objPHPExcel = PHPExcel_IOFactory::load($uploadFilePath);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$startFrom = 2; //default value is 1
			$i=2;
			foreach ($objWorksheet->getRowIterator($startFrom) as $row) {
			$column_A_Value = $objPHPExcel->getActiveSheet()->getCell("A$i")->getValue();//column A
			$cell = $objPHPExcel->getActiveSheet()->getCell("B$i");
			$InvDate= $cell->getValue();
			if(PHPExcel_Shared_Date::isDateTime($cell)) {
			$InvDate = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
			}
			$column_C_Value = $objPHPExcel->getActiveSheet()->getCell("C$i")->getValue();//column C
			$column_D_Value = $objPHPExcel->getActiveSheet()->getCell("D$i")->getValue();//column D
			$column_E_Value = $objPHPExcel->getActiveSheet()->getCell("E$i")->getValue();//column E
			$table_import=$wpdb->prefix.'spidercalendar_custom_content_piece';
			$query_import = "INSERT INTO ".$table_import."(event_id,tactic_name,publish_date,content_channel,cta,url,content_attachment) 
			        values ('".$lastid."','".$column_A_Value."','".$InvDate."','".$column_C_Value."','".$column_D_Value."','".$column_E_Value."','')"; 
			$query_upload = $wpdb->query($query_import);
			$i++;
			}
			if($query_upload)
			{
			echo "Successfully Inserted Along With Import!!!";
			}
			else
			{
			echo "Failed to insert Data with Import";
			}
		}
	 
		else
		{
		
		$lastid = $wpdb->insert_id;
					
		$content_count = $_POST['custom_content_piece'];
		$tablename2=$wpdb->prefix.'spidercalendar_custom_content_piece';
		for($i=0;$i<$content_count;$i++)
		{
		if($tactic_name[$i] == '' && $publish[$i] == '' &&  $cont_channel == '' && $cta[$i] == '' && $url[$i] == '' && $file_attach_name[$i] == '')
		{
		  echo "Successfully Inserted Campaign Without Content Piece!!!";
		}
		 else
		 {
			if($file_attach_name[$i]!="" && $file_attach_size[$i] <= 614400)
			{ 
			$upload_content = move_uploaded_file ($file_attach_path[$i],$base.'/attachments/content_piece/'.$file_attach_name[$i]);
			}
			$tot = $i+1;
			$name_content = 'txt_'.$tot;
			$cont_channel = $_POST[$name_content];
			foreach($cont_channel as $chann)
			{
				$content = $chann;
			}
			
			$publish_date = date('Y-m-d', strtotime($publish[$i]));
			$sql2 = "INSERT INTO ".$tablename2."(event_id, tactic_name, publish_date, content_channel,cta, url, content_attachment) VALUES 
			('$lastid','$tactic_name[$i]','$publish_date','$content','$cta[$i]','$url[$i]','$file_attach_name[$i]')";	
			$query2 = $wpdb->query($sql2);
				if($query2)
				{
				echo "Successfully Inserted With Content Piece!!!";
				}
				else
				{
				echo "Failed to insert Data With Content Piece";
				}
		  }
	    }
	 }
  } 
   else
  {
	   echo "Events Query Insert Failed";
  }
}

	//Update events Functionality
	if($_POST['update'])
	{
	 //echo "Testing Update";	
    $ev_id = htmlentities($_POST['ev_id']);
	$base = dirname(__FILE__);
	
	if(count($_FILES['upload']['name']) > 0)
	 {
		for($i=0; $i<count($_FILES['upload']['name']); $i++) 
		  {
			 $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
			 if($tmpFilePath != "")
			 {
				$shortname = $_FILES['upload']['name'][$i];
				$filePath = $base."/attachments/final_docs/" .$_FILES['upload']['name'][$i];
				if(move_uploaded_file($tmpFilePath, $filePath)) 
				{
					$files[] = $shortname;
				} 
			 }				 
		  }
		  
			//$prefix = $fileList = '';
			foreach ($files as $files_new)
			{
			$fileList .= $files_new.',';
			
			}
	 }
	if($fileList == '')
	{
		$doc = $_POST['prev_doc'];
	}
	else
	{
		$doc = $fileList;
	}
	
	
	$file = $_FILES['business_attachment'];
	$file_name = $file['name'];
	$file_type = $file ['type'];
	$file_size = $file ['size'];
	$file_path = $file ['tmp_name'];
    if($file_name == '')
	{
	 $attach = $_POST['prev_image']; 	
	}
	else
	{
	 $attach = $file_name; 	
	}
	if($file_name!="" && $file_size <= 614400)
	{ 
	$upload = move_uploaded_file ($file_path,$base.'/attachments/'.$file_name);
	}	 
	$audience = $_POST['audience'];
	$chk=""; 
	foreach($audience as $chk1) 
	{ 
	$chk.= $chk1.","; 
	}	
	$tablename_update=$wpdb->prefix.'spidercalendar_event';
	$sql_update = "UPDATE ".$tablename_update." SET title = '$camp_name', date = '$start_date', date_end = '$end_date', 
	       owner = '$owner', partner = '$partner', category = '$category', summary = '$product_summary', channel = '$product_channel',
		   proposition = '$bus_prposition',attachment = '$attach', audience = '$chk', current_state = '$audi_curr_state', 
		   activation = '$audi_activation', key_message = '$key_messages', ideal_voice = '$ideal_voice', analytics = '$analytics',
		   budget = '$budget', effort = '$effort',final_attachment = '$doc'	WHERE id = ".$ev_id;
	  
	       $query_update = $wpdb->query($sql_update);
		 
			$update_content_count = $_POST['update_content_count'];
			$row_id = $_POST['row_id'];
			for($i=0;$i<$update_content_count;$i++)
			{
				if($file_attach_name[$i] == '')
				{
					$attach_prev[$i] = $_POST['prev_image_content'][$i];
				}					
				else
				{
					$attach_prev[$i] = $file_attach_name[$i];
				}
				
				if($file_attach_name[$i]!="" && $file_attach_size[$i] <= 614400)
				{ 
				$upload_content = move_uploaded_file ($file_attach_path[$i],$base.'/attachments/content_piece/'.$file_attach_name[$i]);
				}
				$tot = $i+1;
				$name_content = 'txt_'.$tot;
				$cont_channel = $_POST[$name_content];
				foreach($cont_channel as $chann)
				{
					$content = $chann;
				}
				
				$publish_date = date('Y-m-d', strtotime($publish[$i]));
				$tablename4=$wpdb->prefix.'spidercalendar_custom_content_piece';
				$sql4 = "UPDATE ".$tablename4." SET tactic_name = '$tactic_name[$i]', publish_date = '$publish_date', content_channel = '$content'
				,cta = '$cta[$i]', url = '$url[$i]', content_attachment = '$attach_prev[$i]' WHERE content_id = '$row_id[$i]' AND event_id = ".$ev_id ;
				$query4 = $wpdb->query($sql4);
			}
			   if($query4 || $query_update)
				{ 
			    ?>
					<script>
					   window.location="manage-campaign?action=view&id=<?php echo $ev_id?>&msg=1";
					</script>
				<?php 
				}
				else
				{ ?>
				  <script>
					   window.location="manage-campaign?action=view&id=<?php echo $ev_id?>&msg=2";
					</script>
			    <?php 
				}
				
    }	

     //View and Edit Functionality of custom events

		$camp_id = htmlentities($_GET['id']);	
		$results = $wpdb->get_results( "SELECT custom_events.*,cat.title As cat_title FROM ".$wpdb->prefix."spidercalendar_event As custom_events 
		INNER JOIN ". $wpdb->prefix."spidercalendar_event_category AS cat ON custom_events.category =  cat.id WHERE custom_events.id = ".$camp_id); 
		foreach($results as $row)
		{
		$cat_id = $row->category;	
		$campaign = $row->title;
		$partner = $row->partner;
		$start_date = date("m/d/Y", strtotime($row->date));
		$end_date = date("m/d/Y", strtotime($row->date_end));
		$owner = $row->owner;
		$category_title = $row->cat_title;
		$summary = $row->summary;
		$channel = $row->channel;
		$proposition = $row->proposition;
		$audience = $row->audience;
		$current_state = $row->current_state;
		$activation = $row->activation;
		$key_message = $row->key_message;
		$ideal_voice = $row->ideal_voice;
		$analytics = $row->analytics;
		$budget = $row->budget;
		$effort = $row->effort;
		$attach = $row->attachment;
		$final_attach = $row->final_attachment;
		$final_doc = explode(',', $final_attach);
		}
    
		$content_results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."spidercalendar_custom_content_piece WHERE event_id = ".$camp_id); 
		$rowcount = $wpdb->num_rows;
		$row_id = array();
		$tactic = array();
		$publish_date = array();
		$content_channel = array();
		$cta = array();
		$url = array();
		foreach ($content_results as $row_content){
		$row_id[] = $row_content->content_id;	
		$tactic[] = $row_content->tactic_name;
		$publish_date[] = date("m/d/Y", strtotime($row_content->publish_date));

		$content_channel[] = $row_content->content_channel;
		$cta[] = $row_content->cta;
		$url[] = $row_content->url;
		$content_attachment[] = $row_content->content_attachment;
		}
		
	?>	
	
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<style>
	table 
	{
		font-family: "Intel Clear", "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 15px;
	}
	
	</style>
	<script src="http://code.jquery.com/jquery-1.11.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<link rel="stylesheet" href="/resources/demos/style.css" />
	<script>
	
	$(function() {
	$( "#start_date" ).datepicker();
	});
	$(function() {
	$( "#end_date" ).datepicker();
	});
	$(function() {
	$( "#publish" ).datepicker();
	});
	
	
    function validateForm()
	{
		var camp_name = document.getElementById('campaign_name').value;
		var owner = document.getElementById('owner').value;
		var start_date = document.getElementById('start_date').value;
		var end_date = document.getElementById('end_date').value;
		var category = document.getElementById('category').value;
		var product_summary = document.getElementById('product_summary').value;
		var bus_prposition = document.getElementById('bus_prposition').value;
		//var audience =  document.getElementById("audience").checked = false;
		var key_message = document.getElementById('key_messages').value;
		
		var audience_external = document.getElementById('audience_external').checked;
		var audience_internal_emp = document.getElementById('audience_internal_emp').checked;
		var audience_internal_bb = document.getElementById('audience_internal_bb').checked;
		var audience_managers = document.getElementById('audience_managers').checked;
		var audience_executives = document.getElementById('audience_executives').checked;
		var audience_GAM = document.getElementById('audience_GAM').checked;
		var audience_GER = document.getElementById('audience_GER').checked;
		var audience_GAR = document.getElementById('audience_GAR').checked;
		var audience_LAR = document.getElementById('audience_LAR').checked;
		var audience_others = document.getElementById('audience_others').checked;
		
		if(camp_name == '')
		{
		//alert(camp_name);
		alert('Campaign Name Cannot be Empty !!!');
		return false;
		}
		if(owner == '')
		{
		//alert(owner);
		alert('HRMC Owner Cannot be Empty !!!');
		return false;
		}
		if(start_date == '')
		{
		
		alert('Campaign Start Date Cannot be Empty !!!');
		return false;
		}
		if(end_date == '')
		{
		alert('Campaign End Date Cannot be Empty !!!');
		return false;
		}
		if(category == '')
		{
		alert('Please Select a Campaign Category !!!');
		return false;
		}
		if(product_summary == '')
		{
		alert('Product Summary cannot be Empty !!!');
		return false;
		}
		if(bus_prposition == '')
		{
		alert('Business Proposition cannot be Empty !!!');
		return false;
		}
		if(audience_external == false && audience_internal_emp == false && audience_internal_bb == false && audience_managers == false && audience_executives == false && audience_GAM == false && audience_GER == false && audience_GAR == false && audience_LAR == false && audience_others == false)
		{
		alert('Please check the Applicable options !!!');
		return false;
		}
		if(key_message == '')
		{
		alert('Key Message Cannot be Blank!!!');
		return false;
		}
		return true;
	}
	
	function expandCollapse(showHide) { 
        
        var hideShowDiv = document.getElementById(showHide);
        var label = document.getElementById("expand");

        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	
	function expandCollapse1(showHide1) { 
        
        var hideShowDiv = document.getElementById(showHide1);
        var label = document.getElementById("expand1");
		
        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	
	function expandCollapse2(showHide2) {
        
        var hideShowDiv = document.getElementById(showHide2);
        var label = document.getElementById("expand2");

        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	
	function expandCollapse3(showHide3) {
        
        var hideShowDiv = document.getElementById(showHide3);
        var label = document.getElementById("expand3");

        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	
	function expandCollapse4(showHide4) {
        
        var hideShowDiv = document.getElementById(showHide4);
        var label = document.getElementById("expand4");
     
        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	</script>
	
	<!--View Event Data from Database-->
	<?php if($_GET['action'] == 'view') { //echo $current_url=$_SERVER['REQUEST_URI']; 
	if($_GET['msg'] == 1)
	{
		echo "Camapign Updated Successfully!!!";
	}
	if($_GET['msg'] == 2)
	{
		echo "Failed to Update the Campaign!!!";
	}
	
	?>
	<form action='' method='GET' name='editcustomeventForm' id='editcustomeventForm'>
	
	<div style="float:right;">
	<input type="hidden" name="id" value="<?php echo $camp_id ?>">
	<input type="hidden" name="event_action" value="edit_event">
	<input type="submit" name="update_event" value="Edit Event" style="text-transform:uppercase;background:#0071C5;border:0;border-radius:2px;color:#fff;line-height:0.5;padding: 0.84375em 0.875em 0.78125em;text-align:center;width:150px;cursor:pointer;">
	<input type="button" name="close_event" value="Back" onclick="window.close();" style="text-transform:uppercase;background:#0071C5;border:0;border-radius:2px;color:#fff;line-height:0.5;padding: 0.84375em 0.875em 0.78125em;text-align:center;width:150px;cursor:pointer;">
	</div>
	</form>
	
	<table style="border:1px solid #ccc;width:100%;">
	<tr><td colspan="2" style="padding:5px;border-right:1px solid #ccc;"><strong>Campaign Name:</strong>&nbsp;<span><?php echo $campaign; ?></span></td><td style="padding:5px;" colspan="1"><strong>HR Partner:</strong>&nbsp;<span><?php echo $partner; ?></span></td></tr>
	<tr><td colspan="2" style="padding:5px;border-right:1px solid #ccc;"><strong>Start Date:</strong>&nbsp;<span><?php echo $start_date; ?></span></td><td style="padding:5px;" colspan="1"><strong>End Date:</strong>&nbsp;<span><?php echo $end_date; ?></span></td></tr>
	<tr><td colspan="2" style="padding:5px;border-right:1px solid #ccc;"><strong>HRMC Owner:</strong>&nbsp;<span><?php echo $owner; ?></span></td><td style="padding:5px;" colspan="1"><strong>Category:</strong>&nbsp;<span><?php echo $category_title; ?></span></td></tr>
	<tr><td colspan="2" style="padding:5px;border-right:1px solid #ccc;"><strong>Budget:</strong>&nbsp;<span>$ <?php echo $budget; ?></span></td><td style="padding:5px;" colspan="1"><strong>Effort:</strong>&nbsp;<span><?php echo $effort; ?> hrs</span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Product Brief Summary:</strong><br/><span><?php echo $summary; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Product Channel & Delivery Summary:</strong><br/>&nbsp;<span><?php echo $channel; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Business Proposition:</strong><br/>&nbsp;<span><?php echo $proposition; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Audience:</strong><br/>&nbsp;<span><?php echo $audience; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Audience Curent State:</strong><br/>&nbsp;<span><?php echo $current_state; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Audience Activation:</strong><br/>&nbsp;<span><?php echo $activation; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Key Messages:</strong><br/>&nbsp;<span><?php echo $key_message; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Ideal Voice:</strong><br/>&nbsp;<span><?php echo $ideal_voice; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Analytics:</strong><br/>&nbsp;<span><?php echo $analytics; ?></span></td></tr>
	<tr><td colspan="3" style="padding:5px;"><strong>Final Document Repository:</strong><br/>&nbsp;<span><?php for($j=0;$j<count($final_doc);$j++) { ?> <a style="color:#0071C5;" href="../wp-content/plugins/spider-event-calendar/attachments/final_docs/<?php echo $final_doc[$j];?>"><?php echo $final_doc[$j]." | ";?></a><?php } ?></span></td></tr>
	<?php  if($rowcount != 0) { ?>
	<tr style="background-color:#0071C5;color:#fff;"><td style="padding:5px;" colspan="3"><h3>Content Addition</h3></td></tr>
	<?php for($i=0;$i<$rowcount;$i++) {?>
	<tr style="background-color:#C0C0C0;color:#000;"><td colspan="3" style="padding:5px;"><strong>Content Piece - <?php echo $i + 1 ?></strong></td></tr>
	<tr><td style="padding:5px;" colspan="2"><strong>Tactic Name:</strong>&nbsp;<span><?php echo $tactic[$i]; ?></span></td><td style="padding:5px;" colspan="1"><strong>Publish Date:</strong>&nbsp;<span><?php if($publish_date[$i] != '01/01/1970') { echo $publish_date[$i]; } ?></span></td></tr>
	<tr><td style="padding:5px;"><strong>Channel:</strong>&nbsp;<span><?php echo $content_channel[$i]; ?></span></td><td style="padding:5px;"><strong>CTA:</strong>&nbsp;<span><?php echo $cta[$i]; ?></span></td><td style="padding:5px;"><strong>URL:</strong>&nbsp;<span><?php echo $url[$i]; ?></span></td></tr>
	<?php } } ?>
	</table>
	
	<!--Edit Event Data from Database-->
	<?php }
    
    elseif($_GET['event_action'] == 'edit_event')
	{ 
    ?>    
    <form action='' method='post' name='updatecustomeventForm' id='updatecustomeventForm' enctype="multipart/form-data" onsubmit="return validateForm()">
	<h3>Edit Marcomm Plan</h3>
	<table>
	<tr>
	<td colspan="2"><b>Campaign Name:</b><span style='color:red'>*</span><input type='text' name='campaign_name' id='campaign_name' style="padding:8px;border-color:#C0C0C0;width:100%" value="<?php echo $campaign; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>HRMC Owner:</b><span style='color:red'>*</span><input type='text' name='owner' id='owner' style="padding:8px;border-color:#C0C0C0;width:100%" value="<?php echo $owner; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>HR Partner:</b><input type='text' name='partner' id='partner' style="padding:8px;border-color:#C0C0C0;width:100%" value="<?php echo $partner; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>Start Date:</b><span style='color:red'>*</span><input type='text' name='start_date' id='start_date' style="padding:8px;border-color:#C0C0C0;width:100%" value="<?php echo $start_date; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>End Date:</b><span style='color:red'>*</span><input type='text' name='end_date' id='end_date' style="padding:8px;border-color:#C0C0C0;width:100%" value="<?php echo $end_date; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>Category:</b><span style='color:red'>*</span>
	<select id='category' name='category' style="padding:8px;border-color:#C0C0C0;width:100%">
	<option value=''>--Select Category--</option>
	 <?php foreach ($query25 as $key => $category) {
            ?>
            <option value="<?php echo $category->id; ?>" <?php if($category->id == $cat_id) echo "selected"; ?>><?php if(isset($category)) echo $category->title ?></option>
            <?php
          }
     ?> 	
	</select></td>
	</tr>

	<tr style="background-color:#0071C5;color:#fff;"><td colspan="2">
	<label style="padding:15px;"><b>Product,Service&Distribution History</b></label>
	</td></tr>
	<tr>
	<td colspan="2"><b>Product Brief Summary:</b>
	<span style='color:red'>*</span>
	<textarea name='product_summary' id='product_summary' style="padding:10px;border-color:#C0C0C0;" placeholder="Provide a Brief description of the HR product,service or program - including proce and cost"><?php echo $summary; ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Product Channel & Delivery Summary:</b>
	<textarea name='product_channel' id='product_channel' style="padding:10px;border-color:#C0C0C0;" placeholder="Provide a Brief description of how and where the product will be delivered"><?php echo $channel ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Business Proposition:</b><span style='color:red'>*</span>
	<textarea name='bus_prposition' id='bus_prposition' style="padding:10px;border-color:#C0C0C0;" placeholder="Where does it align with Intel corporate, business or HR priorities?"><?php echo $proposition; ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Attachment:</b><input type='file' name='business_attachment' id='business_attachment' style="width:100%;padding:10px;border-color:#C0C0C0;" ><span><?php echo $attach ?></span></td>
	</tr>
	<input type="hidden" name="prev_image" value="<?php echo $attach ?>">
	
	<tr>
	<td colspan="2"><b>Final Document Repository:</b><input id='upload' name="upload[]" type="file" multiple="multiple" style="width:100%"/><span><?php for($i=0;$i<count($final_doc);$i++) { echo $final_doc[$i].','; } ?></span></td>
	</tr>
	<input type="hidden" name="prev_doc" value="<?php for($i=0;$i<count($final_doc);$i++) { echo $final_doc[$i].','; } ?>">
	<tr style="background-color:#0071C5;color:#fff;"><td colspan="2">
	<label style="padding:15px;"><b>Audience & Environment Analysis</b></label>
	</td></tr>
	<tr><td><?php 
	            
				$string1 = preg_replace('/\,$/', '', $audience); //Remove dot at end if exists
				$aud_explode = explode(',',$string1);
				
				?></td></tr>
	<tr>
	<td colspan="2"><b>Audience:</b><span style='color:red'>*</span> 
	<input type='checkbox' id= 'audience_external' name='audience[]' value='External' <?php if(in_array("External", $aud_explode)){ ?> checked="checked" <?php } ?> > External &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_internal_emp' name='audience[]' value='Internal-All Employees' <?php if(in_array("Internal-All Employees", $aud_explode)){ ?> checked="checked" <?php } ?> > Internal - All Employees &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_internal_bb' name='audience[]' value='Internal-All BB' <?php if(in_array("Internal-All BB", $aud_explode)){ ?> checked="checked" <?php } ?> > Internal - All BB &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_managers' name='audience[]' value='Managers' <?php if(in_array("Managers", $aud_explode)){ ?> checked="checked" <?php } ?> > Managers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_executives' name='audience[]' value='Executives(G12+)' <?php if(in_array("Executives(G12+)", $aud_explode)){ ?> checked="checked" <?php } ?> > Executives(G12+)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GAM' name='audience[]' value='Regional-GAM' <?php if(in_array("Regional-GAM", $aud_explode)){ ?> checked="checked" <?php } ?> > Regional - GAM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GER' name='audience[]' value='Regional-GER' <?php if(in_array("Regional-GER", $aud_explode)){ ?> checked="checked" <?php } ?> > Regional - GER &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GAR' name='audience[]' value='Regional-GAR' <?php if(in_array("Regional-GAR", $aud_explode)){ ?> checked="checked" <?php } ?> > Regional - GAR &nbsp;&nbsp;
	<input type='checkbox' id= 'audience_LAR' name='audience[]' value='Regional-LAR' <?php if(in_array("Regional-LAR", $aud_explode)){ ?> checked="checked" <?php } ?> > Regional - LAR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_others' name='audience[]' value='Others' <?php if(in_array("Others", $aud_explode)){ ?> checked="checked" <?php } ?> > Others 
	</td>
	</tr>
	<tr>
	<td colspan="2"><b>Audience Current State:</b>
	<textarea name='audi_curr_state' id='audi_curr_state' style="padding:10px;border-color:#C0C0C0;" placeholder="What is their current mindset or behavior"><?php echo $current_state; ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Audience Activation:</b>
	<textarea name='audi_activation' id='audi_activation' style="padding:10px;border-color:#C0C0C0;" placeholder="How difficult is it to persuade the audience? IS this a hard sell or the product sell itself?"><?php echo $activation; ?></textarea></td>
	</tr>
	
	<tr style="background-color:#0071C5;color:#fff;"><td colspan="2">
	<label style="padding:15px;"><b>Communication Goals and Promotional Mix</b></label>
	</td></tr>
	<tr>
	<td colspan="2"><b>Key Messages:</b><span style='color:red'>*</span>
	<textarea name='key_messages' id='key_messages' style="padding:10px;border-color:#C0C0C0;" placeholder="Shore summary of the compelling story. What are the top 3 key messages?"><?php echo $key_message; ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Ideal Voice:</b>
	<input type='text' name='ideal_voice' id='ideal_voice' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="Who is the most credible messenger or spokesperson to land those messages?" value="<?php echo $ideal_voice; ?>"></td>
	</tr>
	
	<tr style="background-color:#0071C5;color:#fff;"><td colspan="2">
	<label style="padding:15px;"><b>Media Weight, Reach and Costs</b></label>
	</td></tr>
	<tr>
	<td colspan="2"><b>Analytics:</b>
	<textarea name='analytics' id='analytics' style="padding:10px;border-color:#C0C0C0;" placeholder="What goals or metrics used to measure marketing communication success?"><?php echo $analytics; ?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><b>Budget(In Dollars):</b>
	<input type='number' name='budget' id='budget' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="What is the communication budget for this campaign or program?" value="<?php echo $budget; ?>"></td>
	</tr>
	<tr>
	<td colspan="2"><b>Effort(In Hours):</b>
	<input type='number' name='effort' id='effort' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="What is the estimated number of HRMC hours to launch and run this engagement" value="<?php echo $effort; ?>"></td>
	</tr>
	<?php for($i=0;$i<$rowcount;$i++) {?>
	<script>
	$(function() {
	$( "#publish_"+ <?php echo $i?> ).datepicker();
	});
	
	</script>
	<tr style="background-color:#C0C0C0;"><td colspan="2" style="padding:5px;">Content Piece - <?php echo $i + 1 ?></td></tr>
	<tr>
	<td style="width:50%;"><strong><b>Tactic Name:</b><br/>
	 <input type="text" name="tactic[]" id="tactic" value="<?php echo $tactic[$i]; ?>" style="width:90%;border-color:#C0C0C0;"></td>
	
	<td  style="width:50%;"><strong><b>Publish Date:</b><br/>
	 <input type="text" name="publish[]" id="publish_<?php echo $i ?>" value="<?php if($publish_date[$i] != '01/01/1970') { echo $publish_date[$i]; }?>" style="width:90%;border-color:#C0C0C0;"></td>
	</tr>
	<tr>
	<td colspan="2"><strong><b>Channel:</b><br/>
	<input type='radio' value='Circuit HR Page' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Circuit HR Page') {?> checked="checked" <?php } ?>/> Circuit HR Page &nbsp;
	<input type='radio' value='Circuit News' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Circuit News') {?> checked="checked" <?php } ?>/> Circuit News &nbsp;&nbsp;&nbsp;
	<input type='radio' value='Circuit Microsite' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Circuit Microsite') {?> checked="checked" <?php } ?>/> Circuit Microsite &nbsp;
	<input type='radio' value='Wordpress Microsite' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Wordpress Microsite') {?> checked="checked" <?php } ?>/> Wordpress Microsite &nbsp;
	<input type='radio' value='Ask Vote Answer' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Ask Vote Answer') {?> checked="checked" <?php } ?>/> Ask Vote Answer &nbsp;
	<input type='radio' value='Double Dutch' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Double Dutch') {?> checked="checked" <?php } ?>/> Double Dutch &nbsp;
	<input type='radio' value='Physical Poster' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Physical Poster') {?> checked="checked" <?php } ?>/> Physical Poster <br/>
	<input type='radio' value='Digital Sign' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Digital Sign') {?> checked="checked" <?php } ?>/> Digital Sign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Email' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Email') {?> checked="checked" <?php } ?>/> Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Inside Blue' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Inside Blue') {?> checked="checked" <?php } ?>/> Inside Blue &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Meetup' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Meetup') {?> checked="checked" <?php } ?>/> Meetup &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='My Intel App' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'My Intel App') {?> checked="checked" <?php } ?>/> My Intel App &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Intel Newsroom' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Intel Newsroom') {?> checked="checked" <?php } ?>/> Intel Newsroom 
	<input type='radio' value='Sharepoint' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Sharepoint') {?> checked="checked" <?php } ?>/> Sharepoint <br/>
	<input type='radio' value='Webcast' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Webcast') {?> checked="checked" <?php } ?>/> Webcast &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Enterprise Wiki' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Enterprise Wiki') {?> checked="checked" <?php } ?>/> Enterprise Wiki
	<input type='radio' value='Twitter' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Twitter') {?> checked="checked" <?php } ?>/> Twitter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Linkedin' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Linkedin') {?> checked="checked" <?php } ?>/> Linkedin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Facebook' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Facebook') {?> checked="checked" <?php } ?>/>  Facebook &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Instagram' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Instagram') {?> checked="checked" <?php } ?>/> Instagram &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='radio' value='Others' name='txt_<?php echo $i + 1?>[]' <?php if($content_channel[$i] == 'Others') {?> checked="checked" <?php } ?>/> Others 
	</td>
	</tr>
	<tr>
	<td  style="width:50%;"><b>CTA:</b><br/>
	 <input type="text" name="cta[]" id="cta" value="<?php echo $cta[$i]; ?>" style="width:90%;border-color:#C0C0C0;"></td>
	
	<td  style="width:50%;"><b>url:</b><br/>
	 <input type="text" name="url[]" id="url" value="<?php echo $url[$i]; ?>" style="width:90%;border-color:#C0C0C0;"></td>
	</tr>
	<input type="hidden" name="row_id[]" value="<?php echo $row_id[$i] ?>">
	<tr>
	<td colspan="2"><b>Content Attachment:</b>
	 <input type="file" name="attachment[]" id="attachment" style="width:31%;border-color:#C0C0C0;"> <span><?php echo $content_attachment[$i]; ?></span></td>
	</tr>
	<input type="hidden" name="prev_image_content[]" value="<?php echo $content_attachment[$i]; ?>">
	<?php } ?>
	<input type="hidden" name="ev_id" value="<?php echo $_GET['id']?>"/>
	<input type="hidden" name="update_content_count" value="<?php echo $rowcount?>"/>
	<tr>
	<td><input type="submit" name="update" value="Update Event" style="padding:15px;width:140px;background-color:#0071C5;">
	<input type="button" name="cancel" value="Cancel" style="padding:15px;width:140px;background-color:#0071C5;" onclick="window.location.href='manage-campaign?action=view&id=<?php echo $_GET['id'] ?>';"></td>
	</tr>
	</table>	
	</form>    


		
<?php	}

	else { ?>
	
	<form action='' method='post' name='customeventForm' id='customeventForm' enctype="multipart/form-data" onsubmit="return validateForm()">
	<h3>Create Marcomm Plan</h3>
	<table>
	<tr>
	<td><b>Campaign Name:</b><span style='color:red'>*</span><input type='text' name='campaign_name' id='campaign_name' style="padding:8px;border-color:#C0C0C0;width:100%;"></td>
	</tr>
	<tr>
	<td><b>HRMC Owner:</b><span style='color:red'>*</span><input type='text' name='owner' id='owner' style="padding:8px;border-color:#C0C0C0;width:100%"></td>
	</tr>
	<tr>
	<td><b>HR Partner:</b><input type='text' name='partner' id='partner' style="padding:8px;border-color:#C0C0C0;width:100%"></td>
	</tr>
	<tr>
	<td><b>Start Date:</b><span style='color:red'>*</span><input type='text' name='start_date' id='start_date' style="padding:8px;border-color:#C0C0C0;width:100%"></td>
	</tr>
	<tr>
	<td><b>End Date:</b><span style='color:red'>*</span><input type='text' name='end_date' id='end_date' style="padding:8px;border-color:#C0C0C0;width:100%"></td>
	</tr>
	<tr>
	<td><b>Category:</b><span style='color:red'>*</span>
	<select id='category' name='category' style="padding:8px;border-color:#C0C0C0;width:100%">
	<option value=''>--Select Category--</option>
	 <?php foreach ($query25 as $key => $category) {
            ?>
            <option value="<?php echo $category->id; ?>"><?php if(isset($category)) echo $category->title ?></option>
            <?php
          }
     ?> 	
	</select></td>
	</tr>
	</table>
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse('showHide');" id="expand">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Product, Service & Distribution History</b></label>
	</td></tr>
	</table>
	</div>
	
	<div id="showHide" style="display: none;">
	<table>
	<tr>
	<td><b>Product Brief Summary:</b>
	<span style='color:red'>*</span>
	<textarea name='product_summary' id='product_summary' style="padding:10px;border-color:#C0C0C0;" placeholder="Provide a Brief description of the HR product,service or program - including proce and cost"></textarea></td>
	</tr>
	<tr>
	<td><b>Product Channel & Delivery Summary:</b>
	<textarea name='product_channel' id='product_channel' style="padding:10px;border-color:#C0C0C0;" placeholder="Provide a Brief description of how and where the product will be delivered"></textarea></td>
	</tr>
	<tr>
	<td><b>Business Proposition:</b><span style='color:red'>*</span>
	<textarea name='bus_prposition' id='bus_prposition' style="padding:10px;border-color:#C0C0C0;" placeholder="Where does it align with Intel corporate, business or HR priorities?"></textarea></td>
	</tr>
	<tr>
	<td><b>Attachment:</b><input type='file' name='business_attachment' id='business_attachment' style="width:100%;padding:8px;border-color:#C0C0C0;"></td>
	</tr>
	</table></div>
	
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse1('showHide1');" id="expand1">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Audience & Environment Analysis</b></label>
	</td></tr>
	</table>
	</div>
	
	<div id="showHide1" style="display:none;">
	<table>
	<tr>
	<td><b>Audience:</b><span style='color:red'>*</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id= 'audience_external' name='audience[]' value='External'> External &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_internal_emp' name='audience[]' value='Internal-All Employees'> Internal - All Employees &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_internal_bb' name='audience[]' value='Internal-All BB'> Internal - All BB &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_managers' name='audience[]' value='Managers'> Managers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_executives' name='audience[]' value='Executives(G12+)'> Executives(G12+)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GAM' name='audience[]' value='Regional-GAM'> Regional - GAM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GER' name='audience[]' value='Regional-GER'> Regional - GER &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_GAR' name='audience[]' value='Regional-GAR'> Regional - GAR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_LAR' name='audience[]' value='Regional-LAR'> Regional - LAR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type='checkbox' id= 'audience_others' name='audience[]' value='Others'> Others 
	</td>
	</tr>
	<tr>
	<td><b>Audience Current State:</b>
	<textarea name='audi_curr_state' id='audi_curr_state' style="padding:10px;border-color:#C0C0C0;" placeholder="What is their current mindset or behavior"></textarea></td>
	</tr>
	<tr>
	<td><b>Audience Activation:</b>
	<textarea name='audi_activation' id='audi_activation' style="padding:10px;border-color:#C0C0C0;" placeholder="How difficult is it to persuade the audience? is this a hard sell or the product sell itself?"></textarea></td>
	</tr>
	</table>
	</div>
	
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse2('showHide2');" id="expand2">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Communication Goals and Promotional Mix</b></label>
	</td></tr>
	</table></div>
	
	<div id="showHide2" style="display:none;">
	<table>
	<tr>
	<td><b>Key Messages:</b><span style='color:red'>*</span>
	<textarea name='key_messages' id='key_messages' style="padding:10px;border-color:#C0C0C0;" placeholder="Shore summary of the compelling story. What are the top 3 key messages?"></textarea></td>
	</tr>
	<tr>
	<td><b>Ideal Voice:</b>
	<input type='text' name='ideal_voice' id='ideal_voice' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="Who is the most credible messenger or spokesperson to land those messages?"></td>
	</tr>
	</table></div>
	
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse3('showHide3');" id="expand3">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Media Weight, Reach and Costs</b></label>
	</td></tr>
	</table></div>
	
	<div id="showHide3" style="display:none;">
	<table>
	<tr>
	<td><b>Analytics:</b>
	<textarea name='analytics' id='analytics' style="padding:10px;border-color:#C0C0C0;" placeholder="What goals or metrics used to measure marketing communication success?"></textarea></td>
	</tr>
	<tr>
	<td><b>Budget(In Dollars):</b>
	<input type='number' name='budget' id='budget' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="What is the communication budget for this campaign or program?"></td>
	</tr>
	<tr>
	<td><b>Effort(In Hours):</b>
	<input type='number' name='effort' id='effort' style="width:100%;padding:8px;border-color:#C0C0C0;" placeholder="What is the estimated number of HRMC hours to launch and run this engagement"></td>
	</tr>
	</table>
	</div>
	
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse4('showHide4');" id="expand4">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Individual Tactics</b></label>
	</td></tr></table>
	</div>
	
	<div id="showHide4" style="display:none;">
	<table>
	<tr><td><b>Import File:</b> <span style="font-size:12px;"><a href='../wp-content/plugins/spider-event-calendar/Excel_upload.xlsx' style='text-decoration:none;font-weight:bold;color:#0071C5;'>Download the Template for Individual Tactics</a></span>
	<br/><input type="file" name="import_tactics" id="import_tactics" style="width:100%;padding:8px;border-color:#C0C0C0;"></td></tr>
	<tr><td><span style="width:100%;font-weight:bold;padding-left:50%;">OR</span></td>
	<tr>
	<td>
	<?php 
	echo "<div id='contentWrapper'>";
			echo "<div style='width:100%; padding:10px; margin:10px 0px; background; #f1f1f1; border:1px solid #ccc; float:left;'>";
				echo "<input type='hidden' name='custom_content_piece' id='custom_content_piece' value='1' />";
				echo "<h3>Content Addition</h3>";
				echo "<div style='width:50%; padding:5px; float:left;'>";
						echo "<label><b>Tactic Name:</b></label> <input type='text' value='' name='tactic[]' style='padding:8px;border-color:#C0C0C0;width:80%'/>";
				echo "</div>";
				echo "<div style='width:50%; padding:5px; float:left;'>";
						echo "<label><b>Publish Date:</b></label> <input type='text' value='' id='publish' name='publish[]' style='width:80%;padding:8px;border-color:#C0C0C0;'/>";
				echo "</div>";
				echo "<div style='width:100%; padding:5px; float:left;'>";
						echo "<label><b>Channel:</b></label><br/>
						<input type='radio' value='Circuit HR Page' name='txt_1[]'/> Circuit HR Page &nbsp;&nbsp;
						<input type='radio' value='Circuit News' name='txt_1[]'/> Circuit News &nbsp;&nbsp;
						<input type='radio' value='Circuit Microsite' name='txt_1[]'/> Circuit Microsite &nbsp;&nbsp;
						<input type='radio' value='Wordpress Microsite' name='txt_1[]'/> Wordpress Microsite &nbsp;&nbsp;
						<input type='radio' value='Ask Vote Answer' name='txt_1[]'/> Ask Vote Answer &nbsp;&nbsp;
						<input type='radio' value='Double Dutch' name='txt_1[]'/> Double Dutch &nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Physical Poster' name='txt_1[]'/> Physical Poster <br/>
						<input type='radio' value='Digital Sign' name='txt_1[]'/> Digital Sign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Email' name='txt_1[]'/> Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Inside Blue' name='txt_1[]'/> Inside Blue &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Meetup' name='txt_1[]'/> Meetup &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='My Intel App' name='txt_1[]'/> My Intel App &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Intel Newsroom' name='txt_1[]'/> Intel Newsroom &nbsp;
						<input type='radio' value='Sharepoint' name='txt_1[]'/> Sharepoint <br/>
						<input type='radio' value='Webcast' name='txt_1[]'/> Webcast &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Enterprise Wiki' name='txt_1[]'/> Enterprise Wiki
						<input type='radio' value='Twitter' name='txt_1[]'/> Twitter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Linkedin' name='txt_1[]'/> Linkedin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Facebook' name='txt_1[]'/>  Facebook &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Instagram' name='txt_1[]'/> Instagram &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' value='Others' name='txt_1[]'/> Others";
						
				echo "</div>";
				
			    echo "<div style='width:33%; padding:5px; float:left;'>";
						echo "<label><b>Call To Action:</b></label> <input type='text' value='' name='cta[]' style='padding:8px;border-color:#C0C0C0;width:90%;'/>";
				echo "</div>";	
				echo "<div style='width:=33%; padding:5px; float:left;'>";
						echo "<label><b>Destination URL:</b></label> <input type='text' value='' name='url[]' style='padding:8px;border-color:#C0C0C0;width:90%;'/>";
				echo "</div>";	
				echo "<div style='width:33%; padding:5px; float:left;'>";
						echo "<label><b>Attachment:</b></label> <input type='file' value='' name='attachment[]' style='padding:8px;border-color:#C0C0C0;width:90%;'/>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
	
	
	echo "<script>";
	echo "jQuery(function(){";
	echo "jQuery('#addnewContent').click(function(){";
	    echo "var count = +$('#custom_content_piece').val()+1;";
		echo "var container = '<div style=\'width:100%; padding:10px; margin:10px 0px; background; #f1f1f1; border:1px solid #ccc; float:left;\'>'; ";
			echo "container += '<h3>Content Addition</h3>'; ";
			echo "container += '<div style=\'width:50%; padding:5px; float:left;\'>';";
			echo "container += '<label><b>Tactic Name:</b></label> <input type=\'text\' value=\'\' name=\'tactic[]\' style=\'padding:8px;border-color:#C0C0C0;width:80%;\'/>';";
			echo "container += '</div>';";
			echo "container += '<div style=\'width:50%; padding:5px; float:left;\'>';";
			echo "container += '<label><b>Publish Date:</b></label> <input type=\'text\' value=\'\' class=\'publish\' name=\'publish[]\' style=\'padding:8px;border-color:#C0C0C0;width:80%;\'/>';";
			echo "container += '</div>';";
			
			echo "container += '<div style=\'width:100%; padding:5px; float:left;\'>';";
					echo "container += '<label><b>Channel:</b></label><br/>';";
					echo "container += '<input type=\'radio\' value=\'Circuit HR Page\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Circuit HR Page &nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Circuit News\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Circuit News &nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Circuit Microsite\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Circuit Microsite &nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Wordpress Microsite\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' /> Wordpress Microsite &nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Ask Vote Answer\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' /> Ask Vote Answer &nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Double Dutch\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Double Dutch &nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Physical Poster\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' /> Physical Poster<br/>';";
					echo "container += '<input type=\'radio\' value=\'Digital Sign\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Digital Sign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Email\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Inside Blue\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' /> Inside Blue &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Meetup\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Meetup &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'My Intel App\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  My Intel App &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Intel Newsroom\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Intel Newsroom &nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Sharepoint\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Sharepoint <br/>';";
					echo "container += '<input type=\'radio\' value=\'Webcast\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Webcast &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Enterprise Wiki\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Enterprise Wiki';";
					echo "container += '<input type=\'radio\' value=\'Twitter\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Twitter &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Linkedin\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Linkedin &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Facebook\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Facebook &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Instagram\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Instagram &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';";
					echo "container += '<input type=\'radio\' value=\'Others\' name=\'txt";
					echo "_'+count+'[]";
					echo "\' />  Others';";
			
			echo "container += '</div>';";
			
			
			echo "container += '<div style=\'width:33%; padding:5px; float:left;\'>';";
					echo "container += '<label><b>Call To Action:</b></label><input type=\'text\' value=\'\' name=\'cta[]\' style=\'padding:8px;border-color:#C0C0C0;width:90%\'/>';";
			echo "container += '</div>';";
			echo "container += '<div style=\'width:33%; padding:5px; float:left;\'>';";
					echo "container += '<label><b>Destination URL:</b></label><input type=\'text\' value=\'\' name=\'url[]\' style=\'padding:8px;border-color:#C0C0C0;width:90%\'/>';";
			echo "container += '</div>';";
			echo "container += '<div style=\'width:33%; padding:5px; float:left;\'>';";
					echo "container += '<label><b>Attachment:</b></label><input type=\'file\' value=\'\' name=\'attachment[]\' style=\'padding:8px;border-color:#C0C0C0;width:90%\'/>';";
			echo "container += '</div>';";
			
		 echo "container +=	'</div>'; ";
		 
		 echo "jQuery('#contentWrapper').append(container)";
		 echo "})";
	     echo "});";
	echo "</script>";	
	?>
	<script>
	$('body').on('focus',".publish", function(){
	$(this).datepicker();
	});
	
	$(document).ready(function(){
	$('#addnewContent').click( function() {
            var counter = $('#custom_content_piece').val();
			//alert(counter);
            counter++ ;
            $('#custom_content_piece').val(counter);
			var count = $('#custom_content_piece').val();
			//alert(count);
    });
	});
	</script>
	<?php
	echo "<br/>";echo "<br/>";
	echo "<div style='float:left; width:100%; margin:20px 0px;'>";
	echo "<div  id='addnewContent' style='
		float:right;
	    background: #0071C5;
		border: 0;
		border-radius: 2px;
		color: #fff;
		font-family: Montserrat, \"Helvetica Neue\", sans-serif;
		font-weight: 100;
		line-height: 0.5;
		padding: 0.84375em 0.875em 0.78125em;
		text-transform: uppercase;
		text-align:center;
		width:340px;
		cursor:pointer;
	'> + Add Another Content Piece</div>";
	echo "</div>";
	
	
	?></td></tr>
	</table></div>
	
	<div>
	<table>
	<tr>
	<td><b>Final Document Repository:</b>
	<input id='upload' name="upload[]" type="file" multiple="multiple" style="width:100%"/></td>
	</tr>
	
	</table>
	</div>
	
	<tr>
	<td><input type="submit" name="submit" value="Add Event" style="padding:15px;;width:140px;background-color:#0071C5;"></td>
	</tr>
	</table>	
	</form>
	
<?php
	}	
}
add_action( 'wp', 'elegance_referal_init' );


//View Grid events

//Add fron-end form
function elegance_grid_init()
{
	if(is_page('campaign-view')){	
		add_filter('the_content', 'func_grid_content');
	}
	
}

function func_grid_content(){
	  global $wpdb;
	  
	  
	  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	  $url_path = parse_url($url, PHP_URL_PATH);
	  $basename = pathinfo($url_path, PATHINFO_BASENAME);
	  
	  //PPT Export Functionality Start
	if($_POST['ppt'])
	{
		$ppt_id = $_POST['exp_ppt'];
		$ppt_results = $wpdb->get_results("SELECT title,owner,date,date_end,summary,proposition,audience,key_message FROM ".$wpdb->prefix."spidercalendar_event WHERE id = ".$ppt_id);
	    foreach($ppt_results as $ppt_row)
		{
			$ppt_camp_name = $ppt_row->title;
			$ppt_owner_name = $ppt_row->owner;
			$ppt_summary = $ppt_row->summary;
			$ppt_prop = $ppt_row->proposition;
			$ppt_audience = $ppt_row->audience;
			$ppt_key_msg = $ppt_row->key_message;
		}	
		$base_dir = dirname(__FILE__);
		/** PHPPowerPoint */
		include $base_dir.'/Classes/PHPPowerPoint.php';
		/** PHPPowerPoint_IOFactory */
		include $base_dir.'/Classes/PHPPowerPoint/IOFactory.php';
		// Create new PHPPowerPoint object
		$objPHPPowerPoint = new PHPPowerPoint();
		// Set properties
		$objPHPPowerPoint->getProperties()->setCreator("Test");
		$objPHPPowerPoint->getProperties()->setLastModifiedBy("Test");
		$objPHPPowerPoint->getProperties()->setTitle("Office 2007 PPTX Test Document");
		$objPHPPowerPoint->getProperties()->setSubject("Office 2007 PPTX Test Document");
		$objPHPPowerPoint->getProperties()->setDescription("Test document for Office 2007 PPTX, generated using PHP classes.");
		$objPHPPowerPoint->getProperties()->setKeywords("office 2007 openxml php");
		$objPHPPowerPoint->getProperties()->setCategory("Test result file");
		// Remove first slide
		$objPHPPowerPoint->removeSlideByIndex(0);
	    // Create templated slide 1
		$currentSlide = createTemplatedSlide2($objPHPPowerPoint); // local function
		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(200);
		$shape->setWidth(800);
		$shape->setOffsetX(10);
		$shape->setOffsetY(200);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

		$textRun = $shape->createTextRun('HRMC CAMPAIGN');
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(65);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FFFFFFFF' ) );
		

		$shape->createBreak();
		$shape->createBreak();
		$textRun = $shape->createTextRun($ppt_camp_name);
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(28);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FFFFFFFF' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('By : '.$ppt_owner_name);
		$textRun->getFont()->setBold(false);
		$textRun->getFont()->setSize(16);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( 'FFFFD700' ) );
		
		// Create templated slide 2
		$currentSlide = createTemplatedSlide($objPHPPowerPoint); // local function
		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(100);
		$shape->setWidth(930);
		$shape->setOffsetX(10);
		$shape->setOffsetY(10);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
		$textRun = $shape->createTextRun('Campaign: '.$ppt_camp_name);
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(32);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '444682B4' ) );
		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(600);
		$shape->setWidth(1230);
		$shape->setOffsetX(10);
		$shape->setOffsetY(100);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
		$shape->createBreak();
		$shape->createBreak();
		$textRun = $shape->createTextRun('- HRMC Owner : '.$ppt_owner_name);
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );
		$shape->createBreak();
		$shape->createBreak();
		$textRun = $shape->createTextRun('- Executive Summary : Lorem ipsum dolor sit amet consectetur adipiscing elit scelerisque 
		egestas neque rhoncus potenti congue est imperdiet ftrtyu rtewe');
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );
			
		// Create templated slide 3
		$currentSlide = createTemplatedSlide($objPHPPowerPoint); // local function

		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(100);
		$shape->setWidth(1230);
		$shape->setOffsetX(10);
		$shape->setOffsetY(10);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
		$textRun = $shape->createTextRun('Goals');
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(32);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '444682B4' ) );

		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(600);
		$shape->setWidth(1230);
		$shape->setOffsetX(10);
		$shape->setOffsetY(100);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

		$textRun = $shape->createTextRun('- Product Brief Summary');
		$textRun->getFont()->setSize(30);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#357EC7' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('    - '.$ppt_summary);
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('- Business proposition');
		$textRun->getFont()->setSize(30);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#357EC7' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('    - '.$ppt_prop);
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

		// Create templated slide 4
		$currentSlide = createTemplatedSlide($objPHPPowerPoint); // local function

		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(100);
		$shape->setWidth(1230);
		$shape->setOffsetX(10);
		$shape->setOffsetY(10);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

		$textRun = $shape->createTextRun('Audience & Messaging');
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(32);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#4863A0' ) );

		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(600);
		$shape->setWidth(1230);
		$shape->setOffsetX(10);
		$shape->setOffsetY(100);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

		$shape->createBreak();
		$textRun = $shape->createTextRun('- Target Audience:');
		$textRun->getFont()->setSize(30);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#357EC7' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('   - '.$ppt_audience);
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('- Key Messages:');
		$textRun->getFont()->setSize(30);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#357EC7' ) );

		$shape->createBreak();
		$shape->createBreak();

		$textRun = $shape->createTextRun('   - '.$ppt_key_msg);
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

		$shape->createBreak();
		$shape->createBreak();

		//create Dynamic Slides
		$ppt_content_results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."spidercalendar_custom_content_piece WHERE event_id = ".$ppt_id); 
		$pptcount = $wpdb->num_rows;
		$cta = array();
		$content_attachment = array();
		foreach ($ppt_content_results as $ppt_content){
		$ppt_cta[] = $ppt_content->cta;
		$ppt_content_attachment[] = $ppt_content->content_attachment	;
		}
		for($i=0;$i<$pptcount;$i++)
		{
		$ind = $i+1;
		// Create templated slide 
		$currentSlide = createTemplatedSlide3($objPHPPowerPoint,$ppt_content_attachment[$i]); // local function
		
		// Create a shape (text)
		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(100);
		$shape->setWidth(930);
		$shape->setOffsetX(10);
		$shape->setOffsetY(10);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

		$textRun = $shape->createTextRun('Media: '.$ind);
		$textRun->getFont()->setBold(true);
		$textRun->getFont()->setSize(32);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '#4863A0' ) );

		$shape->createBreak();

		$shape = $currentSlide->createRichTextShape();
		$shape->setHeight(500);
		$shape->setWidth(430);
		$shape->setOffsetX(490);
		$shape->setOffsetY(100);
		$shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
		$textRun = $shape->createTextRun('CTA:');
		$textRun->getFont()->setSize(24);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

		$shape->createBreak();
		$textRun = $shape->createTextRun($ppt_cta[$i]);
		$textRun->getFont()->setSize(20);
		$textRun->getFont()->setName('Intel Clear');
		$textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );
		}
		
		// Save PowerPoint 2007 file
		$objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
		$objWriter->save(str_replace('.php', '.pptx', __FILE__));
		?>
		<span style="color:#CD5C5C">PPT Generated Successfully for Camapign : <b style="color:#21618C">"<?php echo $ppt_camp_name; ?>"</b> </span> -
		<?php if(is_numeric($basename)) { ?>
		<a href="../../wp-content/plugins/spider-event-calendar/calendar.pptx" style="text-decoration:none;font-size:14px;font-weight:bold;color:#0071C5;">Click Here to Download !!!</a>
		<?php } else { ?>
		<a href="../wp-content/plugins/spider-event-calendar/calendar.pptx" style="text-decoration:none;font-size:14px;font-weight:bold;color:#0071C5;">Click Here to Download !!!</a>
		<?php
		}
		echo "<br/><br/>";
	}
	  
	  //PPT Export Functionality End
	  
	  $perpage = 10;
	  if(is_numeric($basename)) { $page  = $basename; } else { $page=1; }; 
	  $calc = $perpage * $page;
	  $start = $calc - $perpage;
	  
	  if($_POST['search_month'] != '')
	  {
	  $month_val = $_POST['search_month'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE month(a.date) = '".$month_val."' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  elseif($_POST['search_category'] != '')
	  {
	  $cat_val = $_POST['search_category'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE a.category = '".$cat_val."' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  elseif($_POST['search_channel'] != '')
	  {
	  $channel_val = $_POST['search_channel'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE b.content_channel = '".$channel_val."' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  elseif($_POST['search_campaign'] != '')
	  {
	  $camp_val = $_POST['search_campaign'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE a.title LIKE '%".$camp_val."%' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  elseif($_POST['search_owner'] != '')
	  {
	  $owner_val = $_POST['search_owner'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE a.owner LIKE '%".$owner_val."%' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  elseif($_POST['search_campaign'] != '' && $_POST['search_owner'] != '')
	  {
	  $camp_val = $_POST['search_campaign'];
	  $owner_val = $_POST['search_owner'];	  
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id WHERE a.title LIKE '%".$camp_val."%' AND a.owner LIKE '%".$owner_val."%' GROUP BY b.event_id ORDER BY a.id ASC "); 
	  $evcount = $wpdb->num_rows;  
	  }
	  else
	  {
	
	  $event_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
      FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	  ON a.id = b.event_id GROUP BY b.event_id ORDER BY a.id ASC LIMIT $start, $perpage"); 
	  $evcount = $wpdb->num_rows;
	  }
?>
	<script>
	function expandCollapse(showHide_search) {
        
        var hideShowDiv = document.getElementById(showHide_search);
        var label = document.getElementById("expand");

        if (hideShowDiv.style.display == 'none') {
            label.innerHTML = label.innerHTML.replace("[+]", "[-]");
            hideShowDiv.style.display = 'block';            
        } else {
            label.innerHTML = label.innerHTML.replace("[-]", "[+]");
            hideShowDiv.style.display = 'none';

        }
    }
	</script>
	
	<div>
	<table>
	<tr style="background-color:#0071C5;color:#fff;"><td onclick="expandCollapse('showHide_search');" id="expand">
	<label style="padding:15px;"><span style="cursor:pointer;"> [+] </span><b>Filter Campaigns</b></label>
	</td></tr>
	</table></div>
	
	<div id="showHide_search" style="display:none;">	
		<form action="" name="search_drop" method="POST">
		<table>
		<tr><td><b style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;">By Month:</b> <select name="search_month" style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;width: 25%; height: 48px;padding:3px;" onchange="this.form.submit();">
		<option value="">Select Month</option>
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">September</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option></select>
		
		&nbsp; <b style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;">By Category:</b>
		<select id='search_category' name='search_category' style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;width: 25%; height: 48px;padding:3px;" onchange="this.form.submit();">
		<option value=''>Select Category</option>
		<?php 
		$query7 = $wpdb->get_results("SELECT title,id FROM " . $wpdb->prefix . "spidercalendar_event_category");
		foreach ($query7 as $key => $category) {
		?>
		<option value="<?php echo $category->id; ?>"><?php if(isset($category)) echo $category->title ?></option>
		<?php
		}
		?> 	
		</select>
		
		&nbsp; <b style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;">By Channel:</b>
		<select id='search_channel' name='search_channel' style="width: 25%; height: 48px;padding:3px;font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;" onchange="this.form.submit();">
		<option value=''>Select Channel</option>
		<option value="Circuit HR Page">Circuit HR Page</option>
		<option value="Circuit News">Circuit News</option>
		<option value="Circuit Microsite">Circuit Microsite</option>
		<option value="Wordpress Microsite">Wordpress Microsite</option>
		<option value="Ask Vote Answer">Ask Vote Answer</option>
		<option value="Double Dutch">Double Dutch</option>
		<option value="Physical Poster">Physical Poster</option>
		<option value="Digital Sign">Digital Sign</option>
		<option value="Email">Email</option>
		<option value="Inside Blue" >Inside Blue</option>
		<option value="Meetup">Meetup</option>	
		<option value="My Intel App">My Intel App</option>	
		<option value="Intel Newsroom">Intel Newsroom</option>	
		<option value="Sharepoint">Sharepoint</option>	
		<option value="Webcast">Webcast</option>	
		<option value="Enterprise Wiki">Enterprise Wiki</option>	
		<option value="Twitter">Twitter</option>	
		<option value="Linkedin">Linkedin</option>
		<option value="Facebook">Facebook</option>
		<option value="Instagram" >Instagram</option>	
		<option value="Others" >Others</option>	
		</select>
		</form>		
		<br/>
		<br/>
		<form  action="" name="search_text" method="POST">
		<b style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;">By Campaign:</b>
		<input type="text" name="search_campaign" style="width: 33%; height: 48px;padding:3px;font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;" />
		
		&nbsp; <b style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;">By Owner:</b>
		<input type="text" name="search_owner" style="width: 33%; height: 48px;padding:3px;font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;" />
		
		&nbsp; <input type="submit" name="submit" value="Search" style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:13px;padding-top:8px;padding-bottom:8px;width:180px;background-color:#336699;"></td>
	    </form>
		</td>
		</tr>
		
		</table>
	</div>
	<?php if($_POST['search_month'] != ''){	
	if($_POST['search_month'] == '01') { $year_filter = "January"; }
	if($_POST['search_month'] == '02') { $year_filter = "February"; }
	if($_POST['search_month'] == '03') { $year_filter = "March"; }
	if($_POST['search_month'] == '04') { $year_filter = "April"; }
	if($_POST['search_month'] == '05') { $year_filter = "May"; }
	if($_POST['search_month'] == '06') { $year_filter = "June"; }
	if($_POST['search_month'] == '07') { $year_filter = "July"; }
	if($_POST['search_month'] == '08') { $year_filter = "August"; }
	if($_POST['search_month'] == '09') { $year_filter = "September"; }
	if($_POST['search_month'] == '10') { $year_filter = "October"; }
	if($_POST['search_month'] == '11') { $year_filter = "November"; }
	if($_POST['search_month'] == '12') { $year_filter = "December"; }
	
	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Month : </span><i><strong><?php echo $year_filter ?></i></strong></div>
	<?php }?>
	
	<?php if($_POST['search_category'] != ''){
		  $cat_filter = htmlentities($_POST['search_category']);
	      $query_cat = $wpdb->get_results("SELECT title,id FROM " . $wpdb->prefix . "spidercalendar_event_category WHERE id=".$cat_filter);
          foreach($query_cat as $cat_search){ $cat_filter = $cat_search->title; }
	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Category : </span><i><strong><?php echo $cat_filter ?></i></strong></div>
	<?php }?>
	
	<?php if($_POST['search_channel'] != ''){	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Channel : </span><i><strong><?php echo htmlentities($_POST['search_channel']) ?></i></strong></div>
	<?php }?>
	
	<?php if($_POST['search_campaign'] != '' && $_POST['search_owner'] != ''){	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Campaign : </span>
	<i><strong><?php echo htmlentities($_POST['search_campaign']) ?></i></strong> &nbsp;&&nbsp; <span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Owner : </span><strong><?php echo htmlentities($_POST['search_owner']) ?></strong></div>
	<?php }?>
	
	<?php if($_POST['search_campaign'] != '' && $_POST['search_owner'] == ''){	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Campaign : </span><i><strong><?php echo htmlentities($_POST['search_campaign']) ?></i></strong></div>
	<?php }?>
	
	<?php if($_POST['search_owner'] != '' && $_POST['search_campaign'] == ''){	?>
	<div><span style="font-family:Intel Clear,Helvetica Neue,Helvetica,Arial,sans-serif; font-size:15px;">Results Filtered By Owner : </span><i><strong><?php echo htmlentities($_POST['search_owner']) ?></i></strong></div>
	<?php }?>
	
		<table class="grid">
		<tbody>
		<tr style="background-color: #0071C5;">
		<th style="width:5%;padding: 5px;color:#fff;border-left:1px solid #959492;border-right:1px solid #959492;text-align:center;font-size:11px;"><b>Event Id</b></th>
		<th style="width:25%;padding: 5px;color:#fff;border-right:1px solid #959492;text-align:center;font-size:12px;"><b>Campaign Name</b></th>
		<th style="width:10%;padding: 5px;color:#fff;border-right:1px solid #959492;text-align:center;font-size:12px;"><b>Owner</b></th>
		<th style="width:20%;padding: 5px;color:#fff;border-right:1px solid #959492;text-align:center;font-size:12px;"><b>Start - End Date</b></th>
		<th style="width:30%;padding: 5px;color:#fff;border-right:1px solid #959492;text-align:center;font-size:12px;"><b>Channel</b></th>
		<th style="width:5%;padding: 5px;color:#fff;border-right:1px solid #959492;text-align:center;font-size:11px;"><b>Export To PPT</b></th>
		</tr>
		<?php 
		if($evcount == 0)
		{ ?>
		<tr>
		<td style="padding: 5px;border-right:1px solid #959492;text-align:center;font-weight:bold;" colspan="6">No Results Found</td>
		<?php 
		}
		else
		{
		foreach ($event_results as $ev_res){
		$campaign = $ev_res->title;
		$evid = $ev_res->event_id;
		$start_date = date("m/d/Y", strtotime($ev_res->date));
		$end_date = date("m/d/Y", strtotime($ev_res->date_end));
		$owner = $ev_res->owner;
		$channel = $ev_res->channel;
		?>
		<tr>
		<td style="padding: 5px;border-left:1px solid #959492;border-right:1px solid #959492;text-align:center;"><?php echo $evid ?></td>
		<td style="padding: 5px;border-right:1px solid #959492;"><a href="manage-campaign?action=view&&id=<?php echo $evid ?>" style="color:#000;"><?php echo $campaign ?></a></td>
		<td style="padding: 5px;border-right:1px solid #959492;"><?php echo $owner ?></td>
		<td style="padding: 5px;border-right:1px solid #959492;"><?php echo $start_date ?> to <?php echo $end_date ?></td>
		<td style="padding: 5px;border-right:1px solid #959492;"><?php echo $channel ?></td>
		<form name="export_form" action="" method="POST">
		<input type="hidden" name="exp_ppt" value="<?php echo $evid ?>">
		<td style="padding: 5px;border-right:2px solid #959492;width:10px;">
		<input type="submit" name="ppt" value="Export" style="padding:3px;width:80px;height:35px;background-color:#0071C5;font-size:12px;"></td>
		</form>
		</tr>
		
		<?php } } ?>
		
		</tbody>
		</table>
		<table width="100%">
		<tr><td style="text-align:center;">
			<?php 
			if($_POST['search_month'] != '')
			{
			 $month_val = $_POST['search_month'];
			 $page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
             FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	         ON a.id = b.event_id WHERE month(a.date) = '".$month_val."' GROUP BY b.event_id");  
			$tot = $wpdb->num_rows;
			}
			elseif($_POST['search_category'] != '')
	        {
			 $category_val = $_POST['search_category'];
			 $page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
             FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	         ON a.id = b.event_id WHERE a.category = '".$category_val."' GROUP BY b.event_id");  
			$tot = $wpdb->num_rows;
			}
			elseif($_POST['search_channel'] != '')
	        {
			 $channel_val = $_POST['search_channel'];
			 $page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
             FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	         ON a.id = b.event_id WHERE b.content_channel = '".$channel_val."' GROUP BY b.event_id");  
			$tot = $wpdb->num_rows;
			}
			elseif($_POST['search_campaign'] != '')
			{
			$camp_val = $_POST['search_campaign'];	  
			$page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
			FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
			ON a.id = b.event_id WHERE a.title LIKE '%".$camp_val."%' GROUP BY b.event_id"); 
			$tot = $wpdb->num_rows;
			}
			elseif($_POST['search_owner'] != '')
			{
			$owner_val = $_POST['search_owner'];	  
			$page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
			FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
			ON a.id = b.event_id WHERE a.owner LIKE '%".$owner_val."%' GROUP BY b.event_id"); 
			$tot = $wpdb->num_rows; 
			}
			elseif($_POST['search_campaign'] != '' && $_POST['search_owner'] != '')
			{
			$camp_val = $_POST['search_campaign'];
			$owner_val = $_POST['search_owner'];	  
			$page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
			FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
			ON a.id = b.event_id WHERE a.title LIKE '%".$camp_val."%' AND a.owner LIKE '%".$owner_val."%' GROUP BY b.event_id"); 
			$tot = $wpdb->num_rows; 
			}
			else			
			{
			$page_results = $wpdb->get_results("SELECT a.title,a.owner,a.date,a.date_end,b.event_id, GROUP_CONCAT(b.content_channel SEPARATOR ', ') as channel 
             FROM ".$wpdb->prefix."spidercalendar_event As a INNER JOIN ". $wpdb->prefix."spidercalendar_custom_content_piece AS b 
	         ON a.id = b.event_id GROUP BY b.event_id");  
			$tot = $wpdb->num_rows;
			$total_pages = ceil($tot / $perpage);  
			if($page <=1 ){
            echo "<span id='page_links' style='font-weight: bold;'>Prev </span>";
			}
			else
			{
			$j = $page - 1;
			echo "<span><a id='page_a_link' href='?page=$j'>< Prev</a></span>";
			}
			for($i=max($page-5,1); $i <=  max(1,min($total_pages,$page+5)); $i++)
			{
			 if($i<>$page)
			 {
			  echo "<span><a id='page_a_link' href='?page=$i'>$i</a></span>";
			 }
			 else
             {
			  echo "<span id='page_links' style='font-weight: bold;'>$i</span>";
			 }
			}
			if($page == $total_pages || $total_pages == 0)
			{
			echo "<span id='page_links' style='font-weight: bold;'> Next ></span>";
			}
			else
			{
			$j = $page + 1;
			echo "<span><a id='page_a_link' href='?page=$j'>Next</a></span>";
			} 
			}
			
			?>
		</td></tr>
		</table>
		
		<style>
		table.grid tr:nth-child(even) {background: #E6E6FA}
		table.grid tr:nth-child(odd) {background: #FFFFFF}
		page_links
		 {
		  border-radius:3px;	 
		  font-family: arial, verdana;
		  font-size: 12px;
		  border:1px #000000 solid;
		  padding: 6px;
		  margin: 3px;
		  background-color: #ADD8E6;
		  text-decoration: none;
		 }
		 #page_a_link
		 {
		  border-radius:3px;	 
		  font-family: arial, verdana;
		  font-size: 12px;
		  border:1px #000000 solid;
		  color: #000;
		  background-color: #ADD8E6;
		  padding: 6px;
		  margin: 3px;
		  text-decoration: none;
		 }
		
		table 
		{
			font-family: "Intel Clear", "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 15px;
		}
		
		</style>		
<?php
}
add_action( 'wp', 'elegance_grid_init' );

//Gantt Chart
function elegance_gantt_init()
{
	if(is_page('chart'))
	{	
		add_filter('the_content', 'gant_content');
	}
	
}
function gant_content()
{
global $wpdb;
$curr_year = $get_month_details =  Date('Y');
$sel_month = isset($_GET['sel_gant_year']) ? htmlentities($_GET['sel_gant_year']) : "";
if($sel_month == '') $sel_month = $curr_year;
$aChartData = $aCategories = array();	

$query_cat = $wpdb->get_results("SELECT a.id, a.title as name, b.title, b.date as start_date, b.date_end as end_date 
			        FROM ".$wpdb->prefix."spidercalendar_event_category As a INNER JOIN ". $wpdb->prefix."spidercalendar_event AS b 
			        ON a.id = b.category WHERE year(b.date) = '".$sel_month."'");
$rowcount = $wpdb->num_rows;
				
$j = 1;
//events count
/*$event_cat = $wpdb->get_results("SELECT category,count(category) AS tot_events FROM ".$wpdb->prefix."spidercalendar_event WHERE year(date) = '".$sel_month."' GROUP BY category");
$Totcount = $wpdb->num_rows;
$cat_height = (400/$Totcount);
foreach($event_cat as $tot_eve)
{
	$total_eve = $tot_eve->tot_events;		
	$cat = $tot_eve->category;	
	$eve_height = ($cat_height/$total_eve);
	$event_height[] = array("cat"=>$cat, "eve_height"=>$eve_height); 
}*/
//echo"<PRE>";
//print_r($event_height);
$chart_toppadding = "4";
$graph_flag = 1; 
$grsph_vsl = 1;

foreach($query_cat as $row)
{    
	$graph_cat_id = $row->id;
	
	if($grsph_vsl != $row->id){
			$grsph_vsl = $row->id;
			$chart_toppadding = "3";
		}
		/*if(in_array($row->id,$event_height[$row->id-1])){
			
			 $chart_toppadding =$event_height[$row->id-1]['eve_height'];
		}*/		
	if(count($aCategories)){
		if(array_key_exists($row->id, $aCategories)) {
			$graph_cat_id = $graph_cat_id."-".$graph_flag;		
			//$chart_toppadding = $chart_toppadding*$graph_flag ."%";
			$chart_toppadding = 8*$graph_flag ."%";
			$j++;
		}
					
	}
	$aCategories[$row->id] = $j;
    $aChartData[] = array("id"=>$row->id, "cat_name"=>$row->name, "event_title"=>$row->title, "event_start_date" => date("d/m/Y", strtotime($row->start_date)), "event_end_date" => date("d/m/Y", strtotime($row->end_date)), "graph_cat_id" => $graph_cat_id, "chart_toppadding" => $chart_toppadding);
}

$get_month_details = $sel_month;
$firstDayOfYear = "01-01-".$get_month_details;
$lastDayOfYear = "31-12-".$get_month_details;

//check the first day of the given year, whether its starting from sunday or not. if not get last sunday and go upto saturday of the given year as week 1
$first_day_of_year = date('D', strtotime($firstDayOfYear));

$week_start_date = "";
$week_last_date = "";

$week_no = "1";
$week_label = "WW $week_no";
//echo $first_day_of_year;die;
if($first_day_of_year == "Sun"){
	$week_start_date = date("d/m/Y", strtotime($firstDayOfYear));
	$week_last_date = date("d/m/Y", strtotime('next saturday', strtotime($firstDayOfYear)));
} else {
	$firstDayOfYear = date('Y/m/d', strtotime('previous sunday', strtotime($firstDayOfYear)));
	$nextSaturday = date("Y/m/d", strtotime('next saturday', strtotime($firstDayOfYear)));
	$week_start_date = $firstDayOfYear;
	$week_last_date = $nextSaturday;
}
$aWeekStartEndDates = array();
if($week_start_date != ""){
	$aWeekStartEndDates[] = array("week_start" => date('d/m/Y', strtotime($week_start_date)), "week_end" => date('d/m/Y', strtotime($week_last_date)), "week_label" => "WW $week_no");
	$week_no++;
}
$nextSaturday = date("Y/m/d", strtotime('next saturday', strtotime($firstDayOfYear)));
$nextSunday = date("Y/m/d", strtotime('next sunday', strtotime($firstDayOfYear)));
while (date('Y', strtotime($nextSaturday)) == $get_month_details) {
	$week_start = $nextSunday;
    $nextSaturday = date("Y/m/d", strtotime('+1 week', strtotime($nextSaturday)));
    $nextSunday = date("Y/m/d", strtotime('+1 week', strtotime($nextSunday)));
	$week_end = $nextSaturday;
	$aWeekStartEndDates[] = array("week_start" => date('d/m/Y', strtotime($week_start)), "week_end" => date('d/m/Y', strtotime($week_end)), "week_label" => "WW $week_no");
	$week_no++;
}
$first_day_this_month = date('m-01-Y'); // hard-coded '01' for first day
$last_day_this_month  = date('m-t-Y');

//Get the months first and last date
$aMonthsDetails = array();
for($month_no = "01"; $month_no <= 12; $month_no++){
	$first_date = $get_month_details."-".$month_no."-01";
	$month_label_name = date("M", strtotime($first_date)) ." " .substr($get_month_details,2,4);
	$aMonthsDetails[] = array("month_start_date" => date("01/m/Y", strtotime($first_date)), "month_last_date" => date("t/m/Y", strtotime($first_date)), "month_label_name" => $month_label_name);
	
}
$final_results = array("month_data" => $aMonthsDetails, "week_data" => $aWeekStartEndDates,"chart_data" => $aChartData);
$json_chart_data = json_encode($final_results);
?>
<script type="text/javascript" src="../wp-content/plugins/spider-event-calendar/fusionjs/fusioncharts.js"></script>
<script type="text/javascript" src="../wp-content/plugins/spider-event-calendar/fusionjs/fusioncharts.theme.fint.js?cacheBust=56"></script>
<script type="text/javascript">

	FusionCharts.ready(function(){
		var final_data = JSON.parse(JSON.stringify(<?php echo $json_chart_data; ?>));
		console.log(final_data);
		var json_chart_data = final_data['chart_data'];
		var json_month_data = final_data['month_data'];
		var json_week_data = final_data['week_data'];
		
		
		var response_count = json_chart_data.length;
		var aCategories = [];
		var aTasks = [];
		
		var i = 1;
		//console.log("135::response_count::"+response_count); return false;
		//framing data
		if(response_count > 0){
			for (chart_id in json_chart_data) {
				var cat_id = json_chart_data[chart_id]['id'];
				aTasks.push({
					label: json_chart_data[chart_id]['event_title'],
					processid:  cat_id,
					start:  json_chart_data[chart_id]['event_start_date'],
					end:  json_chart_data[chart_id]['event_end_date'],
					id:   json_chart_data[chart_id]['graph_cat_id'],
					//color: "#008ee4",
					color:"#8FE0F2",
					height: "4%",
					toppadding: json_chart_data[chart_id]['chart_toppadding']
				});
				var cat_exist_flag = in_array(aCategories, cat_id);
				//decoding &amp to &
				var encodedStr = json_chart_data[chart_id]['cat_name'];
				var parser = new DOMParser;
				var dom = parser.parseFromString(
				'<!doctype html><body>' + encodedStr,
				'text/html');
				var decodedString = dom.body.textContent;
				if(cat_exist_flag == true){
					aCategories.push({
						label: decodedString, 
						id:  cat_id
					});
				} 
				i++;
			}
		}
		
		//framing months
		var aMonthData = [];
		var month_count = json_month_data.length;
		if(month_count > 0){
			for (month_key in json_month_data) {
				aMonthData.push({
					start:  json_month_data[month_key]['month_start_date'],
					end:  json_month_data[month_key]['month_last_date'],
					label: json_month_data[month_key]['month_label_name']
				});
			}
		}
		
		//framing month week
		var aMonthWeekData = [];
		var week_count = json_week_data.length;
		if(week_count > 0){
			for (week_key in json_week_data) {
				//console.log(json_week_data[week_key]['week_label']);return false;
				aMonthWeekData.push({
					start:  json_week_data[week_key]['week_start'],
					end:  json_week_data[week_key]['week_end'],
					label: json_week_data[week_key]['week_label']
				});
			}
		}
		
		//if(response_count > 0){
			var fusioncharts = new FusionCharts({
				type: 'gantt',
				renderAt: 'chart-container',
				width: '1050',
				height: '700',
				dataFormat: 'json',
				dataSource: {
					"chart": {
						"caption": "Campaign Plans",
						"dateformat": "dd/mm/yyyy",
						"outputdateformat": "ddds mns yy",
						"ganttwidthpercent": "60",
						"ganttPaneDuration": "40",
						"ganttPaneDurationUnit": "d",
						"plottooltext": "$processName{br}$label starting date $start{br}$label ending date $end",
						"legendBorderAlpha": "0",
						"legendShadow": "0",
						"usePlotGradientColor": "0",
						"showCanvasBorder": "0",
						"flatScrollBars": "1",
						"gridbordercolor": "#333333",
						"gridborderalpha": "20",
						"slackFillColor": "#e44a00",
						"taskBarFillMix": "light+0",
						"exportEnabled": "1",
						"exportFormats": "PNG=Export as High Quality Image|PDF=Export as Printable",
					},
					"categories": [{
						"bgcolor": "#0071C5",
						"align": "middle",
						"fontcolor": "#ffffff",
						"fontsize": "12",
						"category": aMonthData
					}, {
						"bgcolor": "#ffffff",
						"fontcolor": "#333333",
						"fontsize": "11",
						"align": "center",
						"category": aMonthWeekData
					}],
					"processes": {
						"headertext": "Task",
						"fontcolor": "#000000",
						"fontsize": "11",
						"isanimated": "1",
						//"bgcolor": "#6baa01",
						"bgcolor": "#000",
						"headervalign": "bottom",
						"headeralign": "left",
						"headerbgcolor": "#999999",
						"headerfontcolor": "#ffffff",
						"headerfontsize": "12",
						"align": "left",
						"isbold": "1",
						"bgalpha": "25",
						"process": aCategories
					},
					"tasks": {
						"showlabels": "0",
						"task": aTasks
					}
				}
			});
			//console.log(fusioncharts.args.dataSource.tasks);
			fusioncharts.render();
		//}
	});
	
	function in_array(categories_array, cat_id) {
		if(categories_array.length > 0) {
			for(var i=0;i<categories_array.length;i++) {
				if(categories_array[i].id === cat_id){
					return false;
				}
			}
			return true;
		}
		return true;
	}
</script>

<form action="" method="GET" name="sel_years" > 
	<select name="sel_gant_year" style="width:25%;height:40px;padding:5px;margin-left:757px;" onchange="this.form.submit();">
		<option value="2018" <?php if($sel_month == '2018') { echo "selected"; } ?>>2018</option>
		<option value="2019" <?php if($sel_month == '2019') { echo "selected"; } ?>>2019</option>
		<option value="2020" <?php if($sel_month == '2020') { echo "selected"; } ?>>2020</option>
		<option value="2021" <?php if($sel_month == '2021') { echo "selected"; } ?>>2021</option>
		<option value="2022" <?php if($sel_month == '2022') { echo "selected"; } ?>>2022</option>
		<option value="2023" <?php if($sel_month == '2023') { echo "selected"; } ?>>2023</option>
		<option value="2024" <?php if($sel_month == '2024') { echo "selected"; } ?>>2024</option>
		<option value="2025" <?php if($sel_month == '2025') { echo "selected"; } ?>>2025</option>
		<option value="2026" <?php if($sel_month == '2026') { echo "selected"; } ?>>2026</option>
		<option value="2027" <?php if($sel_month == '2027') { echo "selected"; } ?>>2027</option>
		<option value="2028" <?php if($sel_month == '2028') { echo "selected"; } ?>>2028</option>
		<option value="2029" <?php if($sel_month == '2029') { echo "selected"; } ?>>2029</option>
		<option value="2030" <?php if($sel_month == '2030') { echo "selected"; } ?>>2030</option>
		
	</select>
</form>
<div id="chart-container">FusionCharts XT will load here!<?php echo $get_month_details;?></div>	
<?php }
add_action( 'wp', 'elegance_gantt_init' );
/**
 * Creates a templated slide
 **/
function createTemplatedSlide(PHPPowerPoint $objPHPPowerPoint)
{
	// Create slide
	$slide = $objPHPPowerPoint->createSlide();
	
	// Add background image
	$shape = $slide->createDrawingShape();
	$shape->setName('Background');
	$shape->setDescription('Background');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/images/intel_background.png');
	$shape->setWidth(950);
	$shape->setHeight(720);
	$shape->setOffsetX(0);
	$shape->setOffsetY(0);
	// Return slide
	return $slide;
}
	
function createTemplatedSlide2(PHPPowerPoint $objPHPPowerPoint)
{
	// Create slide
	$slide = $objPHPPowerPoint->createSlide();
	
	// Add background image
	$shape = $slide->createDrawingShape();
	$shape->setName('Background');
	$shape->setDescription('Background');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/images/home.png');
	$shape->setWidth(950);
	$shape->setHeight(720);
	$shape->setOffsetX(0);
	$shape->setOffsetY(0);
	
	// Return slide
	return $slide;
}

function createTemplatedSlide3(PHPPowerPoint $objPHPPowerPoint,$res)
{
    $slide = $objPHPPowerPoint->createSlide();
	if($res == '')
	{
	$shape = $slide->createDrawingShape();
	$shape->setName('Background');
	$shape->setDescription('Background');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/images/intel_background.png');
	$shape->setWidth(950);
	$shape->setHeight(720);
	$shape->setOffsetX(0);
	$shape->setOffsetY(0);	
	
	$shape = $slide->createDrawingShape();
	$shape->setName('Media');
	$shape->setDescription('Media');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/images/no_image.png');
	$shape->setWidth(50);
	$shape->setHeight(250);
	$shape->setOffsetX(10);
	$shape->setOffsetY(140);
	}
	else
	{
	$shape = $slide->createDrawingShape();
	$shape->setName('Background');
	$shape->setDescription('Background');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/images/intel_background.png');
	$shape->setWidth(950);
	$shape->setHeight(720);
	$shape->setOffsetX(0);
	$shape->setOffsetY(0);	
	
	$shape = $slide->createDrawingShape();
	$shape->setName('Media');
	$shape->setDescription('Media');
	$shape->setPath('./wp-content/plugins/spider-event-calendar/attachments/content_piece/'.$res);
	$shape->setWidth(50);
	$shape->setHeight(250);
	$shape->setOffsetX(10);
	$shape->setOffsetY(140);
	}
    return $slide;
}	


//Add Events page on activation:
function install_events_pg(){
        $new_page_title = 'Campaign View';
        $new_page_content = '';
        $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code below, unless you know what you're doing
        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }
}//end install_events_pg function to add page to wp on plugin activation

register_activation_hook(__FILE__, 'install_events_pg');

function install_manage_events_pg(){
        $new_page_title = 'Manage Campaign';
        $new_page_content = '';
        $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code below, unless you know what you're doing
        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }
}//end install_events_pg function to add page to wp on plugin activation

register_activation_hook(__FILE__, 'install_manage_events_pg');

function install_chart_events_pg(){
        $new_page_title = 'Chart';
        $new_page_content = '';
        $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code below, unless you know what you're doing
        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }
}//end install_events_pg function to add page to wp on plugin activation

register_activation_hook(__FILE__, 'install_chart_events_pg');

function install_calendar_events_pg(){
        $new_page_title = 'Marcom Plan Calendar';
        $new_page_content = '[Spider_Calendar id="1" theme="13" default="month" select="month,list,week,day,"]';
        $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code below, unless you know what you're doing
        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_author' => 1,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }
}//end install_events_pg function to add page to wp on plugin activation

register_activation_hook(__FILE__, 'install_calendar_events_pg');
?>
