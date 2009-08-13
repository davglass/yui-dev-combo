<?php
$q = $_SERVER['QUERY_STRING'];
$sha1 = sha1($q);

function execGit($git, $cmd, $echo=false) {
    $output = array();
    $result;

    $oldUMask = umask(0022);
    exec($git.' '.$cmd . " 2>&1", $output, $result);
    umask($oldUMask);
    
    if ($echo) {
        echo('<pre># git '.$cmd."\n\n".implode($output, "\n").'</pre>');
    }
}

if ($_GET['fetch'] && $_GET['version']) {
    fetchTag($_GET['fetch'], $_GET['version'], true);
}

function fetchTag($tag, $version, $echo=false) {
    if (apc_fetch('git-fetch')) {
        if ($echo) {
            echo('Another process is fetching a tag, please wait...');
        } else {
            echo('alert("Another process is fetching a tag, please wait...");');
        }
        exit;
    }
    apc_store('git-fetch', true);
    $base = '/tmp/yuidev/base/yui'.$version;
    $dir = '/tmp/yuidev/lib/'.$tag;
    $str = '';

    if (is_dir($dir)) {
        if ($echo) {
            $str .= 'Tag already exists!!';
        }
    } elseif (is_dir($base)) {
        mkdir($dir);
        $git = `which git`;
        $git = trim($git);
        $git .= ' --no-pager --git-dir='.$base.'/.git --work-tree='.$base;
        $str .= 'Fetch ('.$version.'.x) Tag: '.$tag.'<br>';

        chdir($base);
        execGit($git, 'checkout master', $echo);
        execGit($git, 'pull', $echo);
        execGit($git, 'checkout -b '.$tag, $echo);
        exec('cp -R '.$base.'/build '.$dir.'/build');
        execGit($git, 'checkout master', $echo);
        
        $str .= '<p>Tag sync done, you can now use this tag as a combo URL:</p>';
        $file = (($version == 3) ? 'yui/yui-min' : 'yuiloader-dom-event/yuiloader-dom-event');
        $str .= '<textarea style="width: 90%; height: 40px;"><script src="http://dev-combo.davglass.com/?'.$tag.'/build/'.$file.'.js"></script></textarea>';
    }
    apc_store('git-fetch', false);
    if ($echo) {
        echo($str);
        exit;
    }
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

$pre = '/tmp/yuidev/lib/';

$tag = explode('/', $files[0]);
$tag = $tag[0];
$version = substr($tag, 3, 1);
$build = str_replace('yui'.$version.'-', '', $tag);
if (!@is_dir($pre.$tag)) {
    fetchTag($tag, $version);
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
    global $pre, $tag, $build;
    $out = '';
    foreach ($files as $k => $file) {
        if (@is_file($pre.$file)) {
            $out .= @file_get_contents($pre.$file)."\n";
        }
    }
    $out = str_replace('@VERSION@', $tag, $out);
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
