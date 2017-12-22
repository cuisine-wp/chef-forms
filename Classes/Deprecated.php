<?php

    namespace CuisineForms;

    use Cuisine\Utilities\Logger;

    class Deprecated{

        /**
         * Array holding all deprecated filters
         *
         * @var Array
         */
        protected $filterMap;

        /**
         * Array holding all deprecated actions
         *
         * @var Array
         */
        protected $actionMap;


        public function __construct()
        {
            $this->setDeprecatedFilterMap();
            $this->setDeprecatedActionMap();

            $this->testDrive();

		    foreach ( $this->filterMap as $new => $old ) {
			    add_filter( $new, [ $this, 'deprecatedFilterMapping' ] );
            }
            
            foreach( $this->actionMap as $new => $old ) {
                add_action( $new, [ $this, 'deprecatedActionMapping' ] );
            }
        }

        /**
         * The deprecated filter map
         *
         * @return void
         */
        public function setDeprecatedFilterMap()
        {
            $this->filterMap = [
                'cuisine_forms_entry_postdata' => 'chef-forms-entry-postdata',
                'cuisine_forms_setting_fields'	=> 'chef_forms_setting_fields',
                'cuisine_forms_form_post_type_options'	=> 'chef_forms_form_post_type_options',
                'cuisine_forms_entry_post_type_options'	=> 'chef_forms_entry_post_type_options',
                'cuisine_forms_field_types'	=> 'chef_forms_field_types',
                'cuisine_forms_use_mandrill' => 'chef_forms_use_mandrill',
                'cuisine_forms_smtp_settings' => 'chef_forms_smtp_settings',
                'cuisine_forms_smtp_custom_options' => 'chef_forms_smtp_custom_options',
                'cuisine_forms_validation_errors' => 'chef_forms_validation_errors',
                'cuisine_forms_field_default_value' => 'chef_forms_field_default_value',
                'cuisine_forms_field_properties' => 'chef_forms_field_properties',
                'cuisine_forms_field_tabs' => 'chef_forms_field_tabs',
                'cuisine_forms_advanced_field_settings' => 'chef_forms_advanced_field_settings',
                'cuisine_forms_row_class' => 'chef_forms_row_class',
                'cuisine_forms_show_footer' => 'chef_forms_show_footer',
                'cuisine_forms_maintain_msg' => 'chef_forms_maintain_msg',
                'cuisine_forms_no_ajax' => 'chef_forms_no_ajax',
                'cuisine_forms_display_form' => 'chef_forms_display_form',
                'cuisine_forms_classes' => 'chef_forms_classes',
                'cuisine_forms_submit_method' => 'chef_forms_submit_method',
                'cuisine_forms_enctype' => 'chef_forms_enctype',
                'cuisine_forms_return_link' => 'chef_forms_return_link',
                'cuisine_forms_fields' => 'chef_forms_fields',
                'cuisine_forms_notifications' => 'chef_forms_notifications',
                'cuisine_forms_entry_values' => 'chef_forms_entry_values',
                'cuisine_forms_upload_dir' => 'chef_forms_upload_dir',
                'cuisine_forms_notification_template' => 'chef_forms_notification_template',
                'cuisine_forms_notification_params' => 'chef_forms_notification_params',
                'cuisine_forms_main_settings_fields' => 'chef_forms_main_settings_fields',
                'cuisine_forms_confirmation_settings_fields' => 'chef_forms_confirmation_settings_fields',
                'cuisine_forms_default_field_args' => 'chef_forms_default_field_args',
                'cuisine_forms_entries_query' => 'chef_forms_entries_query',
                'cuisine_forms_entry_fields' => 'chef_forms_entry_fields',
                'cuisine_forms_notification_sub_fields' => 'chef_forms_notification_sub_fields',
                'cuisine_forms_standard_fields' => 'chef_forms_standard_fields',
                'cuisine_forms_advanced_fields' => 'chef_forms_advanced_fields',
                'cuisine_forms_design_fields' => 'chef_forms_design_fields'
            ];
        }
        
        /**
         * The deprecated action map
         *
         * @return void
         */
        public function setDeprecatedActionMap()
        {
            $this->actionMap = [
                'cuisine_forms_loaded' => 'chef_forms_loaded',
                'cuisine_forms_field_tab_content' => 'chef_forms_field_tab_content',
                'cuisine_forms_init_form' => 'chef_forms_init_form',
                'cuisine_forms_after_init' => 'chef_forms_after_init',
                'cuisine_forms_before_form' => 'chef_forms_before_form',
                'cuisine_forms_before_fields' => 'chef_forms_before_fields',
                'cuisine_forms_after_fields' => 'chef_forms_after_fields',
                'cuisine_forms_after_form' => 'chef_forms_after_form',
                'cuisine_forms_before_file_upload' => 'chef_forms_before_file_upload',
                'cuisine_forms_after_file_upload' => 'chef_forms_after_file_upload',
                'cuisine_forms_before_uploads' => 'chef_forms_before_uploads',
                'cuisine_forms_render_settings_panels' => 'chef_forms_render_settings_panels',
                'cuisine_forms_form_settings_nav' => 'chef_forms_form_settings_nav',
                'cuisine_forms_form_settings_save' => 'chef_forms_form_settings_save',
                'cuisine_forms_before_entry_list' => 'chef_forms_before_entry_list',
                'cuisine_forms_after_entry_list' => 'chef_forms_after_entry_list',
                'cuisine_forms_before_entry_toolbar_controls' => 'chef_forms_before_entry_toolbar_controls',
                'cuisine_forms_after_entry_toolbar_controls' => 'chef_forms_after_entry_toolbar_controls',
                'cuisine_forms_status_indicator' => 'chef_forms_status_indicator',
                'cuisine_forms_before_entry_fields' => 'chef_forms_before_entry_fields',
                'cuisine_forms_after_entry_fields' => 'chef_forms_after_entry_fields'
            ];
        }

        /**
         * Map a deprecated action
         *
         * @param Mixed $data
         * @param Mixed $arg_1
         * @param Mixed $arg_2
         * @param Mixed $arg_3
         * 
         * @return $data
         */
        public function deprecatedActionMapping( $data, $arg_1 = '', $arg_2 = '', $arg_3 = '' )
        {
            $actionMap = $this->actionMap;
        
            $action = current_action();
            if ( isset( $actionMap[ $action ] ) ) {
                if ( has_action( $actionMap[ $action ] ) ) {
                    $data = do_action( $actionMap[ $action ], $data, $arg_1, $arg_2, $arg_3 );
                    if ( ! defined( 'DOING_AJAX' ) ) {
                        //_deprecated_function( 'The ' . $actionMap[ $action ] . ' action', '', $filter );
                        Logger::error( "The $actionMap[$action] action is deprecated!" );
                    }
                }
            }
            return $data;
        }

         /**
         * Map a deprecated filter
         *
         * @param Mixed $data
         * @param Mixed $arg_1
         * @param Mixed $arg_2
         * @param Mixed $arg_3
         * 
         * @return $data
         */
        public function deprecatedFilterMapping( $data, $arg_1 = '', $arg_2 = '', $arg_3 = '' )
        {
            $filterMap = $this->filterMap;
        
            $filter = current_filter();
            if ( isset( $filterMap[ $filter ] ) ) {
                if ( has_filter( $filterMap[ $filter ] ) ) {
                    $data = apply_filters( $filterMap[ $filter ], $data, $arg_1, $arg_2, $arg_3 );
                    if ( ! defined( 'DOING_AJAX' ) ) {
                        Logger::error( "The $filterMap[$filter] action is deprecated!" );
                        //_deprecated_function( 'The ' . $filterMap[ $filter ] . ' filter', '', $filter );
                    }
                }
            }
            return $data;
        }

        public function testDrive()
        {
            /*$test = new \CuisineSections\SectionTypes\ContentSection([
                'id' => 1,
                'position' => 1,
                'post_id' => 19,
                'container_id' => 0, 
                'title' => 'Section title',
                'hide_title' => 0,
                'hide_container' => 1,
                'view' => 'fullwidth',
                'type' => 'section',
                'columns' => array(
                    1 => 'content'
                )
            ]);
            dd( $test );*/
        }
    }