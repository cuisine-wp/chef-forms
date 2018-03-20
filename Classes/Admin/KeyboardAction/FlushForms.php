<?php

    namespace CuisineForms\Admin\KeyboardAction;

    use Keyboard_Action\Actions\Action;

    class FlushForms extends Action{

        public function __construct() {
            $this->id          = 'flushs';
            $this->description = 'Flush all generated forms';
            $this->aliases     = array( 'fl', 'regenerate-forms', 'forms' );
            $this->title       = __( 'Flush all generated forms', 'cuisine-forms' );
            $this->type        = 'default';
    		$this->variables   = $this->get_variables();

            parent::__construct();
        }
    
        /**
	     * Override parent to get variables live - which helps when refreshing actions.
	    */
	    public function get_variables() {
		    return ['wop' => 'Wop', 'wap' => 'Wap'];
	    }

        public function do_action( $data ) {

            $forms = get_option( 'existingForms' );
            foreach( $forms as $id => $form ){
                wp_delete_post( $id );
            }

            delete_option( 'existingForms' );
            $this->after_action_success_response();
        } // No server side action   
    }