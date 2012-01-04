<?php
	/*
	
		This file is part of OhHi
		http://github.com/brandon-lockaby/OhHi
		
		(c) Brandon Lockaby http://about.me/brandonlockaby for http://oh-hi.info
		
		OhHi is free software licensed under Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
		http://creativecommons.org/licenses/by-nc-sa/3.0/
		
	*/
	
	class OhHiImage {
		public $dir;
		public $filename;
		public $exif;
		public $width;
		public $height;
		
		protected function __construct($dir, $filename) {
			$this->dir = $dir;
			$this->filename = $filename;
			$this->populateExif();
			$this->populateDimensions();
		}
		
		private function populateExif() {
			$this->exif = array();
			$desired_exif_names = array(
			  'FileName'
			  //, 'DateTime'
			  , 'DateTimeOriginal'
			  //, 'DateTimeDigitized'
			  //, 'Model'
			  //, 'Make'
			  //, 'Software'
			  //, 'FocalLength'
			  //, 'FocalLengthIn35mmFilm'
			  , 'ISOSpeedRatings'
			  //, 'ExposureProgram'
			  , 'ExposureBiasValue'
			  , 'FocalLengthIn35mmFilm'
			  , 'ExposureTime'
			  //, 'FNumber'
			);
			$exif = @exif_read_data(SITE_ROOT . '/' . $this->dir . '/' . $this->filename);
			if($exif === false) {
				return;
			}
			$filtered = array();
			foreach($desired_exif_names as $name) {
				if(!empty($exif[$name])) {
					$filtered[$name] = $exif[$name];
				}
			}
			if(isset($exif['COMPUTED']['ApertureFNumber'])) {
				$filtered['ApertureFNumber'] = $exif['COMPUTED']['ApertureFNumber'];
			}
			$this->exif = $filtered;
		}
		
		private function populateDimensions() {
			list($this->width, $this->height) = getimagesize(SITE_ROOT . '/' . $this->dir . '/' . $this->filename);
		}
		
		public static function fromFile($dir, $filename) {
			if(!preg_match('/(\.jpg$)|(\.jpeg$)|(\.gif$)/', $filename)) {
				return NULL;
			}
			if(!file_exists(SITE_ROOT . '/' . $dir . '/' . $filename)) {
				return NULL;
			}
			return new OhHiImage($dir, $filename);
		}
		
		public function getHtml() {
			$html_safe_dir = html_safe($this->dir);
			$html_safe_filename = html_safe($this->filename);
			$html_safe_url = html_safe($this->dir . ($this->dir == '\\' || $this->dir == '/' ? '' : '/') . $this->filename);
			$html_safe_exif = html_safe($this->exif);
			$html_safe_width = html_safe($this->width);
			$html_safe_height = html_safe($this->height);
			$result = "<span class=\"image\" data-dir=\"{$html_safe_dir}\" data-filename=\"{$html_safe_filename}\">" .
				"<img src=\"{$html_safe_url}\"" .
				" width=\"{$html_safe_width}\" height=\"{$html_safe_height}\"/>" .
			$result .= "<div class=\"exif\">";
			foreach($html_safe_exif as $key => $value) {
				$result .= "{$key}({$value})<br/>";
			}
			$result .= "</div>"; // class="exif"
			$result .= "</span>"; // class="image"
			return $result;
		}
	}
?>