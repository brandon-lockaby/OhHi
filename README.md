OhHi
====

Originally written for [oh-hi.info](http://oh-hi.info), this software allows one to run a certain style of photo gallery by simply dropping the photos into a folder.  Photos are chronologically sorted based on EXIF tags.  Modern browsers can enjoy lovely histograms along with selected EXIF data when the user clicks an image.

Usage
-----

1. Drop the `oh-hi` folder into your web root.
2. Drop the supplied `index.php` script into any folder containing images.

Problem?
--------

The software creates a file named `cache` in your image folder -- if something breaks, try removing the cache file