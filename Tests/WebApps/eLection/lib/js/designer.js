var localize = JSON.parse($("#language").text());
var datetime_format = localize['datetime_format'];
function generateSpinner(center){
	var data = '<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>';
	if(typeof center !== "undefined"){
		if(center)
			data = '<center>'+data+'</center>';
	}
	return data;
}
function clock(target){
	var today = new Date();
	var D = localize['date_days'][today.getDay()];
	var d = today.getDate();
	var M = localize['date_months'][today.getMonth()];
	var Y = today.getYear();
	var h = today.getHours();
	var m = today.getMinutes();
	var datetimenow = "";
	if(m<10) m = "0"+m;
	if(h<10) h = "0"+h;
		datetimenow = datetime_format.replace(/Y/g, Y);
		datetimenow = datetimenow.replace(/M/g, M);
		datetimenow = datetimenow.replace(/d/g, d);
		datetimenow = datetimenow.replace(/D/g, D);
		datetimenow = datetimenow.replace(/h/g, h);
		datetimenow = datetimenow.replace(/m/g, m);
	target.html(datetimenow);
}
var msgboxShown = false;
var msgboxClosable = true;
function designer_msgbox(ktn, buttons, callback){
	var delay = 0;
	if(msgboxShown){
		designer_msgbox_close();
		delay = 500;
	}
	setTimeout(function(){
		msgboxShown = true;
		$("#msgbox .ktn").html(ktn);
		$("#msgbox .buttons").html(buttons);
		$("#msgbox-overlay").fadeIn(500);
		$("#msgbox").slideDown(200);
		if(typeof callback === "function"){
			setTimeout(callback, 200);
		}
	}, delay);
}
function designer_msgbox_close(callback){
	if(msgboxClosable == false) return;
	$("#msgbox-overlay").fadeOut(500);
	$("#msgbox").slideUp(200);
	$(".content").removeClass("blur");
	setTimeout(function(){
		$("#msgbox .ktn").html("");
		$("#msgbox .buttons").html("");
		$("#msgbox").removeAttr("style");
		msgboxShown = false;
	}, 200);
	if(typeof callback === "function"){
		setTimeout(callback, 500);
	}
}
function changeLanguage(lang){
	var curLang = $.cookie("el_lang");
	if(lang == curLang) return false;
	designer_msgbox(generateSpinner());
	$.cookie("el_lang", lang, {path: '/'});
	setTimeout(function(){
		window.location.reload();
	}, 1000);
}
function openWindow(windowId, callback){
	var activeWindow = $("#mainwindow .window.opened");
	if(activeWindow.length){
		activeWindow.fadeOut(200, function(){
			$(this).removeClass("opened");
			$(windowId).fadeIn(200, function(){
				$(this).addClass("opened");
				if(typeof callback === "function") callback();
			});
		});
	} else {
		$(windowId).fadeIn(200, function(){
			$(this).addClass("opened");
			if(typeof callback === "function") callback();
		});
	}
}
function destroyWindow(windowId){
	if($(windowId).hasClass("opened")) return false;
	$(windowId).remove();
}
function agreeLicense(){
	var escKeyBind = function(e){
		if(e.which == 27){
			$("#msgbox-no").click();
		}
	}
	var buttons = '<button id="msgbox-no" class="btn">'+localize['btn_disagree']+'</button>';
	buttons += '<button id="msgbox-yes" class="btn">'+localize['btn_agree']+'</button>';
	var ktn = localize['ask_agree_license'];
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-yes").on("keydown", escKeyBind);
		$("#msgbox-yes").on("click", function(){
			designer_msgbox_close();
			openWindow('#window-mysql-connect', function(){
				$("#mysql_password").focus();
			});
		});
		$("#msgbox-no").on("click", function(){
			designer_msgbox_close();
		});
		$("#msgbox-yes").focus();
	});
}
function cancelInstall(){
	var escKeyBind = function(e){
		if(e.which == 27){
			$("#msgbox-no").click();
		}
	}
	var buttons = '<button id="msgbox-no" class="btn">'+localize['btn_no']+'</button>';
	buttons += '<button id="msgbox-yes" class="btn">'+localize['btn_yes']+'</button>';
	var ktn = localize['ask_cancel_install'];
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-yes").on("keydown", escKeyBind);
		$("#msgbox-yes").on("click", function(){
			designer_msgbox_close();
			openWindow('#window-welcome');
		});
		$("#msgbox-no").on("click", function(){
			designer_msgbox_close();
		});
		$("#msgbox-yes").focus();
	});
}
function showInfoText(selector, text, type, icon){
	if(typeof selector === "undefined") return false;
	if(typeof icon === "undefined"){
		var icon = "";
	} else {
		icon = '<i class="material-icons">'+icon+'</i>';
	}
	if(typeof type === "undefined"){
		var type = "";
	} else {
		type = "text-"+type;
	}
	$(selector).html('<div class="'+type+'">'+icon+text+'</div>').show();
}
function selectDatabase(){
	var selectedDb = $("#window-mysql-database .db-list a.selected");
	if(selectedDb.length){
		selectedDb.removeClass("selected");
	}
	$(this).addClass("selected");
	selectedDb = $("#window-mysql-database .db-list a.selected");
	var dbName = selectedDb.find("label:first-child").text();
	var dbNameBold = "<b>"+dbName+"</b>";
	$("#btn-database-remove, #mysql-database-ok").prop("disabled", false);
	if(selectedDb.hasClass("danger")){
		showInfoText("#mysql-database-info", localize['error_mysql_database_system'], "danger", "warning");
		$("#btn-database-remove, #mysql-database-ok").prop("disabled", true);
	} else if (selectedDb.hasClass("warning")){
		showInfoText("#mysql-database-info", sprintf(localize['error_mysql_database_not_null'], dbNameBold), "warning", "warning");
	} else if (selectedDb.hasClass("update")){
		showInfoText("#mysql-database-info", sprintf(localize['success_mysql_database_update'], dbNameBold), "success", "done");
	} else if (selectedDb.hasClass("available")){
		showInfoText("#mysql-database-info", sprintf(localize['success_mysql_database'], dbNameBold), "success", "done");
	}
}
function addDatabase(){
	var textKeyBind = function(e){
		switch(e.which){
			case 13:
				$("#msgbox-create").click();
			break;
			case 27:
				$("#msgbox-cancel").click();
			break;
		}
	}
	var addDatabaseBind = function(){
		if($("#mysql_database_name").val().length == 0){
			$("#mysql_database_name").focus();
			return false;
		}
		$("#msgbox-cancel, #msgbox-create, #mysql_database_name").prop("disabled", true);
		showInfoText("#mysql-database-create-info", localize['text_database_creating']);
		processToServer('create_database');
	}
	var buttons = '<div id="mysql-database-create-info" class="installer-info pull-left"></div>';
	buttons += '<button id="msgbox-cancel" class="btn">'+localize['btn_cancel']+'</button>';
	buttons += '<button id="msgbox-create" class="btn">'+localize['btn_create']+'</button>';
	var ktn = '<div class="form-group label-floating">';
	ktn += '<label class="control-label">'+localize['text_database_create']+'</label>';
	ktn += '<input class="form-control" id="mysql_database_name" type="text">';
	ktn += '</div>';
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-cancel").on("click", designer_msgbox_close);
		$("#msgbox-create").on("click", addDatabaseBind);
		$("#mysql_database_name").on("keydown", textKeyBind).focus();
	});
}
function deleteDatabase(){
	var dbName = $("#window-mysql-database .db-list a.selected label:first-child").text();
	var ktn = sprintf(localize['ask_database_delete'], "<b>"+dbName+"</b>");
	var buttons = '<div id="mysql-database-delete-info" class="installer-info pull-left"></div>';
	buttons += '<button id="msgbox-cancel" class="btn">'+localize['btn_cancel']+'</button>';
	buttons += '<button id="msgbox-delete" class="btn">'+localize['btn_delete']+'</button>';
	var escKeyBind = function(e){
		if(e.which == 27){
			$("#msgbox-cancel").click();
		}
	}
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-cancel").on("click", designer_msgbox_close);
		$("#msgbox-cancel").on("keydown", escKeyBind).focus();
		$("#msgbox-delete").on("click", function(){
			processToServer('delete_database');
		});
	});
}
function confirmInstall(){
	var dbName = $("#window-mysql-database .db-list a.selected label:first-child").text();
	var ktn = sprintf(localize['ask_confirm_install'], "<b>"+dbName+"</b>");
	var buttons = '<button id="msgbox-cancel" class="btn">'+localize['btn_cancel']+'</button>';
	buttons += '<button id="msgbox-install" class="btn">'+localize['btn_install']+'</button>';
	var escKeyBind = function(e){
		if(e.which == 27){
			$("#msgbox-cancel").click();
		}
	}
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-cancel").on("click", designer_msgbox_close);
		$("#msgbox-install").on("keydown", escKeyBind).focus();
		$("#msgbox-install").on("click", function(){
			designer_msgbox_close(function(){
				processToServer("install-1");
			});
		});
	});
}
function setInstallProgress(percent, duration, status){
	duration = duration / 1000;
	transition = 'all '+duration+'s linear';
	$("#window-installing .progress .progress-bar").css({
		"transition": transition,
		"width": percent
	});
	if(typeof status === "string"){
		$("#installing-process-info").text(status);
	}
}
function installErrorAlert(text){
	var ktn = "<div>"+localize['error_install']+"</div>";
	ktn += text;
	var buttons = '<button id="msgbox-ok" class="btn">'+localize['btn_ok']+'</button>';
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-ok").on("click", designer_msgbox_close).focus();
	});
}
function skipPostInstall(){
	var ktn = localize['ask_confirm_skip'];
	var buttons = '<button id="msgbox-cancel" class="btn">'+localize['btn_cancel']+'</button>';
	buttons += '<button id="msgbox-skip" class="btn">'+localize['btn_skip']+'</button>';
	var escKeyBind = function(e){
		if(e.which == 27){
			$("#msgbox-cancel").click();
		}
	}
	designer_msgbox(ktn, buttons, function(){
		$("#msgbox-cancel").on("click", designer_msgbox_close).focus();
		$("#msgbox-skip").on("keydown", escKeyBind);
		$("#msgbox-skip").on("click", function(){
			designer_msgbox_close(function(){
				openWindow("#window-finish");
			});
		});
	});
}

function processToServer(step, callback){
	var formData = false;
	switch(step){
		case "check_requirements":
			$('#window-requirements .window-content').html(generateSpinner(true));
			$('#btn-requirements-recheck, #btn-requirements-done').prop('disabled', true);
			formData = {
				action: step
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				$('#btn-requirements-recheck').prop('disabled', false);
				$('#window-requirements .window-content').html(output['text']);
				if(output['installable']){
					$('#btn-requirements-done').prop('disabled', false);
				}
			}
		break;
		case "mysql_connect":
			$("#mysql_connect_do").prop('disabled', true);
			showInfoText("#mysql-connect-info", "Connecting...");
			formData = {
				action: step,
				mysql_host: $("#mysql_host").val(),
				mysql_user: $("#mysql_user").val(),
				mysql_password: $("#mysql_password").val()
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					showInfoText("#mysql-connect-info", output['text'], "success", "done");
					openWindow('#window-mysql-database', function(){ processToServer('mysql_databases'); });
				} else {
					showInfoText("#mysql-connect-info", output['text'], "danger", "error");
				}
				$("#mysql_connect_do").prop('disabled', false);
			}
		break;
		case "mysql_databases":
			$("#btn-databases-refresh").prop('disabled', true);
			$("#window-mysql-database .db-list").html(generateSpinner(true));
			$("#mysql-database-info").hide();
			formData = {
				action: "mysql_get_databases"
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				var text, dbClassesUsed;
				var dbClasses = {
					"unformatted": "available",
					"system": "danger",
					"unknown": "warning",
					"election": "update"
				}
				var databases = output['databases'];
				$("#window-mysql-database .db-list").html('');
				for(r=0;r<databases.length;r++){
					dbClassesUsed = dbClasses[databases[r]['type']];
					if(databases[r]['type'] == "eLection"){
						if(databases[r]['upgradable'])
							dbClassesUsed = "update";
						else dbClassesUsed = "warning";
					}
					text = '<label>'+databases[r]['name']+'</label>';
					text = text + '<label>'+databases[r]['type']+'</label>';
					text = '<a class="'+dbClassesUsed+'">'+text+'</a>';
					$("#window-mysql-database .db-list").append(text);
				}
				$("#window-mysql-database .db-list a").on("click", selectDatabase);
				$("#btn-databases-refresh").prop('disabled', false);
				$("#btn-database-remove, #mysql-database-ok").prop("disabled", true);
				if(typeof callback === "function") callback();
			}
		break;
		case "create_database":
			$("#msgbox-cancel, #msgbox-create, #mysql_database_name").prop("disabled", true);
			showInfoText("#mysql-database-create-info", localize['text_database_creating']);
			formData = {
				action: "create_database",
				db_name: $("#mysql_database_name").val()
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case 403:
						showInfoText("#mysql-database-create-info", output['text'], "danger", "error");
						$("#msgbox-cancel, #msgbox-create, #mysql_database_name").prop('disabled', false);
						$("#mysql_database_name").focus();
					break;
					case 200:
						showInfoText("#mysql-database-create-info", output['text'], "success", "done");
						designer_msgbox_close(function(){
							processToServer('mysql_databases', function(){
								$("#window-mysql-database .db-list a").each(function(){
									dbName = $(this).find("label:first-child").text();
									if(dbName == output['dbName'])
										$(this).click();
										return false;
								});
							});
						});
					break;
				}
			}
		break;
		case "delete_database":
			$("#msgbox-cancel, #msgbox-delete").prop("disabled", true);
			showInfoText("#mysql-database-delete-info", localize['text_database_deleting']);
			formData = {
				action: "delete_database",
				db_name: $("#window-mysql-database .db-list a.selected label:first-child").text()
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case 403:
						showInfoText("#mysql-database-delete-info", output['text'], "danger", "error");
						$("#msgbox-cancel, #msgbox-delete").prop('disabled', false);
						$("#msgbox-delete").focus();
					break;
					case 200:
						showInfoText("#mysql-database-delete-info", output['text'], "success", "done");
						designer_msgbox_close(function(){
							processToServer('mysql_databases');
						});
					break;
				}
			}
		break;
		case "install-1":
			var dbName = $("#window-mysql-database .db-list a.selected label:first-child").text();
			$("#window-installing p").text(sprintf(localize['text_install_ondb'], '"'+dbName+'"'));
			openWindow('#window-installing');
			formData = {
				action: "save_preferences",
				db_name: dbName
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					setTimeout(function(){
						processToServer("install-2");
					}, 1000);
					if(output['install_type'] == "upgrade"){
						$("#window-installing h2").text(localize['title_upgrading_eLection']);
						$("#window-installing p").text(sprintf(localize['text_upgrade_ondb'], '"'+dbName+'"'));
					}
				} else {
					installErrorAlert(output['text']);
				}
			}
		break;
		case "install-2":
			setInstallProgress("50%", 10000, sprintf(localize['text_install_about_seconds'], "15"));
			destroyWindow("#window-loading, #window-welcome, #window-license, #window-requirements, #window-mysql-connect, #window-mysql-database");
			formData = {
				action: "install_packages"
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					setTimeout(function(){
						setInstallProgress("50%", 10000, sprintf(localize['text_install_about_seconds'], "10"));
					}, 5000);
					setTimeout(function(){
						processToServer("install-3");
					}, 10000);
				} else {
					installErrorAlert(output['text']);
				}
			}
		break;
		case "install-3":
			setInstallProgress("90%", 5000, sprintf(localize['text_install_about_seconds'], "5"));
			setTimeout(function(){
				setInstallProgress("90%", 5000, sprintf(localize['text_install_about_seconds'], "4"));
			}, 1000);
			setTimeout(function(){
				setInstallProgress("90%", 5000, sprintf(localize['text_install_about_seconds'], "3"));
			}, 2000);
			setTimeout(function(){
				setInstallProgress("90%", 5000, sprintf(localize['text_install_about_seconds'], "2"));
			}, 3000);
			setTimeout(function(){
				setInstallProgress("90%", 5000, localize['text_install_about_second']);
			}, 4000);
			formData = {
				action: "install_package_scripts"
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					setTimeout(function(){
						processToServer("install-4");
					}, 5000);
				} else {
					installErrorAlert(output['text']);
				}
			}
		break;
		case "install-4":
			setInstallProgress("100%", 1000, localize['text_install_about_second']);
			formData = {
				action: "install_save_conn"
			}
			var runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					switch(output['install_type']){
						case "install":
							setTimeout(function(){
								setInstallProgress("100%", 100, localize['success_installed']);
							}, 1000);
							setTimeout(function(){
								openWindow('#window-user', function(){
									onEnterKey = function(e){
										if(e.which == 13){
											if($("#user_id").val().length == 0){
												$("#user_id").focus();
												return false;
											}
											if($("#user_pass").val().length == 0){
												$("#user_pass").focus();
												return false;
											}
											if($("#user_name").val().length == 0){
												$("#user_name").focus();
												return false;
											}
											$("#update-admin-ok").click();
										}
									}
									$("#user_id, #user_name, #user_pass").on("keypress", onEnterKey);
									$("#user_id").select().focus();
									destroyWindow("#window-installing");
								});
							}, 2000);
						break;
						case "upgrade":
							setTimeout(function(){
								setInstallProgress("100%", 100, localize['success_upgraded']);
							}, 1000);
							setTimeout(function(){
								$("#window-finish h2").text(localize['title_upgrade_finish']);
								openWindow("#window-finish", function(){
									destroyWindow("#window-installing");
								});
							}, 2000);
						break;
					}
				} else {
					installErrorAlert(output['text']);
				}
			}
		break;
		case "update-admin":
			$("#user_id, #user_pass, #user_name, #update-admin-skip, #update-admin-ok").prop("disabled", true);
			showInfoText("#update-admin-info", localize['text_admin_saving']);
			var myId = $("#user_id").val();
			var myPass = $("#user_pass").val();
			var myName = $("#user_name").val();
			formData = {
				action: "update-admin",
				myId: myId,
				myPass: myPass,
				myName: myName
			}
			runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					openWindow("#window-timezone", function(){
						destroyWindow("#window-user");
					});
				} else {
					$("#user_id, #user_pass, #user_name, #update-admin-skip, #update-admin-ok").prop("disabled", false);
					$(output['component']).focus();
					showInfoText("#update-admin-info", output['text'], "danger", "error");
				}
			}
		break;
		case "update-timezone":
			$("#my_timezone, #update-timezone-skip, #update-timezone-ok").prop("disabled", true);
			formData = {
				action: "update-timezone",
				myTimezone: $("#my_timezone").val()
			}
			runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					openWindow("#window-finish", function(){
						destroyWindow("#window-timezone");
					});
				} else {
					installErrorAlert(output['text']);
					$("#my_timezone, #update-timezone-skip, #update-timezone-ok").prop("disabled", false);
				}
			}
		break;
		case "install-cleanup":
			formData = {
				action: "install-cleanup"
			}
			runOnSuccess = function(data){
				var output = JSON.parse(data);
				if(output['code'] == 200){
					top.location = './admin/?_installed';
				}
			}
		break;
	}
	if(formData === false) return true;
	var sending = $.post("./lib/processor.php",formData);
	sending.fail(function(){
		installErrorAlert("System error occured!");
	});
	sending.done(runOnSuccess);
}

setInterval(function(){clock($("#clock"))}, 1000);
clock($("#clock"));
$("#toolbar .nav-dropdown").on("show.bs.dropdown", function(event){
  $(event.relatedTarget).addClass("focus");
});
$("#toolbar .nav-dropdown").on("hidden.bs.dropdown", function(event){
  $(event.relatedTarget).removeClass("focus");
});
$('li.multi-dropdown').hover(function(){
     $(this).children('ul').delay(10).fadeIn(10);
}, function(){
     $(this).children('ul').delay(10).fadeOut(10);
});
$("#toolbar, #mainwindow").contextmenu(function(){
	return false;
});