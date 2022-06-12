<?php
/*
    Does the line contain a <link>?

    $hline      - a line of text read from the input file

    Returns:
        > 0     - it contains a <link>, return value is position of "href="
        false   - it does not contain a <link>, or if it does then it's commented out 
        -1      - it does not contain a <link>
*/
function isLink($hline) {
    $ret = -1;
    // is the line commented out? 
    // get positions of markers...
    $cbeg = strpos($hline, '<!--');
    $cend = strpos($hline, '-->');
    // first check...
    if(strpos($hline, '<link') !== false) {
        // second check...
        if(strpos($hline, 'rel="stylesheet"') !== false) {
            // get the href
            $href = strpos($hline, 'href=');
            // see if the "href=" is between comment markers
            if($href === false || ($href > $cbeg) && ($href < $cend)) $ret = false;
            else $ret = $href; // return position, > 0
            }
        }
    }
    return $ret;
}

/*
    Does the line contain a <script>?

    $hline      - a line of text read from the input file

    Returns:
        > 0     - it contains a <script>, return value is position of "src="
        false   - it does not contain a <script>, or if it does then it's commented out 
        -1      - it does not contain a <script>
*/
function isScript($hline) {
    $ret = -1;
    // is the line commented out? 
    // get positions of markers...
    $cbeg = strpos($hline, '<!--');
    $cend = strpos($hline, '-->');

    // first check...
    // script?
    if(strpos($hline, '<script') !== false) {
        // get the src
        $src = strpos($hline, 'src=');
        // see if the "src=" is between comment markers
        if($src === false || ($src > $cbeg) && ($src < $cend)) $ret = false;
        else $ret = $src; // return position, > 0
    }
    return $ret;
}

/*
    Determines if a line should be excluded 
    from further processing.

    $hline      - a line of text read from the input file
    $exclude    - an array of strings, if found in $hline then exclude

    Returns:
        true    - exclude(skip) this line
        false   - process this line
*/
function isExcluded($hline, $exclude) {
    $excl = false;
    // <link> or <script> containin the following 
    // will be excluded
    if((strpos($hline, 'http://') === false) && 
       (strpos($hline, 'https://') === false) && 
       (strpos($hline, 'site.css') === false) && 
       (strpos($hline, 'site.js') === false) && 
       (strpos($hline, 'site.min.css') === false) && 
       (strpos($hline, 'site.min.js') === false) && 
       (strpos($hline, 'jquery') === false) && 
       (strpos($hline, '//') === false)) {
        // check the exclusion list
        if(count($exclude) > 0) {
            for($ix = 0;$ix < count($exclude);$ix++) {
                if(strpos($hline, $exclude[$ix]) !== false) {
                    // exclude this one
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

/*
    Get the URL out of the line, starts at an 
    offset and ends with a double-quote 

    $hline      - a line of text read from the input file
    $offset     - starting poistion where the URL begins

    Returns:

        A string containing the URL of the resource

*/
function getURL($hline, $offset) {
    $url = substr($hline, $offset);
    $url = substr($url, 0, strpos($url, '"'));
    return $url;
}

/*
    Get the full path to the resource

    $url        - string returned from getURL()
    $fileroot   - path to root of resource file tree

    Returns:
        Full tile path to the resource 
*/
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

/*
    Put the contents of a resource into the 
    output file.

    $fpath      - the full path returned from getFilePath()
    $fout       - file handle to the output file

    Returns: 
        true    - resource content was written to the output file 
        false   - the resource file could not be found
*/
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