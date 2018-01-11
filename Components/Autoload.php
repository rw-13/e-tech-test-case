<?php

function __autoload($classname) {
    require_once(ROOT . DIRECTORY_SEPARATOR . "$classname" . '.php');
}