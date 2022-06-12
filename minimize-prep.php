<?php

function isLink($hline) {
    $ret = -1;

    $cbeg = strpos($hline, '<!--');
    $cend = strpos($hline, '-->');

    if(strpos($hline, '<link') !== false) {
        if(strpos($hline, 'rel="stylesheet"') !== false) {
            // get the href
            $href = strpos($hline, 'href=');
            if($href === false || ($href > $cbeg) && ($href < $cend)) {
                $ret = false;
            } else {
                $ret = $href;
            }
        }
    }
    return $ret;
}

function isScript($hline) {
    $ret = -1;

    $cbeg = strpos($hline, '<!--');
    $cend = strpos($hline, '-->');

    // script?
    if(strpos($hline, '<script') !== false) {
        // get the src
        $src = strpos($hline, 'src=');
        if($src === false || ($src > $cbeg) && ($src < $cend)) $ret = false;
        else $ret = $src;
    }
    return $ret;
}

/*
    Determines if a line should be excluded 
    from further processing.
*/
function isExcluded($hline, $exclude) {
    $excl = false;

    if((strpos($hline, 'http://') === false) && 
       (strpos($hline, 'https://') === false) && 
       (strpos($hline, 'site.css') === false) && 
       (strpos($hline, 'site.js') === false) && 
       (strpos($hline, 'site.min.css') === false) && 
       (strpos($hline, 'site.min.js') === false) && 
       (strpos($hline, 'jquery') === false) && 
       (strpos($hline, '//') === false)) {
        if(count($exclude) > 0) {
            for($ix = 0;$ix < count($exclude);$ix++) {
                if(strpos($hline, $exclude[$ix]) !== false) {
                    $excl = true;
                    break;
                }
            }
        }
    } else {
        $excl = true;
    }
    return $excl;
}

function getURL($hline, $offset) {
    $url = substr($hline, $offset);
    $url = substr($url, 0, strpos($url, '"'));
    return $url;
}

function getFilePath($url, $fileroot) {
    if(strpos($url, './') === 0) {
        $fpath = $fileroot . substr($url, 2);
    } else {
        if(strpos($url, '/') === 0) {
            $fpath = $fileroot . substr($url, 1);
        } else {
            $fpath = $fileroot . $url;
        }
    }
    return $fpath;
}

function putContents($fpath, $fout) {
    if(file_exists($fpath) === false) {
        return false;
    } else {
        $content = file_get_contents($fpath);
        fwrite($fout, $content);
        fwrite($fout, "\n");
        fflush($fout);
        return true;
    }
}
?>