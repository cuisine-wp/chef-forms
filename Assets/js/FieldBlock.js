


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

		}



	});


	jQuery( document ).ready( function( $ ){


		setFieldBlocks();


		jQuery( '.form-builder-fields' ).sortable({
			placeholder: 'field-dashed',
			update: function (event, ui) {

				var i = 0;
				jQuery('.field-block' ).each( function( index, obj ){

					jQuery( this ).find( '.position-input' ).val( i );

					i++;
				});


			}
		});

	});


	function setFieldBlocks(){

		jQuery('.field-block' ).each( function( index, obj ){
			var fblock = new FieldBlock( { el: obj } );
		});

	}