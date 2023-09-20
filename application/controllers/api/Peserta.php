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

class Peserta extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('Peserta_model');
		$this->load->model('User_model');
	}
	public function no_reg_post($jenis_kelamin)
	{
		if ($jenis_kelamin == 'laki-laki') {
			$cek_no_reg 	= $this->Peserta_model->cek_no_reg($jenis_kelamin);

			$kode 		= 'siatlet-pa-';
			$urutan 	= 1;

			if (!empty($cek_no_reg)) {
				$last_reg 	= $cek_no_reg->no_reg;
				$last_num 	= intval(str_replace($kode, '', $last_reg));
				$next_num 	= $last_num + 1;
				$no_reg 	= $kode.sprintf('%03s', $next_num);
			}
			else{
				$start_num 	= 1;
				$no_reg 	= $kode.sprintf('%03s', $start_num);
			}
		}
		if ($jenis_kelamin == 'perempuan') {
			$cek_no_reg 	= $this->Peserta_model->cek_no_reg($jenis_kelamin);
			
			$kode 		= 'siatlet-pi-';
			$urutan 	= 1;

			if (!empty($cek_no_reg)) {
				$last_reg 	= $cek_no_reg->no_reg;
				$last_num 	= intval(str_replace($kode, '', $last_reg));
				$start_num 	= $last_num + 1;
				$no_reg 	= $kode.sprintf('%03s', $start_num);
			}
			else{
				$start_num 	= 1;
				$no_reg 	= $kode.sprintf('%03s', $start_num);
			}
		}
		return $no_reg;
	}
	public function add_post() 
	{ // *
		if (count($this->post()) == 9) { 

			if ( $this->post('token') && $this->post('nama_peserta') && $this->post('tempat_lahir') && $this->post('tanggal_lahir') && $this->post('jenis_kelamin') && $this->post('berat_badan') && $this->post('alamat') && $this->post('pekerjaan') && $this->post('id_lomba') )
			{ // nama parameter sesuai


				$no_reg 			= $this->no_reg_post($this->post('jenis_kelamin'));
				$nama_peserta		= $this->post('nama_peserta');
				$tempat_lahir		= $this->post('tempat_lahir');
				$tanggal_lahir 		= $this->post('tanggal_lahir');
				$jenis_kelamin 		= $this->post('jenis_kelamin'); 
				$berat_badan 		= $this->post('berat_badan');
				$alamat 			= $this->post('alamat');
				$pekerjaan 			= $this->post('pekerjaan');
				$id_lomba 			= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key) {

						$data 	= array(
							'no_reg'			=> $no_reg,
							'nama_peserta'		=> $nama_peserta,
							'tempat_lahir'		=> $tempat_lahir,
							'tanggal_lahir' 	=> $tanggal_lahir,
							'jenis_kelamin' 	=> $jenis_kelamin,
							'berat_badan' 		=> $berat_badan,
							'alamat' 			=> $alamat,
							'pekerjaan' 		=> $pekerjaan,
							'id_lomba' 			=> $id_lomba,
							'created_at'		=> date('Y-m-d H-i-s')
						);
						$this->Peserta_model->insert($data); // insert ke database

						$response['meta']['code'] 		= '200';
						$response['meta']['status'] 	= 'success';
						$response['meta']['message'] 	= 'Data peserta berhasil ditambahkan';
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function update_post() 
	{ // *
		if (count($this->post()) == 11) { 

			if ( $this->post('token') && $this->post('id_peserta') && $this->post('no_reg') && $this->post('nama_peserta') && $this->post('tempat_lahir') && $this->post('tanggal_lahir') && $this->post('jenis_kelamin') && $this->post('berat_badan') && $this->post('alamat') && $this->post('pekerjaan') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_peserta 		= $this->post('id_peserta');
				$no_reg 			= $this->post('no_reg');
				$nama_peserta		= $this->post('nama_peserta');
				$tempat_lahir		= $this->post('tempat_lahir');
				$tanggal_lahir 		= $this->post('tanggal_lahir');
				$jenis_kelamin 		= $this->post('jenis_kelamin');  
				$berat_badan 		= $this->post('berat_badan');
				$alamat 			= $this->post('alamat');
				$pekerjaan 			= $this->post('pekerjaan');
				$id_lomba 			= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ){
						$cek_data 			= $this->Peserta_model->get_by_id($id_peserta);

						if (!empty($cek_data)) {
							
							$data 	= array(
								'no_reg'			=> $no_reg,
								'nama_peserta'		=> $nama_peserta,
								'tempat_lahir'		=> $tempat_lahir,
								'tanggal_lahir' 	=> $tanggal_lahir,
								'jenis_kelamin' 	=> $jenis_kelamin,
								'berat_badan' 		=> $berat_badan,
								'alamat' 			=> $alamat,
								'pekerjaan' 		=> $pekerjaan,
								'id_lomba' 			=> $id_lomba,
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->Peserta_model->update($id_peserta, $data);

							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['meta']['message'] 	= 'Data peserta berhasil diubah';
						}
						else {
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_id_post() 
	{ // *
		if (count($this->post()) == 2) { 

			if ( $this->post('token') && $this->post('id_peserta') )
			{ // nama parameter sesuai
				$id_peserta 		= $this->post('id_peserta');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_by_id($id_peserta);

						if (!empty($cek_data)) {

							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['meta']['message'] 	= 'Data ditemukan';
							$response['data']['id_peserta'] = $cek_data->id_peserta;
							$response['data']['id_lomba'] 	= $cek_data->id_lomba;
							$response['data']['nama_lomba'] = $cek_data->nama_lomba;
							$response['data']['no_reg'] 	= $cek_data->no_reg;
							$response['data']['nama_peserta'] 	= $cek_data->nama_peserta;
							$response['data']['tempat_lahir'] 	= $cek_data->tempat_lahir;
							$response['data']['tanggal_lahir'] 	= $cek_data->tanggal_lahir;
							$response['data']['jenis_kelamin'] 	= $cek_data->jenis_kelamin;
							$response['data']['berat_badan'] 	= $cek_data->berat_badan;
							$response['data']['alamat'] 		= $cek_data->alamat;
							$response['data']['pekerjaan'] 		= $cek_data->pekerjaan;
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_all_post() 
	{ // *
		if (count($this->post()) == 1) { 

			if ( $this->post('token') )
			{ // nama parameter sesuai
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_all();

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_peserta'] 	= $value->id_peserta;
								$response['data'][$key]['id_lomba'] 	= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 	= $value->nama_lomba;
								$response['data'][$key]['no_reg'] 		= $value->no_reg;
								$response['data'][$key]['nama_peserta'] 	= $value->nama_peserta;
								$response['data'][$key]['tempat_lahir'] 	= $value->tempat_lahir;
								$response['data'][$key]['tanggal_lahir'] 	= $value->tanggal_lahir;
								$response['data'][$key]['jenis_kelamin'] 	= $value->jenis_kelamin;
								$response['data'][$key]['berat_badan'] 	= $value->berat_badan;
								$response['data'][$key]['alamat'] 		= $value->alamat;
								$response['data'][$key]['pekerjaan'] 	= $value->pekerjaan;
							}
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function peserta_by_lomba_post() 
	{ // *
		if (count($this->post()) == 2) { 

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$token				= $this->post('token');
				$id_lomba			= $this->post('id_lomba');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_by_lomba($id_lomba);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $cek_data;
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function peserta_putra_post() 
	{ // *
		if (count($this->post()) == 2) {

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$token				= $this->post('token');
				$id_lomba			= $this->post('id_lomba');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_peserta_putra($id_lomba);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_peserta'] 	= $value->id_peserta;
								$response['data'][$key]['id_lomba'] 	= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 	= $value->nama_lomba;
								$response['data'][$key]['no_reg'] 		= $value->no_reg;
								$response['data'][$key]['nama_peserta'] 	= $value->nama_peserta;
								$response['data'][$key]['tempat_lahir'] 	= $value->tempat_lahir;
								$response['data'][$key]['tanggal_lahir'] 	= $value->tanggal_lahir;
								$response['data'][$key]['jenis_kelamin'] 	= $value->jenis_kelamin;
								$response['data'][$key]['berat_badan'] 	= $value->berat_badan;
								$response['data'][$key]['alamat'] 		= $value->alamat;
								$response['data'][$key]['pekerjaan'] 	= $value->pekerjaan;
							}
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function peserta_putri_post() 
	{ // *
		if (count($this->post()) == 2) { 

			if ( $this->post('token') && $this->post('id_lomba'))
			{ // nama parameter sesuai
				$token				= $this->post('token');
				$id_lomba			= $this->post('id_lomba');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_peserta_putri($id_lomba);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_peserta'] 	= $value->id_peserta;
								$response['data'][$key]['id_lomba'] 	= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 	= $value->nama_lomba;
								$response['data'][$key]['no_reg'] 		= $value->no_reg;
								$response['data'][$key]['nama_peserta'] 	= $value->nama_peserta;
								$response['data'][$key]['tempat_lahir'] 	= $value->tempat_lahir;
								$response['data'][$key]['tanggal_lahir'] 	= $value->tanggal_lahir;
								$response['data'][$key]['jenis_kelamin'] 	= $value->jenis_kelamin;
								$response['data'][$key]['berat_badan'] 	= $value->berat_badan;
								$response['data'][$key]['alamat'] 		= $value->alamat;
								$response['data'][$key]['pekerjaan'] 	= $value->pekerjaan;
							}
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function delete_post() 
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_peserta') )
			{ // nama parameter sesuai
				$id_peserta 		= $this->post('id_peserta');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->Peserta_model->get_by_id($id_peserta);

						if (!empty($cek_data)) {

							$this->Peserta_model->delete($id_peserta);

							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['meta']['message'] 	= 'Data peserta berhasil dihapus';
						}
						else{
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
							$response['meta']['message'] 	= 'Data peserta tidak ditemukan';
						}
					}
					else{
						$response['meta']['code'] 		= '404';
						$response['meta']['meta']['status'] 	= 'not found';
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
					}
				}
				else{
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
					$response['meta']['message'] 	= 'Token tidak valid';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
}
?>