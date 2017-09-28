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
	protected $fromEmail;


	/**
	 * Name to send this from
	 *
	 * @var string ( email )
	 */
	protected $fromName;


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
	public $attachments = array();


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
		$this->fromEmail = get_option( 'chef_forms_from_email', $adminMail );
		$this->fromName = get_option( 'chef_forms_from_name', $siteName );

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
		$this->properties = $this->sanitizeProperties( $notify );
		$this->entry = ( isset( $_POST['entry'] ) ? $_POST['entry'] : array() );

		$this->to = Tag::notification(
						$this->properties['to'],
						$this->fields,
						$this->entry
		);

		$this->subject = Tag::notification(
							$this->properties['title'],
							$this->fields,
							$this->entry
		);

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

		add_filter( "wp_mail_from", array( &$this, 'setFrom' ) );
		add_filter( "wp_mail_from_name", array( &$this, 'setFromName' ) );
		add_filter( "wp_mail_content_type", array( &$this, 'setMimeType' ) );

			$test = wp_mail(
				$this->to,
				$this->subject,
				$this->message,
				$this->headers,
				$this->attachments
			);

		remove_filter( "wp_mail_content_type", array( &$this, 'setMimeType' ) );
		remove_filter( "wp_mail_fromName", array( &$this, 'setFromName' ) );
		remove_filter( "wp_mail_from", array( &$this, 'setFrom' ) );

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
		$msg = Tag::notification( $msg, $this->fields, $this->entry );

		$default = Url::path( 'chef-forms', 'chef-forms/Templates/Email/' ).'Html.php';
		$notificationTemplate = apply_filters( 'chef_forms_notification_template', 'email/layout', $this );

		ob_start();

			$params = apply_filters( 'chef_forms_notification_params', array( 'msg' => $msg ), $this );
			Template::find( $notificationTemplate, $default )->display( $params );

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

		if( !isset( $_POST['entry'] ) ){
 			$this->headers = [];
 			return false;
		}

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
	protected function sanitizeProperties( $properties ){

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
	public function setMimeType(){
		return "text/html";
	}

	/**
	 * Set the sendee's email
	 *
	 * @return string
	 */
	public function setFrom(){
		return $this->fromEmail;
	}


	/**
	 * Set the sendee's name
	 *
	 * @return string
	 */
	public function setFromName(){
		return $this->fromName;
	}

}