<?php

namespace ChefForms\Creators;

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
	 * Generate controls for form design
	 * 
	 * @return string ( html, echoed )
	 */
	public function design(){

		echo $this->makeButtons( 'design' );

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

		$types = FieldCreator::getAvailableTypes();
		$types = Sort::pluck( $types, 'name' );



		$in_standard = array( 'text', 'textarea', 'email', 'checkbox', 'number' );
		$in_adv = array( 'file', 'checkboxes', 'radio', 'select', 'date', 'hidden', 'address' );

		$in_des = array( 'html', 'break' );

		$in_standard = apply_filters( 'chef_forms_standard_fields', $in_standard );
		$in_adv = apply_filters( 'chef_forms_advanced_fields', $in_adv );
		$in_des = apply_filters( 'chef_forms_design_fields', $in_des );

		$return = array( 'standard' => array(), 'advanced' => array(), 'design' => array() );


		foreach( $types as $key => $value ){

			if( in_array( $key, $in_standard ) )
				$return['standard'][ $key ] = $value;

			if( in_array( $key, $in_adv ) )
				$return['advanced'][ $key ] = $value; 

			if( in_array( $key, $in_des ) )
				$return['design'][ $key ] = $value;
 		}


		return $return;

	}

}
