<?php
    
namespace CuisineForms\Front\Form;


	class Entry{

		/**
		 * Form object for this entry
		 * 
		 * @var CuisineForms\Front\Form;
		 */
		public $form;

		/**
		 * Array of files being send
		 * 
		 * @var array
		 */
		public $files = array();


		/**
		 * Make a form entry
		 * 
		 * @return array
		 */
		public function make( $_form ){

			$this->form = $_form;

			//setup the $entry variable
			if( !empty( $_POST ) )
				$this->sanitizeData();


			//first upload files, if we have any:
			if( !empty( $_FILES ) )
				$this->uploadFiles();


			$entry = $this->save();
			
			return $entry;
		}


		/**
		 * Save a single entry
		 * 
		 * @return array $entry
		 */
		public function save(){

			do_action( 'before_entry_save', $this->form, $_POST['entry'] );

			$title = 'Inschrijving '.\get_the_title( $this->form->id ).' - '.date( 'd-m-Y' );

			$args = array(
				'post_title'	=> 	$title,
				'post_parent' 	=>	$this->form->id,
				'post_type' 	=> 	'form-entry',
				'post_status'	=> 	'publish',
				'post_date'		=> 	date( 'Y-m-d H:i:s' ), 
				'post_date_gmt'	=>	date( 'Y-m-d H:i:s' )
			);

			$args = apply_filters( 'cuisine_forms_entry_postdata', $args, $this->form );
			$entryId = wp_insert_post( $args );

			//set entry id in the post global, for easy acces:
			$_POST['entry_id'] = $entryId;
			$entry = $this->constructEntryArray();


			//save all fields
			update_post_meta( $entryId, 'entry', $entry );

			if( !empty( $this->files ) )
				update_post_meta( $entryId, 'files', array_values( $this->files ) ); 

			do_action( 'after_entry_save', $this->form, $entry );

			return $entry;

		}

		/**
		 * Constructs the correct entry-array, with files if needbe
		 * 
		 * @return array
		 */
		public function constructEntryArray(){

			$entry = $_POST['entry'];

			if( !empty( $this->files ) )
				$entry = array_merge( $entry, $this->files );
			

			$entry = apply_filters( 'cuisine_forms_entry_values', $entry );

			return $entry;
		}


		/**
		 * Map entry values to form fields
		 * 
		 * @return array
		 */
		public function map( $_form, $_entry = array() ){

			$this->form = $_form;

			//setup the $entry variable
			if( !empty( $_POST ) )
				$this->sanitizeData();

			$_mapped = array();

			//if there's no entry set:
			if( empty( $_entry ) ){

				//fallback on the $_POST global
				if( empty( $_POST ) )
					return false;

				$_entry = $_POST['entry'];
			} 

			
			//set a prefix
			$prefix = 'field_'.$this->form->id.'_';

			//loop through the form fields:
			foreach( $this->form->fields as $field ){

				$id = str_replace( $prefix, '', $field->name );
				$_mapped[ $id ] = array();

				//find the corresponding entry value:
				foreach( $_entry as $entryField ){
					
				    if( $field->name == $entryField['name'] ){

				    	//get the label, from placeholders if we have to:
				    	$label = $field->label;
				    	if( $label == '' && $field->properties['placeholder'] != '' )
				    	    $label = $field->properties['placeholder'];

				    	//add the label, name and value to the mapped array:
				    	$_mapped[ $id ]['label'] = $label;
				    	$_mapped[ $id ]['name'] = $field->name;
				    	$_mapped[ $id ]['value'] = $entryField['value'];
	
				    } 
				}


			}

			return $_mapped;
		}




		/**
		 * Upload files, if they're being send
		 * 
		 * @return void
		 *
		 * 
		 */
		private function uploadFiles(){

			if( !empty( $_FILES ) ){

				$filesAvailable = false;

				foreach( $_FILES as $file ){
					if( $file['size'] > 0 )
						$filesAvailable = true;
				}


				if( $filesAvailable ){

					$folder = $this->makeDir();
	
					//if there's a folder:		
					if( $folder ){
						
						$path = $folder['path'];
						$url = $folder['url'];

						foreach( $_FILES as $key => $file ){
							
							if( $file['size'] > 0 ){
								do_action( 'cuisine_forms_before_file_upload', $file, $this->form );
		
								//upload the bunch:
								$tempFile = $file['tmp_name'];
								
								$filename = date('YmdHis').'-'.$file['name'];
								$targetFile = $path . $filename;
		
								$upload = move_uploaded_file( $tempFile, $targetFile );
		
								if( $upload ){
									
									//prep a response:
									$value = array(
										'name'		=> $file['name'],
										'mime_type'	=> $file['type'],
										'path' 		=> $targetFile,
										'url'		=> $url . $filename,
										'width'		=> 0,
										'height'	=> 0
									);

									$info = getimagesize( $targetFile );

									if( is_array( $info ) && !empty( $info ) ){

										if( isset( $info[0] ) )
											$value['width'] = $info[ 0 ];

										if( isset( $info[1] ) )
											$value['height'] = $info[ 1 ];
									}

									//add the response to the files array:
									$this->files[] = array(

										'name'	=> $key,
										'value'	=> $value

									);
		
					    		}else{
									//add an error:
									$this->message = array( 'error' => true, 'message' => __( 'Upload failed, please try again later', 'cuisineforms' ) );
		
					    		}
	
					    		do_action( 'cuisine_forms_after_file_upload', $file, $this->form );
					  		}
					    }
	
					}else{
					   	//add an error; no upload folder.
						$this->form->message = array( 
							'error' => true, 
							'message' => __( 'The upload-folder couldn\'t be created...', 'cuisineforms' )
						);
					}
				}
			}

			if( !empty( $this->form->message ) ){
				echo json_encode( $this->form->message );
				die();
			}
		}


		/**
		 * Make directories 
		 * 
		 * @return mixed
		 */
		private function makeDir(){

			$uploadDir = wp_upload_dir();
			$uploadFolder = apply_filters( 
				'cuisine_forms_upload_dir',
				'chef-forms'
			);
			
			$uploadFolder = trailingslashit( $uploadFolder ).'form_'.$this->form->id;
			
			$base = trailingslashit( $uploadDir['basedir'] ).$uploadFolder;
			$baseUrl = trailingslashit( 
				content_url( 'uploads/'.$uploadFolder )
			);
			
			do_action( 'cuisine_forms_before_uploads', $this->form );
			
			//create a base directory, if necissary:
			if( !is_dir( $base ) ){
			
				$folder = mkdir( $base );
			
			}else{
			    		
				$folder = true;
			    	
			}
			
			$uploadPath = $base . DIRECTORY_SEPARATOR;
					
			if( $folder ){

				return array(
					'path'	=> $uploadPath,
					'url'	=> $baseUrl

				);

			}

			return false;
		}

		/**
		 * Clean up the POST global for processing
		 * 
		 * @return void
		 */
		private function sanitizeData(){

			$entry = array();
			$_entry = $_POST;
			unset( $_entry['action'] );
			unset( $_entry['post_id'] );
			unset( $_entry['_chef_form_submit'] );
			unset( $_entry['_wp_http_referer'] );
			unset( $_entry['_fid'] );
			unset( $_entry['_rootPid'] );

			//remove anti-spam measures before saving
			$_entry = AntiSpam::sanitizeEntry( $_entry );

			foreach( $_entry as $name => $value ){

				$entry[] = array(
					'name'	=> $name,
					'value'	=> $value
				);

			}


			$_POST['entry'] = $entry;
			return $entry;

		}

	}


   
