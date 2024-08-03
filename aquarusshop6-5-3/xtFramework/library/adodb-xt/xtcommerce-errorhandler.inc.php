<?php
/**
 * @version V5.18 3 Sep 2012   (c) 2000-2012 John Lim (jlim#natsoft.com). All rights reserved.
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
 * Latest version is available at http://php.weblogs.com
 *
*/


// added Claudio Bustos  clbustos#entelchile.net
if (!defined('ADODB_ERROR_HANDLER_TYPE')) define('ADODB_ERROR_HANDLER_TYPE',E_USER_ERROR); 

if (!defined('ADODB_ERROR_HANDLER')) define('ADODB_ERROR_HANDLER','ADODB_Error_Handler');

/**
* Default Error Handler. This will be called with the following params
*
* @param $dbms		the RDBMS you are connecting to
* @param $fn		the name of the calling function (in uppercase)
* @param $errno		the native error number from the database
* @param $errmsg	the native error msg from the database
* @param $p1		$fn specific parameter - see below
* @param $p2		$fn specific parameter - see below
* @param $thisConn	$current connection object - can be false if no connection object created
*/
function ADODB_Error_Handler($dbms, $fn, $errno, $errmsg, $p1, $p2, &$thisConnection)
{
	if (error_reporting() == 0) return; // obey @ protocol
	switch($fn) {
	case 'EXECUTE':
		$sql = $p1;
		$inputparams = $p2;

		$s = "$dbms error: [$errno: $errmsg] in $fn(\"$sql\")\n";
		break;

	case 'PCONNECT':
	case 'CONNECT':
		$host = $p1;
		$database = $p2;

		$s = "$dbms error: [$errno: $errmsg] in $fn($host, '****', '****', $database)\n";
		break;
	default:
		$s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)\n";
		break;
	}
	/*
	* Log connection error somewhere
	*	0 message is sent to PHP's system logger, using the Operating System's system
	*		logging mechanism or a file, depending on what the error_log configuration
	*		directive is set to.
	*	1 message is sent by email to the address in the destination parameter.
	*		This is the only message type where the fourth parameter, extra_headers is used.
	*		This message type uses the same internal function as mail() does.
	*	2 message is sent through the PHP debugging connection.
	*		This option is only available if remote debugging has been enabled.
	*		In this case, the destination parameter specifies the host name or IP address
	*		and optionally, port number, of the socket receiving the debug information.
	*	3 message is appended to the file destination
	*/
	static $adodb_error_handler_trace_recursion_detected = false;
	$t = date('Y-m-d H:i:s');
	if(!$adodb_error_handler_trace_recursion_detected)
	{
		$adodb_error_handler_trace_recursion_detected = true;

		$e = new Exception('SQL-ERROR');
		$trace = xtcommerce_errorhandler_getExceptionTrace($e);
		$trace_hash = md5($trace);

		if (defined('ADODB_ERROR_LOG_TYPE'))
		{
			if (DB_ERROR_LOG_TRACE == true && !in_array($fn, array('CONNECT', 'PCONNECT')))
			{
				$s .= $trace;
			}

			if (defined('ADODB_ERROR_LOG_DEST'))
			{
				error_log("($t) $s", ADODB_ERROR_LOG_TYPE, ADODB_ERROR_LOG_DEST);
			}
			else
			{
				error_log("($t) $s", ADODB_ERROR_LOG_TYPE);
			}
		}

		global $ADODB_THROW_EXCEPTIONS,$store_handler;

		/**
		 * Send email if ADODB_THROW_EXCEPTIONS === false (everywhere but in xtWizard)
		 * dont send twice per x minutes
		*/
		$sendErrorMail = defined('DB_ERROR_SEND_MAIL') ? constant('DB_ERROR_SEND_MAIL') : true;
		if ($ADODB_THROW_EXCEPTIONS !== true && $sendErrorMail)
		{
			$respect_min_file_age = false;
			if( defined('DB_ERROR_MIN_MAIL_TIME')) // seconds
			{
				if(DB_ERROR_MIN_MAIL_TIME > 0)
				{
					$respect_min_file_age = DB_ERROR_MIN_MAIL_TIME;
				}
			}

			$error_le_file_prefix = "xtcommerce-errorhandler_last_error";
			$error_le_file = _SRV_WEBROOT._SRV_WEB_LOG. "{$error_le_file_prefix}.{$trace_hash}.txt";
			// $file_mtime = filemtime($error_le_file);
			// $expire_file_time = $file_mtime + $respect_min_file_age;
			// $current_time = time();
			if(file_exists($error_le_file)
				&& $respect_min_file_age
				&& filemtime($error_le_file) + $respect_min_file_age + 2 >= time() )
			{
				// doing nothing, just touching the file to reduce mails sent
				// error_log('skipping db error mail');
				touch($error_le_file);
			}
			else
			{
				if($respect_min_file_age)
				{
					$delete_files = glob(_SRV_WEBROOT . _SRV_WEB_LOG . "{$error_le_file_prefix}.*.txt");
					foreach ($delete_files as $path)
					{
						unlink($path);
					}

					file_put_contents($error_le_file, 'created by xt db error handler xtFramework/library/adodb-xt/xtcommerce-errorhandler.inc.php');
				}

				$s .= "\n SERVER_NAME => " . $_SERVER['SERVER_NAME'] . "\n";
				$s .= "\n HTTP_HOST => " . $_SERVER['HTTP_HOST'] . "\n\n";
				$s .= "\n Current Shop Id = " . $store_handler->shop_id;

				if (!empty($store_handler))
				{
					$s .= "\n All Stores:";
					foreach ($store_handler->getStores() as $st)
					{
						$s .= " \n id: " . $st["id"] . ' - Name: ' . $st["text"];
					}
				}

				$s .= "\n\n see xtLogs/db_error.log";
				$sys_version = defined('_SYSTEM_VERSION') ? constant('_SYSTEM_VERSION') : '';
				@mail(_CORE_DEBUG_MAIL_ADDRESS, 'SQL Error - xt:Commerce ' . $sys_version, $s);
			}

		}

		$error_file = _SRV_WEBROOT.'xtCore/GlobalErrorPage.html';
		global $adminHandler_expected_content_type;

		$log_file_info = constant("USER_POSITION") == "admin" ? "xtLogs/db_error.log" : "log";
		$err_msg       = constant("USER_POSITION") == "admin" ? "<br/><pre>{$errmsg}</pre>" : "";
		$msg = "DB-Error, check {$log_file_info} for details {$err_msg}";

		global $store_handler;
		if($store_handler)
		{
			$msg = str_replace('[STORE_ID]', $store_handler->shop_id, $msg);
		}

		if ($adminHandler_expected_content_type == 'json')
		{
			$ret = new stdClass();
			$ret->success = false;
			$ret->error = true;
			$ret->error_message = $msg;
			die(json_encode($ret));
		}
		else if (file_exists($error_file))
		{
			if ($ADODB_THROW_EXCEPTIONS !== true)
			{
				$fp = fopen($error_file,"rb");
				$buffer = fread($fp, filesize($error_file));
				fclose($fp);
				$buffer = str_replace('[ERR_MSG]', $msg, $buffer);
				$buffer = str_replace('[BG_COLOR]', '#f5ecbe', $buffer);

				if($store_handler)
				{
					$buffer = str_replace('[STORE_ID]', $store_handler->shop_id, $buffer);
				}

				if(!headers_sent())
				{
					header($_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable');
					header('Status: 503 Service Temporarily Unavailable', true, 503);
					header('Retry-After: 300');
				}
				die ($buffer);
			}
			else
			{
				throw new Exception($errmsg, $errno);
			}
		}
		else
		{
			if(!headers_sent())
			{
				header($_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable');
				header('Status: 503 Service Temporarily Unavailable', true, 503);
				header('Retry-After: 300');
			}
			die('mysql error - please check log');
		}
	}
	else {
		if (defined('ADODB_ERROR_LOG_DEST'))
		{
			error_log(
				"($t) probably detected recursion for last exception in xt's adodb error handler. up following exception was"
				.PHP_EOL."    $s",
				ADODB_ERROR_LOG_TYPE, ADODB_ERROR_LOG_DEST);

		}
		else
		{
			error_log(
				"($t) probably detected recursion for last exception in xt's adodb error handler. up following exception was"
				.PHP_EOL."    $s",
				ADODB_ERROR_LOG_TYPE);
		}
		http_response_code(500);
        die('mysql error - please check log');
    }
}

function xtcommerce_errorhandler_getExceptionTrace($exception)
{
	$trace = "";

	$allowedFunctions = array('include', 'include_once', 'require', 'require_once', 'getTemplate', 'fetch');
	$c = 0;
	foreach ($exception->getTrace() as $frame)
	{
		$args = "...";
		if (isset($frame['args']) && in_array($frame['function'], $allowedFunctions))
		{
			$args = array();
			foreach ($frame['args'] as $arg)
			{
				if (is_string($arg))
				{
					$args[] = "'" . $arg . "'";
				}
				elseif (is_array($arg))
				{
					$args[] = "Array";
				}
				elseif (is_null($arg))
				{
					$args[] = 'NULL';
				}
				elseif (is_bool($arg))
				{
					$args[] = ($arg) ? "true" : "false";
				}
				elseif (is_object($arg))
				{
					$args[] = get_class($arg);
				}
				elseif (is_resource($arg))
				{
					$args[] = get_resource_type($arg);
				}
				else
				{
					$args[] = $arg;
				}
			}
			$args = join(", ", $args);
		}
		$trace .= sprintf("#%s %s(%s): %s(%s)\n",$c,
			$frame['file'],
			$frame['line'],
			$frame['function'],
			$args);
		$c++;
	}
	return $trace;
}

?>
