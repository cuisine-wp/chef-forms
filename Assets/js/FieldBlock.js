


	var FieldBlock = Backbone.View.extend({


		fieldId: '',
		formId: '',
		postId: '',

		/**
		 * Events for this View
		 * @return object
		 */
		events: {

			'click .field-preview': 'toggleField',
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
		 * Open or close this field
		 * 
		 * @return void
		 */
		toggleField: function(){

			var self = this;
			
			self.$el.find( '.field-options' ).toggle();
			self.$el.toggleClass( 'fold-out' );

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
					self.$el.remove();
				});
			}
		}



	});


	jQuery( document ).ready( function( $ ){


		setFieldBlocks();
		setFieldPositions();


		jQuery( '.form-builder-fields' ).sortable({
			placeholder: 'field-dashed',
			handle: '.field-preview',
			update: function (event, ui) {

				setFieldPositions();
		
			}
		});

	});


	/**
	 * Set the position of each field as an incrementing number
	 * 
	 */
	function setFieldPositions(){

		var i = 0;
		jQuery('.field-block' ).each( function( index, obj ){

			jQuery( this ).find( '.position-input' ).val( i );
			i++;

		});
	}


	/**
	 * Set the field backbone-objects
	 * 
	 */
	function setFieldBlocks(){

		jQuery('.field-block' ).each( function( index, obj ){
			var fblock = new FieldBlock( { el: obj } );
		});

	}