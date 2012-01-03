<?php
	class OhHiFileCache {
		private $filename;
		private $data;
		private $dirty = false;
	
		function __construct($dir) {
			$this->filename = SITE_ROOT . '/' . $dir . '/cache';
			$this->load();
		}
		
		function __destruct() {
			if($this->dirty) {
				$this->persist();
			}
		}
		
		private function load() {
			if(!file_exists($this->filename)) {
				$this->data = array();
			}
			else {
				$this->data = unserialize(file_get_contents($this->filename));
			}
			$this->dirty = false;
		}
		
		private function persist() {
			file_put_contents($this->filename, serialize($this->data));
			$this->dirty = false;
		}
		
		public function getData() {
			return $this->data;
		}
		
		public function setData($data) {
			$this->data = $data;
			$this->dirty = true;
		}
		
		public function get($key) {
			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}
			else {
				return NULL;
			}
		}
		
		public function set($key, $value) {
			if(!array_key_exists($key, $this->data)) {
				$dirty = true;
			}
			else if($this->data[$key] !== $value) {
				$dirty = true;
			}
			if($dirty) {
				$this->dirty = true;
				$this->data[$key] = $value;
			}
		}
		
		public function remove($key) {
			if(array_key_exists($key, $this->data)) {
				unset($this->data[$key]);
				$this->dirty = true;
			}
		}
	}
?>