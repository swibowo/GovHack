<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Rest extends CI_Controller
{
	public $data;
	
        public function __construct()
        {
                parent::__construct();

                // disable compression so that there are no errors when echoing stuff
                $this->config->set_item('compress_output', FALSE);

		$this->load->model('ac_model');
        }

        public function index()
        {
		$this->load->view('ac/index', $this->data);
        }

	public function chronicle($params)
	{
	}

	public function entries()
	{
                $records = $this->ac_model->db_select($this->dbn_records,"","","","year, seriesNumber DESC");

		echo json_encode($records);
	}
	
	public function test123()
	{
		$this->load->model('ac_model');
		$this->ac_model->db_select('govHack_records');
		echo "here";
	}
}
