<?php

namespace ChefForms\Admin\Form\Builder;

use Cuisine\Utilities\Sort;
use ChefForms\Fields\FieldFactory;

class Toolbar{


	/**
	 * Returns the HTML for the Toolbelt:
	 * 
	 * @return string (html, echoed)
	 */
	public function build(){


		$labels = $this->getLabels();
		$fields = $this->getFields();


		$html = '';
		$html .= '<div class="toolbar form-field-bar">';

			$html .= '<ul class="main-form-nav">';

				foreach( $labels as $key => $label ){

					if( !empty( $fields[ $key ] ) ){

						$html .= '<li class="form-nav-item">';
	
							$html .= '<span class="btn">';
							$html .= '<i class="dashicons '.$label['icon'].'"></i>';
							$html .= $label['label'].'</span>';
	
							$html .= '<ul class="submenu">';
							foreach( $fields[$key] as $type => $item ){

								$html .= '<li class="add-field button" data-type="'.$type.'">';

								if( isset( $item['icon'] ) )
									$html .= '<i class="dashicons '.$item['icon'].'"></i>';

								$html .= $item['name'].'</li>';

							}
							$html .= '</ul>';
	
						$html .= '</li>';

					}
				}

			$html .= '</ul>';

			$html .= '<span class="update-btn-wrapper">';
				$html .= '<span class="update-btn" id="updatePost">'.__( 'Update' ).'</span>';
			$html .= '</span>';
			$html .= '<span class="spinner"></span>';

		$html .= '</div>';

		echo $html;
	}



	/**
	 * Get available fields and order then:
	 * 
	 * @return string ( html, echoed )
	 */
	private function getFields(){

		$types = FieldFactory::getAvailableTypes();
		//$types = Sort::pluck( $types, 'name' );



		$in_standard = array( 'text', 'textarea', 'email', 'checkbox', 'number', 'checkboxes', 'radio', 'select' );
		$in_adv = array( 'file', 'wysiwyg', 'date', 'password', 'hidden', 'address' );

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

	/**
	 * Returns labels and icons for the main buttons 
	 * 
	 * @return array
	 */
	private function getLabels(){

		$labels = array(

			'standard' => array(
				'label' => __( 'Default fields', 'chefforms' ),
				'icon' 	=> 'dashicons-editor-ul'
			),

			'advanced' => array(
				'label'	=> __( 'Advanced fields', 'chefforms' ),
				'icon'	=> 'dashicons-forms'
			),

			'design' => array(
				'label' => __( 'Design elements', 'chefforms' ),
				'icon' 	=> 'dashicons-admin-appearance'
			)
		);

		$labels = apply_filters( 'chef_forms_toolbar_labels', $labels );
		return $labels;
	}

	/**
	 * Create the sidebar to switch between the builder, notifications and settings
	 * 
	 * @return string (html, echoed)
	 */ 
	public function sidebar(){

		echo '<nav class="form-nav">';

			echo '<span class="nav-btn current" data-type="field">';
				echo '<span class="dashicons dashicons-hammer"></span>';
				echo '<b>'.__( 'Form builder', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn" data-type="notifications">';
				echo '<span class="dashicons dashicons-megaphone"></span>';
				echo '<b>'.__( 'Notifications', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn nav-link settings" data-type="settings">';
				echo '<span class="dashicons dashicons-admin-generic"></span>';
				echo '<b>'.__( 'Settings', 'chefforms' ).'</b>';
			echo '</span>';

		echo '</nav>';

	}

}
