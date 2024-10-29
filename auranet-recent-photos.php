<?php
/*
Plugin Name: Auranet Recent Photos 2.0
Plugin URI: https://www.auranet.com.br/downloads/plugins/auranet-recent-photos.zip
Description: Auranet Recent Photos exibe as últimas fotos de um usuário do Instagram previamente configuradas em seu site Wordpress. Use [instagram]
Author: Auranet Info
Version: 2.0
*/
define('ARPURLPLUGIN', plugin_dir_url( __FILE__ ));
add_shortcode('instagram', 'ARP_get_instagram_photos');
function ARP_get_instagram_photos(){
$user = get_option('aura_recent_photos_user');
$total_images = get_option('aura_recent_photos_total_images') == '' ? 10 : get_option('aura_recent_photos_total_images');
	$html = '<div class="img_ins"></div>';
	$html .= '<script type="text/ecmascript">
		jQuery(function($){
			var name = "'.$user.'";
			$.get("https://images"+~~(Math.random()*33)+"-focus-opensocial.googleusercontent.com/gadgets/proxy?container=none&url=https://www.instagram.com/" + name + "/", function(html) {
				if (html) {
					var regex = /_sharedData = ({.*);<\/script>/m,
					json = JSON.parse(regex.exec(html)[1]),
					edges = json.entry_data.ProfilePage[0].graphql.user.edge_owner_to_timeline_media.edges;
				  	$.each(edges, function(n, edge) {
					   if (n < '.$total_images.'){
						 var node = edge.node;
						$(".img_ins").append(\'<a href="https://instagr.am/p/\'+node.shortcode+\'" target="_blank"><img alt="\'+node.thumbnail_src+\'" src="\'+node.thumbnail_src+\'"></a>\');
					   }
				  });
				}
			});
		});	
		</script>';
		add_action('wp_footer', function() use( $html ){
    });		
	echo $html;		
}
add_action('admin_init', 'ARP_reg_function' );
add_action('init', 'ARP_lang_init');
function ARP_lang_init() {
    $path = dirname(plugin_basename( __FILE__ )) . '/languages/';
    $loaded = load_plugin_textdomain( 'aurarecentphotos', false, $path);
    if (isset($_GET['page']) == basename(__FILE__) && !$loaded) {          
        $msg = '<div class="error">Idioma: ' . esc_html(__('Não é possível localizar o arquivo: ' . $path, 'aurarecentphotos')) . '</div>';
        return $msg;
    } 
} 
add_action('admin_menu', 'ARP_page_menu');
function ARP_page_menu() {
	$page =	add_options_page('Aura Fotos Recentes', 'Aura Fotos Recentes', 'manage_options', 'ARP_recent_photos_menu', 'ARP_recent_photos_options_page') ;
}
function ARP_reg_function() {
	register_setting('aura-settings-group-arp', 'aura_recent_photos_backcolor' );
	register_setting('aura-settings-group-arp', 'aura_recent_photos_user' );
	register_setting('aura-settings-group-arp', 'aura_recent_photos_total_images');
	register_setting('aura-settings-group-arp', 'aura_recent_photos_img_width' );
	register_setting('aura-settings-group-arp', 'aura_recent_photos_img_border' );
	register_setting('aura-settings-group-arp', 'aura_recent_photos_border_color' );
	register_setting('aura-settings-group-arp', 'aura_recent_photos_img_space' );
}
function ARP_recent_photos_options_page(){?>
<form method="post" action="options.php">
    <?php settings_fields('aura-settings-group-arp' ); ?>
<div class="wrap">
<div id="icon-tools" class="icon32"><br /></div>
<h2>Aura Fotos Recentes</h2>
<hr>
<h3><?php echo esc_html(__('Configurações Gerais', 'aurarecentphotos'));?></h3>
 <table width="200" class="form-table">
   <tr valign="top"><?php echo esc_html(__('Copie e cole [instagram] na página que deseja mostrar suas fotos recentes do Instagram'));?>
  <td width="100" scope="row"><div align="left"><?php echo esc_html(__('Usuário', 'aurarecentphotos'));?></div></td>
  <td width="441" align="left"><input type="text" name="aura_recent_photos_user" value="<?php echo get_option('aura_recent_photos_user')?>"  />
    <i><?php echo esc_html(__('Digite o nome do usuário no Instagram', 'aurarecentphotos'));?></i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Quatidade de imagens', 'aurarecentphotos'));?></td>
  <td align="left">
     <?php $total_images = get_option('aura_recent_photos_total_images') == '' ? 10 : get_option('aura_recent_photos_total_images')?>
  <input type="number" min="1" max="25" step="1" name="aura_recent_photos_total_images" value="<?php echo $total_images ?>" class="slider" id="border">
     <i>(<?php echo esc_html(__('Padrão 10 imagens', 'aurarecentphotos'))?>)</i></td>
</tr>
  <tr valign="top">
  <td width="100" scope="row"><div align="left"><?php echo esc_html(__('Cor do Fundo', 'aurarecentphotos'));?></div></td>
  <td width="441" align="left"><input type="text" class="color" name="aura_recent_photos_backcolor" value="<?php echo get_option('aura_recent_photos_backcolor')?>"  />
    <i><?php echo esc_html(__('Clique para selecionar a cor', 'aurarecentphotos'));?></i></td>
</tr>

<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Cor da Borda', 'aurarecentphotos'));?></td>
  <td align="left">
  <input type="text" class="color" name="aura_recent_photos_border_color" value="<?php echo get_option('aura_recent_photos_border_color')?>"  />
  <i><?php echo esc_html(__('Clique para selecionar a cor', 'aurarecentphotos'));?></i>
  </td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Espessura da Borda', 'aurarecentphotos'));?></td>
  <td align="left">
     <?php echo 'Borda '.get_option('aura_recent_photos_img_border'); $bordersize = get_option('aura_recent_photos_img_border') == '' ? 2 : get_option('aura_recent_photos_img_border')?>
  0 <input type="range" min="0" max="50" value="<?php echo $bordersize?>" class="slider" id="border"><span id="val_border"><?php echo $bordersize ?></span>
  <input type="hidden" align="absmiddle" name="aura_recent_photos_img_border" value="<?php echo $bordersize ;?>"  />
     <i>(<?php echo esc_html(__('Padrão 2px', 'aurarecentphotos'))?>)</i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Largura das Imagens', 'aurarecentphotos'));?></td>
  <td align="left"><?php $width = get_option('aura_recent_photos_img_width') == '' ? 200 : get_option('aura_recent_photos_img_width')?>
  50 <input type="range" align="absmiddle" min="50" max="400" value="<?php echo $width?>" class="slider" id="width"><span id="val_width"><?php echo $width?></span>
  <input type="hidden" name="aura_recent_photos_img_width" value="<?php echo $width;?>"  />
     <i>(<?php echo esc_html(__('Padrão 200px', 'aurarecentphotos'))?>)</i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Espaço entre imagens', 'aurarecentphotos'));?></td>
  <td align="left">
     <?php $image_space = get_option('aura_recent_photos_img_space') == '' ? 2 : get_option('aura_recent_photos_img_space')?>
  0 <input type="range" min="0" max="50" value="<?php echo $image_space?>" class="slider" id="space"><span id="val_space"><?php echo $image_space ?></span>
  <input type="hidden" align="absmiddle" name="aura_recent_photos_img_space" value="<?php echo $image_space ;?>"  />
     <i>(<?php echo esc_html(__('Padrão 2px', 'aurarecentphotos'))?>)</i></td>
</tr>
 </table>
    <p class="submit">
    <input type="submit" class="button" value="<?php echo esc_html(__('Salvar Alterações', 'aurarecentphotos')); ?>" />
    </p>
</div>
</form><?php
}	
function ARP_style_and_script_admin_outher($hook) {
     wp_enqueue_script( 'script-jscolor', ARPURLPLUGIN . 'js/jscolor/jscolor.js', array(), '1.0.0', true );
	 wp_enqueue_script( 'script-aura-arp', ARPURLPLUGIN . 'js/arp_script.js', array(), '1.0.0', true );
	 wp_enqueue_style( 'style-aura-arp', ARPURLPLUGIN . 'css/arp_style.css', array());
}
add_action( 'admin_enqueue_scripts', 'arp_style_and_script_admin_outher' );

function ARP_styles_thumb() {
	$width = get_option('aura_recent_photos_img_width')==''? 200 : get_option('aura_recent_photos_img_width');
	$bgcolor = get_option('aura_recent_photos_backcolor')==''? 'fff' : get_option('aura_recent_photos_backcolor');
	$border_height = get_option('aura_recent_photos_img_border')==''? 2 : get_option('aura_recent_photos_img_border');
	$border_color = get_option('aura_recent_photos_border_color')==''? 'ccc' : get_option('aura_recent_photos_border_color');
	$image_space = get_option('aura_recent_photos_img_space')==''? 2 : get_option('aura_recent_photos_img_space');
	$user = get_option('aura_recent_photos_user')==''? '' : get_option('aura_recent_photos_user');
    wp_enqueue_style(
        'arp-aura-style',
        plugin_dir_url((__FILE__)) . 'css/arp_style.css'
    );
    $custom_css = '.img_ins img{padding:'. $image_space.'px;background-color:#'.$bgcolor.'; float:left; margin:10px; border:'. $border_height.'px #'.$border_color.' solid; width:'. $width.'px}';
    wp_add_inline_style( 'arp-aura-style', $custom_css );
}
add_action( 'wp_footer', 'ARP_styles_thumb');