<?php

namespace ChefForms\Front\Form;

use Cuisine\Utilities\Url;
use Cuisine\Wrappers\Template;
use ChefForms\Front\Form\Tag;


class Notification {



	/**
	 * Send this to
	 * 
	 * @var string ( email )
	 */
	public $to;


	/**
	 * Send this from
	 * 
	 * @var string ( email )
	 */
	protected $from_email;


	/**
	 * Name to send this from
	 * 
	 * @var string ( email )
	 */
	protected $from_name;


	/**
	 * Subject of the mail
	 * 
	 * @var string
	 */
	protected $subject;

	/**
	 * Template file to send
	 * 
	 * @var string
	 */
	protected $template;


	/**
	 * The eventual message
	 * 
	 * @var string
	 */
	protected $message;


	/**
	 * Headers of this notification
	 *
	 * @var array
	 */
	public $headers = array();


	/**
	 * Attachments of this notification
	 *
	 * @var array
	 */
	protected $attachments = array();


	/**
	 * All properties for this notification
	 * 
	 * @var array
	 */
	public $properties;

	/**
	 * All fields for this form
	 * 
	 * @var array
	 */
	public $fields;


	/**
	 * Current entry
	 * 
	 * @var array
	 */
	public $entry;



	/**
	 * Setup the basics
	 * 
	 * @param  array $notify
	 * @return void       
	 */
	function __construct(){

		$adminMail = get_bloginfo( 'admin_email' );
		$siteName = get_bloginfo( 'blogname' );
		$this->from_email = get_option( 'chef_forms_from_email', $adminMail );
		$this->from_name = get_option( 'chef_forms_from_name', $siteName );

	}

	/*======================================*/
	/*========= Public functions ===========*/
	/*======================================*/


	/**
	 * Create notification
	 * 
	 * @return ChefForms\Front\Notification
	 */
	public function make( $notify, $fields ){

		$this->fields = $fields;
		$this->properties = $this->getProperties( $notify );
		$this->entry = ( isset( $_POST['entry'] ) ? $_POST['entry'] : array() );

		$this->to = Tag::notification( $this->properties['to'], $this->entry );
		$this->subject = Tag::notification( $this->properties['title'], $this->entry );


		$this->setHeaders();
		$this->createMessage();

		return $this;
	}


	/**
	 * Send the notification
	 * 
	 * @return void
	 */
	public function send(){

		add_filter( "wp_mail_from", array( &$this, 'from' ) );
		add_filter( "wp_mail_from_name", array( &$this, 'from_name' ) );
		add_filter( "wp_mail_content_type", array( &$this, 'mime_type' ) );

			$test = wp_mail( 
				$this->to, 
				$this->subject, 
				$this->message, 
				$this->headers, 
				$this->attachments
			);


		remove_filter( "wp_mail_content_type", array( &$this, 'mime_type' ) );
		remove_filter( "wp_mail_from_name", array( &$this, 'from_name' ) );
		remove_filter( "wp_mail_from", array( &$this, 'from' ) );

	}



	/*======================================*/
	/*========= Message generation =========*/
	/*======================================*/


	/**
	 * Create the html message
	 * 
	 * @return void
	 */
	public function createMessage(){

		$msg = $this->properties['content'];

		if( $msg == '' )
			$msg = $this->generateDefaultMessage();


		$all_fields = array( '{{alle_velden}}', '{{ alle_velden }}' );
		$msg = str_replace( $all_fields , $this->generateDefaultMessage(), $msg );
		$msg = Tag::notification( $msg, $this->entry );

		$default = Url::path( 'chef-forms', 'chef-forms/Templates/Email/' ).'Html.php';
		
		ob_start();

			$params = array( 'msg' => $msg );
			Template::element( 'forms/Notification', $default )->display( $params );

		$this->message = ob_get_clean();

	}


	/**
	 * Create all the fields
	 * 
	 * @return void
	 */
	public function generateDefaultMessage(){

		if( !isset( $_POST['entry'] ) )
			return '';

		$entryItems = $_POST['entry'];
		$html = '<table style="width:540px">';

		foreach( $this->fields as $field ){

			$html .= $field->getNotificationPart( $entryItems );

		}

		$html .= '</table>';

		return $html;
	}


	/*======================================*/
	/*========= E-mail headers     =========*/
	/*======================================*/

	/**
	 * Set the headers for this notification
	 *
	 * @return void
	 */
	public function setHeaders(){

		if( !isset( $_POST['entry'] ) )
 			$this->headers = [];

		$replyTo = '';
		$entryItems = $_POST['entry'];

		//The reply-to header gets populated by the first email input value:
		foreach( $this->fields as $field ){
			
			$type = $field->type;
			if( !is_string( $type ) )
				continue;

			if( $type == 'email' && $replyTo == '' ){
				foreach( $entryItems as $entry ){

					if( $entry['name'] == $field->name )
						$replyTo = $entry['value'];

				}
			}
		}

		if( $replyTo != '' )
			$this->headers[] = "Reply-To: <$replyTo>\r\n";

	}



	/*======================================*/
	/*========= Getters & Setters ==========*/
	/*======================================*/

	/**
	 * Get the properties, with a default fallback
	 *
	 * @param  array $properties
	 * @return array
	 */
	protected function getProperties( $properties ){

		if( !isset( $properties['title'] ) )
			$properties['title'] = 'Bericht website';

		if( !isset( $properties['content'] ) )
			$properties['content'] = '{{ alle_velden }}';

		if( isset( $properties['attachments'] ) )
			$this->attachments = $properties['attachments'];

		return $properties;
	}



	/*======================================*/
	/*========= Filters ====================*/
	/*======================================*/


	/**
	 * Sets the right mime type
	 * 
	 * @return string
	 */
	public function mime_type(){
		return "text/html";
	}

	/**
	 * Set the sendee's email
	 * 
	 * @return string
	 */
	public function from(){
		return $this->from_email;
	}


	/**
	 * Set the sendee's name
	 *
	 * @return string
	 */
	public function from_name(){
		return $this->from_name;
	}

}