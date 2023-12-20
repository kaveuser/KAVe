<?php 
	class Installer {
		private $db;

		function __construct(){
			
		}
		function getCurrentTimestamp(){
			return date("Y-m-d H:i:s",time());
		}
		function getLang($part="installation", $setlang="default", $langdir="default"){
			if($part === false) return false;
			if(!isset($_COOKIE['el_lang'])) {
			    setcookie('el_lang', 'en-us', 0, "/");
			    $_COOKIE['el_lang'] = 'en-us';
			}
			$curlang = $_COOKIE['el_lang'];
			if($setlang != "default"){
				$curlang = $setlang;
			}
			if($langdir == "default")
				$filelang = __DIR__."/../languages/$curlang/loc_$part.tp"; else
				$filelang = "$langdir/$curlang/loc_$part.tp";
			if(!file_exists($filelang)) return true;
			$output = file_get_contents($filelang);
			return json_decode($output, true);
		}
		function getLangProp($lang_id="en-us", $prop="id"){
			$filelang = __DIR__."/../languages/$lang_id/info.tp";
			if(!file_exists($filelang)) return false;
			$output = file_get_contents($filelang);
			$output = json_decode($output, true);
			return $output[$prop];
		}
		function getListLang(){
			$dirlang = __DIR__."/../languages/";
			$langlist = array_diff(scandir($dirlang), array('..', '.'));
			$output = "";
			if(!isset($_COOKIE['el_lang'])) {
			    setcookie('el_lang', 'en-us', 0, "/");
			    $_COOKIE['el_lang'] = 'en-us';
			    $selected = 'en-us';
			} else {
				$selected = $_COOKIE['el_lang'];
			}
			foreach ($langlist as $ls) {
				if(!is_dir($dirlang.$ls)) continue;
				$langname = $this -> getLangProp($ls, "lang");
				if($selected == $ls)
					$langname = "<i class=\"material-icons on-dropdown-menu\">check</i> <strong>$langname</strong>";
				$output .= "<li><a onclick=\"changeLanguage('$ls')\">$langname</a></li>\n";
			}
			return $output;
		}
		function installStep($write = false){
			$installStepFile = __DIR__."/../installed.step";
			if($write === false){
				$data = file_get_contents($installStepFile);
				return trim($data);
			} else {
				$data = trim($write);
				return file_put_contents($installStepFile, $data);
			}
		}
		function netralize_db($data){
			$data = preg_replace("/[^0-9A-Za-z._\-\s]/", "", $data);
			$data = trim($data);
			return $data;
		}
		function netralize_noinduk($data){
			return preg_replace("/[^0-9]/", "", $data);
		}
		function netralize_nama($data){
			$data = preg_replace("/[^A-Za-z.,\-'\s]/", "", $data);
			$data = trim($data);
			return str_replace("'","\'",$data);
		}
		function netralize_timezone($data){
			return preg_replace("/[^A-Za-z\/]/", "", $data);
		}
		function timezone_list() {
		    static $timezones = null;

		    if ($timezones === null) {
		        $timezones = [];
		        $offsets = [];
		        $now = new DateTime('now', new DateTimeZone('UTC'));

		        foreach (DateTimeZone::listIdentifiers() as $timezone) {
		            $now->setTimezone(new DateTimeZone($timezone));
		            $offsets[] = $offset = $now->getOffset();
		            $timezones[$timezone] = '(' . $this->format_GMT_offset($offset) . ') ' . $this->format_timezone_name($timezone);
		        }

		        array_multisort($offsets, $timezones);
		    }

		    return $timezones;
		}
		function format_timezone_name($name) {
		    $name = str_replace('/', ', ', $name);
		    $name = str_replace('_', ' ', $name);
		    $name = str_replace('St ', 'St. ', $name);
		    return $name;
		}
		function format_GMT_offset($offset) {
		    $hours = intval($offset / 3600);
		    $minutes = abs(intval($offset % 3600 / 60));
		    return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
		}
		function getTimezoneList(){
			$timezones = $this->timezone_list();
			$output = "<option value=\"default\">System Default</option>\n";
			foreach($timezones as $val => $label){
				$output .= "<option value=\"$val\">$label</option>\n";
			}
			return $output;
		}
		function emptyDir($dir) {
		    if (is_dir($dir)) {
		        $scn = scandir($dir);
		        foreach ($scn as $files) {
		            if ($files !== '.') {
		                if ($files !== '..') {
		                    if (!is_dir($dir . '/' . $files)) {
		                        unlink($dir . '/' . $files);
		                    } else {
		                        $this -> emptyDir($dir . '/' . $files);
		                        rmdir($dir . '/' . $files);
		                    }
		                }
		            }
		        }
		    }
		}
	}
?>