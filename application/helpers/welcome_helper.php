<?php if (! defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * @file
 * Helper functions for default controller.
 * @var  [type]
 */

/**
 * Load header component.
 * @var [type]
 */
if (!function_exists('header_component')) {
  /**
   * define variables for header component.
   *
   * @return array
   *   Array containing all header variables.
   */
  function header_component() {
    $header = [];
    // set page title.
    $header['header_pageTitle'] = "BlueDome Technologies";
    //loading assets.
    $header['styles_css'] = base_url('assets/css/styles.css');
    // Site information builder.
    $header['siteInformation'] = '<div class="col-sm-3 logodv""><a href="' .site_url(). '"><img src="'. base_url() .'/assets/img/logo.png"></a></div>';
    // Main navigation builder.
    $header['mainNavigation'] = _render_menu_items();

    return $header;
  }
}

/**
 * Callback function for render menu items.
 * @var [type]
 */
if (!function_exists('_render_menu_items')) {
  /**
   * Render menu items.
   * @return array
   *   Render array.
   */
  function _render_menu_items() {
    $render = '';
  	$items = _get_menu_items();

    if (!empty($items)) {
      $render = '<div class="col-sm-9 menudv">';
  		$render .= '<nav class="navbar navbar-expand-lg navbar-light">';

  		$render .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
  						<span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
  					  </button>';

  		$render .= '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
  		$render .= '<ul class="navbar-nav mr-auto">';

  		foreach ($items as $url => $title) {
  			$render .= '<li class="nav-item">';
  			$render .= '<a class="nav-link" href= "' . $url . '">'. $title . '</a>';
  			$render .= '</li>';
  		}
  		$render .= '</ul>';
  		$render .= '</div>';
  		$render .= '</nav>';
  		$render .= '</div>';
    }

    return $render;
  }
}

/**
 * callback to get menu items.
 * @var [type]
 */
if (!function_exists('_get_menu_items')) {
  /**
   * get menu items.
   *
   * @return array
   *   Array containing menu items.
   */
  function _get_menu_items() {
    return [
  		site_url('pages/solution') => 'SOLUTION',
  		site_url('pages/inspection') => 'THE BLUE DOME PROCESS',
  		site_url('pages/contact') => 'CONTACT',
  		site_url('user/login') => 'COMPANY LOGIN',
  	];
  }
}
