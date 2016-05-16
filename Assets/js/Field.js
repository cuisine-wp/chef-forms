
	var FieldBlock = Backbone.View.extend({


		fieldId: '',
		formId: '',
		postId: '',

		/**
		 * Events for this View
		 * @return object
		 */
		events: {

			'click .field-preview .button': 'showLightbox',
			'click .field-options .close': 'closeLightbox',
			'change .update': 'updatePreview',
			'click .delete-field': 'deleteField',
		},


		/**
		 * Events for this View
		 * @return this
		 */
		initialize: function(){

			var self = this;

			self.fieldId = self.$el.data('field_id');
			self.formId = self.$el.data( 'form_id' );
			self.postId = self.formId;

			return this;
		},

		/**
		 * Open the lightbox
		 * 
		 * @return void
		 */
		showLightbox: function(){

			var self = this;
			console.log( 'show!' );
			self.$el.find( '.field-options' ).addClass( 'active' );

		},

		closeLightbox: function(){

			var self = this;
			self.$el.find( '.field-options' ).removeClass( 'active' );

		},

		/**
		 * Update the preview based on the changing of input items:
		 * 
		 * @return void
		 */
		updatePreview: function( e ){

			var self = this;

			if( jQuery( e.target ).hasClass( 'update-label' ) ){
				var _preview = self.$el.find('.preview-label');
				var _value = self.$el.find('.label-field').val();

				if( self.$el.find('.req-field').is(':checked') )
					_value += ' *';

				_preview.html( _value );

			}else if( jQuery( e.target ).hasClass( 'update-placeholder' ) ){
				
				var _preview = self.$el.find('.preview-input');
				var _value = jQuery( e.target ).val();

				_preview.prop('placeholder', _value );
			}

		},

		/**
		 * Delete a field
		 * 
		 * @return void
		 */
		deleteField: function(){

			var self = this;

			if( confirm( "Weet je zeker dat je deze sectie wil verwijderen?" ) ){
				self.$el.slideUp( 'slow', function(){
					self.destroy();
					self.$el.remove();
				});
			}
		},

		/**
		 * Remove all field-events
		 * 
		 * @return void
		 */
		destroy: function(){
			this.undelegateEvents();
		}

	});