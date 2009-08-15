#!/bin/bash

git=`which git`
apc=`php -m | grep apc`

if [ "$git" == "" ]; then
    echo "Git not found, exiting..";
    exit;
else
    version=`$git --version`
    echo "Found $version"
fi

if [ "$apc" == "" ]; then
    echo "------------------------------------------------------------------------------------------"
    echo "APC PHP module is not installed."
    echo "Performance will be better if it's installed.. I'm just sayin.."
    echo "------------------------------------------------------------------------------------------"
    exit;
fi

if [ -d /tmp/yuidev ]; then
    echo "Found a tmp directory called /tmp/yuidev"
    echo "Do you want me to remove it? [y/N]"
    read output
    if [ "$output" != "y" ]; then
        echo "Exiting.."
        exit
    fi
fi

echo "Making tmp directories.."
mkdir -p /tmp/yuidev/base
wait
mkdir -p /tmp/yuidev/lib
wait
mkdir -p /tmp/yuidev/cache
wait

echo "Fetching sources.. This may take a few minutes, so grab a drink.."
cd /tmp/yuidev/base/
wait
echo "------------------------------------------------------------------------------------------"
echo "Fetching YUI3 Sources. http://github.com/yui/yui3"
$git --no-pager clone git://github.com/yui/yui3.git
wait
echo "------------------------------------------------------------------------------------------"
echo "Fetching YUI2 Sources. http://github.com/yui/yui2"
$git --no-pager clone git://github.com/yui/yui2.git
wait
echo "------------------------------------------------------------------------------------------"
echo "Fetching libs.."
echo "------------------------------------------------------------------------------------------"
cd /tmp/yuidev/lib
wait
echo "YUI 2.7.0"
curl http://yuilibrary.com/downloads/yui2/yui_2.7.0b.zip -o yui2.zip
wait
unzip -q yui2.zip
wait
mkdir 2.7.0
wait
cd 2.7.0
mv ../yui/build ./
wait
cd ../
wait
rm -rRf yui
wait
rm -rRf yui2.zip
wait
echo "YUI 3.0.0b1"
curl http://yuilibrary.com/downloads/yui3/yui_3.0.0b1.zip -o yui3.zip
wait
unzip -q yui3.zip
wait
mkdir 3.0.0b1
wait
cd 3.0.0b1
mv ../yui/build ./
wait
cd ../
wait
rm -rRf yui
wait
rm -rRf yui3.zip
wait
echo "------------------------------------------------------------------------------------------"
echo "Fetching complete.."
echo ""
echo "Temporary directories need admin (sudo) privileges, asking.."
sudo chown -R www:www /tmp/yuidev
sudo chmod -R +w /tmp/yuidev
echo "Installation complete."
echo ""
echo "------------------------------------------------------------------------------------------"
echo ""
echo "Now put the index.php and test.php files under your web root, then open the test.php file."
echo "You can try test.php to load YUI 3.0.0b1"
echo "Then try test.php?version=yui3-1291 to load the yui3-1291 tag."
echo ""
echo "------------------------------------------------------------------------------------------"
echo ""
exit;

