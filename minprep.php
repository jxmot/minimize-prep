<?php
/*
    Prepare for Minimization - minprep

    This script will open a specified file and search 
    its contents for CSS links and JS file script tags.

    The file can be: *.html, *.php

    For each type of file it finds the contents are read 
    in and then appended to a single CSS or JS file.

    NOTES:
        Commented out lines will be ignored. But they 
        must be commented out correctly. The comment MUST
        start and end on the same line as the link or script 
        tags.

        Links or source tags that have "http://" or "https://" 
        in them will also be ignored. And source tags with 
        "jquery" will also be ignored.

        Be sure to verify that the files found match what 
        is expected. Just in case something was not commented 
        correctly.
*/
echo "Starting preparation...\n";
$minprep = json_decode(file_get_contents('./minprep.json'));
if(file_exists($minprep->fileroot.$minprep->input) === false) {
    echo $minprep->fileroot.$minprep->input . ' does not exist!';
    die();
}
echo "Input: {$minprep->fileroot}{$minprep->input}\n";
echo "Files Root Path: {$minprep->fileroot}\n";
echo "{$minprep->cssout} and {$minprep->jsout} will be overwritten.\n";

$cssout = @fopen($minprep->cssout, 'w');
$jsout  = @fopen($minprep->jsout, 'w');
$htmlin = @fopen($minprep->fileroot . $minprep->input, 'r');
$hline  = '';

$bashout = null;
$bashfile = './rmvresources.sh';
if($minprep->mkbash === true) {
    echo "Creating {$bashfile} file\n\n";
    $bashout = @fopen($bashfile, 'w');
    fwrite($bashout, "#!/bin/bash\n");
} else echo "\n";

while(!feof($htmlin)) {
    $hline = fgets($htmlin);

    $cbeg =  strpos($hline, '<!--');
    $cend =  strpos($hline, '-->');

    // parse the line...
    // link?
    if(strpos($hline, '<link') !== false) {
        if(strpos($hline, 'rel="stylesheet"') !== false) {
            // get the href
            $href = strpos($hline, 'href=');
            if($href === false || ($href > $cbeg) && ($href < $cend)) continue;
            if((strpos($hline, 'http://') === false) && 
               (strpos($hline, 'https://') === false) && 
               (strpos($hline, 'site.css') === false) && 
               (strpos($hline, '//') === false)) {
                $url = substr($hline, $href + 6);
                $url = substr($url, 0, strpos($url, '"'));
                echo 'CSS found - ' . $url . "\n";
                $css = file_get_contents($minprep->fileroot . $url);
                if($minprep->filecomment === true) fwrite($cssout, "\n/* **** {$url} **** */\n");
                else fwrite($cssout, "\n");
                fwrite($cssout, $css);
                if($minprep->mkbash === true) {
                    fwrite($bashout, "rm -f {$minprep->fileroot}{$url}\n");
                }
            }
        }
    } else {
        // script?
        if(strpos($hline, '<script') !== false) {
            // get the src
            $src = strpos($hline, 'src=');
            if($src === false || ($src > $cbeg) && ($src < $cend)) continue;
            if((strpos($hline, 'http://') === false) && 
               (strpos($hline, 'https://') === false) && 
               (strpos($hline, 'site.js') === false) && 
               (strpos($hline, 'jquery') === false) && 
               (strpos($hline, '//') === false)) {
                $url = substr($hline, $src + 5);
                $url = substr($url, 0, strpos($url, '"'));
                echo 'JS found - ' . $url . "\n";
                $js = file_get_contents($minprep->fileroot . $url);
                if($minprep->filecomment === true) fwrite($jsout, "\n/* **** {$url} **** */\n");
                else fwrite($jsout, "\n");
                fwrite($jsout, $js);
                if($minprep->mkbash === true) {
                    fwrite($bashout, "rm -f {$minprep->fileroot}{$url}\n");
                }
            }
        }
    }
}

fflush($cssout);
fclose($cssout);

fflush($jsout);
fclose($jsout);

fclose($htmlin);

if($minprep->mkbash === true) {
    fflush($bashout);
    fclose($bashout);
}

echo "\nPreparation Complete.\n";
?>