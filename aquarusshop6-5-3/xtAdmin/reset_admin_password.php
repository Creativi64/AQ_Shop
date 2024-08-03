<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

include '../xtFramework/admin/main.php';



if (isset($_POST["sent"])&& $_POST["sent"]==1)
{
	unset($_POST["sent"]);
	$xtc_acl->reset_admin_password($_POST);
	
}
if ($_GET["action"]=='check_code')
{
	$xtc_acl->checkCode($_GET["remember"]);
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
<title>xt:Commerce 6 Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
<link rel="stylesheet" type="text/css" href="css/admin.css" />

</head>
<body class="hold-transition login-page">


<div class="login-box">
  <div class="login-logo">
    <a href="https://www.xt-commerce.com" target="_blank"><img src="images/layout/logo_xt_admin.png" alt="xt:Commerce" /></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
  <?php if (isset($xtc_acl->_errorMsg) && ! empty($xtc_acl->_errorMsg)) { ?>  
    <div class="alert alert-warning alert-dismissible">
                <?php echo $xtc_acl->_errorMsg;?>
    </div>
  <?php } ?>
  
  
  <?php if (isset($xtc_acl->_successMsg) && ! empty($xtc_acl->_successMsg)) { ?>
  	<div class="alert alert-success  alert-dismissible">
                <?php echo $xtc_acl->_successMsg;?>
    </div>
  <?php } ?>

    <form action="reset_admin_password.php?send=1" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="reset_email" class="form-control" placeholder="<?php echo TEXT_EMAIL;?>">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="hidden" name="action" value="check_captcha"  />
        <img src="captcha.php" alt="Sicherheitscode" />
      </div>
      <div class="form-group has-feedback">
        <input type="text" name="captcha" class="form-control" placeholder="<?php echo TEXT_CAPTCHA;?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="row">
        <div class="col-xs-8">
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <input type="hidden" name="sent" value="1"  />
          <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo BUTTON_NEXT; ?></button>
        </div>
        <!-- /.col -->
      </div>
    </form>

	<p>&copy; <?php echo date('Y');?> <a href="https://www.xt-commerce.com" target="_blank">xt:Commerce GmbH</a></p>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->



</body>
</html>