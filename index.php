<?php
$q = $_SERVER['QUERY_STRING'];
$sha1 = sha1($q);

if ($_GET['fetch'] && $_GET['version']) {
    $base = '/tmp/yuidev/base/yui'.$_GET['version'];
    $tag = '/tmp/yuidev/lib/'.$_GET['fetch'];
    if (is_dir($tag)) {
        echo('Tag already exists!!');
    } elseif (is_dir($base)) {
        mkdir($tag);
        $git = `which git`;
        $git = trim($git);
        $git .= ' --git-dir='.$base.'/.git --work-tree='.$base;
        echo('Fetch ('.$_GET['version'].'.x) Tag: '.$_GET['fetch'].'<br>');
        $cmd = 'cd '.$base.' && '.$git.' checkout master && '.$git.' pull && '.$git.' checkout '.$_GET['fetch'] .' && cp -R ./build '.$tag.'/build && '.$git.' checkout master';
        //echo('<pre>'.$cmd.'</pre>');
        //passthru($cmd);
        $out = array()
        exec($cmd, $out);
        echo('<pre>'.implode($out, "\n").'</pre>');
        
        echo('Tag sync done, you can now use this tag as a combo URL');
    } else {
        echo('Fetch failed..');
    }
    exit;
}

if ($_GET['flush']) {
    apc_clear_cache('user');
}

if (!function_exists('apc_fetch')) {
    echo('<strong>Error:</strong> APC must be installed!!');
    exit;
}


$files = explode('&', $q);
if (!sizeOf($files)) {
    echo('<strong>Error:</strong> No files found.');
    exit;
}
$version = explode('/', $files[0]);
$version = $version[0];
$build = str_replace('yui'.$_GET['version'].'-', '', $version);

$pre = '/tmp/yuidev/lib/';
if (!is_dir($pre)) {
    mkdir($pre, 0777, true);
}

$out = getCache($sha1);

if (!$out) {
    $out = writeFiles($files, $sha1);
}

function getCache($sha1) {
    $out = apc_fetch('combo-'.$sha1);
    if (!$out) {
        if (is_file('/tmp/yuidev/cache/'.$sha1[0].'/'.$sha1)) {
            $out = @file_get_contents('/tmp/yuidev/cache/'.$sha1[0].'/'.$sha1);
            apc_store('combo-'.$sha1, $out, 1800);
        }
    }
    return $out;
}

function writeFiles($files, $sha1) {
    global $pre, $version, $build;
    $out = '';
    foreach ($files as $k => $file) {
        if (@is_file($pre.$file)) {
            $out .= @file_get_contents($pre.$file)."\n";
        }
    }
    $out = str_replace('@VERSION@', $version, $out);
    $out = str_replace('@BUILD@', $build, $out);
    apc_store('combo-'.$sha1, $out, 1800);
    @mkdir('/tmp/yuidev/cache/'.$sha1[0].'/', 0777, true);
    @file_put_contents('/tmp/yuidev/cache/'.$sha1[0].'/'.$sha1, $out);
    return $out;
}

header('Content-Type: application/x-javascript');
header('Cache-Control: max-age=315360000');
header('Expires: '.date('r', mktime() + (60 * 60 * 24 * 365 * 10)));
header('Age: 0');
echo($out);
exit;

?>
