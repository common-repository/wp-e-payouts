<?php
/*
@wordpress-plugin
Plugin Name: WP e-Payouts
Author URI:  http://www.e-payouts.com
Description: The Wp e-Payouts Plugin (WPEPP) is a free plugin that allows you to integrate payments
Author: e-Payouts
Version: 1.1
License: GNU General Public License v2
Text Domain: wp-epayouts
*/
$sprotocol=isset($_SERVER["REQUEST_SCHEME"])?$_SERVER["REQUEST_SCHEME"]:((isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"]=="on")?"https":"http");

define("WPEPP_URL","https://paymentbox.e-payouts.com/");
define("WPEPP_PLUGIN_NAME","wp-epayouts");
define("WPEPP_PN_URL","Wp-Epayouts");
define("WPEPP_TABLE","wp_epayouts");
define("WPEPP_PROTOCOL","$sprotocol://");

global $wpdb;

register_activation_hook(__FILE__,'WPEPP_init');

// Manage user language
function WPEPP_lang_init() 
{
	$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/' ;
	load_plugin_textdomain( WPEPP_PLUGIN_NAME, false, $lang_dir );
}
add_action('plugins_loaded', 'WPEPP_lang_init');

$lang = "es";
$b_lang = get_bloginfo('language');
if ( $b_lang == 'en-EN' ) $lang = "en";

if (isset($_GET["data"]) and $_GET["data"]!="") $_GET["DATAS"] = sanitize_text_field($_GET["data"]);
if (isset($_GET["codes"]) and $_GET["codes"]!="") $_GET["RECALL"] = sanitize_text_field($_GET["codes"]);
if (isset($_GET["code"]) and $_GET["code"]!="") $_GET["RECALL"] = sanitize_text_field($_GET["code"]);

if (isset($_GET["data"])&&$_GET["DATAS"]!="")
{
		if (!isset($_GET["trxid"]) and !isset($_GET["transaction_id"]) )
		{
			$_GET["trxid"] = "D";
			$_GET["transaction_id"] = "D";
		}
	add_action('init', 'WPEPP_redirect', 0);
}

// Menu & Shortcode & Setting Links
function WPEPP_add_settings_link($links, $file)
{
	static $this_plugin;
	if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if( $file == $this_plugin )
	{
		$settings_link = '<a href="options-general.php?page=Wp-Epayouts">'.__( 'Configuration', WPEPP_PLUGIN_NAME ).'</a>';
		$links = array_merge(array($settings_link), $links);
	}
	return $links;
}
add_filter('plugin_action_links', 'WPEPP_add_settings_link', 10, 2);

add_action('admin_menu', 'WPEPP_Admin' );
add_shortcode('epayouts', 'WPEPP_LCK_EP');

function WPEPP_redirect()
{
	$tmp = explode('::',$_GET["DATAS"]);
	$PAGE    = $tmp[0];
	$SECTION = $tmp[1];
	if (isset($_GET["RECALL"]) and $_GET["RECALL"]!="")
	{
		setcookie("POST_".$PAGE."_".$SECTION,sanitize_text_field($_GET["RECALL"]),time()+3600*24,"/");
		header( "Location: ".get_bloginfo('wpurl')."/?p=$PAGE&s=$SECTION&ok=".sanitize_text_field($_GET["RECALL"]) );
	} else {
		header( "Location: ".get_bloginfo('wpurl')."/?p=$PAGE&s=$SECTION" );
	}
	exit();
}

function WPEPP_init()
{
	global $wpdb;
	$re_ = $wpdb->get_var("SELECT id FROM ".WPEPP_TABLE." LIMIT 1;");
	if ($re_ == "" or !$re_)
	{
		$sql = "CREATE TABLE ".WPEPP_TABLE."
				(
					id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
					uid INTEGER NOT NULL DEFAULT '0',
					mid INTEGER NOT NULL DEFAULT '0', 
					price DECIMAL(10,2) NOT NULL DEFAULT '0',
					itype INTEGER NOT NULL DEFAULT '1',
					color INTEGER NOT NULL DEFAULT '1',
					ucode VARCHAR(32) NOT NULL DEFAULT '',
					store VARCHAR(64) NOT NULL DEFAULT '',
					name VARCHAR(64) NOT NULL DEFAULT '',
					description TEXT NOT NULL DEFAULT ''
				);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

function WPEPP_LCK_EP($atts, $content)
{
	$lang = "es";
	$b_lang = get_bloginfo('language');
	if ( $b_lang == 'en-EN' ) $lang = "en";
		
	global $wpdb;
	$wpdb->flush();
	extract(shortcode_atts(array('id' => '0'), $atts));
	$page  = get_the_ID();
	
	$re_ = $wpdb->get_row("SELECT * FROM ".WPEPP_TABLE." WHERE id='".intval($id)."';");
	$uid = $re_->uid;
	$mid = $re_->mid;
	$price = $re_->price;
	$itype = $re_->itype;
	$extra = urlencode($re_->ucode);
	$color = $re_->color;
	$name = urlencode($re_->name);
	$descr = urlencode($re_->description);
	$store = urlencode($re_->store);
	$color=$color==1?"green":($color==2?"red":"black");

	$url=WPEPP_URL."?uid=$uid&mid=$mid&price=$price&ucode=$extra&name=$name&description=$descr&title=$store";
	if($itype==1) $r='<!-- Payment Box IFRAME -->
<!-- Add the code below into the <body> section on your website -->
<div style="position:relative; padding-bottom:56.25%; padding-top:25px; height:0; min-height: 400px;">
<iframe src="'.$url.'" allowtransparency="yes" width="100%" height="100%" style="border:0; position:absolute; top:0; left:0; width:100%; height:100%; min-height: 400px; overflow:auto"></iframe>
</div>';
	elseif($itype==2)
	{
		wp_enqueue_style("ep-lightbox-css","https://paymentbox.e-payouts.com/css/ep-lightbox.css");
		wp_enqueue_script("ep-lightbox-js","https://paymentbox.e-payouts.com/js/ep-lightbox.js");
		wp_add_inline_script("ep-inline-js",'window.onload = function() {
[].forEach.call(document.querySelectorAll(\'[ep-lightbox]\'), function(el) {
el.lightbox = new EpLightbox(el);
});
};');
		$r='<a href="'.$url.'" ep-lightbox="iframe" url="'.$url.'" title="Pay with e-Payouts"><img src="'.WPEPP_URL.'/image.php?uid='.$uid.'&mid='.$mid.'&color='.$color.'" alt="pay" /></a>';
	} else $r='<button onClick="window.open(\''.$url.'\'); return false;" style="background-color:#00a953;border:2px solid #00793A;color:#FFFFFF;font-size:20px;padding:15px 30px;">PAY</button>';

	if ($id == 0) $r = '[epayouts id="X"]'.do_shortcode($content).'[/epayouts]';
		
	if (!is_single() and !is_page() ) $r='';
	return $r;
}

function WPEPP_Admin()
{
	add_options_page('E-Payouts', 'Wp e-Payouts' , 'manage_options', 'Wp-Epayouts', 'WPEPP_EPayouts_admin');
}

function WPEPP_EPayouts_admin()
{
	$lang = "es";
	$b_lang = get_bloginfo('language');
	if ( $b_lang == 'en-EN' ) $lang = "en";
	
	global $wpdb;
	$wpdb->flush();

	if (!current_user_can('manage_options'))
	{
		wp_die( __('You do not have the necessary permissions to access this page.') );
	}

	$uid = "";
	$mid = "";
	// Add new product
	if ( isset($_POST) and !isset($_GET["act"])&&isset($_POST["insert"]))
	{
		if (isset($_POST["id_uid"]) && isset($_POST["id_mid"]) )
		{
			if ( is_numeric($_POST["id_uid"]) && is_numeric($_POST["id_mid"]) )
			{
				$id_uid = intval($_POST["id_uid"]);
				$id_mid = intval($_POST["id_mid"]);
				$id_price = floatval($_POST["id_price"]);
				$id_color = intval($_POST["id_color"]);
				$id_itype = intval($_POST["id_itype"]);
				$id_ucode = preg_replace("/[^A-Za-z0-9\-\.,_]/","",$_POST["id_ucode"]);
				$id_name = sanitize_text_field(preg_replace("/'/","",$_POST["id_name"]));
				$id_description = sanitize_text_field(preg_replace("/'/","",$_POST["id_description"]));
				$id_store = sanitize_text_field(preg_replace("/'/","",$_POST["id_store"]));
				$sql = "INSERT INTO ".WPEPP_TABLE." (uid,mid,price,color,itype,ucode,name,description,store) VALUES ('$id_uid','$id_mid','$id_price','$id_color','$id_itype','$id_ucode','$id_name','$id_description','$id_store');";
				$wpdb->query($sql);
				echo '<div id="message" class="updated highlight"><p>'.__( 'Product added! Ref:', WPEPP_PLUGIN_NAME ).' <strong>'.$id_name.' ('.$id_ucode.')</strong></p></div>';
			} else {
				echo '<div id="message" class="error"><p>'.__( 'Please enter a valid product!', WPEPP_PLUGIN_NAME ).'</p></div>';
			}
		}
	}
	// Update product infos
	if (isset($_POST) and isset($_GET["act"]) and $_GET["act"] == "save"&&isset($_POST["edit"])&&intval($_POST["edit"]))
	{
		unset($_GET["act"]);
		if (isset($_POST["id_uid"]) && isset($_POST["id_mid"]) )
		{
			if ( is_numeric($_POST["id_uid"]) && is_numeric($_POST["id_mid"]) )
			{
				$uid = 		intval($_POST["id_uid"]);
				$mid = 		intval($_POST["id_mid"]);
				$price =	floatval($_POST["id_price"]);
				$color =	intval($_POST["id_color"]);
				$itype =	intval($_POST["id_itype"]);
				$ucode =	preg_replace("/[^A-Za-z0-9\-\.,_]/","",$_POST["id_ucode"]);
				$id_name = sanitize_text_field(preg_replace("/'/","",$_POST["id_name"]));
				$id_description = sanitize_text_field(preg_replace("/'/","",$_POST["id_description"]));
				$id_store = sanitize_text_field(preg_replace("/'/","",$_POST["id_store"]));
				$sql = "UPDATE ".WPEPP_TABLE." SET uid='$uid', mid='$mid',price='$price',color='$color',itype='$itype',ucode='$ucode',name='$id_name',description='$id_description',store='$id_store' WHERE id='".intval($_POST["edit"])."';";
				$wpdb->query($sql);
				echo '<div id="message" class="updated highlight"><p>'.__( 'Product updated! Ref :', WPEPP_PLUGIN_NAME ).' <strong>'.$id_name.' ('.$ucode.')</strong></p></div>';
			} else {
				echo '<div id="message" class="error"><p>'.__( 'Please enter a valid product!', WPEPP_PLUGIN_NAME ).'</p></div>';
			}
		}
	}
	// Delete product infos
	if (isset($_GET["del"])&&intval($_GET["del"]))
	{
		$sql = "DELETE FROM ".WPEPP_TABLE." WHERE id='".intval($_GET["del"])."';";
		$wpdb->query($sql);
		echo '<div id="message" class="updated highlight"><p>'.__( 'Product Ref :', WPEPP_PLUGIN_NAME ).' <strong>'.intval($_GET["del"]).' '.__( 'deleted!', WPEPP_PLUGIN_NAME ).'</strong></p></div>';
	}
	$btn_action = __( 'Add product', WPEPP_PLUGIN_NAME );
	$url_action = "";
	$titre_box = __( 'Add product', WPEPP_PLUGIN_NAME );
	$btn_cancel ="";
	$hidden ="";
	$disabled = "";
	include_once("wp-epayouts_admin.php");
}
?>
