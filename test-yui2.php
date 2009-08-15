<?php
$version = (($_GET['version']) ? $_GET['version'] : '2.7.0');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>2.x local combo test</title>
    <script type="text/javascript" src="index.php?<?php echo($version); ?>/build/yuiloader/yuiloader-min.js"></script>
    <script type="text/javascript">
    var loader = new YAHOO.util.YUILoader({
        comboBase: 'index.php?',
        combine: true,
        require: ['event', 'dom'],
        onSuccess: function() {
            YAHOO.util.Dom.setStyle(document.body, 'background-color', 'green');
            document.body.innerHTML = 'YUI 2.x version ' + YAHOO.VERSION + ' loaded.';
        }
    });
    loader.insert();
    </script>
</head>
<body>
</body>
</html>

