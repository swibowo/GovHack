<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller
{
	private $dbn_records = "records";
	private $dbn_tags = "tags";

        public function __construct()
        {
                parent::__construct();

                // disable compression so that there are no errors when echoing stuff
                $this->config->set_item('compress_output', FALSE);

		// Load models here
                $this->load->model('ac_model');
		$this->load->model('image_model');
        }

        public function index()
        {
                echo "Ajax test response.";
        }

        public function getyear($width, $year=null)
        {
                $this->getlist($width, $year);
        }

        public function getcategory($width, $category=null)
        {
                $this->getlist($width, null,$category);
        }

        public function getlist($width, $year=null, /*$category=null,*/ $offset=0, $limit=5)
        {
                $where_clause = array();
                $join_clause = array();
		$max_record = 0;
		$next_offset = 0;
		$has_next = false;
		$prev_offset = 0;
		$has_prev = false;
		$records = array();
		$display_current = false;
		$default_select = "`".$this->dbn_records."`.`id`, `local_image`, `description`, `year`";


                if(!empty($year) && is_numeric($year) && $year > 0 && $year < date("Y"))
                {
                        $year = (int) $year;
                        $where_clause['year'] = $year;
                }

                if(!empty($category) && is_string($category))
                {
                        $where_clause[$this->dbn_tags.'.title'] = urldecode($category);
                        $join_clause[$this->dbn_tags] = " ".$this->dbn_tags.".recordID=".$this->dbn_records.".id and ".$this->dbn_tags.".typeID=-1 ";
                }


		$count_record = $this->ac_model->db_select($this->dbn_records,"count(`id`) as record_count",$where_clause,$join_clause,"year, itemNumber DESC");
		$max_record = $count_record[0]['record_count'];

		$max_year = $this->ac_model->db_select($this->dbn_records,"max(`year`) as max_year",$where_clause,$join_clause,"year DESC, itemNumber DESC");
		$max_year = $max_year[0]['max_year'];

		if(empty($year)) $year = $max_year;

		// later records
                $tmp_where_clause['year >'] = $year;
		if ($offset > 0 && ($offset + $limit) > $max_record)
		{
			if ($offset < $max_record)
			{
				$tmp_record_limit = ($offset + $limit) - $max_record;
				$tmp_offset = 0;
			}
			else 
			{
				$tmp_offset = ($offset - $max_record);
				$tmp_record_limit = $limit;
			}
			$tmp_where_clause['year >'] = $year;
			$next_records = $this->ac_model->db_select($this->dbn_records,$default_select,$tmp_where_clause,$join_clause,"year ASC, itemNumber ASC", $tmp_record_limit, $tmp_offset);
		}
		
		$check_next_records =  $this->ac_model->db_select($this->dbn_records,"`".$this->dbn_records."`.`id`, `local_image`, `description`",array('year >=' => $year),$join_clause,"year DESC, itemNumber DESC", 0, $offset+$limit);
		if (count($check_next_records) > 0)
		{
			$has_next = true;
			$next_offset = $offset+$limit;
		}

		// earlier records
		if ($offset < 0)
		{
			$tmp_record_limit = $offset * -1; // just to make positive value
			$tmp_offset = ($tmp_record_limit >= $limit) ? $tmp_record_limit : 0;

			if ($tmp_record_limit > $limit)
			{
				$tmp_record_limit = $limit;
			}

			$tmp_where_clause = array();
			if (!empty($year)) 
			{
                        	$tmp_where_clause['year <'] = $year;
			}

                        $prev_records = $this->ac_model->db_select($this->dbn_records,$default_select,$tmp_where_clause,$join_clause,"year DESC, itemNumber DESC", $tmp_record_limit, $tmp_offset);

			if ($tmp_offset > 0) 
			{
				$display_current = false;
			}
			else
			{
				$offset = 0;
				$display_current = true;
			}
		}
                $check_prev_records =  $this->ac_model->db_select($this->dbn_records,"`".$this->dbn_records."`.`id`, `local_image`, `description`",array('year <' => $year),$join_clause,"year DESC, itemNumber DESC", 0, (($offset-$limit)*-1));

                if (count($check_prev_records) >= $limit )
                {
                        $has_prev = true;
                        $prev_offset = $offset-$limit;
                }

		if ($offset >= 0 && ($max_record - $offset) > 0)
		{
			$display_current = true;
		}
		if($display_current)
		{
                	$current_records = $this->ac_model->db_select($this->dbn_records,$default_select,$where_clause,$join_clause,"year DESC, itemNumber DESC", $limit, $offset);
		}

		// ok, lets populate records now with special array looping
/*
		if(!empty($prev_records) && count($prev_records) > 0)
		{
			foreach($prev_records as $tmp_record)
			{
				array_push($records, $tmp_record);
			}
		}
                if(!empty($current_records) && count($current_records) > 0)
                {
                        foreach($current_records as $tmp_record)
                        {
                                array_push($records, $tmp_record);
                        }
                }
*/
                if(!empty($next_records) && count($next_records) > 0)
                {
                        foreach($next_records as $tmp_record)
                        {
                                array_push($records, $tmp_record);
                        }
                }
                if(!empty($current_records) && count($current_records) > 0)
                {
                        foreach($current_records as $tmp_record)
                        {
                                array_push($records, $tmp_record);
                        }
                }
                if(!empty($prev_records) && count($prev_records) > 0)
                {
                        foreach($prev_records as $tmp_record)
                        {
                                array_push($records, $tmp_record);
                        }
                }
                //$width = ($width / 2) / 100 * 80;
                $width = $width / 100 * 80;
                $new_records = $this->ac_model->convert_images($records, $width);

                $output = array(
                        'count' => count($new_records),
			'next' => $next_offset,
			'has_next' => $has_next,
			'previous' => $prev_offset,
			'has_prev' => $has_prev,
                        'records' => $new_records,
			'width' => $width,
                );

                echo json_encode($output);
        }

	public function getlocation($record_id)
	{
		$where_clauses = array (
			'typeID' => 2,
			'recordID' => $record_id
		);

		$map_records = $this->ac_model->db_select($this->dbn_tags, "`id`,`title`,`data`", $where_clauses,"", "", 0);

		$output = array(
			'count' => count($map_records),
			'records' => $map_records
		);

		echo json_encode($output);
	}

	public function setwidth($width)
	{
		if(empty($width) || !is_numeric($width))
		{
			// Set default width
			$width =320;
		}
		$this->session->set_userdata("screen_width", $width);	
		//echo $this->session->userdata("screen_width");	
	}
}
