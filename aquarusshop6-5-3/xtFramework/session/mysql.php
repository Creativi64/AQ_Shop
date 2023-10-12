<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/adodb-xt/adodb-session2-xt.php';

$options['table'] = TABLE_SESSIONS;
ADOdb_Session :: config('mysqli', _SYSTEM_DATABASE_HOST, _SYSTEM_DATABASE_USER, _SYSTEM_DATABASE_PWD, _SYSTEM_DATABASE_DATABASE, $options);
ADOdb_session :: Persist($connectMode = false);

@ini_set( 'session.hash_function', 0); // 0 = md5 1 = sha-1
@ini_set( 'session.hash_bits_per_character', 4); // 4 bits/char: 32 char SID
@ini_set( 'session.use_trans_sid', 0 );
@ini_set( 'session.gc_probability', GC_PROBABILITY);
@ini_set( 'session.gc_divisor', GC_DIVISOR);
