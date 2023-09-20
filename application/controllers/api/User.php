<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class User extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('User_model');
	}
	public function add_post()
	{ // *
		if (count($this->post()) == 3) { // jumlah parameter sesuai

			if ( $this->post('nama') && $this->post('username') && $this->post('level') ){ // nama parameter sesuai

				$nama 				= $this->post('nama');
				$username 			= $this->post('username');
				$level 				= $this->post('level');

				$check_akun 		= $this->User_model->cek_level_username($username, $level);

				if (!(empty($check_akun))) {
					$response['meta']['message'] 	= 'Username sudah terdaftar';
					$response['meta']['code'] 		= '02';
					$response['meta']['status'] 	= 'error';
				}
				else{
					$data 	= array(
						'nama'				=> $nama,
						'username'			=> $username,
						'password_decrypt'	=> $username,
						'password_encrypt'	=> md5($username),
						'level' 			=> $level,
						'created_at'		=> date('Y-m-d H-i-s')
					);
					$this->User_model->insert($data);

					$response['meta']['message'] 	= 'Data user berhasil ditambahkan';
					$response['meta']['code'] 		= '200';
					$response['meta']['status'] 	= 'success';
				}		
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function reset_password_post()
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('username') && $this->post('level') ){

				$username 			= $this->post('username');
				$level 				= $this->post('level');

				$cek_username 		= $this->User_model->cek_level_username($username, $level);

				if(!empty($cek_username)){
					$data 	= array(
						'username'			=> $username,
						'password_decrypt'	=> $username,
						'password_encrypt'	=> md5($username),
						'level' 			=> $level,
						'updated_at'		=> date('Y-m-d H-i-s')
					);
					$this->User_model->reset_password($data, $username, $level);

					$response['meta']['message'] 	= 'Reset password berhasil dilakukan';
					$response['meta']['code'] 		= '200';
					$response['meta']['status'] 	= 'success';
				}
				if(empty($cek_username)){
					$response['meta']['message'] 	= 'Data user tidak ditemukan';
					$response['meta']['code'] 		= '404';
					$response['meta']['status'] 	= 'not found';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function change_password_post()
	{ // *
		if (count($this->post()) == 4) { // jumlah parameter sesuai

			if ( $this->post('id_user') && $this->post('level') && $this->post('password_baru') && $this->post('token') ){

				$id_user 			= $this->post('id_user');
				$password_baru		= $this->post('password_baru');
				$level 				= $this->post('level');
				$token 				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ){

						$cek_username 		= $this->User_model->cek_level_iduser($id_user, $level);

						if(!empty($cek_username)){
							$data 	= array(
								'password_decrypt'	=> $password_baru,
								'password_encrypt'	=> md5($password_baru),
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->User_model->update($id_user, $data);

							$response['meta']['message'] 	= 'Ubah password berhasil dilakukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						if(empty($cek_username)){
							$response['meta']['message'] 	= 'Data user tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Token tidak valid';
						$response['meta']['code'] 		= '03';
						$response['meta']['status'] 	= 'error';
					}
				}
				else{
					$response['code'] 		= '03';
					$response['status'] 	= 'error';
					$response['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function update_post()
	{ // *
		if (count($this->post()) == 5 || count($this->post()) == 6) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_user') && $this->post('nama') && $this->post('username') && $this->post('level') )
			{ // nama parameter sesuai
				$id_user 			= $this->post('id_user');
				$nama 				= $this->post('nama');
				$username 			= $this->post('username');
				$password 			= $this->post('password');
				$level 				= $this->post('level');
				$token 				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ){

						$cek_user 			= $this->User_model->get_by_id($id_user);

						if (!empty($cek_user)) { 
							if ($password != "") { 
								$data 	= array(
									'nama'				=> $nama,
									'username'			=> $username,
									'password_decrypt' 	=> $password,
									'password_encrypt' 	=> md5($password),
									'level' 			=> $level, 
									'updated_at'		=> date('Y-m-d H-i-s')
								);
								$this->User_model->update($id_user, $data);
							}
							else{
								$data 	= array(
									'nama'				=> $nama,
									'username'			=> $username,
									'level' 			=> $level, 
									'updated_at'		=> date('Y-m-d H-i-s')
								);
								$this->User_model->update($id_user, $data);
							}

							$response['meta']['message'] 	= 'Data user berhasil diubah';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{ 
							$response['meta']['message'] 	= 'Data user tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Token tidak valid';
						$response['meta']['code'] 		= '03';
						$response['meta']['status'] 	= 'error';
					}
				}
				else{
					$response['code'] 		= '03';
					$response['status'] 	= 'error';
					$response['message'] 	= 'Token tidak valid';
				}
			}
			else{
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function delete_post()
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai
			if ($this->post('token') && $this->post('id_user')) { // nama parameter sesuai 

				$id_user 	= $this->post('id_user');
				$token 		= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_user 	= $this->User_model->get_by_id($id_user);

						if (!empty($cek_user)) {
							$this->User_model->delete($id_user);

							$response['meta']['message'] 	= 'Data user berhasil dihapus';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{	
							$response['meta']['message'] 	= 'Data user tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}				
			}
			else{  // nama parameter tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_id_post()
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai
			if ($this->post('token') && $this->post('id_user')) { // nama parameter sesuai 

				$id_user 	= $this->post('id_user');
				$token 		= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_user 	= $this->User_model->get_by_id($id_user); // cek data user by id_user

						if (!empty($cek_user)) { // data user ditemukan

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data']['id_user']	= $cek_user->id_user;
							$response['data']['nama'] 		= $cek_user->nama;
							$response['data']['username'] 	= $cek_user->username;
							$response['data']['password'] 	= $cek_user->password_decrypt;
							$response['data']['level'] 		= $cek_user->level;
							$response['data']['created_at'] = $cek_user->created_at;
							$response['data']['updated_at']	= $cek_user->updated_at;
						}
						else{	// data user tidak ditemukan
							$response['meta']['message'] 	= 'Data user tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				} 
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}				
			}
			else{  // nama parameter tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_all_post()
	{ // *
		if (count($this->post()) == 1) { // jumlah parameter sesuai
			if ($this->post('token')) {

				$token 				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_user 	= $this->User_model->get_all();

						if (!empty($cek_user)) { // data user ditemukan
							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $cek_user;
						}
						else{	// data user tidak ditemukan
							$response['meta']['message'] 	= 'Data user tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}	
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}	
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_level_post()
	{ // *
		if (count($this->post()) == 1) { // jumlah parameter sesuai

			if ( $this->post('level') ){

				$level 				= $this->post('level');

				$cek_level 		= $this->User_model->cek_level($level);

				if(!empty($cek_level)){

					$response['meta']['message'] 	= 'Data ditemukan';
					$response['meta']['code'] 		= '200';
					$response['meta']['status'] 	= 'success';
					$response['data'] 				= $cek_level;
				}
				else {
					$response['meta']['message'] 	= 'Data user tidak ditemukan';
					$response['meta']['code'] 		= '404';
					$response['meta']['status'] 	= 'not found';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
}
?>