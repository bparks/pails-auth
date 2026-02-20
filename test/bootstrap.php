<?php
/**
 * Bootstrap for pails-auth unit tests.
 * Loads Composer autoload and the Permission model (no DB required for declare/declared_permissions tests).
 */
$loader = require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/Permission.php';

error_reporting(E_ALL & ~E_USER_WARNING);
