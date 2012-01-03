<?php
	require_once(dirname(__FILE__) . '/helpers.php');
	require_once(dirname(__FILE__) . '/OhHiFileCache.php');
	require_once(dirname(__FILE__) . '/OhHiImage.php');
	require_once(dirname(__FILE__) . '/OhHiDir.php');
	
	class OhHi {
		static $firstImage = '';
		static $lastImage = '';
	
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
		<title>OH-HI</title>
		<link rel="stylesheet" href="/oh-hi/css/all.css"/>
		<link rel="alternate" type="application/rss+xml" href="?get=rss"/>
		<link rel="start" href="?"/>
		<script src="/oh-hi/js/jquery.min.js"></script>
		<script src="/oh-hi/js/jquery.livequery.min.js"></script>
		<script src="/oh-hi/js/scroll-startstop.events.jquery.min.js"></script>
		<script src="/oh-hi/js/behavior.js"></script>
	</head>
	<body>
		<?php
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