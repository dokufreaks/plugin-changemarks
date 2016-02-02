<?php
/**
 * Changemarks Plugin: highlight text with !!>text!!
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_changemarks_highlighted extends DokuWiki_Syntax_Plugin {

    var $ins = 'plugin_changemarks_highlighted'; // instruction of this plugin

    /**
     * return some info
     */
    function getInfo() {
        return array(
                'author' => 'Gina Häußge, Michael Klier, Esther Brunner',
                'email'  => 'dokuwiki@chimeric.de',
                'date'   => '2008-02-12',
                'name'   => 'Changemarks Plugin (highlighted)',
                'desc'   => 'Highlight text',
                'url'    => 'http://wiki.splitbrain.org/plugin:changemarks',
                );
    }

    function getType() { return 'formatting'; }
    function getSort() { return 123; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern('\!\![^\r\n]*?>(?=.*?\!\!)', $mode, $this->ins);
    }

    function postConnect() {
        $this->Lexer->addExitPattern('\!\!', $this->ins);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {

            // entry pattern with optional title
            case DOKU_LEXER_ENTER:
                // strip markup
                $match = substr($match, 2, -1);
                return array($state, $match);

                // inserted text
            case DOKU_LEXER_UNMATCHED:
                return array($state, $match);

                // exit pattern
            case DOKU_LEXER_EXIT:
                return array($state);


            default:
                return false;
        }
    }            

    /**
     * Create output
     */
    function render($mode, Doku_Renderer $renderer, $data) {
        if (($mode == 'xhtml') && (is_array($data))) {
            switch ($data[0]) {
                case DOKU_LEXER_ENTER:
                    $title = ($data[1] ? ' title="'.hsc($data[1]).'"' : '');
                    $renderer->doc .= '<span class="highlighted"'.$title.'>';
                    return true;
                case DOKU_LEXER_UNMATCHED:
                    $renderer->doc .= hsc($data[1]);
                    return true;
                case DOKU_LEXER_EXIT:
                    $renderer->doc .= '</span>';
                    return true;
                default:
                    return false;
            }
        }
        return false;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8: 
