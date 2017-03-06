<?php

namespace ChefForms\Admin\Form\Entries;

use ChefForms\Wrappers\Form;
use Cuisine\Utilities\Date;


class Entry{

	/**
	 * ID of this entry
	 *
	 * @var int
	 */
	var $id;


	/**
	 * This entries parent form
	 *
	 * @var ChefForms\Wrappers\Form
	 */
	var $form;

	/**
	 * An array of fields
	 *
	 * @var array
	 */
	var $fields;

	/**
	 * This entries date, saved as a unix timestamp
	 *
	 * @var int
	 */
	var $date;

	/**
	 * Status of this entry
	 *
	 * @var string
	 */
	var $status;

	/**
	 * Status posibilities
	 *
	 * @var array
	 */
	var $statusses;

	/**
	 * Returns this entry, scaffolded
	 *
	 * @param $args Array
	 * @return ChefForms\Admin\Form\Entries\Entry
	 */
	public function make( $args ){

		$this->id = $args['entry_id'];
		$this->form = Form::make( $args['form_id'] );
		$this->date = $args['date'];
		$this->fields = get_post_meta( $this->id, 'entry', true );

		return $this;
	}


	/**
	 * Build the html to go in the manager
	 *
	 * @return string (html, echoed)
	 */
	public function build(){


		echo '<div class="single-entry">';

			echo '<div class="entry-preview">';

				//status:
				do_action( 'chef_forms_status_indicator', $this, $this->form->id );

				//date:
				echo '<div class="entry-date">';
					echo $this->getDate();
				echo '</div>';

				//which form?
				echo '<span class="form-title">';
					echo get_the_title( $this->form->id );
				echo '</span>';

			echo '</div>';


			do_action(
				'chef_forms_before_entry_fields',
				$this,
				$this->form->id
			);

			echo '<div class="entry-fields">';

				$fields = apply_filters(
					'chef_forms_entry_fields',
					$this->form->fields
				);

				echo '<table cellpadding="0" cellspacing="0">';

					foreach( $fields as $field ){

						echo $field->getNotificationPart( $this->fields );

					}

				echo '</table>';

			echo '</div>';

			do_action(
				'chef_forms_after_entry_fields',
				$this,
				$this->form->id
			);


		echo '</div>';
	}


	/**
	 * Returns the date in the right format
	 *
	 * @return string
	 */
	private function getDate(){

		return Date::get( $this->date );

	}


}