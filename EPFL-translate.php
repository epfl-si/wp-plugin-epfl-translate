<?php
/**
 * EPFL Translate
 *
 * @package     EPFLTranslate
 * @author      ISAS-FSD
 * @copyright   Copyright (c) 2025, EPFL
 * @license     GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: EPFL Translate
 * Plugin URI:  https://github.com/epfl-si/wp-plugin-epfl-translate
 * Description: Tweak the polylang language chooser widget to add a link to Google Translate
 * Version:     1.1.0
 * Author:      ISAS-FSD
 * Author URI:  https://go.epfl.ch/idev-fsd
 * Contributor: Nicolas Borboën <nicolas.borboen@epfl.ch>, Saskya Panchaud <saskya.panchaud@epfl.ch>
 * Text Domain: EPFL-Translate
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * EPFL Translate is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * EPFL Translate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EPFL Translate. If not, see <http://www.gnu.org/licenses/>.
 */

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
  $hostTranslated = str_replace('.','-',str_replace('-','--', $_SERVER['HTTP_HOST']));

  return "https://" . $hostTranslated . ".translate.goog" . $_SERVER[ 'REQUEST_URI' ] . "?_x_tr_sl=$from&_x_tr_tl=$to&_x_tr_hl=$from&_x_tr_pto=wapp";
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
    echo "\t\t<span>" . strtoupper( $value ) . "</span>";
    echo "\t\t<svg class='icon' aria-hidden='true'><use xlink:href='#icon-translate'></use></svg>";
    echo "\t</a>";
    echo "</li>";
  }
}
add_action( 'EPFL_theme_after_language_switcher', '\EPFLTranslate\EPFL_theme_after_language_switcher' );

add_filter( 'EPFL_theme_2018_max_inline_languages_count', function () { return 0; } );

function display_banner () {
  global $wp;
  $lang = get_current_language();
  $current_url = home_url( add_query_arg( array(), $wp->request ) );
  switch ($lang) {
    case "fr":
      $translation = "Traduction automatique";
      $message =
          "Cette page a été traduite automatiquement à l’aide de Google Translate et peut contenir des erreurs. En cas de doute, veuillez consulter le <a href='$current_url' class='alert-link'>texte original</a>.";
      break;
    case "de":
      $translation = "Automatische Übersetzung";
      $message = "Diese Seite wurde automatisch mit Google Translate übersetzt und kann Fehler enthalten. Im Zweifelsfall konsultieren Sie bitte den <a href='$current_url' class='alert-link'>Originaltext</a>.";
      break;
    case "it":
      $translation = "Traduzione automatica";
      $message = "Questa pagina è stata tradotta automaticamente con Google Translate e potrebbe contenere errori. In caso di dubbi, si prega di fare riferimento al <a href='$site_url' class='alert-link'>testo originale</a>.";
      break;
    default:
      $translation = "Automatic translation";
      $message = "This page was automatically translated using Google Translate and may contain errors. If in doubt, please refer to the  <a href='$current_url' class='alert-link'>original text</a>.";
  }
  ?>

  <div id="wp-body-open-marker"></div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const isTranslated = window.location.hostname.includes("translate.goog");
      if (!isTranslated) return;

      const navLang = document.querySelector('.nav-lang');
      if (navLang) {
          navLang.style.display = 'none';
      }

      const marker = document.getElementById("wp-body-open-marker");
      if (!marker) return;

      const banner = document.createElement("div");
      banner.innerHTML = `
        <div class="container">
          <div class="alert alert-info fade show" role="alert" style="margin-bottom:0;">
            <strong><?php echo $translation; ?>
            </strong> <?php echo "$message"; ?>
          </div>
        </div>
      `;

      marker.insertAdjacentElement("afterend",banner);
    });
  </script>
<?php
}

add_action( 'wp_body_open', '\EPFLTranslate\display_banner' );
