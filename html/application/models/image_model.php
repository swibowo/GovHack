<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class image_model extends CI_Model
{
	private $da_host = "https://www.destinationaustralia.gov.au";
	private $dsn_records = "records";

        public function __construct()
        {
                parent::__construct();
		$this->load->database();
        }

	public function check_image_files($record_ids=null)
	{
		// $record_ids preferred to be array of ids
		if(!empty($record_ids) && !is_array($record_ids)) 
		{
			$record_ids = array($record_ids);
		}

		foreach($record_ids as $record_id)
		{
			$this->db->select();
			$this->db->where('id',(int) $record_id);
			
			$query = $this->db->get($this->dsn_records);
			$row = $query->result_array();

			if(empty($row)) continue;

			if(!empty($row['image']) && (empty($row['local_image']) || !file_exists($row['local_image'])))
			{
				$stream = file_get_contents($this->da_host.$row['image']);
				if(!empty($stream))
				{
					$asset_path = 'assets/tmp/'.basename($row['image']);
					file_put_contents($asset_path, $stream);

					$data = array(
						'local_image' => $asset_path,
					);

					$this->db->where('image', $row['image']);
					$this->db->update('records', $data);
				}
				else
				{
					// log error
				}
			}
		}
	}

	public function resize_image($width, $original_path, $new_path)
	{	
		if(!file_exists($original_path))
		{
			// If file is not there, try to download from server
			
		}

		// return nothing if error
		if(empty($width) || empty($original_path) || !file_exists($original_path) || empty($new_path))
		{
			return false;
		}

		// if the file exists, it has been already created, please save same memory :)
		if(file_exists($new_path))
		{
			return $new_path;
		}
			
		$this->load->library('image_lib');

		$original_size = getimagesize($original_path);

		$or_width = $original_size[0];
		$or_height = $original_size[1];

		if($width < $or_width)
		{
			$proportion = $or_width / $width;
			$expected_height = $or_height / $proportion;

			// resize image
			$config = array();
			$config['source_image'] = $original_path;
			$config['width'] = $width;
			$config['height'] = $expected_height;
			$config['new_image'] = FCPATH.$new_path;
			$config['maintain_ratio'] = true;

			$this->image_lib->initialize($config);
			$this->image_lib->resize();
			$this->image_lib->clear();

			return $new_path;
		}
		else
		{
			return $original_path;
		}
	}
}
