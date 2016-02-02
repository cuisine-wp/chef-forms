<?php
    
namespace ChefForms\Front;


	class Entry{

		/**
		 * Form object for this entry
		 * 
		 * @var ChefForms\Front\Form;
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

			//first upload files, if we have any:
			if( !empty( $_FILES ) )
				$this->uploadFiles();


			//setup the $entry variable
			if( !empty( $_POST ) )
				$this->sanitizeData();


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

			$entryId = wp_insert_post( $args );

			//set entry id in the post global, for easy acces:
			$_POST['entry_id'] = $entryId;
			$entry = $_POST['entry'];

			$entry = apply_filters( 'chef_forms_entry_values', $entry );

			//save all fields
			update_post_meta( $entryId, 'entry', $entry );

			do_action( 'after_entry_save', $this->form, $entry );

			return $entry;

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
								do_action( 'chef_forms_before_file_upload', $file, $this->form );
		
								//upload the bunch:
								$tempFile = $file['tmp_name'];
								
								$filename = date('YmdHis').'-'.$file['name'];
								$targetFile = $path . $filename;
		
								$upload = move_uploaded_file( $tempFile, $targetFile );
		
								if( $upload ){
									//add a response:
									$info = getimagesize( $targetFile );
							
									$file['path'] = $targetFile;
									$file['url'] = $url . $filename;
							
									$this->files[ $key ] = $file;
		
					    		}else{
									//add an error:
									$this->message = array( 'error' => true, 'message' => 'Uploaden 		mislukt, probeer het later nog eens.' );
		
					    		}
	
					    		do_action( 'chef_forms_after_file_upload', $file, $this->form );
					  		}
					    }
	
					}else{
					   	//add an error; no upload folder.
						$this->form->message = array( 
							'error' => true, 
							'message' => 'De upload-map kan niet aangemaakt worden...'
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
				'chef_forms_upload_dir',
				'chef-forms'
			);
			
			$uploadFolder = trailingslashit( $uploadFolder ).'form_'.$this->form->id;
			
			$base = trailingslashit( $uploadDir['basedir'] ).$uploadFolder;
			$baseUrl = trailingslashit( 
				content_url( 'uploads/'.$uploadFolder )
			);
			
			do_action( 'chef_forms_before_uploads', $this->form );
			
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

			foreach( $_entry as $name => $value ){

				$entry[] = array(
					'name'	=> $name,
					'value'	=> $value
				);

			}

			//add files to entry:
			if( !empty( $this->files ) ){

				foreach( $this->files as $key => $info ){
					$entry[] = array(
								'name'	=> $key,
								'value'	=> $info['url'],
								'data'	=> $info
					);
				}
			}

			$_POST['entry'] = $entry;
			return $entry;

		}

	}


   
