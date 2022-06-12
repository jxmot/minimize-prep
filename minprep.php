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

        For additional details: https://github.com/jxmot/minimize-prep/#readme
*/
// for silent running set to false
$_dbgmp = true;

if($_dbgmp) echo "Starting preparation...\n";
$minprep = json_decode(file_get_contents('./minprep.json'));
if(file_exists($minprep->fileroot.$minprep->input) === false) {
    echo 'ERROR ' . $minprep->fileroot.$minprep->input . ' does not exist!';
    die();
}

require_once('minimize-prep.php');

if($_dbgmp) {
    echo "Input: {$minprep->fileroot}{$minprep->input}\n";
    echo "Files Root Path: {$minprep->fileroot}\n";
    echo "{$minprep->fileroot}{$minprep->cssout} and {$minprep->fileroot}{$minprep->jsout} will be overwritten.\n\n";
}

// open output files and input file...
$cssout = @fopen($minprep->fileroot . $minprep->cssout, 'w');
$jsout  = @fopen($minprep->fileroot . $minprep->jsout, 'w');
$htmlin = @fopen($minprep->fileroot . $minprep->input, 'r');

// Create the script to remove resources?
if($minprep->mkremove === true) {
    if($_dbgmp) echo "Creating {$minprep->rmvscript} file\n\n";
    $bashout = @fopen($minprep->rmvscript, 'w');
    fwrite($bashout, "#!/bin/bash\n");
} else if($_dbgmp) echo "\n";

// read lines until the end...
while(!feof($htmlin)) {
    $hline = fgets($htmlin);
    $fpath = '';

    // parse the line...
    // link?
    if(($r = isLink($hline)) > 0) {
        // it's a link...
        $href = $r;
    
        if(isExcluded($hline, $minprep->cssexclude)) {
            if($_dbgmp) echo 'Excluded - ' . ltrim($hline);
            continue;
        }
    
        $fpath = getFilePath(getURL($hline, $href + 6), $minprep->fileroot);
        if(putContents($fpath, $cssout)) {
            if($_dbgmp) echo "Found - " . getURL($hline, $href + 6) . "\n";
            if($minprep->mkremove === true) {
                fwrite($bashout, "rm -f {$fpath}\n");
            }
        } else {
            echo "ERROR File Not Found: {$fpath}\n";
        }
    } else {
        if($r === false) continue;
        else {
            if(($r = isScript($hline)) > 0) {
                // it's a script
                $src = $r;
    
                if(isExcluded($hline, $minprep->jsexclude)) {
                    if($_dbgmp) echo 'Excluded - ' . ltrim($hline);
                    continue;
                }
    
                $fpath = getFilePath(getURL($hline, $src + 5), $minprep->fileroot);
                if(putContents($fpath, $jsout)) {
                    if($_dbgmp) echo "Found - " . getURL($hline, $src + 5) . "\n";
                    if($minprep->mkremove === true) {
                        fwrite($bashout, "rm -f {$fpath}\n");
                    }
                } else {
                    echo "ERROR File Not Found: {$fpath}\n";
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

if($minprep->mkremove === true) {
    fflush($bashout);
    fclose($bashout);
}

if($_dbgmp) echo "\nPreparation Complete.\n";
?>