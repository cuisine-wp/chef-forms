var FormManager = Backbone.View.extend({

	formId: '',
	fields: [],

	/**
	 * Events for this View
	 * @return object
	 */
	events: {
		'click .nav-btn': 'toggleView',
		'click .settings-nav .form-nav-item': 'toggleSettingsPanel',
		'click .add-field': 'addFieldByClick'
	},


	/**
	 * Events for this View
	 * @return this
	 */
	initialize: function(){

		var self = this;

		self.formId = self.$el.data('form_id');

		self.setToolbar();

		self.setFields();
		self.setEvents();

		self.calculateRowsAndPositions();

		return this;
	},

	/**
	 * Set the stickyness of the toolbar
	 */
	setToolbar: function(){

		var self = this;

		if( jQuery( '.form-builder-fields').length > 0 ){

			//set width:
			var _w = jQuery('.form-builder-fields').innerWidth();
			var _builder = jQuery('.toolbar');
			var _container = jQuery('.form-builder-fields');
			var _offset = _builder.offset().top;

			_builder.css({
				width: _w+'px'
			});



			//set the builder as sticky:
			jQuery( window ).on( 'scroll', function(){

				var _scrollPos = jQuery( window ).scrollTop();
				_scrollPos += jQuery( '#wpadminbar' ).outerHeight();


				if( _scrollPos > _offset && _builder.hasClass( 'sticky' ) == false ){

					var _padding = _builder.outerHeight() + 30;
					_builder.addClass( 'sticky' );
					_container.css({
						'padding-top' : _padding+'px'
					});
				}else if( _scrollPos < _offset && _builder.hasClass( 'sticky' ) == true ){
					_builder.removeClass( 'sticky' );
					_container.css({
						'padding-top' : '0px'
					});
				}

			});

			jQuery('#updatePost').on( 'click tap', function(){
				jQuery('.form-field-bar .spinner').addClass( 'show' );
				jQuery('#publish').trigger( 'click' );
			});
		}
	},


	/**
	 * Init and refresh the Field objects:
	 */
	setFields: function(){

		var self = this;

		if( self.fields.length > 0 ){

			for( var i = 0; self.fields.length > i; i++ ){
				self.fields[ i ].destroy();
			}
		}


		self.fields = [];

		jQuery('.field-block' ).each( function( index, obj ){
			var _field = new FieldBlock( { el: obj } );
			self.fields.push( _field );
		});


	},

	/**
	 * Initiate various events:
	 *
	 * @return void
	 */
	setEvents: function(){

		var self = this;
		self.addFieldByDrag();

		jQuery('#updatePost').on( 'click tap', function(){
			jQuery('.toolbar .spinner').addClass( 'show' );
		});

	},



	/**
	 * Set the sorting events
	 *
	 * @return void
	 */
	setSorting: function(){

		var self = this;

		if( jQuery( '.row' ).data( 'sortable' ) )
			jQuery( ".row" ).sortable( "destroy" );

		var _extraOffset = 0;
		jQuery('.row:not(.ignore)').sortable({
			connectWith: '.row:not(.full)',
			tolerance: 'pointer',
			placeholder: 'placeholder',
			scroll: true,
			scrollSensitivity: 80,
			scrollSpeed: 30,
			forcePlaceholderSize: true,
			cursorAt: {top: 50, left: 50},
			start: function( e, ui ){

				//jQuery( this ).disableSelection();
				jQuery('.form-builder-fields').addClass('sorting');

			},
		    stop: function( e, ui ){

				//jQuery( this ).enableSelection();
				jQuery('.form-builder-fields').removeClass('sorting');
				jQuery('.row:not(.ignore)').sortable('refresh');

				_extraOffset = 0;
				if( jQuery( ui.item ).hasClass( 'add-field') == false )
					self.calculateRowsAndPositions();

		    }

		});
	},




	/**
	 * Toggle different views:
	 *
	 * @return void
	 */
	toggleView: function( e ){

		var self = this;
		var btn = jQuery( e.target );
		var type = btn.data('type');

		jQuery( '.nav-btn' ).removeClass( 'active' );
		btn.addClass( 'active' );

		jQuery( '.form-view' ).removeClass( 'active' );
		jQuery( '.main-form-nav').removeClass( 'active' );
		jQuery( '#'+type+'-container' ).addClass( 'active' );
		jQuery( '#nav-bar-'+type).addClass('active');
	},


	/**
	 * Toggle the settings panels 
	 * 
	 * @param Event e
	 * @return void
	 */
	toggleSettingsPanel: function( e ){

		var self = this;
		var btn = jQuery( e.target );
		var slug = btn.data( 'slug' );

		jQuery( '.settings-nav .form-nav-item' ).removeClass( 'active' );
		btn.addClass( 'active' );

		jQuery( '.settings-panel' ).removeClass( 'active' );
		jQuery( '#panel-'+slug ).addClass( 'active' );

	},


	/**********************************************/
	/****** Adding fields
	/**********************************************/


	/**
	 * Add a field by dragging a button:
	 *
	 * @return void
	 */
	 addFieldByDrag: function( e ){

	 	var self = this;

	 	jQuery('.add-field').draggable({
	 		connectToSortable: '.row:not(.full)',
	 		helper: 'clone',

	 		create: function( event, ui ){

				var _ignoreRows = $( this ).data('ignorerows');
				if( _ignoreRows == true || _ignoreRows == 'true' ){
					jQuery( this ).draggable( "option", "connectToSortable", ".row.empty" );
				}

	 		},

	 		stop: function( event, ui ){

				var _ignoreRows = $( this ).data('ignorerows');
	 			var _placeholder = jQuery('.row .add-field.ui-draggable-handle' );
	 			_placeholder.addClass('placeholder placeholder-block');

	 			if( _ignoreRows == true || _ignoreRows == 'true' )
	 				_placeholder.addClass('full ignoreRows');

	 			_placeholder.html( '<span class="spinner"></span> Adding field...' );
	 			_placeholder.parent().removeClass( 'empty' );



	 			var data = {
	 				action: 'createField',
	 				post_id: self.formId,
	 				type: _placeholder.data( 'type' )
	 			}

	 			jQuery.post( ajaxurl, data, function( response ){

	 				if( response !== 'error' ){
	 					_placeholder.replaceWith( response );
	 					self.setFields();
	 					self.calculateRowsAndPositions();
	 				}

	 			});

	 		}
	 	});
	},


	/**
	 * Add a field to the builder by clicking the button
	 *
	 * @param void
	 */
	addFieldByClick: function( e ){

		e.preventDefault();

		var self = this;
		var _type = jQuery( e.target ).data('type');

		var data = {
			action: 'createField',
			post_id: self.formId,
			type: _type
		}


		jQuery.post( ajaxurl, data, function( response ){

			if( response !== 'error' ){

				jQuery( '.section-wrapper.msg' ).remove();

				var _response = '<div class="row">'+response+'</div>';
				jQuery( '.form-builder-fields' ).append( _response );

				self.setFields();
				self.calculateRowsAndPositions();
			}

		});



		return false;
	},


	/**********************************************/
	/****** Row functions
	/**********************************************/

	/**
	 * Recalculate every row and field position:
	 *
	 * @return void
	 */
	calculateRowsAndPositions: function(){

		var self = this;
		var rowId = 1;
		var position = 0;

		jQuery( window ).trigger( 'before-row-calculation' );

		jQuery('.form-builder-fields .row').each( function(){

			var _children = jQuery( this ).find( '.field-block' );

			if( _children.length > 0 && jQuery( this ).hasClass( 'ignoreRows') == false ){

				jQuery( this ).removeClass( 'full' );
				jQuery( this ).removeClass( 'empty' );

				_children.each( function(){

					jQuery( this ).find('.position-input').val( position );
					jQuery( this ).find('.row-input').val( rowId );
					position++;
				});

				if( _children.length >= 3 )
					jQuery( this ).addClass( 'full' );

				rowId++;

			}else{

				//remove empty rows:
				jQuery( this ).remove();
			}

		});

		jQuery( window ).trigger( 'before-row-calculation' );

		//after changing that, add new empty rows:
		self.setupRows();

		//and reinit sorting:
		self.setSorting();

	},

	/**
	 * Setup the initial empty rows
	 *
	 * @return {[type]} [description]
	 */
	setupRows: function(){

		var self = this;
		self.$el.find('.row:not(.empty)').after( '<div class="row empty"></div>' );

	}

});


jQuery( document ).ready( function( jQuery ){

	if( jQuery('.form-manager').length > 0 )
		window.formManager = new FormManager( { el: jQuery('.form-manager' ) } );

	//handle entry-toggling:
	jQuery('.single-entry .entry-preview').on( 'click tap', function(){
		jQuery( this ).parent().toggleClass( 'active' );
	});
});

