<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

ini_set('session.save_handler','redis');
ini_set('session.save_path',REDIS_SERVER);