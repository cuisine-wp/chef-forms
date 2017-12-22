<?php
namespace CuisineForms;

class Autoloader
{

    /**
     * Load the initial static files:
     *
     * @return void
     */
    public function load()
    {
        //for the front:
        Front\SMTP::getInstance();
        Front\Ajax::getInstance();
        Front\Assets::getInstance();
        Front\EventListeners::getInstance();
        
        //and the admin:
        if( is_admin() ){
            Admin\Ajax::getInstance();
            Admin\Assets::getInstance();
            Admin\EventListeners::getInstance();
        
            Fields\Tabs\AdvancedSettingsTab::getInstance();
            Admin\Form\Entries\EventListeners::getInstance();
            Admin\Form\Settings\BaseSettingsPanelListener::getInstance();
        }
    }


    /**
     * Register the autoloader
     *
     * @return CuisineForms\Autoloader
     */
    public function register()
    {
        spl_autoload_register(function ($class) {

            try{
                if ( stripos( $class, __NAMESPACE__ ) === 0 ) {

                    $filePath = str_replace( '\\', DS, substr( $class, strlen( __NAMESPACE__ ) ) );
                    include( __DIR__ . DS . 'Classes' . $filePath . '.php' );

                }
            }catch( Exception $e ){
                
                dd( $e->getMessage() );

            }

        });

        
        return $this;
    }
}