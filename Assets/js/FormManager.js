var FormManager = Backbone.View.extend({

	formId: '',

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

		self.setEvents();

		return this;
	},


	setEvents: function(){

		var self = this;
		jQuery( '.add-field' ).on( 'click', function( e ){
			
			e.preventDefault();

			self.addField( e );

			return false;
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

				jQuery( '.section-wrapper.msg' ).remove();
				jQuery( '.form-builder-fields' ).append( response );

				setFieldBlocks();
			}

		});



		return false;
	}



});


jQuery( document ).ready( function( $ ){


	setFormManager();

});


function setFormManager(){

	var fmanager = new FormManager( { el: jQuery('.form-manager' ) } );
	
}


