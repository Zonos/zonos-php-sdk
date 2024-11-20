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
        'anchorSelector' => get_option('zonos_placement_element_selector'),
        'cartUrlPattern' => get_option('zonos_cart_url_pattern'),
        'countryOverrideBehavior' => get_option('zonos_country_override_behavior'),
        'currencyBehavior' => get_option('zonos_currency_behavior'),
        'currencyElementSelector' => get_option('zonos_currency_element_selector'),
        'desktopLocation' => get_option('zonos_desktop_location'),
        'productAddToCartElementSelector' => get_option('zonos_product_add_to_cart_element_selector'),
        'productDescriptionElementSelector' => get_option('zonos_product_description_element_selector'),
        'productPriceElementSelector' => get_option('zonos_product_price_element_selector'),
        'productTitleElementSelector' => get_option('zonos_product_title_element_selector'),
        'automaticPopUp' => get_option('zonos_automatically_open'),
      )
    );
  }
}