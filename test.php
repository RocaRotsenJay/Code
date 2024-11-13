<?php
if (class_exists('MongoDB\Client')) {
    echo "MongoDB PHP Driver is installed!";
} else {
    echo "MongoDB PHP Driver is not installed.";
}
?>