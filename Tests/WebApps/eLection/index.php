<?php
	error_reporting(0);
	session_start();
	require_once "./lib/classes/Installer.php";
	$installer = new Installer();
	$localize = $installer -> getLang();
?>
<!doctype html>
<html lang="en">

<head>
	<title>Installer</title>
    <meta charset="utf-8" />
	<link rel="shortcut icon" href="./lib/images/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <!-- Bootstrap core CSS     -->
    <link href="./lib/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="./lib/css/material-dashboard.css?v=1.2.0" rel="stylesheet" />
	<link href="./lib/css/material-tripath.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
	<link href="./lib/css/material-icons/material-icons.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
	<script src="./lib/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<link href="./lib/css/designer.css" rel="stylesheet" />
</head>

<body>
    <div class="wrapper">
    	<?php include("./lib/images/nice-background.php"); ?>
        <div class="main-panel width-full">
			<div id="toolbar" class="noselect">
				<ul class="toolbar">
					<li class="nav-dropdown">
						<a data-toggle="dropdown" class="main menubar"><div></div></a>
						<ul class="dropdown-menu menu-bar">
							<li><a target="_blank" href="//sourceforge.net/p/election-by-tripath/wiki/Documentation%20-%20Installer%20Guide/"><?php echo $localize['menu_help_guide']; ?></a></li>
							<li><a target="_blank" href="//fauzantrif.wordpress.com/"><?php echo $localize['menu_help_visit']; ?></a></li>
						</ul>
					</li>
					<li class="nav-dropdown">
						<a data-toggle="dropdown" class="main"><strong>Installer</strong></a>
						<ul class="dropdown-menu menu-bar">
							<li class="multi-dropdown">
								<a><?php echo $localize['menu_languages']; ?> <span class="shortcut"><b class="caret caret-right align-right"></b></span></a>
								<ul class="dropdown-menu width-auto">
									<?php echo $installer -> getListLang(); ?>
								</ul>
							</li>
						</ul>
					</li>
					<li class="nav-dropdown pull-right">
						<a class="main" id="clock"></a>
					</li>
				</ul>
			</div>

			<div id="msgbox-overlay"></div>
			<div id="msgbox">
				<div class="ktn"></div>
				<div class="buttons"></div>
			</div>
	        <div class="content noselect">
		        <div class="col-md-8 col-centered">
			       	<div class="card" id="mainwindow">
			       		<div class="window opened" id="window-loading">
			       			<div class="window-header height-100">
			       				<div class="vertical-middle">
			       					<div class="spinner">
			       						<div class="double-bounce1"></div>
			       						<div class="double-bounce2"></div>
			       					</div>
			       				</div>
			       			</div>
						</div>
			       		<div class="window" id="window-welcome">
			       			<div class="window-header">
			       				<div class="vertical-middle">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2>Install eLection</h2>
			       					<h4>Arctic Fox</h4>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle">
			       					<button onclick="openWindow('#window-requirements', function(){ processToServer('check_requirements'); })">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_next']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-requirements">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2>Requirements Check</h2>
			       					<p>Your server must meet minimum requirements to continue install eLection.</p>
			       					<div class="window-content"></div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button id="btn-requirements-recheck" class="pull-left" onclick="processToServer('check_requirements')">
			       						<div class="icon">
			       							<i class="material-icons">replay</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_recheck']; ?></div>
			       					</button>
			       					<button id="btn-requirements-done" disabled="disabled" onclick="openWindow('#window-license')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_next']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-license">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2><?php echo $localize['title_license']; ?></h2>
			       					<p><?php echo $localize['text_license']; ?></p>
			       					<div class="lisensi">
			       						<?php echo str_replace("\n","<br>",file_get_contents("./license.tp")); ?>
			       					</div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle">
			       					<button onclick="openWindow('#window-requirements')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_back</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_disagree']; ?></div>
			       					</button>
			       					<button onclick="agreeLicense()">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_agree']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-mysql-connect">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2><?php echo $localize['title_mysql_connection']; ?></h2>
			       					<p><?php echo $localize['text_mysql_connection']; ?></p>
			       					<div class="window-content">
			       						<div class="row">
			       							<div class="col-sm-12">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_mysql_host']; ?></label>
													<input class="form-control" id="mysql_host" type="text" value="localhost">
												</div>
			       							</div>
			       						</div>
			       						<div class="row">
			       							<div class="col-sm-6">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_mysql_user']; ?></label>
													<input class="form-control" id="mysql_user" type="text" value="root">
												</div>
			       							</div>
			       							<div class="col-sm-6">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_mysql_pass']; ?></label>
													<input class="form-control" id="mysql_password" type="password">
												</div>
			       							</div>
			       						</div>
			       						<div class="row">
			       							<div class="col-sm-12">
			       								<div class="left">
			       									<div id="mysql-connect-info" class="installer-info"></div>
												</div>
			       							</div>
			       						</div>
			       					</div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button class="pull-left" onclick="cancelInstall()">
			       						<div class="icon">
			       							<i class="material-icons">close</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_cancel']; ?></div>
			       					</button>
			       					<button onclick="openWindow('#window-license')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_back</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_back']; ?></div>
			       					</button>
			       					<button id="mysql_connect_do" onclick="processToServer('mysql_connect')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_next']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-mysql-database">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2><?php echo $localize['title_mysql_database']; ?></h2>
			       					<p><?php echo $localize['text_mysql_database']; ?></p>
			       					<div class="window-content no-padding">
			       						<div class="db-list">
			       							
			       						</div>
			       						<div class="window-content-buttons">
			       							<button class="btn" onclick="addDatabase()">
			       								<i class="material-icons">add</i>
			       							</button>
			       							<button id="btn-database-remove" class="btn" onclick="deleteDatabase()" disabled>
			       								<i class="material-icons">remove</i>
			       							</button>
			       							<button id="btn-databases-refresh" class="btn pull-right" onclick="processToServer('mysql_databases')">
			       								<i class="material-icons">replay</i>
			       							</button>
			       						</div>
			       					</div>
			       					<div class="left">
			       						<div id="mysql-database-info" class="installer-info"></div>
			       					</div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button class="pull-left" onclick="cancelInstall()">
			       						<div class="icon">
			       							<i class="material-icons">close</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_cancel']; ?></div>
			       					</button>
			       					<button onclick="openWindow('#window-mysql-connect')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_back</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_back']; ?></div>
			       					</button>
			       					<button id="mysql-database-ok" onclick="confirmInstall()" disabled="disabled">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_install']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-installing">
			       			<div class="window-header height-100">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2><?php echo $localize['title_installing_eLection']; ?></h2>
			       					<h4>Arctic Fox</h4>
			       					<div class="row">
			       						<div class="col-sm-6 col-centered">
				       						<p></p>
					       					<div class="progress">
												<div class="progress-bar">
													<div class="progress-animator"></div>
												</div>
											</div>
					       					<span id="installing-process-info"></span>
				       					</div>
				       				</div>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-user">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/user.png" class="logo">
			       					<h2><?php echo $localize['title_user']; ?></h2>
			       					<p><?php echo $localize['text_user']; ?></p>
			       					<div class="window-content">
			       						<div class="row">
			       							<div class="col-sm-6">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_admin_id']; ?></label>
													<input class="form-control" id="user_id" type="text" value="1234">
												</div>
			       							</div>
			       							<div class="col-sm-6">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_admin_pass']; ?></label>
													<input class="form-control" id="user_pass" type="password" value="">
												</div>
			       							</div>
			       						</div>
			       						<div class="row">
			       							<div class="col-sm-12">
			       								<div class="form-group label-floating">
													<label class="control-label"><?php echo $localize['txt_admin_name']; ?></label>
													<input class="form-control" id="user_name" type="text" value="">
												</div>
			       							</div>
			       						</div>
			       					</div>
			       					<div class="left">
			       						<div id="update-admin-info" class="installer-info"></div>
			       					</div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button class="pull-left" id="update-admin-skip" onclick="skipPostInstall()">
			       						<div class="icon">
			       							<i class="material-icons">close</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_skip']; ?></div>
			       					</button>
			       					<button id="update-admin-ok" onclick="processToServer('update-admin');">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_next']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-timezone">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<div class="logo">
			       						<i class="material-icons">public</i>
			       					</div>
			       					<h2><?php echo $localize['title_timezone']; ?></h2>
			       					<p><?php echo $localize['text_timezone']; ?></p>
			       					<div class="window-content">
			       						<div class="row">
			       							<div class="col-sm-12">
			       								<div class="form-group label-floating">
													<label class="control-label">Timezone</label>
													<select class="form-control" id="my_timezone">
														<?php echo $installer -> getTimezoneList(); ?>
													</select>
												</div>
			       							</div>
			       						</div>
			       						<div class="suggestions">
			       							<a onclick="$('#my_timezone').val('Asia/Jakarta').change();" class="btn">
			       								<?php echo $localize['btn_wib']; ?>
			       							</a>
			       							<a onclick="$('#my_timezone').val('Asia/Makassar').change();" class="btn">
			       								<?php echo $localize['btn_wita']; ?>
			       							</a>
			       							<a onclick="$('#my_timezone').val('Asia/Jayapura').change();" class="btn">
			       								<?php echo $localize['btn_wit']; ?>
			       							</a>
			       						</div>
			       					</div>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button id="update-timezone-skip" class="pull-left" onclick="skipPostInstall()">
			       						<div class="icon">
			       							<i class="material-icons">close</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_skip']; ?></div>
			       					</button>
			       					<button id="update-timezone-ok" onclick="processToServer('update-timezone')">
			       						<div class="icon">
			       							<i class="material-icons">arrow_forward</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_next']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       		<div class="window" id="window-finish">
			       			<div class="window-header">
			       				<div class="vertical-middle width-100">
			       					<img src="./lib/images/arctic-fox.png" class="logo">
			       					<h2><?php echo $localize['title_install_finish']; ?></h2>
			       					<p><?php echo $localize['text_install_finish']; ?></p>
			       				</div>
			       			</div>
			       			<div class="window-footer">
			       				<div class="vertical-middle width-100">
			       					<button onclick="$(this).prop('disabled', true); processToServer('install-cleanup');">
			       						<div class="icon">
			       							<i class="material-icons">done</i>
			       						</div>
			       						<div class="label"><?php echo $localize['btn_finish']; ?></div>
			       					</button>
			       				</div>
			       			</div>
			       		</div>
			       	</div>
		        </div>
        	</div>
        </div>
    </div>
    <div id="language" class="hidden">
    	<?php 
    		echo json_encode($localize);
    	?>
    </div>
</body>
<!--   Core JS Files   -->
<script src="./lib/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./lib/js/material.min.js" type="text/javascript"></script>
<script src="./lib/js/jquery.cookie.js"></script>
<script src="./lib/js/sprintf.js"></script>
<!--  Dynamic Elements plugin -->
<script src="./lib/js/arrive.min.js"></script>
<!--  Notifications Plugin    -->
<script src="./lib/js/bootstrap-notify.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="./lib/js/material-dashboard.js?v=1.2.0"></script>
<script src="./lib/js/designer.js"></script>
<script>
<?php 
	switch($installer -> installStep()){
		case "installed-preferences":
			echo 'openWindow("#window-installing");';
			echo 'processToServer("install-2");';
		break;
		case "installed-package":
			echo 'openWindow("#window-installing");';
			echo 'processToServer("install-3");';
		break;
		case "installed-scripts":
			echo 'openWindow("#window-installing");';
			echo 'processToServer("install-4");';
		break;
		case "installed-conn":
		case "updated-admin":
		case "updated-timezone":
			if($_SESSION['ins_action'] == "upgrade"){
				echo '$("#window-finish h2").text("'.$localize['title_upgrade_finish'].'");';
			}
			echo 'openWindow("#window-finish", function(){';
				echo 'destroyWindow("#window-loading, #window-welcome, #window-license, #window-requirements, #window-mysql-connect, #window-mysql-database, #window-user, #window-timezone, #window-installing");';
			echo '});';
		break;
		default: 
			echo 'openWindow("#window-welcome");';
		break;
	}
?>
</script>
</html>
