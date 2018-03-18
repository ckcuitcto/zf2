/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	// config.filebrowserBrowseUrl = 'ckfinder/ckfinder.html';
 //   config.filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?type=Images';
 //   config.filebrowserFlashBrowseUrl = 'ckfinder/ckfinder.html?type=Flash';
 //   config.filebrowserUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
 //   config.filebrowserImageUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
 //   config.filebrowserFlashUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';


    config.filebrowserBrowseUrl = BASEPATH +'/ckfinder/ckfinder.html';
    config.filebrowserImageBrowseUrl= BASEPATH +'/ckfinder/ckfinder.html?type=Images';
    config.filebrowserFlashBrowseUrl = BASEPATH +'/ckfinder/ckfinder.html?type=Flash';
    config.filebrowserUploadUrl = BASEPATH +'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.filebrowserImageUploadUrl = BASEPATH +'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
    config.filebrowserFlashUploadUrl = BASEPATH +'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
};

