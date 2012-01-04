<?php
	/*
	
		This file is part of OhHi
		http://github.com/brandon-lockaby/OhHi
		
		(c) Brandon Lockaby http://about.me/brandonlockaby for http://oh-hi.info
		
		OhHi is free software licensed under Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
		http://creativecommons.org/licenses/by-nc-sa/3.0/
		
	*/
	
	require_once(dirname(__FILE__) . '/helpers.php');
	require_once(dirname(__FILE__) . '/OhHiFileCache.php');
	require_once(dirname(__FILE__) . '/OhHiImage.php');
	require_once(dirname(__FILE__) . '/OhHiDir.php');
	
	class OhHi {
		static $firstImage = '';
		static $lastImage = '';
	
		static function renderTopbar() {
			$links = array(
				'chronological' => '/',
				'tech' => '/tech/',
				'info' => '/info/'
			);
			
			print('<div id="topbar">');
			$last = count($links) - 1;
			for($i = 0; $i <= $last; $i++) {
				$link = each($links);
				print("<span><a href=\"{$link['value']}\">{$link['key']}</a></span>");
				if($i != $last) {
					print('<span>|</span>');
				}
			}
//			print('<span>|</span><span><a href="http://oh-hi.info/?get=rss" target="_blank"><img src="/oh-hi/images/feed-icon-14x14.png"/></a></span>');
//			print('<span>|</span><span><a href="http://oh-hi.info/?get=rss" target="_blank"><img src="/oh-hi/images/04.png"/></a></span>');
//			print('<span>|</span><span><a href="http://www.google.com/ig/add?feedurl=http%3a%2f%2foh-hi.info%2f%3fget%3drss" target="_blank"><img src="/oh-hi/images/31.png"/></a></span>');
			print('<span>|</span><span><a href="http://feeds.feedburner.com/Http/oh-hiinfo" target="_blank"><img src="/oh-hi/images/04.png"/></a></span>');

			print('</div>'); // id="topbar"
		}
		
		static function renderImages() {
			$dir = new OhHiDir(dirname($_SERVER['SCRIPT_NAME']));
			$get = strtolower($_GET['get']);
			$from = isset($_GET['from']) ? $_GET['from'] : '';
			switch($get) {
				case 'next':
				case 'below':
					$images = $dir->getBelow($from, 32);
					break;
				case 'previous';
				case 'above':
					$images = $dir->getAbove($from, 32);
					break;
				default:
					$images = $dir->getIndex($from, 32);
					break;
			}
			OhHi::$firstImage = $images[0] ? $images[0] : '';
			print('<div id="images">');
			foreach($images as $image) {
				print($image->getHtml());
				OhHi::$lastImage = $image;
			}
			print('</div>'); // id="images"
		}
		
		static function renderRss() {
			include(dirname(__FILE__) . '/OhHiRss.php');
		}
		
		static function renderIndex() {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<meta name="keywords" content="oh-hi,andrew@oh-hi,andrew@oh-hi.info,andrew eglington,andrew wade eglington,eglington,photo,photograph,photographer,photography,picture,image,catadioptric,mirror lens,600mm,1000mm"/>
		<title>OH-HI</title>
		<link rel="icon" type="image/ico" href="/oh-hi/images/oh-hi_16px.png"/>
		<link rel="stylesheet" href="/oh-hi/css/all.css"/>
		<link rel="alternate" type="application/rss+xml" href="?get=rss"/>
		<link rel="author" href="/info/"/>
		<link rel="help" href="/info/"/>
		<link rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/"/>
		<link rel="start" href="?"/>
		<script src="/oh-hi/js/jquery.min.js"></script>
		<script src="/oh-hi/js/jquery.livequery.min.js"></script>
		<script src="/oh-hi/js/scroll-startstop.events.jquery.min.js"></script>
		<script src="/oh-hi/js/behavior.js"></script>
	</head>
	<body>
		<?php
			OhHi::renderTopbar();
			OhHi::renderImages();
		?>
		<noscript>
			<div id="noscript" style="clear: both">
				<p>
					Automatic loading of images is disabled because your browser doesn't have JavaScript enabled.
					<a href="http://enable-javascript.com/">Learn more</a>
				</p>
				<p id="nav">
					<a href="?">FIRST</a>
					<a href="?get=previous&from=<?php echo html_safe(OhHi::$firstImage->filename); ?>">PREVIOUS</a>
					<a href="?get=next&from=<?php echo html_safe(OhHi::$lastImage->filename); ?>">NEXT</a>
				</p>
			</div>
		</noscript>
	</body>
</html>
<?php
		}
		
		static function run() {
			$get = strtolower($_GET['get']);
			if($get === 'rss') {
				OhHi::renderRss();
			}
			else if($get === 'below' || $get === 'above') {
				OhHi::renderImages();
			}
			else { // if($get === 'next' || $get === 'previous' || true) {
				OhHi::renderIndex();
			}
		}
	}
?>