<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

class filter_m4a extends moodle_text_filter {
    
    function filter($text, array $options = array()) {
        
        if (!is_string($text)) {
            // non string data can not be filtered anyway
            return $text;
        }
        
        $newtext = $text; // fullclone is slow and not needed here
    
        $search = '/<a.*?href="([^<]+\.m4a)(\?d=([\d]{1,4}%?)x([\d]{1,4}%?))?"[^>]*>.*?<\/a>/is';
        $newtext = preg_replace_callback($search, 'm4a_filter_callback', $newtext);
    
        return $newtext;
    }
    
}

function m4a_filter_callback($link) {
    global $CFG;
    
    $ret = '';

    static $count = 0;
    $count++;
    if ($count === 1) {
        $ret .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/filter/m4a/flowplayer-3.1.4.min.js"></script>';
    }
    
    $id = 'filter_flv_'.time().$count; //we need something unique because it might be stored in text cache

    $url = addslashes_js($link[1]);

    $ret .= '   <a href="#" style="display:block;width:400px;height:20px" id="'.$id.'"></a>
                <script>
                    flowplayer("'.$id.'", "'.$CFG->wwwroot.'/filter/m4a/flowplayer-3.1.5.swf", {
                        clip: {
                            url: "'.$url.'",
                            autoBuffering: true,
                            autoPlay: false
                        },
                        plugins: {
                            controls: {
                                fullscreen: false,
                                height: 20
                            }
                        }
                    });
                </script>';

    return $ret;

}

?>