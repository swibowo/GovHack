<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

if(!defined('BASEPATH')) exit('No direct script access allowed');

class retrieve extends CI_Controller
{
        public function __construct()
        {
                parent::__construct();

		$this->load->driver('cache', array('adapter' => 'memcached'));
        }

        public function _index($p = '')
        {
		if($p == 'run')
		{
			$this->db->select('image');
			$this->db->from('records');
			//$this->db->where('local_image is null');
			$this->db->order_by('id', 'asc');
			//$this->db->limit(100);
			//$this->db->join('comments', 'comments.id = blogs.id');

			$query = $this->db->get();
		
			$fp = fopen('log.txt', 'w');
	
			//mkdir('assets');
			//mkdir('assets/tmp');

			foreach($query->result_array() as $row)
			{
				if(!empty($row['image']) && empty($row['local_image']))
				{
					$stream = file_get_contents('https://www.destinationaustralia.gov.au'.$row['image']);
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
						fwrite($fp, "Error for file ".'https://www.destinationaustralia.gov.au'.$row['image']."\n");
					}
				}
				else
				{
					if(file_exists($row['local_image']))
					{
						fwrite($fp, "COOL ".$row['local_image']." the file exists\n");
					}
				}
			}	

			fclose($fp);
		}
        }

	function _cache()
	{
		$this->load->driver('cache', array('adapter' => 'memcached'));//, 'backup' => 'file'));

		/*

        	$foo = $this->cache->get('testkey');

		if(empty($foo))
		{
		     echo 'Saving to the cache!<br />';
		     $foo = 'test value';

		     // Save into the cache for 5 minutes
		     $this->cache->save('testkey', $foo, 300);
		}
		else
		{
		   var_dump($foo);
		}
		*/

		        $this->db->select('image');
                        $this->db->from('records');
                        $this->db->order_by('id', 'asc');
                        $query = $this->db->get();

                        foreach($query->result_array() as $row)
			{
				
			}
	}
}
