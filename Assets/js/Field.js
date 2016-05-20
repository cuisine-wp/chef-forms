
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
			'click .field-options .save-field': 'closeLightbox',
			'click .tab': 'openTab', 
			'change .update': 'updatePreview',
			'sorted .multifield-field-wrapper table': 'updateChoicePreview',
			'change .multifield-field-wrapper input': 'updateChoicePreview',
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

			self.setValidateSelect();

			return this;
		},

		/**
		 * Open the lightbox
		 * 
		 * @return void
		 */
		showLightbox: function(){

			var self = this;
			self.$el.find( '.field-options' ).addClass( 'active' );

			//set the first tab as active:
			self.$el.find('.tab:first').trigger('click');
		},

		closeLightbox: function(){

			var self = this;
			self.$el.find( '.field-options' ).removeClass( 'active' );

		},

		/**
		 * Make tabs work in lightbox:
		 * 
		 * @return void
		 */
		openTab: function( e ){

			var self = this;
			var _type = $( e.target ).data('tab');

			self.$el.find('.field-setting-tab-content').removeClass( 'active' );
			self.$el.find('#tab-'+_type).addClass('active');
			self.$el.find('.tab').removeClass( 'active' );

			$( e.target ).addClass( 'active' );

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
		 * Update choice field previews
		 * 
		 * @return void
		 */
		updateChoicePreview: function(){

			var self = this;
			var _choices = '';
			var _type = 'checkbox';

			var i = 0;

			if( self.$el.find('.preview-select').length > 0 )
				_type = 'select';

			self.$el.find('.multi-row').each( function(){

				var _checked = $( this ).find('.checkb input').is(':checked');
				var _label = $( this ).find('.value input').val();
					
				if( _type !== 'select' ){

					if( i <= 3 ){
						_choices += '<span class="choice-wrapper">';

							_choices += '<input class="preview-input preview-checkbox" disabled type="'+_type+'"';
							if( _checked )
								_choices += ' checked';

							_choices += '>';
							_choices += '<span class="choice-label">'+_label+'</span>';

						_choices += '</span>';
					}

				}else{
					if( i <= 3 ){
						_choices += '<option';
						if( _checked )
							_choices += ' selected';

						_choices += '>'+_label+'</option>';

					}
				}

				i++;
			});

			if( _type == 'select' ){
			
				self.$el.find('.preview-select').html( _choices );
			
			}else{

				var _value = self.$el.find('.label-field').val();
				if( self.$el.find('.req-field').is(':checked') )
					_value += ' *';

				var _html = '<label class="preview-label">';
				_html += _value;
				_html += '</label>';
				_html += _choices;
				self.$el.find('.field-live-preview, .field-preview').html( _html );

			}
		},


		/**
		 * Set validate selector:
		 *
		 * @return void
		 */
		setValidateSelect: function(){

			var self = this;
			var _validate = self.$el.find('.validate-selector');
			if( _validate.length > 0 ){

				_validate.chosen()
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

					//recalculate rows and positions:
					window.formManager.calculateRowsAndPositions();
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