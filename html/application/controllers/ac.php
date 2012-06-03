<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ac extends CI_Controller
{
	public $data;

	private $dbn_records = "records";
	private $dbn_tags = "tags";
	
        public function __construct()
        {
                parent::__construct();

		$this->load->model('ac_model');

		$this->data['title'] = 'Aussie Chronology';
        }

        public function index()
        {
		$this->getlist();
        }

	public function getyear($year=null)
	{
		$this->getlist($year);
	}

	public function getcategory($category=null)
	{
		$this->getlist(null,$category);
	}

	public function getlist($year=null, /*$category=null,*/ $offset=null, $limit=5)
	{
		$where_clause = array();
		$join_clause = array();

		if(!empty($year) && is_numeric($year) && $year > 0 && $year < date("Y"))
		{
			$year = (int) $year;
			$where_clause['year'] = $year;
			$this->data['selected_year'] = $year;
		}

		if(!empty($category) && is_string($category))
		{
			$where_clause[$this->dbn_tags.'.title'] = urldecode($category);
			$join_clause[$this->dbn_tags] = " ".$this->dbn_tags.".recordID=".$this->dbn_records.".id and ".$this->dbn_tags.".typeID=-1 ";
			$this->data['selected_category'] = urldecode($category);
		}
		

		$this->data['records'] = $this->ac_model->db_select($this->dbn_records,"",$where_clause,$join_clause,"year DESC, itemNumber DESC");

		$this->load->view('ac/index', $this->data);
	}

	public function test123()
	{
		$records = $this->ac_model->db_select($this->dbn_records);
		print_r($records);
	}

	public function dick()
	{
		$this->load->model('image_model');
		$orig = 'assets/tmp/7418172_0001.jpg';
		$new = 'assets/a.jpg';
		$this->image_model->resize_image('200', $orig, $new);
		echo $new;
	}
}

