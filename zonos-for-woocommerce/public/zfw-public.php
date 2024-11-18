<?php

/**
 * Contains the Admin functionality
 *
 * @Class ZFW_Public
 */
class ZFW_Public
{

  /**
   * Loads Zonos Elements Script in the DOM
   *
   * Uses timestamp as version query parameter
   * to prevent browsers from loading cached
   * versions of the script
   *
   * @method load_zonos_elements_script
   */
  function load_zonos_elements_script()
  {
    $timestamp = time();
    wp_enqueue_script(
      'zfw_zonos_elements',
      'https://js.zonos.com/dist/scripts/loadZonos.js?timestamp=' . $timestamp,
    );
    wp_enqueue_script(
      'zfw_public',
      ZFW_DIRECTORY_URL . 'public/js/zfw_public.js',
      array('zfw_zonos_elements'),
      ZFW_VERSION,
    );

    wp_localize_script(
      'zfw_public',
      'args',
      array(
        'apiKey' => get_option('zonos_api_key'),
        'storeId' => get_option('zonos_store_id'),
      )
    );
  }
}