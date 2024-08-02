<?php
/* Smarty version 4.3.2, created on 2024-07-22 19:31:01
  from '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/dashboard.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.2',
  'unifunc' => 'content_669e97550ddec4_56125511',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '11e21ae103ba98b9654d5de153ad5df503f505ad' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/dashboard.html',
      1 => 1697144119,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_669e97550ddec4_56125511 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/smarty/xt_plugins/function.hook.php','function'=>'smarty_function_hook',),1=>array('file'=>'/homepages/2/d41324517/htdocs/aquarus_shop/xtFramework/library/vendor/smarty/smarty/libs/plugins/modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
?>
<!-- Content Wrapper. Contains page content -->
  <div class="xcontent-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="xcontent">
      <!-- row -->
      <div class="row">
        <div class="col-md-8">
          <!-- The time line -->
          <?php echo smarty_function_hook(array('key'=>"dashboard_above_news_tpl"),$_smarty_tpl);?>

          <ul class="timeline">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['news']->value, '_news');
$_smarty_tpl->tpl_vars['_news']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['_news']->value) {
$_smarty_tpl->tpl_vars['_news']->do_else = false;
?>
            <!-- timeline time label -->
            <li class="time-label">
                  <span class="bg-red">
                    <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['_news']->value['pubDate'],"%a, %d %B");?>

                  </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-envelope bg-blue"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['_news']->value['pubDate'],"%H:%M");?>
</span>

                <h3 class="timeline-header"><?php echo $_smarty_tpl->tpl_vars['_news']->value['title'];?>
</h3>

                <div class="timeline-body">
                  <?php echo $_smarty_tpl->tpl_vars['_news']->value['description'];?>

                </div>
                <div class="timeline-footer">
                  <a class="btn btn-primary btn-xs" href="<?php echo $_smarty_tpl->tpl_vars['_news']->value['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog">Read more</a>
                </div>
              </div>
            </li>
            <!-- END timeline item -->
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
        </div>
        <!-- /.col -->
        <div class="col-md-4">

<?php echo smarty_function_hook(array('key'=>"dashboard_above_plugin_tpl"),$_smarty_tpl);?>


          <?php if ($_smarty_tpl->tpl_vars['store_handler']->value->_lic_license_isdemo == '1') {?>
          <div class="info-box">
            <span class="info-box-icon info-box-orange"><i class="far fa-clock"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Test-Version bis: <?php echo $_smarty_tpl->tpl_vars['store_handler']->value->_lic_trial_end_time;?>
</span>
              <span class="info-box-number"><a href="https://addons.xt-commerce.com/de/Shopsoftware/xt:Commerce-6-Einzelshoplizenz.html" target="_blank">Vollversion kaufen</a></span>
              <span class="info-box-number"><a href="https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/1025540098/xt+Commerce+Test-Version+FAQ" target="_blank">FAQ zur Demo-Version</a></span>
            </div>
          </div>
          <?php }?>

          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo (defined('XT_LASTEST_ACTIVITIES') ? constant('XT_LASTEST_ACTIVITIES') : null);?>
</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th><?php echo (defined('XT_TOP_PLUGINS') ? constant('XT_TOP_PLUGINS') : null);?>
</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['top_plugins']->value, '_topplugin');
$_smarty_tpl->tpl_vars['_topplugin']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['_topplugin']->value) {
$_smarty_tpl->tpl_vars['_topplugin']->do_else = false;
?>
                  <tr>
                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_topplugin']->value['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog" target="_blank"><?php echo $_smarty_tpl->tpl_vars['_topplugin']->value['title'];?>
</a></td>
                  </tr>
                   <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>                 
                  </tbody>
                </table>
              </div>
              
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th><?php echo (defined('XT_NEW_PLUGINS') ? constant('XT_NEW_PLUGINS') : null);?>
</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['new_plugins']->value, '_newplugin');
$_smarty_tpl->tpl_vars['_newplugin']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['_newplugin']->value) {
$_smarty_tpl->tpl_vars['_newplugin']->do_else = false;
?>
                  <tr>
                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_newplugin']->value['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog" target="_blank"><?php echo $_smarty_tpl->tpl_vars['_newplugin']->value['title'];?>
</a></td>
                  </tr>
                   <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>                 
                  </tbody>
                </table>
              </div>
              
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        </div>
      </div>
      <!-- /.row -->


    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper --><?php }
}
