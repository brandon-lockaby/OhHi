<?php
	/*
	
		This file is part of OhHi
		http://github.com/brandon-lockaby/OhHi
		
		(c) Brandon Lockaby http://about.me/brandonlockaby for http://oh-hi.info
		
		OhHi is free software licensed under Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
		http://creativecommons.org/licenses/by-nc-sa/3.0/
		
	*/
	
	header('Content-type: text/xml');
	$site_url = 'http://' . $_SERVER['SERVER_NAME'];
	$html_safe_site_url = html_safe($site_url);
	$html_safe_dir = html_safe(dirname($_SERVER['SCRIPT_NAME']));
	if($html_safe_dir === '\\' || $html_safe_dir === '/') {
		$html_safe_dir = '';
	}
	$dir = new OhHiDir(dirname($_SERVER['SCRIPT_NAME']));
	$images = $dir->getIndex('', 64);
	echo '<?xml version="1.0"?>';
?><rss version="2.0">
	<channel>
		<title><?php echo $html_safe_site_url . $html_safe_dir; ?></title>
		<link><?php echo $html_safe_site_url . $html_safe_dir; ?></link>
		<description><?php echo $html_safe_site_url . $html_safe_dir; ?></description>
		<language>en-us</language>
		<copyright>Copyright (C) <?php echo date('Y') . ' ' . html_safe($_SERVER['SITE_NAME']); ?></copyright>
		<pubDate><?php echo date('D, d M Y H:i:s O'); ?></pubDate>
		<lastBuildDate><?php echo date('D, d M Y H:i:s O'); ?></lastBuildDate>
		<generator>OH-HI</generator>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<ttl>15</ttl>
<?php
	foreach($images as $image) {
		$html_safe_filename = html_safe($image->filename);
		$html_safe_url = $html_safe_site_url . $html_safe_dir . '/' . $html_safe_filename;
		$html_safe_link = $html_safe_site_url . $html_safe_dir . '/?from=' . $html_safe_filename;
		$html_safe_exif = html_safe($image->exif);
?>
		<item>
			<title><?php echo $html_safe_filename; ?></title>
			<link><?php echo $html_safe_link; ?></link>
			<description><?php echo html_safe("<a href=\"{$html_safe_link}\"><img src=\"{$html_safe_url}\" width=\"{$image->width}\" height=\"{$image->height}\"/></a>"); ?></description>
			<guid><?php echo $html_safe_link; ?></guid>
			<pubDate><?php echo $html_safe_exif['DateTimeOriginal']; ?></pubDate>
		</item>
<?php
	} // foreach($images as $image)
?>
	</channel>
</rss>