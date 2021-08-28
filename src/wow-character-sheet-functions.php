<?php
/**
 * This file contains functions required by WordPress along with the functions making the 
 * API calls to Blizzard. Basically any functions related to setting up or gathering data 
 * are here.
 */



/*Hooks and Functions required by WordPress*/
register_activation_hook(__FILE__, 'pdxc_wow_activation');
register_deactivation_hook(__FILE__, 'pdxc_wow_deactivation');
register_uninstall_hook(__FILE__, 'pdxc_wow_uninstall');


/**
 *  @function pdxc_wow_activation()
 * 
 * 	Activates plugin and registers plugin settings. in this case we're mostly setting up some 
 * 	constants to hold information required by the API for authentication, but also setting up 
 * 	the shortcode to allow the plugin to be displayed on a page.
 */
function pdxc_wow_activation()
{
    add_shortcode(
        'pdxc_wow_character',
        'pdxc_wow_generate'
    );

    
}

/**
 * 	@function pdxc_wow_deactivation()
 * 
 * Deactivates the plugin.
 */
function pdxc_wow_deactivation()
{
    /*placeholder function, temporarily returns null*/
    return null;
}

/**
 * 	@function pdxc_wow_uninstall()
 * 
 *	Uninstalls the plugin and cleans up the database.
 */

function pdxc_wow_uninstall()
{
    if(!defined('WP_UNINSTALL_PLUGIN'))
    {
        die;
    }
    if(is_multisite())
    {
    delete_site_option('wow-clientKey');
    delete_site_option('wow-clientSecret');
    delete_site_option('wow-character');
    delete_site_option('wow-realm');
    } 
    else		 
    {
    delete_option('wow-clientKey');
    delete_option('wow-clientSecret');
    delete_option('wow-character');
    delete_option('wow-realm');
    }
}



function pdxc_wow_render_charImgBlock($imgUrl, $charName){
    $html = '<div id="wowCharImgBlock">';
        $html .= '<a href="' . $imgURl . '"><img src="' . $imgUrl . '" alt="Avatar of ' . $charName . '"></a>';
    $html .= '</div>';

    return $html;
}
?>