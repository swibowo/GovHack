<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Ac_model extends CI_Model
{
	private $default_limit = 5;

        function __construct()
        {
                parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
        }

	/*
	*
	* Generic MySQL select statement generator
	* 
	*/

	function db_select($table, $select="", $where=array(), $join=array(), $order_by="", $limit=5, $offset=0)
	{

		if (!empty($select)) 
		{
			$this->db->select($select);
		}

		if(!is_numeric($offset) || $offset < 0)
		{
			$offset = 0;
		}

		if (isset($limit) && $limit == 0)
		{
			$limit = $this->default_limit;
		}

		if(!empty($limit) && is_numeric($limit) && $limit > 0)
		{
			$this->db->limit($limit, $offset);
		}

		if(!empty($where) && is_array($where))
		{
			foreach($where as $key=>$value)
			{
				$this->db->where($key, $value);
			}
		}

		if(!empty($join) && is_array($join))
		{
			foreach($join as $key=>$value)
			{
				$this->db->join($key, $value);
			}
		}

		if(!empty($order_by))
		{
			$this->db->order_by($order_by);
		}

		$query = $this->db->get($table);

		$results = array();
		foreach ($query->result_array() as $row)
		{
			array_push($results, $row);
		}

		return $results;
	}

	function convert_images($records=array(), $width=320)
	{
                $new_records = array();
                if(count($records) > 0)
                {
                        foreach($records as $values)
                        {
                                $new_image_path = "";

                                if(!empty($values['local_image']))
                                {
                                        $pos = strrpos($values['local_image'], ".");

                                        $new_image_path = substr($values['local_image'], 0, $pos)."_".$width.substr($values['local_image'], $pos);
					$new_image_path = str_replace("/tmp/","/resized/",$new_image_path);
                                        $new_image_path = $this->image_model->resize_image($width, $values['local_image'], $new_image_path);
                                }

				$values['img'] = $new_image_path;

				array_push($new_records, $values);
                        }
                }

		return $new_records;
	}
}
