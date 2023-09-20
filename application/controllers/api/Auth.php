<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class Auth extends REST_Controller
{
	var $key 	= "";

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';
		$this->load->model('User_model');
	}
	public function register_post() // api tambah data user
	{
		if (count($this->post()) == 7) { // jumlah parameter sesuai

			if ($this->post('username') && $this->post('password') && $this->post('level') && $this->post('nama') && $this->post('no_hp') && $this->post('alamat') && $this->post('jenis_kelamin')) { // nama parameter sesuai

				$username 			= $this->post('username');
				$password 			= $this->post('password');
				$level 				= $this->post('level');
				$nama 				= $this->post('nama');
				$no_hp 				= $this->post('no_hp');
				$alamat 			= $this->post('alamat');
				$jenis_kelamin 		= $this->post('jenis_kelamin');

				$cek_username 		= $this->User_model->cek_username($username);

				if (empty($cek_username)) {

					$data 	= array(
						'username'			=> $username,
						'password' 			=> md5($password),
						'level' 			=> $level, // 1 : Admin , 2 : Pelatih , 3 : Pemilik
						'nama' 				=> $nama,
						'no_hp' 			=> $no_hp,
						'alamat' 			=> $alamat,
						'jenis_kelamin'		=> $jenis_kelamin, // 1 : Laki-Laki , 2 : Perempuan
						'created_at'		=> date('Y-m-d H-i-s')
					);
					$this->User_model->insert($data); // insert ke database

					$response['meta']['message'] 	= 'Registrasi akun berhasil dilakukan';
					$response['meta']['code'] 		= '200';
					$response['meta']['status'] 	= 'success';
				} else {
					$response['meta']['message'] 	= 'Username sudah terdaftar';
					$response['meta']['code'] 		= '04';
					$response['meta']['status'] 	= 'duplicate';
				}
			} else { // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		} else { // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function login_post()
	{ // *
		if (count($this->post()) == '3') {
			if ($this->post('username') && $this->post('password') && $this->post('level')) {
				$username 		= $this->post('username');
				$password 		= $this->post('password');
				$level 			= $this->post('level');

				$cek_akun 		= $this->User_model->login($username, $password, $level); // cek akun dengan username dan password
				if (!empty($cek_akun)) { // akun ditemukan

					$data_session 	= array();
					$data_token['key_secret'] 		= $this->key;
					$data_token['id_user'] 			= $cek_akun->id_user;
					$token 							= JWT::encode($data_token, $this->key, 'HS256');

					$response['meta']['message']	= 'Login sukses';
					$response['meta']['code'] 		= '200';
					$response['meta']['status'] 	= 'success';
					$response['data']['id_user']	= $cek_akun->id_user;
					$response['data']['nama']		= $cek_akun->nama;
					$response['data']['username'] 	= $cek_akun->username;
					$response['data']['level'] 		= $cek_akun->level;
					$response['data']['token']	 	= $token;
				} else { // akun tidak ditemukan
					$response['meta']['message'] 	= 'Username atau Password Salah';
					$response['meta']['code'] 		= '404';
					$response['meta']['status'] 	= 'not found';
				}
			} else {
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		} else {
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
}
