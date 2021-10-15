<?php
/**
 * Plugin Name: EPFL Restauration
 * Description: provides a shortcode to display cafeterias and restaurant menus offers
 * Version: 0.3
 * Author: Lucien Chaboudez
 * Contributors:
 * License: Copyright (c) 2019 Ecole Polytechnique Federale de Lausanne, Switzerland
 **/

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
    $remote_url_menus = $ini_array_sections['remote_url']['remote_url_menus'] . $selected_date;

    // Create a stream
    $cred = sprintf('Authorization: Basic %s',
        base64_encode($ini_array_sections['remote_url']['username_menus'] . ':' . $ini_array_sections['remote_url']['password_menus']));
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
    $context = stream_context_create($opts);
    $menus_file = file_get_contents($remote_url_menus, false, $context);

    // Decodes JSON's file
    $restaurants = json_decode($menus_file,true);
    //$restaurants = array_values(array_unique($restaurants, SORT_REGULAR));

    /* If we want schedule list */
    if($type=='schedule')
    {
        /* Prod */
        //$url = 'https://menus.epfl.ch/cgi-bin/getHoraire?'. $params;
        /* uncomment following line to access test environment */
//        $url = 'https://test-menus.epfl.ch/cgi-bin/getHoraire?'. $params;
/*
        // A utiliser peut-être
        $response = wp_remote_get($url);

        if(is_array($response))
        {
          return $response['body'];
        }*/

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

