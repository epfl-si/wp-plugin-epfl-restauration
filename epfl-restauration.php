<?php
/**
 * Plugin Name: EPFL Restauration
 * Description: provides a shortcode to display cafeterias and restaurant menus offers
 * Version: 0.3
 * Author: Lucien Chaboudez
 * Contributors:
 * License: Copyright (c) 2019 Ecole Polytechnique Federale de Lausanne, Switzerland
 **/

// Analysis with sections
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


    /* If we want schedule list */
    if($type=='schedule')
    {
        /* Prod */
        $url = 'https://menus.epfl.ch/cgi-bin/getHoraire?'. $params;
        /* uncomment following line to access test environment */
//        $url = 'https://test-menus.epfl.ch/cgi-bin/getHoraire?'. $params;

        $response = wp_remote_get($url);

        if(is_array($response))
        {
          return $response['body'];
        }
    }
    else /* We assume $type is equal to 'menu' */
    {
        /* Prod */
        $url = 'https://menus.epfl.ch/cgi-bin/getMenus?'. $params;
        /* uncomment following line to access test environment */
        //$url = 'https://test-menus.epfl.ch/cgi-bin/getMenus?'. $params;

        /* Adding JavaScript */
        wp_enqueue_script( 'epfl_restauration_script', plugin_dir_url(__FILE__).'js/script.js' );


        ob_start();

            /* While rendering the iframe, we have to add current URL in 'name' attribute. This then will be used by JavaScript
             code in iframe content to know where to send a message to tell iframe's height. This information will be used by
             JavaScript code in 'js/script.js' to resize iframe */

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

