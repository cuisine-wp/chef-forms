define([

	'jquery',
	'cuisine-validate',

], function( $ ){


	$( document ).ready( function( $ ){
	
		$('.form').each( function( index, obj ){
			
			var _form = new FormObject();
			_form.init( obj );

		});

	});


	function FormObject(){

		var el = '';
		var formId = '';
		var fields = {};


		this.init = function( obj ){

			var self = this;
			self.el = $( obj );
			self.fields = self.el.find( '.field' );
			self.formId = parseInt( self.el.attr('id').replace( 'form_', '' ) );
			self.setEvents();

		}


		this.setEvents = function(){

			var self = this;

			self.el.find( '.submit-form' ).on( 'click', function( e ){

				e.preventDefault();

				var allValidated = true;

				//validate all fields:
				self.fields.each( function(){

					if( self.validate( jQuery( this ) ) === false ){
						allValidated = false;
					}

				});


				//if all fields are validated
				if( allValidated === true ){
				
					self.showLoader();
					self.send();

				}

				return false;
			});

			//validation:
			self.el.find( '.field' ).on( 'blur', function( e ){
				self.validate( e );
			});

		}


		this.send = function(){

			var self = this;
			var data = {
				action: 'sendForm',
				post_id: self.formId,
				entry: self.el.serializeArray()
			}


			$.post( Cuisine.ajax, data, function( response ){


				if( Validate.json( response ) ){

					self.hideLoader();

					var response = JSON.parse( response );
					self.el.addClass( 'msg' );
					self.el.append('<div class="message">'+ response.message +'</div>' );

					self.resetFields();
					
					//remove message after 3 seconds:
					setTimeout( function(){
					
						self.el.removeClass( 'msg' );
						self.el.find('.message').remove();
					
					}, 5000 );

				}
			});


		}


		this.resetFields = function(){

			var self = this;

			self.fields.each( function(){
				$( this ).val('');
				$( this ).removeClass('validated-false');
				$( this ).removeClass('validated-true');
			});
			
		}



		this.validate = function( evt ){

			var self = this;
			var obj = jQuery( evt.target );

			self.validateField( obj );

		}

		this.validateField = function( obj ){

			var self = this;
			var value = obj.val();

			var validated = true;
			var validateNothing = true;
			var type = '';

			if( obj.data('validate') !== undefined ){
				var validators = obj.data('validate').split(',');

				for( var i = 0; i < validators.length; i++ ){
	
					var criterium = validators[ i ];

					switch( criterium ){

						case 'required':
							if( Validate.empty( value ) === false ){
								validated = false;
								type = 'required';
								break;
							}

						break;
						case 'email':

							if( Validate.email( value ) === false ){
								validated = false;
								type = 'email';
							}

						break;
						case 'numerical':

							if( Validate.number( value ) === false ){
								validated = false;
								type = 'number';
							}

						break;
						case 'address':

							if( Validate.has_number( value ) === false ){
								validated = false;
								type = 'address';
							}

						break;
						case 'zipcode':

							if( Validate.zipcode( value ) === false ){
								validated = false;
								type = 'zipcode';
							}

						break;

					}

					if( obj.attr( 'type' ) === 'checkbox' && criterium == 'required' ){
						if( obj.is(':checked') === false ){
							validated = false;
							type = 'notchecked';
						}	
					}

				}
			}
			
			var valError = obj.parent().find( '.validation-error' );
			valError.remove();
			
			if( validated ){
	
				obj.removeClass('validated-false');
				obj.addClass('validated-true');


			}else if( validated === false ){
				
				obj.removeClass('validated-true');
				obj.addClass('validated-false');

				var valError = ValidationErrors[ type ];
				obj.after( '<span class="validation-error">'+ valError +'</span>' );

			}

			return validated;

		}


		this.showLoader = function(){

			var self = this;
			self.el.addClass( 'active' );

		}


		this.hideLoader = function(){

			var self = this;
			self.el.removeClass( 'active' );

		}
	}


	var ValidationErrors = {

		'required' 	: 'Dit is een verplicht veld',
		'email'		: 'Dit is geen geldig e-mailadres',
		'numerical'	: 'Dit is geen geldig nummer',
		'address'	: 'Vergeet je het huisnummer niet?',
		'zipcode'	: 'Dit is geen geldige postcode'

	}

});