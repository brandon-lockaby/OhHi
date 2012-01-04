<?php
	/*
	
		This file is part of OhHi
		http://github.com/brandon-lockaby/OhHi
		
		(c) Brandon Lockaby http://about.me/brandonlockaby for http://oh-hi.info
		
		OhHi is free software licensed under Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
		http://creativecommons.org/licenses/by-nc-sa/3.0/
		
	*/
	
	class OhHiDir {
		private $dir;
		private $cache;

		function __construct($dir) {
			$this->dir = $dir;
			$this->load();
		}
		
		public static function exifDateTimeOriginalSort($a, $b) {
			return $a->exif['DateTimeOriginal'] > $b->exif['DateTimeOriginal'] ? -1 : 1;
		}
		
		private function load() {
			$this->cache = new OhHiFileCache($this->dir);
			$filenames = @scandir(SITE_ROOT . '/' . $this->dir) or exit(html_safe(SITE_ROOT . '/' . $this->dir) . " not a folder ");
			$sort = false;
			
			// add new files to cache
			
			$pattern = '/(\.jpg$)|(\.jpeg$)|(\.gif$)/';
			foreach($filenames as $filename) {
				if(!preg_match($pattern, $filename)) {
					continue;
				}
				if($this->cache->get($filename) === NULL) {
					$this->cache->set($filename, OhHiImage::fromFile($this->dir, $filename));
					$sort = true;
				}
			}
			
			// remove deleted files from cache
			
			$data = $this->cache->getData();
			foreach($data as $key => $value) {
				if(!in_array($key, $filenames)) {
					$this->cache->remove($key);
				}
			}
			
			// sort
			
			if($sort) {
				$data = $this->cache->getData();
				uasort($data, 'OhHiDir::exifDateTimeOriginalSort');
				$this->cache->setData($data);
			}
		}
		
		public function getData() {
			return $this->cache->getData();
		}
		
		// get images below $from
		// used for ajax as well as noscript linkage
		public function getBelow($from, $number) {
			$data = $this->getData();
			// seek to the desired position
			reset($data);
			if($from && $from != '') {
				while(($kvp = each($data)) && $kvp['key'] !== $from) {
				}
			}
			// result
			$result = array();
			for($i = 0; $i < $number; $i++) {
				$kvp = each($data);
				if($kvp === false) {
					break;
				}
				array_push($result, $kvp['value']);
			}
			return $result;
		}
		
		// get images above $from
		// used for ajax
		public function getAbove($from, $number) {
			if(!$from || $from == '') {
				return array();
			}
			$data = $this->getData();
			// seek to the desired position
			reset($data);
			while(($kvp = each($data)) && $kvp['key'] !== $from) {
			}
			prev($data);
			prev($data);
			// result
			$result = array();
			for($i = 0; $i < $number; $i++) {
				if(is_null(key($data))) {
					break;
				}
				array_unshift($result, current($data));
				prev($data);
			}
			return $result;
		}
		
		// get images below $from including $from
		// used for index and for html5 linkage
		public function getIndex($from, $number) {
			$data = $this->getData();
			// seek to the desired position
			reset($data);
			if($from && $from != '') {
				while(($kvp = each($data)) && $kvp['key'] !== $from) {
				}
				prev($data);
			}
			// result
			$result = array();
			for($i = 0; $i < $number; $i++) {
				$kvp = each($data);
				if($kvp === false) {
					break;
				}
				array_push($result, $kvp['value']);
			}
			return $result;
		}
	}
?>