<?php
/* Smarty version 5.1.0, created on 2024-12-02 18:51:06
  from 'file:/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/dashboard.html' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.1.0',
  'unifunc' => 'content_674df38a62f8a0_38184522',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '11e21ae103ba98b9654d5de153ad5df503f505ad' => 
    array (
      0 => '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages/dashboard.html',
      1 => 1722634767,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_674df38a62f8a0_38184522 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/homepages/2/d41324517/htdocs/aquarus_shop/templates/__xtAdmin/xtCore/pages';
?><!-- Content Wrapper. Contains page content -->
  <div class="xcontent-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="xcontent">
      <!-- row -->
      <div class="row">
        <div class="col-md-8">
          <!-- The time line -->
          <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"dashboard_above_news_tpl"), $_smarty_tpl);?>

          <ul class="timeline">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('news'), '_news');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_news')->value) {
$foreach0DoElse = false;
?>
            <!-- timeline time label -->
            <li class="time-label">
                  <span class="bg-red">
                    <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('_news')['pubDate'],"%a, %d %B");?>

                  </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-envelope bg-blue"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('_news')['pubDate'],"%H:%M");?>
</span>

                <h3 class="timeline-header"><?php echo $_smarty_tpl->getValue('_news')['title'];?>
</h3>

                <div class="timeline-body">
                  <?php echo $_smarty_tpl->getValue('_news')['description'];?>

                </div>
                <div class="timeline-footer">
                  <a class="btn btn-primary btn-xs" href="<?php echo $_smarty_tpl->getValue('_news')['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog">Read more</a>
                </div>
              </div>
            </li>
            <!-- END timeline item -->
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
        </div>
        <!-- /.col -->
        <div class="col-md-4">

<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('hook')->handle(array('key'=>"dashboard_above_plugin_tpl"), $_smarty_tpl);?>


          <?php if ($_smarty_tpl->getValue('store_handler')->_lic_license_isdemo == '1') {?>
          <div class="info-box">
            <span class="info-box-icon info-box-orange"><i class="far fa-clock"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Test-Version bis: <?php echo $_smarty_tpl->getValue('store_handler')->_lic_trial_end_time;?>
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
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('top_plugins'), '_topplugin');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_topplugin')->value) {
$foreach1DoElse = false;
?>
                  <tr>
                    <td><a href="<?php echo $_smarty_tpl->getValue('_topplugin')['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog" target="_blank"><?php echo $_smarty_tpl->getValue('_topplugin')['title'];?>
</a></td>
                  </tr>
                   <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>                 
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
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('new_plugins'), '_newplugin');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('_newplugin')->value) {
$foreach2DoElse = false;
?>
                  <tr>
                    <td><a href="<?php echo $_smarty_tpl->getValue('_newplugin')['link'];?>
?utm_source=dashboard&utm_medium=rss&utm_campaign=blog" target="_blank"><?php echo $_smarty_tpl->getValue('_newplugin')['title'];?>
</a></td>
                  </tr>
                   <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>                 
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
