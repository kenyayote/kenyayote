<?php
/*
 * Plugin Name: 404 error solution
 * Plugin URI: http://kenyayote.com
 * Description: This plugin determines if the request will cause a 404 HTTP error and redirects to the page or post  you want. 
 * Version: 1.2
 * Author: Elvis Nyakreal
 * Author URI: http://kenyayote.com
 */

load_plugin_textdomain( 'fzf', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

add_action('admin_init', 'fourzerofour_register_settings');
add_action('admin_menu', 'fourzerofour_add_menu');
add_action('wp','determine_if_fourzerofour'); 
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );

function fourzerofour_add_menu(){
    //Add menu page
    add_options_page(__('404 Simple Redirect','fzf'), __('404 Redirect','fzf'), 'manage_options', 'fourzerofour', 'fourzerofour_render_options');
}

function fourzerofour_register_settings(){
    register_setting("fourzerofour_options_group", 'fourzerofour_options_group', 'fourzerofour_options_validate');
    add_settings_section('fourzerofour_main', __('404 error solution Options','fzf'), 'fourzerofour_section_text', 'fourzerofour');
    add_settings_field('fourzerofour_url', __('URL to redirect','fzf'), 'fourzerofour_setting_string', 'fourzerofour', 'fourzerofour_main');
}

function fourzerofour_options_validate($input){
    //Autocomplete URL, just in case
    $url_redirect = $input['fourzerofour_url'];
	if(empty($url_redirect)) return $url_redirect;
    if(strpos($url_redirect,'http://')===false){
        $url_redirect = 'http://'.$url_redirect;
    }
    $validated['fourzerofour_url'] = $url_redirect;
    return $validated;
}

function fourzerofour_section_text(){
    ?>
    <p><?php _e('This plugin hooks the normal Wordpress workflow in order to determine if the request is processing will cause a 404 HTTP error.<br/>In that case it prevents Wordpress to do any other processing and sends the user to the page defined in the plugin options.','fzf'); ?></p>
    <?php
}

function fourzerofour_setting_string(){
    $options = get_option('fourzerofour_options_group');
    echo "<input id='plugin_text_string' name='fourzerofour_options_group[fourzerofour_url]' size='80' type='text' value='{$options['fourzerofour_url']}' /> <p class='howto'>".__('Note: If this option is left empty the plugin will redirect user to homepage.','fzf')."</p>";
}

function fourzerofour_render_options(){
	?>
	<div class="wrap">
   <form action="options.php" method="post">
        <?php settings_fields('fourzerofour_options_group'); ?>
        <?php do_settings_sections( 'fourzerofour' ); ?>
        <p class="submit"><input type="submit" value="<?php _e('Save Plugin Options','fzf'); ?>" title="<?php _e('Save Plugin Options','fzf'); ?>" class="button-primary"></p>
    </form>
    </div>
	<?php
    
}

function determine_if_fourzerofour(&$arr){
    global $wp_query;
    
    if($wp_query->is_404){
        
        $options = get_option('fourzerofour_options_group');
        if(!empty($options['fourzerofour_url'])){
            $url_redirect = $options['fourzerofour_url'];
        }else{
            //By default redirect to home
            $url_redirect = site_url();
        }
        
        header('Location: '.$url_redirect);
        die;
    }
}

function add_settings_link($links, $file) {
    static $this_plugin;
    if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

    if ($file == $this_plugin){
    $settings_link = '<a href="options-general.php?page=fourzerofour.php">Settings</a>';
    array_unshift($links, $settings_link);
    }
    return $links;
 }
?>
