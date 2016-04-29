<?php
/**
 * Changemarks Plugin: mark deleted text with -->text--
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
class syntax_plugin_changemarks_deleted extends DokuWiki_Syntax_Plugin {

    var $ins = 'plugin_changemarks_deleted'; // instruction of this plugin
    static protected $helper = NULL;

    function getType() { return 'formatting'; }
    function getSort() { return 122; }

    function connectTo($mode) {
        $this->Lexer->addEntryPattern('<del[^\r\n]*?>(?=.*?</del>)', $mode, $this->ins);
        $this->Lexer->addEntryPattern('\-\-[^\r\n]*?>(?=.*?\-\-)', $mode, $this->ins);
    }

    function postConnect() {
        $this->Lexer->addExitPattern('\-\-', $this->ins);
        $this->Lexer->addExitPattern('</del>', $this->ins);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {

            // entry pattern with optional title
            case DOKU_LEXER_ENTER:
                // strip markup
                if (substr($match, 0, 4) == '<del') $match = substr($match, 5, -1);
                else $match = substr($match, 2, -1);
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
        if (is_array($data)) {
            if ($mode == 'xhtml') {
                switch ($data[0]) {
                    case DOKU_LEXER_ENTER:
                        $title = ($data[1] ? ' title="'.hsc($data[1]).'"' : '');
                        $renderer->doc .= '<del'.$title.'>';
                        return true;
                    case DOKU_LEXER_UNMATCHED:
                        $renderer->doc .= hsc($data[1]);
                        return true;
                    case DOKU_LEXER_EXIT:
                        $renderer->doc .= '</del>';
                        return true;
                    default:
                        return false;
                }
            }
            if ($mode == 'odt') {
                if ($this->helper==NULL) {
                    $this->helper = plugin_load('helper', 'changemarks');
                }
                switch ($data[0]) {
                    case DOKU_LEXER_ENTER:
                        $title = ($data[1] ? ' title="'.hsc($data[1]).'"' : '');
                        $this->helper->renderODTOpenSpan($renderer, 'span', 'dokuwiki del');
                        return true;
                    case DOKU_LEXER_UNMATCHED:
                        $renderer->doc .= hsc($data[1]);
                        return true;
                    case DOKU_LEXER_EXIT:
                        $this->helper->renderODTCloseSpan($renderer);
                        return true;
                    default:
                        return false;
                }
            }
        }
        return false;
    }
}
// vim:ts=4:sw=4:et:enc=utf-8: 
