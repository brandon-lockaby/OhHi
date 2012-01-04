<?php
	/*
	
		This file is part of OhHi
		http://github.com/brandon-lockaby/OhHi
		
		(c) Brandon Lockaby http://about.me/brandonlockaby for http://oh-hi.info
		
		OhHi is free software licensed under Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)
		http://creativecommons.org/licenses/by-nc-sa/3.0/
		
	*/
	
	$cwd = str_replace('\\', '/', getcwd());
	$swd = dirname($_SERVER["SCRIPT_NAME"]);
	if(substr($cwd, -strlen($swd)) === $swd) {
		define('SITE_ROOT', substr($cwd, 0, strrpos($cwd, $swd)));
	}
	else {
		define('SITE_ROOT', $cwd);
	}
	
	function html_safe($var)
	{
		if(is_array($var))
		{
			foreach($var as $key => $value)
				$var[$key] = html_safe($value);
		}
		else
		{
			$var = htmlspecialchars($var, ENT_QUOTES);
		}
		return $var;
	}
	
	// note: javascript must reverse html_safe
	function js_string_safe($var)
	{
		if(is_array($var))
		{
			foreach($var as $key => $value)
				$var[$key] = js_string_safe($value);
		}
		else
		{
			$var = html_safe($var);
			$var = str_replace("\\", "\\\\", $var);
			$var = str_replace("\n", "\\n", $var);
			$var = str_replace("\r", "\\r", $var);
		}
		return $var;
	}
	
	if (!function_exists('json_encode'))
	{
	  function json_encode($a=false)
	  {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
		  if (is_float($a))
		  {
			// Always use "." for floats.
			return floatval(str_replace(",", ".", strval($a)));
		  }
	 
		  if (is_string($a))
		  {
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		  }
		  else
			return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		{
		  if (key($a) !== $i)
		  {
			$isList = false;
			break;
		  }
		}
		$result = array();
		if ($isList)
		{
		  foreach ($a as $v) $result[] = json_encode($v);
		  return '[' . join(',', $result) . ']';
		}
		else
		{
		  foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
		  return '{' . join(',', $result) . '}';
		}
	  }
	}
	
	function strip( $array )
	{
		if (!is_array( $array ))
		{
			$array = stripslashes($array); 
		}
		else
		{
			foreach ($array as $key => $value)
			{
				$array[$key] = strip ( $array[$key] ); 
			}
		}
		return $array;
	}
	if (get_magic_quotes_gpc())
	{
		$_POST = strip($_POST);
		$_GET = strip($_GET);
	}  

?>
