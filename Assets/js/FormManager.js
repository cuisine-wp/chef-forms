var FormManager = Backbone.View.extend({

	formId: '',
	availableTags: {},

	/**
	 * Events for this View
	 * @return object
	 */
	events: {

		'click .nav-btn': 'toggleView',

	},


	/**
	 * Events for this View
	 * @return this
	 */
	initialize: function(){

		var self = this;

		self.formId = self.$el.data('form_id');

		self.setAvailableTags();
		self.setFieldHelperHtml();
		
		self.setEvents();

		return this;
	},


	/**
	 * Create the available-tags object:
	 */
	setAvailableTags: function(){

		var self = this;
		if( window.FormFields !== undefined ){

			self.availableTags = FormFields;

		}else{

			self.availableTags = [];

		}
	},


	setEvents: function(){

		var self = this;
		jQuery( '.add-field' ).on( 'click', function( e ){
			
			e.preventDefault();

			self.addField( e );

			return false;
		});

		//jQuery( )
	},


	setFieldHelperHtml: function(){


		var self = this;
		var _html = '<span class="helper-selector">';

			_html += '<span class="arrow-down"></span>';

			_html += '<div class="field-helper-wrapper">';
			for( var i = 0; self.availableTags.length > i; i++ ){

				var _field = self.availableTags[ i ];

				if( _field.label !== undefined ){

					_html += '<span class="field-helper-item" ';
					_html += 'data-field_id="'+_field.id+'">';
						_html += _field.label;
	
					_html += '</span>';
				}
			}

			_html += '</div>';

		_html += '</span>';

		jQuery('.notifications-container .field-wrapper').append( _html );

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

		jQuery( '.nav-btn' ).removeClass( 'current' );
		btn.addClass( 'current' );

		jQuery( '.form-view' ).removeClass( 'current' );
		jQuery( '#'+type+'-container' ).addClass( 'current' );

	},

	/**
	 * Add a field to the builder
	 * 
	 * @param void
	 */
	addField: function( e ){

		e.preventDefault();


		var self = this;
		var btn = jQuery( e.target );
		var type = btn.data('type');

		var data = {

			action: 'createField',
			post_id: self.formId,
			type: type

		}

		jQuery.post( ajaxurl, data, function( response ){

			if( response !== 'error' ){

				response = JSON.parse( response );

				jQuery( '.section-wrapper.msg' ).remove();
				jQuery( '.form-builder-fields' ).append( response.html );


				self.availableTags.push( response.field );
				setFieldBlocks();

			}

		});



		return false;
	}



});


jQuery( document ).ready( function( $ ){


	setFormManager();

	$( '.single-entry .entry-date' ).on( 'click', function(){
		$( this ).parent().toggleClass( 'active' );
	})

});


function setFormManager(){

	var fmanager = new FormManager( { el: jQuery('.form-manager' ) } );
	
}


