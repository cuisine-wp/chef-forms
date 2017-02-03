define([

	'jquery',
	'cuisine-validate',

], function( $ ){


	$( document ).ready( function( $ ){

		$('.form').each( function( index, obj ){

			var _form = new FormObject();
			_form.init( obj );

		});

		//set the tab-index:
		var i = 1;
		$('.form .field, .form .subfield, .form .submit-form' ).each( function(){

			$( this ).attr( 'tabindex', i );
			i++;

		});

	});


	function FormObject(){

		var el = '';
		var formId = '';
		var fields = {};
		var subfields = {};
		var submitted = '';
		var logMessages = '';
		var dev = '';


		/**
		 * Init this form object
		 *
		 * @param  jQuerySelector obj
		 * @return void
		 */
		this.init = function( obj ){

			var self = this;
			self.el = $( obj );

			//stop a form from initting if it's just an arbitrary .form class:
			if( typeof( self.el.attr('id') ) == 'undefined' )
				return false;


			//checks and debugging:
			self.submitted = false;
			self.logMessages = false;
			self.dev = false;

			self.fields = self.el.find( '.field' );
			self.subfields = self.el.find( '.subfield' );
			self.formId = parseInt( self.el.attr('id').replace( 'form_', '' ) );
			self.setEvents();
			self.setFields();

		}


		this.setEvents = function(){

			var self = this;

			//make the button clickable:
			self.el.find( '.submit-form' ).on( 'click', function( e ){

				e.preventDefault();

				self.logger( 'submit button clicked' );
				self.el.trigger('submit');

			});


			//on submit:
			self.el.on('submit', function( e ){

				//don't prevent default in case of no-ajax
				if( self.allowAjax() )
					e.preventDefault();


				var allValidated = true;
				self.logger( 'submit triggered' );

				//validate all fields:
				self.fields.each( function(){

					if( self.validateField( jQuery( this ) ) === false ){
						allValidated = false;
					}

				});


				//if all fields are validated
				if( allValidated === true && self.submitted == false ){

					self.logger( 'everything validated.' );
					self.showLoader();
					self.send();

				}


				//only return false in the case of no ajax:
				if( self.allowAjax() || allValidated == false )
					return false;

			});



			//field validation on blur:
			self.el.find( '.field' ).on( 'blur', function( e ){

				self.validate( e );

			});
		}

		/**
		 * Send the form, either with Ajax ( preferred ) or regularly
		 *
		 * @return void
		 */
		this.send = function(){

			var self = this;

			//catch non FormData capable browsers ( <IE9 & Opera Mini )
			if( self.allowAjax() === false ){

				self.el.data( 'no-ajax', 'true' );
				self.submitted = true;
				self.el.trigger( 'submit' );

				self.logger( 'non-ajax submit' );

			}else{

				var _data = new FormData( self.el[0] );
				_data.append( 'action', 'sendForm' );
				_data.append( 'post_id', self.formId );

				self.el.trigger( 'beforeSubmit', _data, self );
				self.submitted = true;

				self.logger( 'ajax submit' );

				self.trackAnalytics();

				$.ajax({

					url: Cuisine.ajax,
					type: 'POST',
					data: _data,
					processData: false,
					contentType: false,
					success: function( response ){

						self.onSuccess( response, self );

					},
					error: function( response ){

					}
				});

			}
		}

		/**
		 * Allow ajax
		 *
		 * @return bool
		 */
		this.allowAjax = function(){

			var self = this;
			if( window.FormData == undefined || self.el.data( 'no-ajax') !== undefined )
				return false;

			return true;
		}


		/**
		 * Track an analytics event
		 *
		 * @return void
		 */
		this.trackAnalytics = function(){

			var self = this;
			var _formName = self.el.data( 'title' );

			if( typeof( window.dataLayer ) !== 'undefined' ){

				self.logger( 'google analytics event send via tag manager' );
				window.dataLayer.push({ 'event': 'Form submit: '+_formName });


			else if( typeof( ga ) !== 'undefined' ){

				self.logger( 'google analytics event send' );

				ga('send', 'event', 'Form', 'Submit', _formName );

			}else{
				self.logger( 'google analytics not defined; event not send' );
			}
		}


		/**
		 * Function for succesful send handeling
		 *
		 * @param  json response
		 * @param  FormObject self
		 * @return void
		 */
		this.onSuccess = function( response, self ){

			//used for debugging notifications:
			self.logger( response );

			if( self.dev )
				self.el.append( response );


			if( Validate.json( response ) && self.dev === false ){

				self.hideLoader();

				var response = JSON.parse( response );

				//check if we need to redirect;
				if( response.redirect == true ){

					self.el.trigger( 'beforeRedirect', response, self );

					window.location.href = response.redirect_url;

				}else{

					self.el.trigger( 'onResponse', response, self );


					//otherwise, clear the loader and display the message.
					self.el.addClass( 'msg' );
					self.el.append('<div class="message">'+ response.message +'</div>' );

					self.resetFields();
					self.el.trigger( 'onComplete', response, self );

					//remove message after 3 seconds, if the form doesn't have a data attribute set:
					if( self.el.data( 'maintain-msg' ) === undefined ){

						setTimeout( function(){

							self.el.removeClass( 'msg' );
							self.el.find('.message').remove();
							self.el.trigger( 'onMessageDisappear', self );

						}, 5000 );

					}
				}
			}
		}



		/**
		 * Triggers the JS for our fields on the front-end:
		 *
		 * @return void
		 */
		this.setFields = function(){

			var self = this;

			//set the datepicker:
			if( self.el.find( '.datepicker' ).length > 0 ){
				requirejs( [ 'datepicker' ], function( datepicker ){

					$( ".datepicker.field" ).datepicker();

					//datepicker fields need rechecking of validation:
					$( ".datepicker.field" ).change( function(){
						self.validateField( $( this ) );
					});

				});
			}
		}


		/**
		 * Reset all fields:
		 *
		 * @return void
		 */
		this.resetFields = function(){

			var self = this;

			self.submitted = false;
			self.fields.each( function(){
				$( this ).val('');
				$( this ).removeClass('validated-false');
				$( this ).removeClass('validated-true');
			});


			self.subfields.each( function(){
				$( this ).val('');
				$( this ).prop('checked', false);
				$( this ).removeClass('validated-false');
				$( this ).removeClass('validated-true');
			})

		}


		/**
		 * Figure out the jQuery object behind what we need to validate
		 *
		 * @param  Event evt
		 * @return bool ( self.validateField )
		 */
		this.validate = function( evt ){

			var self = this;
			var obj = jQuery( evt.target );

			self.validateField( obj );

		}

		/**
		 * Actually validate a field
		 *
		 * @param  jQueryObject obj
		 * @return bool
		 */
		this.validateField = function( obj ){

			var self = this;
			var value = obj.val();

			var validated = true;
			var validateNothing = true;
			var type = '';

			//allow plugins to add their validation functions
			obj.trigger( 'validate' );

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

							if( value != '' && Validate.email( value ) === false ){
								validated = false;
								type = 'email';
							}

						break;
						case 'number':

							if( value != '' && Validate.number( value ) === false ){
								validated = false;
								type = 'number';
							}

						break;

						case 'not-negative':

							if( Validate.equalHigherZero( value ) === false ){
								validated = false;
								type = 'equalHigherZero';
							}

						break;

						case 'not-positive':

							if( Validate.equalLowerZero( value ) === false ){
								validated = false;
								type = 'equalLowerZero';
							}

						break;

						case 'address':

							if( value != '' && Validate.has_number( value ) === false ){
								validated = false;
								type = 'address';
							}

						break;
						case 'zipcode':

							//check for validati IDs:
							var _valBy = obj.data('validate').split(',');
							var _country = undefined;
							var _field = false;

							//if there's specific validate data set:
							if( _valBy.length > 1 ){

								for( var i = 0; i < _valBy.length; i++ ){

									var _val = _valBy[ i ];
									if( _val.substring( 0, 4 ) === 'zip-' ){

										//we found a field:
										_field = _val.replace( 'zip-', '' );

									}

								}

								//get the field-value:
								if( _field !== false ){
									var _name = 'field-field_'+self.formId+'_'+_field;
									_country = $('.'+_name ).val();

									self.setReverseZipValidate( $( '.'+_name ), obj.attr('id') );
 								}
							}

							if( value != '' && Validate.zipcode( value, _country ) === false ){
								validated = false;
								type = 'zipcode';
							}

						break;
						case 'reverseValidateZip':

							var _field = $('#'+obj.data('reverse-validate') );
							if( _field ){
								self.validateField( _field );
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

			//check country drop


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

		/**
		 * Reverse check zipcodes:
		 *
		 * @param dropdown jquery-object obj
		 * @param string id for the zipcode field
		 * @return void
		 */
		this.setReverseZipValidate = function( obj, fieldId ){

			var _validation = obj.data( 'validate' );

			if( typeof( _validation ) !== 'undefined' ){
				if( _validation.indexOf( 'reverseValidateZip' ) <= -1 ){

					if( _validation !== '' && _validation != false );
						_validation += ',';

					_validation += 'reverseValidateZip';

				}
			}else{
				_validation = 'reverseValidateZip';
			}

			obj.data( 'validate', _validation );
			obj.data( 'reverse-validate', fieldId );

		}


		/**
		 * Show a loader
		 *
		 * @return void
		 */
		this.showLoader = function(){

			var self = this;
			self.el.addClass( 'active' );

		}

		/**
		 * Hide a loader
		 *
		 * @return void
		 */
		this.hideLoader = function(){

			var self = this;
			self.el.removeClass( 'active' );

		}

		/**
		 * Log errors and responses, when self.logErrors is set.
		 *
		 * @return void
		 */
		this.logger = function( _msg ){

			var self = this;

			if( self.logMessages ){

				console.log( _msg );

			}
		}
	}
});