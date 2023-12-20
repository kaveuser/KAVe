function showNotif(text,icon,type){
	if(icon === undefined) icon = 'notifications';
	if(type === undefined) type = 'primary';
	$.notify({
        icon: icon,
        message: text
    }, {
        type: type,
        timer: 2000,
				delay: 2000,
				z_index: 10000,
        placement: {
            from: 'top',
            align: 'right'
        }
    });
}
function gantiPass(){
	var old_pass = $('#pass_old').val();
	var new_pass_1 = $('#pass_new_1').val();
	var new_pass_2 = $('#pass_new_2').val();
	if(!cekPass()) return false;
	$('#btn_sandi').prop('disabled',true);
	$('#btn_sandi').html("Menyimpan...");
	var sending = $.post("./ajax/op_akun.php",
	{
		sandi_lama: old_pass,
		sandi_baru: new_pass_2
	});
	sending.fail(function(){
		showNotif("Kesalahan sistem!",'error','danger');
		$('#btn_sandi').prop('disabled',false);
		$('#btn_sandi').html("Ganti");
	});
	sending.done(function(data){
		var output = JSON.parse(data);
		switch(output['code']){
			case "403":
				showNotif(output['error'],'error','danger');
				$('#btn_sandi').prop('disabled',false);
				$('#btn_sandi').html("Ganti");
			break;
			case "200":
				showNotif("Kata Sandi berhasil diganti.",'done','success');
				if($('#logout_after').is(':checked')){
					$('#pass_old').val('');
		      $('#pass_new_1').val('');
		      $('#pass_new_2').val('');
					top.location = './logout.php?gantisandi_';
					return true;
				}
				$('#gantiPass').modal('hide');
			break;
		}
	});
}
function instansi(aksi,id){
	switch(aksi){
		case "show":
			$('#card_cari').slideUp();
			$('#card_forms').slideDown();
			if($("#ins_nama").is(":disabled"))
				$('#ins_alamat').focus(); else
				$('#ins_nama').focus();
		break;
		case "hide":
			$('#card_cari').slideDown();
			$('#card_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #ins_nama, #ins_alamat').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #ins_nama, #ins_alamat').prop('disabled',false);
		break;
		case "reset":
			$('#ins_nama, #ins_alamat').val("").keyup().prop('disabled',false);
			$('#siswa_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html("Tambah");
			$('#method').val("");
		break;
		case "batal":
			instansi('hide');
			setTimeout(function(){
				instansi('reset');
			},400);
		break;
		case "tambah":
			instansi('reset');
			$('#method').val("add");
			instansi('show');
		break;
		case "hapus":
			var cfr = confirm("Anda yakin menghapus instansi ini? \nSemua data siswa dan staff yang terkait dengan instansi ini juga akan terhapus.");
			if(cfr){
				$('#method').val("delete");
				$('#btn_post').text("Hapus").click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif("Kesalahan sistem!","error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_instansi.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif("Kesalahan sistem!",'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						instansi('reset');
						$('#ins_nama').val(output['nama']).keyup().prop("disabled", true);
						$('#ins_alamat').val(output['alamat']).keyup();
						$('#siswa_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html("Simpan");
						$('#method').val("update");
						instansi('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_nama = $('#ins_nama').val();
			var pt_alamat = $('#ins_alamat').val();
			if(pt_nama.length == 0){
				$('#ins_nama').focus();
				return false;
			}
			if(pt_alamat.length == 0){
				$('#ins_alamat').focus();
				return false;
			}
			if(method == "update"){
				$('#btn_post').text("Menyimpan...");
			} else {
				$('#btn_post').text("Menambahkan...");
			}
			if(method == "delete"){
				$('#btn_post').text("Menghapus...");
			}
			instansi('disable_button');
			var sending = $.post("./ajax/op_instansi.php",
			{
				nama: pt_nama,
				alamat: pt_alamat,
				aksi: method
			});
			sending.fail(function(){
				showNotif("Kesalahan sistem!",'error','danger');
				if(method == "update")
					$('#btn_post').text("Simpan"); else
					$('#btn_post').text("Tambah");
				if(method == "delete")
					$('#btn_post').text("Hapus");
				instansi('enable_button');
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "update")
							$('#btn_post').text("Simpan"); else
							$('#btn_post').text("Tambah");
						if(method == "delete")
							$('#btn_post').text("Hapus");
						instansi('enable_button');
					break;
					case "200":
						if(method == "update")
							$('#btn_post').text("Simpan"); else
							$('#btn_post').text("Tambah");
						var redir = "&kunci="+output['nama'];
						if(method == "delete"){
							redir = "";
							$('#btn_post').text("Hapus");
						}
						showNotif(output['error'],'done','success');
						$('#btn_post').text("Berhasil!");
						instansi('reset');
						instansi('hide');
						setTimeout(function(){
							top.location = './instansi.php?_success'+redir;
						}, 1000);
					break;
				}
			});
		break;
	}
}
function siswa(aksi,id){
	switch(aksi){
		case "show":
			$('#card_cari').slideUp();
			$('#card_forms').slideDown();
			if($('#siswa_noinduk').prop('disabled'))
				$('#siswa_nama').focus(); else
				$('#siswa_noinduk').focus();
		break;
		case "hide":
			$('#card_cari').slideDown();
			$('#card_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #siswa_noinduk, #siswa_nama, #siswa_kelas').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #siswa_noinduk, #siswa_nama, #siswa_kelas').prop('disabled',false);
		break;
		case "reset":
			$('#siswa_noinduk, #siswa_nama, #siswa_kelas').val("").keyup().prop('disabled',false);
			$('#siswa_id').val("");
			$('#siswa_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html(localize['btn_add']);
			$('#method').val("");
		break;
		case "batal":
			siswa('hide');
			setTimeout(function(){
				siswa('reset');
			},400);
		break;
		case "tambah":
			siswa('reset');
			$('#method').val("add");
			siswa('show');
		break;
		case "hapus":
			var cfr = confirm(localize['text_confirm_student_delete']);
			if(cfr){
				$('#method').val("delete");
				$('#btn_post').text(localize['btn_delete']).click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif(localize['alert_system_error'],"error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_siswa.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						siswa('reset');
						$('#siswa_id').val(output['no_induk']);
						$('#siswa_noinduk').val(output['no_induk']).keyup().prop('disabled',true);
						$('#siswa_nama').val(output['nama']).keyup();
						$('#siswa_kelas').val(output['kelas']).keyup();
						$('#siswa_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html(localize['btn_save']);
						$('#method').val("update");
						siswa('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_id = $('#siswa_id').val();
			var pt_noinduk = $('#siswa_noinduk').val();
			var pt_nama = $('#siswa_nama').val();
			var pt_kelas = $('#siswa_kelas').val();
			if(pt_noinduk.length == 0){
				$('#siswa_noinduk').focus();
				return false;
			}
			if(pt_nama.length == 0){
				$('#siswa_nama').focus();
				return false;
			}
			if(pt_kelas.length == 0){
				$('#siswa_kelas').focus();
				return false;
			}
			if(method == "update"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_saving']);
			} else {
				$('#btn_post').text(localize['btn_processing']);
			}
			if(method == "delete"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text(localize['btn_processing']);
			}
			siswa('disable_button');
			var sending = $.post("./ajax/op_siswa.php",
			{
				id: pt_id,
				no_induk: pt_noinduk,
				nama: pt_nama,
				kelas: pt_kelas,
				aksi: method
			});
			sending.fail(function(){
				showNotif(localize['alert_system_error'],'error','danger');
				if(method == "update")
					$('#btn_post').text(localize['btn_save']); else
					$('#btn_post').text(localize['btn_add']);
				if(method == "delete")
					$('#btn_post').text(localize['btn_delete']);
				siswa('enable_button');
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						if(method == "delete")
							$('#btn_post').text(localize['btn_delete']);
						siswa('enable_button');
					break;
					case "200":
						if(method == "update")
							$('#btn_post').text(localize['btn_save']); else
							$('#btn_post').text(localize['btn_add']);
						var redir = "&kunci="+output['no_induk'];
						if(method == "delete"){
							redir = "";
							$('#btn_post').text(localize['btn_delete']);
						}
						showNotif(output['error'],'done','success');
						$('#btn_post').text(localize['btn_success']);
						siswa('reset');
						siswa('hide');
						setTimeout(function(){
							top.location = './siswa.php?_success'+redir;
						}, 1000);
					break;
				}
			});
		break;
	}
}
function guru(aksi,id){
	switch(aksi){
		case "show":
			$('#card_cari').slideUp();
			$('#card_forms').slideDown();
			if($('#guru_noinduk').prop('disabled'))
				$('#guru_nama').focus(); else
				$('#guru_noinduk').focus();
		break;
		case "hide":
			$('#card_cari').slideDown();
			$('#card_forms').slideUp();
		break;
		case "disable_button":
			$('#btn_post, #btn_batal, #btn_option, #guru_noinduk, #guru_nama, #guru_jabatan, #guru_instansi').prop('disabled',true);
		break;
		case "enable_button":
			$('#btn_post, #btn_batal, #btn_option, #guru_noinduk, #guru_nama, #guru_jabatan, #guru_instansi').prop('disabled',false);
		break;
		case "reset":
			$('#guru_noinduk, #guru_nama, #guru_jabatan, #guru_instansi').val("").keyup().prop('disabled',false);
			$('#guru_id').val("");
			$('#guru_opt').addClass('hidden');
			$('#btn_batal').removeClass('hidden');
			$('#btn_post').html("Tambah");
			$('#method').val("");
		break;
		case "batal":
			guru('hide');
			setTimeout(function(){
				guru('reset');
			},400);
		break;
		case "tambah":
			guru('reset');
			$('#method').val("add");
			guru('show');
		break;
		case "hapus":
			var cfr = confirm("Anda yakin menghapus staff ini?");
			if(cfr){
				$('#method').val("delete");
				$('#btn_post').text("Hapus").click();
			}
		break;
		case "edit":
			if(id === undefined){
				showNotif("Kesalahan sistem!","error","danger");
				return false;
			}
			var sending = $.post("./ajax/op_staff.php",
			{
				id: id,
				aksi: 'fetch'
			});
			sending.fail(function(){
				showNotif("Kesalahan sistem!",'error','danger');
			});
			sending.done(function(data){
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
					break;
					case "200":
						guru('reset');
						$('#guru_id').val(output['no_induk']);
						$('#guru_noinduk').val(output['no_induk']).keyup().prop('disabled',true);
						$('#guru_nama').val(output['nama']).keyup();
						$('#guru_jabatan').val(output['jabatan']).keyup();
						$('#guru_instansi').val(output['instansi']).keyup();
						$('#guru_opt').removeClass('hidden');
						$('#btn_batal').addClass('hidden');
						$('#btn_post').html("Simpan");
						$('#method').val("update");
						guru('show');
					break;
				}
			});
		break;
		case "post":
			var method = $('#method').val().toLowerCase();
			var pt_id = $('#guru_id').val();
			var pt_noinduk = $('#guru_noinduk').val();
			var pt_nama = $('#guru_nama').val();
			var pt_jabatan = $('#guru_jabatan').val();
			var pt_instansi = $('#guru_instansi').val();
			if(pt_noinduk.length == 0){
				$('#guru_noinduk').focus();
				return false;
			}
			if(pt_nama.length == 0){
				$('#guru_nama').focus();
				return false;
			}
			if(pt_jabatan.length == 0){
				$('#guru_jabatan').focus();
				return false;
			}
			if(method == "update"){
				if(pt_id.length == 0){
					showNotif(localize['alert_system_error'],"error","danger");
					return false;
				}
				$('#btn_post').text("Menyimpan...");
			} else {
				$('#btn_post').text("Menambahkan...");
			}
			if(method == "delete"){
				if(pt_id.length == 0){
					showNotif("Kesalahan sistem!","error","danger");
					return false;
				}
				$('#btn_post').text("Menghapus...");
			}
			guru('disable_button');
			var sending = $.post("./ajax/op_staff.php",
			{
				id: pt_id,
				no_induk: pt_noinduk,
				nama: pt_nama,
				jabatan: pt_jabatan,
				instansi: pt_instansi,
				aksi: method
			});
			sending.fail(function(){
				showNotif("Kesalahan sistem!",'error','danger');
				if(method == "update")
					$('#btn_post').text("Simpan"); else
					$('#btn_post').text("Tambah");
				if(method == "delete")
					$('#btn_post').text("Hapus");
				guru('enable_button');
			});
			sending.done(function(data){
				//console.log(data);
				var output = JSON.parse(data);
				switch(output['code']){
					case "403":
						showNotif(output['error'],'error','danger');
						if(method == "update")
							$('#btn_post').text("Simpan"); else
							$('#btn_post').text("Tambah");
						if(method == "delete")
							$('#btn_post').text("Hapus");
						guru('enable_button');
					break;
					case "200":
						if(method == "update")
							$('#btn_post').text("Simpan"); else
							$('#btn_post').text("Tambah");
						var redir = "&kunci="+output['no_induk'];
						if(method == "delete"){
							redir = "";
							$('#btn_post').text("Hapus");
						}
						showNotif(output['error'],'done','success');
						$('#btn_post').text("Berhasil!");
						guru('reset');
						guru('hide');
						setTimeout(function(){
							top.location = './staff.php?_success'+redir;
						}, 1000);
					break;
				}
			});
		break;
	}
}
