<?php
global $wpdb;
$wpdb->flush();
$btn_retour="";
	$id_uid = "";
	$id_mid = "";
	$id_name = "";
	$id_descr = "";
	$id_store = "";
	$id_price = "";
	$id_ucode = "";
	$id_itype = "";
	$id_color = "";
	$debus=false;
	$debut=false;
	$hidden="<input type='hidden' name='insert' value=''/>";

	$url=WPEPP_PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . '?page='.WPEPP_PN_URL;
	$burl=$url;
if (isset($_GET['edit']) and $_GET['edit'] != "")
{
	$re_ = $wpdb->get_row("SELECT * FROM ".WPEPP_TABLE." WHERE id='".intval($_GET['edit'])."';");
	$id_uid = $re_->uid;
	$id_mid = $re_->mid;
	$id_price = $re_->price;
	$id_name = $re_->name;
	$id_descr = $re_->description;
	$id_store = $re_->store;
	$id_ucode = $re_->ucode;
	$id_itype = $re_->itype;
	$id_color = $re_->color;
	$btn_action = __( 'Save changes', WPEPP_PLUGIN_NAME );
	$titre_box = __( 'Edit product', WPEPP_PLUGIN_NAME );
	$hidden= "<input type='hidden' name='edit' value=".intval($_GET['edit']).">";
	if (http_build_query($_GET) != "")
	{
		$url_action = $url.'&act=save';
		$url_cancel= $url;
		$btn_cancel= "<input class='button-secondary' type='button' name='cancel' value='".__( 'Cancel', WPEPP_PLUGIN_NAME )."' onClick='window.location=\"".$url_cancel."\";' />";
		$btn_retour= "<input class='button-primary' type='button' name='retour' value='".__( 'Back to configuration', WPEPP_PLUGIN_NAME )."' onClick='window.location=\"".$url_cancel."\";' />";
	} else {
		$url_action = $url.'&act=save';
		$url_cancel= $url;
		$btn_cancel= "<input class='button-secondary' type='button' name='cancel' value='".__( 'Cancel', WPEPP_PLUGIN_NAME )."' onClick='window.location=\"".$url_cancel."\";' />";
	}
}
		
function CreerLiens($lien, $debut, $nbr_par_page, $id_count)
{
    echo "<center><div class='tablenav-pages'>";
    if ($id_count >= ($nbr_par_page + 1))
	{
        // liens vers pages precedentes/suivantes
        if ($debut - $nbr_par_page >= 0) {
            echo " <a href='$lien" . 0 . "'>&lt;&lt;</a>\n ";
            echo " <a href='$lien" . ($debut - $nbr_par_page) . "'>&lt;</a> ";
        } 

        $p = 1;
        for ($i = 0 ; $i < $id_count ; $i += $nbr_par_page)
		{
            if (($debut / $nbr_par_page) == ($p-1)) echo " <b>$p</b> ";
            else echo " <a href='$lien$i'>$p</a>\n ";
            $p++;
        }

        if ($debut + $nbr_par_page < $id_count)
		{
            echo " <a href='$lien" . ($debut + $nbr_par_page) . "'>&gt;</a> ";
            $pos = ($id_count - ($id_count % $nbr_par_page));
            if (($id_count % $nbr_par_page) == 0) $pos = $pos - $nbr_par_page;
            echo " <a href='$lien$pos'>&gt;&gt;</a>\n ";
        } 
        echo "\n<br>";
    } 
    echo "</div></center>";
}

$lien  = $url.'&debut=';
$lien2 = $url.'&debus=';
	
$linkfr  = $url.'&lang=es';
$linken  = $url.'&lang=en';
		
$nbr_par_page = 10;
$id_count = 0;
?>
<div class='wrap'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" valign="middle"><div id="icon-options-general" class="icon32"></div>
    <h2><?php _e( 'WP e-Payouts', WPEPP_PLUGIN_NAME ) ?></h2></td>
  </tr>
<?php if ($btn_retour!="") { ?>
  <tr>
    <td colspan="2" valign="middle">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" valign="middle"><?php echo $btn_retour ?></td>
  </tr>
<?php } ?>  
<?php if (!isset($_GET["stats"])) { ?> 
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="534">
	<div align="left">
	<form method=post action="<?php echo $url_action?$url_action:$burl; ?>"><?php echo $hidden ?>
	<table width="534" class="widefat">
	<thead>
      <tr>
        <th><div align="left"><strong><?php echo $titre_box ?></strong></div></th>
        <th><div align="right">&nbsp;</div></th>
      </tr>
    </thead>
	<tbody>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Account ID (uid)', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type=text name=id_uid value="<?php echo sanitize_text_field($id_uid); ?>" style='width:200px' <?php echo $disabled ?>></div></td>
      </tr>	
      <tr>
        <td width="25%"><div align="left"><?php _e( 'Module ID (mid)', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type=text name=id_mid value="<?php echo sanitize_text_field($id_mid); ?>" style='width:200px' <?php echo $disabled ?>></div></td>
      </tr>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Product name', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type="text" name="id_name" style="width:200px;" <?php echo $disabled ?> value="<?php echo sanitize_text_field($id_name); ?>"/></div></td>
      </tr>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Product description', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><textarea name="id_description" style="width:200px;" <?php echo $disabled ?>><?php echo sanitize_text_field($id_descr); ?></textarea></div></td>
      </tr> 

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Product price', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type=text name=id_price value="<?php echo sanitize_text_field($id_price); ?>" style='width:200px' <?php echo $disabled ?>></div></td>
      </tr>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Product code', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type=text name=id_ucode value="<?php echo sanitize_text_field($id_ucode); ?>" style='width:200px' <?php echo $disabled ?>></div></td>
      </tr>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Store name', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><input type="text" name="id_store" value="<?php echo sanitize_text_field($id_store); ?>" style='width:200px' <?php echo $disabled ?>></div></td>
      </tr>

      <tr>
        <td width="25%"><div align="left"><?php _e( 'Button type', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><select name=id_itype>
			<option value="1"<?=$id_itype==1?" selected=\"selected\"":""?>><?php _e( 'Embebed', WPEPP_PLUGIN_NAME ) ?></option>
			<option value="2"<?=$id_itype==2?" selected=\"selected\"":""?>><?php _e( 'LightBox', WPEPP_PLUGIN_NAME ) ?></option>
			<option value="3"<?=$id_itype==3?" selected=\"selected\"":""?>><?php _e( 'New page', WPEPP_PLUGIN_NAME ) ?></option>
		</select></div></td>
      </tr>
      <tr>
        <td width="25%"><div align="left"><?php _e( 'Color', WPEPP_PLUGIN_NAME ) ?>: </div></td>
        <td width="75%"><div align="left"><select name=id_color>
			<option value="1"<?=$id_color==1?" selected=\"selected\"":""?>><?php _e( 'Green', WPEPP_PLUGIN_NAME ) ?></option>
			<option value="2"<?=$id_color==2?" selected=\"selected\"":""?>><?php _e( 'Red', WPEPP_PLUGIN_NAME ) ?></option>
			<option value="3"<?=$id_color==3?" selected=\"selected\"":""?>><?php _e( 'Black', WPEPP_PLUGIN_NAME ) ?></option>
		</select></div></td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left"><input class=button-primary type=submit value="<?php echo $btn_action ?>"><?php echo $btn_cancel ?></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
	<tr>
		<td colspan="2"><em><?php _e( 'Help: support@e-payouts.com', WPEPP_PLUGIN_NAME ) ?></em></td>
	</tr>
    </tbody>  
    </table>
	</form>
	</div></td>
    <td><div align="left"></div></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
<?php
	}
?>		
	  		<tr>
			  <td colspan="6"><?php CreerLiens($lien2, $debus, $nbr_par_page, $id_count); ?></td>
			</tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	<div align="left">
	<form method=post>
	  <table class="widefat" width="100%">
	  <thead>
	    <tr>
          <th colspan="7"><div align="left"><strong><?php _e( 'Your products list', WPEPP_PLUGIN_NAME ) ?></strong></div></th>
        </tr>
        <tr>
          <th width="160"><?php _e( 'Account ID', WPEPP_PLUGIN_NAME ) ?></th>
          <th><?php _e( 'Module ID', WPEPP_PLUGIN_NAME ) ?></th>
          <th><?php _e( 'Name', WPEPP_PLUGIN_NAME ) ?></th>
          <th><?php _e( 'Price', WPEPP_PLUGIN_NAME ) ?></th>
          <th><?php _e( 'Code', WPEPP_PLUGIN_NAME ) ?></th>
          <th width="50">&nbsp;</th>
        </tr>
	  </thead>
	  <tbody>
	  <?php
		$wpdb->flush();
		$id_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM ".WPEPP_TABLE." ORDER BY id" , 'DontDelete' ) );
		if ($id_count > 0)
		{
		
			$row = $wpdb->get_results("SELECT * FROM ".WPEPP_TABLE." LIMIT ".intval($debut).",".intval($nbr_par_page)."");
			foreach ($row as $row) 
			{
?>
			<tr>
			  <td><div align="left"><?php echo $row->uid ?></div></td>
			  <td><div align="left"><?php echo $row->mid ?></div></td>
			  <td><div align="left"><?php echo $row->name ?></div></td>
			  <td><div align="right"><?php echo $row->price ?></div></td>
			  <td><div align="left"><?php echo $row->ucode ?></div></td>
			  <td width="250"><div align="left"><code><input type='text' value='<?php echo '[epayouts id="'.intval($row->id).'"] ... [/epayouts]' ?>' readonly='readonly' style='width:240px'/></code></div></td>
			  <td width="50">
				  <a href="<?php echo $url."&edit=".$row->id ?>" title="<?php _e( 'Edit', WPEPP_PLUGIN_NAME ) ?>"><?php _e( 'Edit', WPEPP_PLUGIN_NAME ) ?></a>
				  <a href="<?php echo $url."&del=".$row->id ?>" title="<?php _e( 'Delete', WPEPP_PLUGIN_NAME ) ?>"><?php _e( 'Delete', WPEPP_PLUGIN_NAME ) ?></a>
				</td>
			</tr>
<?php 
			}
		} else {
?>
			<tr>
			  <td colspan="7"><div align="center"><br><?php _e( 'You have not yet added a product.', WPEPP_PLUGIN_NAME ) ?><br>&nbsp;</div></td>
			</tr>
<?php
	    }
?>		
	  		<tr>
			  <td colspan="7"><?php CreerLiens($lien, $debut, $nbr_par_page, $id_count); ?></td>
			</tr>
	  </tbody>
      </table>
	</form>
	</div>	</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
</div>
