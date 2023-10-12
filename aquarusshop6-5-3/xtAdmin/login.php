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


if ($xtc_acl->isLoggedIn())
{
	if (isset($_POST['language'])) {
		header('Location: ejsadmin.php?language='.$_POST['language']);
	} else {
		header('Location: ejsadmin.php');
	}
	exit;
}


$store_handler->checkAdminSSL();
$adminSslInfo = $store_handler->redirectAdminSSL();


?>
<!DOCTYPE html>
<html lang="de">
<head>
<title>xt:Commerce 6 Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
<link rel="icon" href="./favicon.png" type="image/png">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="css/admin.css" />


</head>
<body class="hold-transition login-page">


<?php
if ($_GET["action"]=="reset_password")  $xtc_acl->_successMsg = SUCCESS_PASSWORD_SEND;

if (isset($xtc_acl->_successMsg) && ! empty($xtc_acl->_successMsg)) { ?>
<div id="reset-password-success">
	<ul class="success">
			<li><?php echo $xtc_acl->_successMsg;?></li>
		</ul>
</div><!-- #login-error -->
<?php } ?>


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

      <?php if (!empty($adminSslInfo)) { ?>

          <div class="alert alert-warning alert-dismissible">
              <?php echo $adminSslInfo;?>
          </div>
      <?php } ?>

    <form action="login.php" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="handle" class="form-control" placeholder="<?php echo TEXT_ADMIN;?>">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="passwd" class="form-control" placeholder="<?php echo TEXT_USER_PASSWORD;?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group">
            <select class="form-control" name="language">
                  <?php 
			foreach ($language->_getLanguageList('all') as $key => $val)
			{
				echo '<option value="'.$val['id'].'">'.$val['name'].' ('.$val['id'].')'.'</option>';
			}
                  ?>
                  </select>
      </div>
      <?php 
		($plugin_code = $xtPlugin->PluginCode('login.php:login_form')) ? eval($plugin_code) : false;
		?>
      <div class="row">
        <div class="col-xs-6">
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo BUTTON_LOGIN; ?></button>
        </div>
        <!-- /.col -->
      </div>
    </form>


    <a href="<?php echo 'reset_admin_password.php'; ?>"><?php echo TEXT_LINK_LOST_PASSWORD; ?></a><br/><br/>
	<p>&copy; <?php echo date('Y');?> <a href="https://www.xt-commerce.com" target="_blank">xt:Commerce GmbH</a></p>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

</body>
</html>