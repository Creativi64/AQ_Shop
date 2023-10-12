<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

ini_set('session.save_handler','memcached');
ini_set('session.save_path',MEMCACHE_SERVER);