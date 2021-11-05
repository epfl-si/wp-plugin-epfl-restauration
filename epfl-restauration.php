<?php
/**
 * Plugin Name: EPFL Restauration
 * Description: provides a shortcode to display cafeterias and restaurant menus offers
 * Version: 1.0
 * Author: Jérémy Wolff
 * Contributors: Lucien Chaboudez, Quentin Estoppey
 * License: Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
 **/

define("REMOTE_SERVER_TIMEOUT", 10);  // time to wait until we consider the remote server out of the game
define("REMOTE_SERVER_SSL", true);  // force the server to be https certified
define("LOCAL_CACHE_NAME", 'EPFL_RESTAURATION');  // the option and transient name for the caching
define("LOCAL_CACHE_TIMEOUT", 10 * 60);  // cache time validity, in seconds

// Configuration with sections
$ini_array_sections = parse_ini_file("menus.ini", true);

function epfl_restauration_process_shortcode( $atts, $content = null ) {

    global $wp;
    global $ini_array_sections;

    $atts = shortcode_atts( array(
        'params' => '',
        'type' => 'menu', // can be 'menu' or 'schedule'
    ), $atts );

    /* We remove ? at the beginning if any */
    $params = preg_replace('/^\?/', '', $atts['params']);
    $type = sanitize_text_field($atts['type']);

    /* Including CSS file*/
    wp_enqueue_style( 'epfl_restauration_style', plugin_dir_url(__FILE__).'css/style.css', [], '2.1');

    $images_path = "/wp-content/plugins/epfl-restauration/images/";

    $vars = parse_url( $params, $component = -1 );

    parse_str($params, $params_array);

    // Selection resto_id in URL's parameter
    if(empty($params_array['resto_id'])) {
        $selected_resto_id = null;
    } else {
        $selected_resto_id = $params_array['resto_id'];
    }

    // Selection date of the menus
    if(empty($_GET['date'])) {
        $selected_date = date('Y-m-d');
    } else {
        $selected_date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
    }

    // Language settings
    // Try to get language from WordPress
    if(get_locale() == "en_US" || get_locale() == "en_GB") $params_array['lang'] = "en";
    // Language set to french if empty
    if (empty($params_array['lang']) || $params_array['lang'] == 'fr') {
        $lang = 'fr';
        switch_to_locale('fr_FR');
        $menus_categories = 'french_menus_categories';
    } else {
        $lang = 'en';
        switch_to_locale('en_US');
        $menus_categories = 'english_menus_categories';
    }

    // Connection to Nutrimenu
    if(empty(get_option("epfl_restauration_api_url")) || empty(get_option("epfl_restauration_api_username")) || empty(get_option("epfl_restauration_api_password"))){
        return error_msg('error_configuration', $lang);
    }
    $remote_url_api = get_option("epfl_restauration_api_url") . "?date=" . $selected_date;

    // Create a stream
    $cred = sprintf('Authorization: Basic %s',
        base64_encode(get_option("epfl_restauration_api_username") . ':' . get_option("epfl_restauration_api_password")));
    $opts = array(
        'http'=>array(
            'method'=>"GET",
            'header' => $cred
        ),
        /*"ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        )*/
    );
    /*
    // Use standard API for remote GET
    $context = stream_context_create($opts);
    $menus_file = file_get_contents($remote_url_api, false, $context);
    */

    $cache_key = LOCAL_CACHE_NAME . md5(serialize($remote_url_api));

    if ((defined('WP_DEBUG') && WP_DEBUG) || false === ( $menus_file = get_transient( $cache_key ) ) ) {    // local tests
//    if (false === ( $menus_file = get_transient( $cache_key ) ) ) {
        // No transient, then try to get some data if the cache is empty
        // Use WP API for remote GET
        $args = array(
            'headers' => $cred,
            'timeout' => REMOTE_SERVER_TIMEOUT,
            'sslverify' => REMOTE_SERVER_SSL
        );
        $request = wp_remote_get($remote_url_api, $args);
        $menus_file = wp_remote_retrieve_body($request);

        // Error of server not responding correctly
        if (wp_remote_retrieve_response_code($request) !== 200) {
            return error_msg('error_unavailable', $lang);
        }

        if (!empty($menus_file)) {
            // Save result in the transient cache
            set_transient($cache_key, $menus_file, LOCAL_CACHE_TIMEOUT);
        } else {
            // Nothing or empty result has been returned from the server, reset local entries
            set_transient($cache_key, [], LOCAL_CACHE_TIMEOUT);
        }

    }

    // Decodes JSON's file
    $restaurants = json_decode($menus_file,true);
    // Sort Restaurant alpha
    usort($restaurants, function($a, $b) {return strcmp($a['name'], $b['name']);});

    // Test array for error
    //$restaurants = [0=>['id'=>'43', 'name'=>"Jeremy cook","menuLines"=>[['id'=>126, 'name'=>"Jeremy's special"]]]];

    /* If we want schedule list */
    if($type == 'schedule')
    {
        ob_start();

        include('schedule.php');

        return ob_get_clean();
    }
    else /* We assume $type is equal to 'menu' */
    {
        /* Adding JavaScript */
        wp_enqueue_script( 'epfl_restauration_script', plugin_dir_url(__FILE__).'js/script.js' );

        ob_start();

        include('menus.php');

        return ob_get_clean();

    }

}

add_action( 'init', function() {
  // define the shortcode
  add_shortcode('epfl_restauration', 'epfl_restauration_process_shortcode');
});

// Web page content traduction
function trad($key_to_traduct, $lang) {
    global $ini_array_sections;

    if (!isset($lang)) {
        $lang = "fr";
    }
    return $ini_array_sections['txt_'.$lang][$key_to_traduct];
}

// Error function
function error_msg($error_message, $lang){

    $error_html = '<div class="alert alert-danger" role="alert">
        <strong>' . trad('error', $lang) . ':</strong> ' . trad($error_message, $lang) . '</div>';

    return $error_html;

}
