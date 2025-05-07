<?php
/**
 * Plugin Name: EPFL Translate
 * Description: Tweak the polylang language chooser widget to add a link to Google Translate
 * Version:     0.1
 * Author:      ISAS-FSD https://github.com/epfl-si/
 * Text Domain: EPFL-Translate
 **/

namespace EPFLTranslate;

function get_current_language ( $default_lang='en' ) {
  if ( function_exists( 'pll_current_language' ) ) {
    return pll_current_language();
  } else {
    return $default_lang;
  }
}

function langsArray () {
  return ['fr', 'en', 'de', 'it'];
}

function getGoogleTranslateURL ( $from='fr', $to='it' ) {
  // _x_tr_sl → Source language
  // _x_tr_tl → Target language
  // _x_tr_hl → Interface language
  return "https://www-epfl-ch.translate.goog" . $_SERVER[ 'REQUEST_URI' ] . "?_x_tr_sl=$from&_x_tr_tl=$to&_x_tr_hl=$from&_x_tr_pto=wapp";
}

function EPFL_theme_after_language_switcher ( $languages ) {
  $pageLanguages = [];
  foreach ( $languages as $key => $value ) {
    $pageLanguages[] = $value[ 'slug' ];
  }
  $langsToAutoTranslate = array_diff( langsArray(), $pageLanguages );
  foreach ( $langsToAutoTranslate as $key => $value ) {
    echo "<li>";
    echo "\t<a href='" . getGoogleTranslateURL( get_current_language(), $value ) . "' aria-label='' class='dropdown-item'>";
    echo "\t\t<span>" . strtoupper( $value ) . " 🈂️</span>";
    echo "\t</a>";
    echo "</li>";
  }
}
add_action( 'EPFL_theme_after_language_switcher', '\EPFLTranslate\EPFL_theme_after_language_switcher' );
