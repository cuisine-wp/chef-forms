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


		echo '<nav class="form-nav">';
			echo '<span class="nav-btn active" data-type="field">';
				echo '<span class="dashicons dashicons-hammer"></span>';
				echo '<b>'.__( 'Form builder', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn" data-type="notifications">';
				echo '<span class="dashicons dashicons-megaphone"></span>';
				echo '<b>'.__( 'Notifications', 'chefforms' ).'</b>';
			echo '</span>';

			echo '<span class="nav-btn" data-type="settings">';
				echo '<span class="dashicons dashicons-admin-generic"></span>';
				echo '<b>'.__( 'Settings', 'chefforms' ).'</b>';
			echo '</span>';
		echo '</nav>';
		echo '<div class="toolbar">';

			$this->buildFieldButtons();

			$this->buildNotificationButtons();

			$this->buildSettingButtons();

			echo '<span class="update-btn-wrapper">';
				echo '<span class="update-btn" id="updatePost">'.__( 'Update' ).'</span>';
			echo '</span>';
			echo '<span class="spinner"></span>';

		echo '</div>';

	}


	/**
	 * Create the field buttons
	 * 
	 * @return string (html, echoed)
	 */
	private function buildFieldButtons(){

		$labels = $this->getLabels();
		$fields = $this->getFields();

		$html = '';
		$html .= '<ul id="nav-bar-field" class="main-form-nav fields-nav active">';

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

		echo $html;

	}

	/**
	 * Notification buttons
	 * 
	 * @return string (html, echoed)
	 */
	private function buildNotificationButtons(){

		$html = '<ul id="nav-bar-notifications" class="main-form-nav">';
			$html .= '<li class="form-nav-item add-notification">';
				$html .= '<i class="dashicons dashicons-plus"></i>';
				$html .= __( 'Add notification', 'chefforms' );
			$html .= '</li>';
		$html .= '</ul>';
		echo $html;
	}

	/**
	 * Setting buttons
	 * 
	 * @return string
	 */
	private function buildSettingButtons(){

		$html = '<ul id="nav-bar-settings" class="main-form-nav settings-nav">';
			$html .= '<li class="form-nav-item main-settings active">';
				$html .= '<i class="dashicons dashicons-admin-generic"></i>';
				$html .= __( 'Main Settings', 'chefforms' );
			$html .= '</li>';
		$html .= '</ul>';

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


}
