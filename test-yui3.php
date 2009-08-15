<?php
$version = (($_GET['version']) ? $_GET['version'] : '3.0.0b1');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>3.x local combo test</title>
    <script type="text/javascript" src="index.php?<?php echo($version); ?>/build/yui/yui-min.js"></script>
    <script type="text/javascript">
    YUI({
        comboBase: 'index.php?',
        combine: true
    }).use('node', function(Y) {
        Y.on('domready', function() {
            Y.get('body').setStyle('backgroundColor', 'green').set('innerHTML', 'YUI 3.x tag ' + YUI.version + ' loaded.');
        });
    });
    </script>
</head>
<body>
</body>
</html>

