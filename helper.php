<?php
/**
 * Helper Component for the Chanemarks plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     LarsDW223
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_changemarks extends DokuWiki_Plugin {

    function renderODTOpenSpan ($renderer, $element, $class) {
        $properties = array ();

        if ( method_exists ($renderer, 'getODTProperties') === false ) {
            // Function is not supported by installed ODT plugin version, return.
            return;
        }

        // Get CSS properties for ODT export.
        $renderer->getODTProperties ($properties, $element, $class, NULL, 'print');

        if ( empty($properties ['background-image']) === false ) {
            $properties ['background-image'] =
                $renderer->replaceURLPrefix ($properties ['background-image'], DOKU_INC);
        }

        if ( $properties ['text-decoration'] == 'line-through' ) {
            $properties ['text-line-through-style'] = 'solid';
        }

        $renderer->_odtSpanOpenUseProperties($properties);
    }

    function renderODTCloseSpan ($renderer) {
        if ( method_exists ($renderer, '_odtSpanClose') === false ) {
            // Function is not supported by installed ODT plugin version, return.
            return;
        }
        $renderer->_odtSpanClose();
    }
}
