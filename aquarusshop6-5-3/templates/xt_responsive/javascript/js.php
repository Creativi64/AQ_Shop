<?php
global $xtMinify, $page;
$type = '';
if(_STORE_META_DOCTYPE_HTML != "html5"){
    $type = 'type="text/javascript" ';
}

$pathes =
[
    /* jquery **/
    '/components/jquery/dist/jquery.min.js',

    /*  bootstrap  **/
    '/components/bootstrap/dist/js/bootstrap.min.js',

    /*  bs-select **/
    '/components/bootstrap-select/dist/js/bootstrap-select.min.js',

    /*  lightgallery  **/
    '/components/lightgallery/dist/js/lightgallery-all.min.js',

    /*  owlcarousel  **/
    '/components/OwlCarousel/owl-carousel/owl.carousel.min.js',

    /*  matchHeight  **/
    '/components/matchHeight/dist/jquery.matchHeight-min.js',

    /*  breakpoint-check  **/
    '/components/jquery-breakpoint-check/js/jquery-breakpoint-check.min.js',

    /*  limit / debounce / throttle  **/
    '/components/limit.js/limit.js',

    /*  helper / equalize heights  **/
    '/javascript/Helper.js',

    /*  listing switch  **/
    '/javascript/ListingSwitch.js',

    /*  listing switch  **/
    '/javascript/MegaMenu.js',

    /*  listing switch  **/
    '/javascript/Template.js',

    /*  moment  **/
    '/components/moment/min/moment.min.js',
    '/components/moment/locale/de.js',
    '/components/moment/locale/en-gb.js',
    '/components/moment/locale/es.js',
    '/components/moment/locale/fr.js',
    '/components/moment/locale/it.js',
    '/components/moment/locale/pl.js',

    /*  date picker  **/
    '/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',

    /*  slideshow  **/
    '/components/slideshow/slideshow.js',

    /*  axios  **/
    '/components/axios/dist/axios.min.js',

    /*  sweet alert  **/
    '/node_modules/sweetalert2/dist/sweetalert2.all.min.js',

    /*  clipboard  **/
    '/components/clipboard/dist/clipboard.min.js',

    /*  bs notify  **/
    '/components/remarkable-bootstrap-notify/bootstrap-notify.min.js'
];

$sort_order = 0;
foreach ($pathes as $path)
{
    $web_path = file_exists(_SRV_WEBROOT . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . $path) ?
        _SRV_WEB_TEMPLATES . _STORE_TEMPLATE  . $path :
        _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE . $path;
    $xtMinify->add_resource($web_path, $sort_order, 'footer');
    $sort_order += 10;
}
?>

<!-- HTML5 shiv IE8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script <?php echo $type ?> src="<?php if(file_exists(_SRV_WEBROOT . _SRV_WEB_TEMPLATES . _STORE_TEMPLATE . 'components/html5shiv/dist/html5shiv.min.js')) echo _SRV_WEB_TEMPLATES . _STORE_TEMPLATE; else echo  _SRV_WEB_TEMPLATES . _SYSTEM_TEMPLATE ?>/components/html5shiv/dist/html5shiv.min.js"></script>
<![endif]-->
