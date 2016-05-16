define([

	'jquery',
	'wysiwyg',

], function( $ ){

	$('.field.editor').trumbowyg({
	    fullscreenable: false,
	    closable: false,
	    lang: 'nl',
	    btns: ['bold', 'italic', '|', 'link', '|', 'btnGrp-lists' ]
	});

});