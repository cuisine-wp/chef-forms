
var MultiField = Backbone.View.extend({


	table: '',

	rows: {},

	prefix: '',


	/**
	 * Events for this View
	 * @return object
	 */
	events: {

		'click .add-row' : 'addRow',
		'click .remove-row' : 'deleteRow'

	},


	/**
	 * Events for this View
	 * @return this
	 */
	initialize: function(){

		var self = this;

		self.table = self.$el.find( 'table tbody' );
		self.rows = self.$el.find( 'tbody tr' );
		self.prefix = self.$el.data('prefix');
		self.showKeys = self.$el.find( '.toggle-keys' ).hasClass( 'showKeys' );


		self.setSorting();
		return this;
	},


	setSorting: function(){

		var self = this;

		self.table.sortable({
			handle: '.drag-row',
			placeholder: 'row-placeholder',
			update: function (event, ui) {

				self.refreshHtml();

			}
		})

	},

	/**
	 * Refresh the html within this field
	 * 
	 * @return void
	 */
	refreshHtml: function(){

		var self = this;
		_.each( self.table.find( 'tr' ), function( item, key ){

			var newId = parseInt( key ) + 1;
			var _prefix = self.prefix.replace( '%id', newId );

			var key = jQuery( item ).find('.key input');
			var value = jQuery( item ).find( '.value input');

			key.attr( 'name', _prefix+'[key]' );
			value.attr( 'name', _prefix+'[label]' );


			if( self.showKeys === false ){
				key.val( value.val() );
			}

		});
	},


	addRow: function(){
	
		var self = this;
		var htmlTemplate = 	_.template( 
						jQuery( '#multifield_row').html()
		);


		var output = htmlTemplate({});
		self.table.append( output );
		self.refreshHtml();

	},


	deleteRow:function( e ){

		var self = this;
		var _parent = jQuery( e.target ).parent().parent();

		_parent.fadeOut( 'slow', function(){
			_parent.remove();
		});

	}



});


jQuery( document ).ready( function( $ ){


	setMultiFields();



});


function setMultiFields(){

	jQuery('.multifield-builder' ).each( function( index, obj ){
		var fblock = new MultiField( { el: obj } );
	});

}