<?php
	
	namespace CuisineForms\Contracts;


	interface SettingsPanel{


		public function make( String name, String $title, Array $options = array() );
		public function set( Array $fields );

	}