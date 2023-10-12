/*
 Copyright (c) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://cksource.com/ckfinder/license
 */
var pathname = window.location.pathname;
pathname = pathname.replace('xtFramework/library/ckfinder3/ckfinder.html','');
var config = {};
config.rememberLastFolder = false;
config.connectorPath = pathname+'xtAdmin/uploadCKFinderConnector.php';
config.defaultViewType = 'thumbnails';
config.forceAscii = true;

CKFinder.define( config );
