<?php

namespace ChefForms\Builders;

use Cuisine\Utilities\Sort;

class FieldControls{


	/**
	 * Generate controls for standard fields
	 * 
	 * @return string ( html, echoed )
	 */
	public function standard(){

		echo $this->makeButtons( 'standard' );

	}


	/**
	 * Generate controls for advanced fields
	 * 
	 * @return string ( html, echoed )
	 */
	public function advanced(){

		echo $this->makeButtons( 'advanced' );

	}


	/**
	 * Generate a row of buttons
	 * 
	 * @return string ( html, echoed )
	 */
	public function makeButtons( $type ){

		$fields = $this->getFields();
		$fields = $fields[ $type ];

		$html = '';
		foreach( $fields as $type => $name ){

			$html .= '<button class="add-field button" data-type="'.$type.'">';
			$html .= $name.'</button>';

		}

		return $html;

	}



	/**
	 * Get available fields and order then:
	 * 
	 * @return string ( html, echoed )
	 */
	private function getFields(){

		$types = FieldBuilder::getAvailableTypes();
		$types = Sort::pluck( $types, 'name' );

		$in_standard = array( 'text', 'textarea', 'email', 'checkbox', 'number' );
		$in_adv = array( 'checkboxes', 'radio', 'select', 'date', 'hidden', 'address' );
		$return = array( 'standard' => array(), 'advanced' => array() );


		foreach( $types as $key => $value ){

			if( in_array( $key, $in_standard ) )
				$return['standard'][ $key ] = $value;

			if( in_array( $key, $in_adv ) )
				$return['advanced'][ $key ] = $value; 
		}


		return $return;

	}

}
