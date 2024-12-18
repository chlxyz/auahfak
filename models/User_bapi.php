<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_bapi extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->helper('array');
		$this->load->library('saprfc');

	}
	/**
	 * [count_hold menghitung jabatan yang dimiliki satu orang dalam satu waktu]
	 * @param  string $nik [nomor induk karyawan max 10 digit]
	 * @param  string $tgl [tanggal YYYYMMDD default tanggal hari ini]
	 * @return [int]      [jumlah posisi]
	 */
	public function count_hold($nik='',$tgl='')
	{
		if ($tgl=='') {
			$tgl = date('Ymd');
		} else {
			$tgl = str_replace('-', '', $tgl);
		}

		$this->saprfc->connect();
		$this->saprfc->functionDiscover('ZHRFM_GETPOSORG_OM');
		$importParamName = array(
			'KEYDATE',
			'OBJID'
		);
		$importParamValue = array(
			$tgl,
			$nik
		);
		$this->saprfc->importParameter($importParamName, $importParamValue);
		$this->saprfc->setInitTable('FI_OUT');
		$this->saprfc->executeSAP();
		$post = $this->saprfc->fetch_rows('FI_OUT');
		$this->saprfc->free();
		$this->saprfc->close();
		return count($post);
	}
	/**
	 * [get_hold_list mendapatkan list jabatan yang sedang dimiliki sesorang dalam satu waktu]
	 * @param  string $nik [nomor induk karyawan max 10 digit]
	 * @param  string $tgl [tanggal YYYYMMDD default tanggal hari ini]
	 * @return [object]      [description]
	 */
	public function get_hold_list($nik='',$tgl='')
	{
		if ($tgl=='') {
			$tgl = date('Ymd');
		} else {
			$tgl = str_replace('-', '', $tgl);
		}
		$this->saprfc->connect();
		$this->saprfc->functionDiscover('ZHRFM_GETPOSORG_OM');
		$importParamName = array(
			'KEYDATE',
			'OBJID'
		);
		$importParamValue = array(
			$tgl,
			$nik
		);
		$this->saprfc->importParameter($importParamName, $importParamValue);
		$this->saprfc->setInitTable('FI_OUT');
		$this->saprfc->executeSAP();
		$post = $this->saprfc->fetch_rows('FI_OUT');
		$this->saprfc->free();
		$this->saprfc->close();
		return $post;
	}
	/**
	 * [get_obj_text mendapatkan Detail object ID  ( Organisasi / Posisi / Orang )]
	 * @param  string $obj_id [ID Object]
	 * @param  string $tgl    [tanggal YYYYMMDD default tanggal hari ini]
	 * @param  string $type   [tipe objek; O = organisasi, S = Posisi, P = orang]
	 * @return [text]         [description]
	 */
	public function get_obj_text($obj_id='',$tgl='',$type='')
	{
		if ($tgl=='') {
			$tgl = date('Ymd');
		} else {
			$tgl = str_replace('-', '', $tgl);
		}
		$this->saprfc->connect();
		$this->saprfc->functionDiscover('ZHRFM_GETOBJTEXT');
		$importParamName = array(
			'FI_KEYDATE',
			'FI_OBJID',
			'FI_OTYPE'
		);
		$importParamValue = array(
			$tgl,
			$obj_id,
			$type
		);
		$this->saprfc->importParameter($importParamName, $importParamValue);
		$this->saprfc->setInitTable('FE_HRP1000');
		$this->saprfc->executeSAP();
		$obj = $this->saprfc->fetch_row('FE_HRP1000','object',1);
		$this->saprfc->free();
		$this->saprfc->close();
		return $obj->STEXT;
	}

	public function get_chief($nik ='',$date='')
	{
		if ($date=='') {
			$date = date('Ymd');
		} else {
			$date = str_replace('-', '', $date);
		}
		$this->saprfc->connect();
		$this->saprfc->functionDiscover('ZHRFM_GETCHIEF');
		$importParamName = array(
			'FI_KEYDATE',
			'FI_PERNR'
		);
		$importParamValue = array(
			$date,
			$nik
		);
		$this->saprfc->importParameter($importParamName, $importParamValue);
		$this->saprfc->setInitTable('FE_HRP1000');
		$this->saprfc->executeSAP();
		$obj = $this->saprfc->fetch_row('FE_HRP1000','object',1);
		$this->saprfc->free();
		$this->saprfc->close();
		return $obj;
	}
}

/* End of file user_bapi.php */
/* Location: ./application/models/user_bapi.php */