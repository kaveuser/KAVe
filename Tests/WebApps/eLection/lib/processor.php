<?php
	error_reporting(0);
	session_start();
	require_once "./classes/Installer.php";
	$installer = new Installer();
	$localize = $installer -> getLang();
	$action = $_POST['action'];
	define("_ROOT_", __DIR__."/../");
	define("_PACKAGES_", __DIR__."/packages/");
	switch($action){
		case "check_requirements":
			if (!defined('PHP_VERSION_ID')) {
			    $version = explode('.', PHP_VERSION);
			    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
			}
			if (PHP_VERSION_ID < 50207) {
			    define('PHP_MAJOR_VERSION',   $version[0]);
			    define('PHP_MINOR_VERSION',   $version[1]);
			    define('PHP_RELEASE_VERSION', $version[2]);
			}
			$phpVersion = PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION.".".PHP_RELEASE_VERSION;
			$zLibModule = extension_loaded("zip");
			$GDModule = extension_loaded("gd");
			$cURLModule = extension_loaded("curl");
			$versionCheck = true;
			if($zLibModule)
				$zLibIcon = '<i class="material-icons text-green">check_circle</i>';
				else $zLibIcon = '<i class="material-icons text-danger">error</i>';
			if($GDModule)
				$GDIcon = '<i class="material-icons text-green">check_circle</i>';
				else $GDIcon = '<i class="material-icons text-danger">error</i>';
			if(PHP_MAJOR_VERSION < 5) $versionCheck = false;
			if(PHP_MAJOR_VERSION == 5){
				if(PHP_MINOR_VERSION < 3) $versionCheck = false;
			}
			if($zLibModule && $GDModule && $versionCheck){
				$installable = true;
				$mainIcon = '<i class="material-icons text-green">check_circle</i>';
			} else {
				$installable = false;
				$mainIcon = '<i class="material-icons text-danger">error</i>';
			}

			if(!$cURLModule){
				if($versionCheck) $mainIcon = '<i class="material-icons text-warning">error</i>';
				$cURLIcon = '<i class="material-icons text-warning">error</i>';
			} else {
				$cURLIcon = '<i class="material-icons text-green">check_circle</i>';
			}

			$text = '<table>';
			$text .= '<tr>';
			$text .= '<td class="icon">';
			$text .= $mainIcon;
			$text .= '</td>';
			$text .= '<td>';
			$text .= "<b>PHP Version:</b> <span>$phpVersion / 5.3.0</span>";
				$text .= '<table>';
				$text .= '<tr>';
				$text .= '<td class="icon">';
				$text .= $zLibIcon;
				$text .= '</td>';
				$text .= '<td>';
				$text .= "zLib Module Extension";
				$text .= '</td>';
				$text .= '</tr>';
				$text .= '<tr>';
				$text .= '<td class="icon">';
				$text .= $GDIcon;
				$text .= '</td>';
				$text .= '<td>';
				$text .= "Graphics Draw (GD) Library Extension";
				$text .= '</td>';
				$text .= '</tr>';
				$text .= '<tr>';
				$text .= '<td class="icon">';
				$text .= $cURLIcon;
				$text .= '</td>';
				$text .= '<td>';
				$text .= "Client URL (cURL) Extension";
				$text .= '</td>';
				$text .= '</tr>';
				$text .= '</table>';
			$text .= '</td>';
			$text .= '</tr>';
			$text .= '</table>';

			$installer -> installStep("preinstall");

			$output = array(
				'installable' => $installable,
				'text' => $text
			);
		break;
		case "mysql_connect":
			$host = $_POST['mysql_host'];
			$user = $_POST['mysql_user'];
			$password = $_POST['mysql_password'];
			$connect = mysqli_connect($host, $user, $password);
			if(!$connect){
				$code = 403;
				$text = sprintf($localize['error_mysql_connection'], mysqli_connect_error($connect));
			} else {
				$code = 200;
				$text = $localize['success_mysql_connection'];
				$_SESSION['ins_host'] = $host;
				$_SESSION['ins_user'] = $user;
				$_SESSION['ins_password'] = $password;
			}
			$output = array(
				'code' => $code,
				'text' => $text
			);
			mysqli_close($connect);
		break;
		case "mysql_get_databases":
			$connect = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password']);
			$databases = [];
			$db_system = array("mysql", "information_schema", "performance_schema", "sys", "phpmyadmin");
			$sql = mysqli_query($connect, "SHOW DATABASES");
			while($row_sql = mysqli_fetch_array($sql)){
				$database = [];
				$database['name'] = $row_sql[0];
				if(in_array($database['name'], $db_system)){
					$database['type'] = "system";
				} else {
					$election_tables = array(
						"tb_guru",
						"tb_hakpilih",
						"tb_kandidat",
						"tb_level",
						"tb_panitia",
						"tb_pengaturan",
						"tb_polling",
						"tb_siswa"
					);
					$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $database['name']);
					$qry = mysqli_query($ct, "SHOW TABLES");
					$qry_count = mysqli_num_rows($qry);
					if($qry_count){
						$db_election = true;
						while($qry_row = mysqli_fetch_array($qry)){
							if(!in_array($qry_row[0], $election_tables)){
								$db_election = false;
								break;
							}
						}
						if($db_election){
							$qry2 = mysqli_query($ct, "SELECT * FROM tb_pengaturan WHERE id = 1");
							$qry2_row = mysqli_fetch_array($qry2);
							$database['type'] = "eLection";
							if($qry2_row['v_major'] < 2){
								$database['upgradable'] = true;
							} else {
								$database['upgradable'] = false;
							}
						} else {
							$database['type'] = "unknown";
						}
					} else {
						$database['type'] = "unformatted";
					}
					mysqli_close($ct);
				}
				array_push($databases, $database);
			}
			$output = array(
				"databases" => $databases
			);
			mysqli_close($connect);
		break;
		case "create_database":
			$dbName = $installer -> netralize_db($_POST['db_name']);
			$connect = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password']);
			$check = mysqli_query($connect, "SHOW DATABASES LIKE '$dbName'");
			if(mysqli_num_rows($check)){
				$code = 403;
				$text = sprintf($localize['error_mysql_database_exists'], "<b>".$dbName."</b>");
			} else {
				$create = mysqli_query($connect, "CREATE DATABASE `$dbName`");
				if($create){
					$code = 200;
					$text = $localize['success_mysql_database_created'];
				} else {
					$code = 403;
					$text = sprintf($localize['error_mysql_database'], mysqli_error($connect));
				}
			}
			$output = array(
				"code" => $code,
				"dbName" => $dbName,
				"text" => $text
			);
			mysqli_close($connect);
		break;
		case "delete_database":
			$dbName = $installer -> netralize_db($_POST['db_name']);
			$connect = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password']);
			$check = mysqli_query($connect, "SHOW DATABASES LIKE '$dbName'");
			if(mysqli_num_rows($check) == 1){
				$create = mysqli_query($connect, "DROP DATABASE `$dbName`");
				if($create){
					$code = 200;
					$text = $localize['success_mysql_database_deleted'];
				} else {
					$code = 403;
					$text = sprintf($localize['error_mysql_database'], mysqli_error($connect));
				}
			} else {
				$code = 403;
				$text = sprintf($localize['error_mysql_database_not_exist'], "<b>".$dbName."</b>");
			}
			$output = array(
				"code" => $code,
				"dbName" => $dbName,
				"text" => $text
			);
			mysqli_close($connect);
		break;
		case "save_preferences":
			$dbName = $installer -> netralize_db($_POST['db_name']);
			$connect = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password']);
			$check = mysqli_query($connect, "SHOW DATABASES LIKE '$dbName'");
			if(mysqli_num_rows($check) == 1){
				$_SESSION['ins_database'] = $dbName;
				$code = 200;
				$text = "";
				$election_tables = array(
					"tb_guru",
					"tb_hakpilih",
					"tb_kandidat",
					"tb_level",
					"tb_panitia",
					"tb_pengaturan",
					"tb_polling",
					"tb_siswa"
				);
				$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $dbName);
				$qry = mysqli_query($ct, "SHOW TABLES");
				$qry_count = mysqli_num_rows($qry);
				if($qry_count){
					$db_election = true;
					$table_list = [];
					while($qry_row = mysqli_fetch_array($qry)){
						if(!in_array($qry_row[0], $election_tables)){
							$db_election = false;
						}
						array_push($table_list, $qry_row[0]);
					}
					$format_query = "SET FOREIGN_KEY_CHECKS = 0;\n";
					foreach($table_list as $table){
						$format_query .= "DROP TABLE IF EXISTS `$table`; \n";
					}
					$format_query .= "SET FOREIGN_KEY_CHECKS = 1;";
					if($db_election){
						$qry2 = mysqli_query($ct, "SELECT * FROM tb_pengaturan WHERE id = 1");
						$qry2_row = mysqli_fetch_array($qry2);
						$database['type'] = "eLection";
						if($qry2_row['v_major'] < 2){
							$_SESSION['ins_action'] = "upgrade";
						} else {
							mysqli_multi_query($ct, $format_query);
							$_SESSION['ins_action'] = "install";
							$text = $format_query;
						}
					} else {
						mysqli_multi_query($ct, $format_query);
						$_SESSION['ins_action'] = "install";
					}
				} else {
					$_SESSION['ins_action'] = "install";
				}
				$installer -> installStep("installed-preferences");
				mysqli_close($ct);
			} else {
				$code = 403;
				$text = $localize['error_saving_preferences'];
			}
			$output = array(
				"code" => $code,
				"dbName" => $dbName,
				"text" => $text,
				"install_type" => $_SESSION['ins_action']
			);
			mysqli_close($connect);
		break;
		case "install_packages":
			define("_PACKAGE_APPS_", _PACKAGES_."Applications.pkg");
			$update = new ZipArchive;
	        if($update -> open(_PACKAGE_APPS_)){
	          for($i = 0; $i < $update -> numFiles; $i++){
	            $file = $update->statIndex($i);
	            if($file['size'] == 0){
	              $dir = _ROOT_.$file['name'];
	              if(!file_exists($dir)){
	                mkdir($dir);
	              }
	              continue;
	            }
	            $update -> extractTo(_ROOT_, $file['name']);
	          }
	        }
	        $update -> close();
	        $installer -> installStep("installed-package");
			$code = 200;
			$output = array(
				"code" => $code,
				"text" => $text
			);
		break;
		case "install_package_scripts":
			$code = 200;
			if($_SESSION['ins_action'] == "install"){
				$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $_SESSION['ins_database']);
				$qry = file_get_contents(_PACKAGES_."Scripts-Install.pkg");
				$qry = mysqli_multi_query($ct, $qry);
				if(!$qry){
					$code = 403;
					$text = mysqli_error($ct);
				}
				mysqli_close($ct);
				sleep(1);
			}
			if($code == 200){
				$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $_SESSION['ins_database']);
				$qry = file_get_contents(_PACKAGES_."Scripts-ArcticFox.pkg");
				$qry = mysqli_multi_query($ct, $qry);
				if($qry){
					$code = 200;
					$installer -> installStep("installed-scripts");
				} else {
					$code = 403;
					$text = mysqli_error($ct);
				}
				mysqli_close($ct);
			}
			$output = array(
				"code" => $code,
				"text" => $text
			);
		break;
		case "install_save_conn":
			$connection = file_get_contents(_PACKAGES_."Scripts-Connection.pkg");
			$connection = sprintf($connection, $_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $_SESSION['ins_database']);
			$target = _ROOT_."admin/inc/conn.php";
			if(file_exists($target)) unlink($target);
			if(file_put_contents($target, $connection)){
				$code = 200;
				$installer -> installStep("installed-conn");
			} else {
				$code = 403;
				$text = $localize['error_saving_conn'];
			}
			$output = array(
				"code" => $code,
				"text" => $text,
				"install_type" => $_SESSION['ins_action']
			);
		break;
		case "update-admin":
			$myId = $installer -> netralize_noinduk($_POST['myId']);
			$myPass = $_POST['myPass'];
			$myName = $installer -> netralize_nama($_POST['myName']);
			$myName = ucwords($myName);
			$code = 200;
			if(strlen($myName) < 2){
				$code = 403;
				$output = array(
					'code' => $code,
					'component' => '#user_name',
					'text' => $localize['error_admin_name']
				);
			}
			if(strlen($myPass) < 1){
				$code = 403;
				$output = array(
					'code' => $code,
					'component' => '#user_pass',
					'text' => $localize['error_admin_pass']
				);
			}
			if(strlen($myId) < 1){
				$code = 403;
				$output = array(
					'code' => $code,
					'component' => '#user_id',
					'text' => $localize['error_admin_id']
				);
			}
			if($code == 200){
				$myPass = md5($myPass);
				$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $_SESSION['ins_database']);
				$qry = mysqli_query($ct, "UPDATE tb_panitia SET no_induk='$myId', nama='$myName', password='$myPass' WHERE id=1");
				if($qry){
					$installer -> installStep("installed-admin");
					$code = 200;
					$output = array(
						'code' => $code
					);
				} else {
					$code = 403;
					$text = sprintf($localize['error_update_admin'], mysqli_error($ct));
					$output = array(
						'code' => $code,
						'component' => '#user_id',
						'text' => $text
					);
				}
				mysqli_close($ct);
			}
		break;
		case "update-timezone":
			$myTimezone = $installer -> netralize_timezone($_POST['myTimezone']);
			if(strlen($myTimezone) < 2){
				$output = array(
					'code' => 403,
					'text' => $localize['error_timezone_format']
				);
			} else {
				$ct = mysqli_connect($_SESSION['ins_host'], $_SESSION['ins_user'], $_SESSION['ins_password'], $_SESSION['ins_database']);
				$qry = mysqli_query($ct, "UPDATE tb_pengaturan SET timezone='$myTimezone' WHERE id=1");
				if($qry){
					$output = array(
						'code' => 200
					);
					$installer -> installStep("installed-timezone");
				} else {
					$output = array(
						'code' => 403,
						'text' => sprintf($localize['error_update_admin'], mysqli_error($ct))
					);
				}
				mysqli_close($ct);
			}
		break;
		case "install-cleanup":
			$toRemove = array(
				"../lib/", 
				"../index.php"
			);
			$toRename = array(
				"../index-tmp.php" => "../index.php",
				"../lib-tmp" => "../lib"
			);
			foreach($toRemove as $files){
				if(file_exists($files)){
					if(is_dir($files)){
						$installer -> emptyDir($files);
						rmdir($files);
					} else {
						unlink($files);
					}
				}
			}
			sleep(1);
			foreach ($toRename as $key => $value) {
				$file = $key;
				$fileTo = $value;
				if(file_exists($file)){
					rename($file, $fileTo);
				}
			}
			unset(
				$_SESSION['ins_host'],
				$_SESSION['ins_user'],
				$_SESSION['ins_password'],
				$_SESSION['ins_database'],
				$_SESSION['ins_action']
			);
			$output = array('code' => 200, 'text' => $text);
		break;
	}
	echo json_encode($output);
?>