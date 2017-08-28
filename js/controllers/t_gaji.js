app.controller('t_gajiCtrl', function ($scope, Data, toaster, $filter) {
	//Todo: implement filters on master & detail

	$scope.displayed = [];
	$scope.gaji = [];
	$scope.isEditing = false;
	$scope.isViewing = false;
	$scope.isDetail = false;

	$scope.detailList = [];
	$scope.form = {};

	$scope.semuaBulan = [{num: 1, name: "Januari"}, {num: 2, name: "Februari"}, {num: 3, name: "Maret"}, {num: 4, name: "April"}, {num: 5, name: "Mei"}, {num: 6, name: "Juni"}, {num: 7, name: "Juli"}, {num: 8, name: "Agustus"}, {num: 9, name: "September"}, {num: 10, name: "Oktober"}, {num: 11, name: "November"}, {num: 12, name: "Desember"}];

	Data.get("departement").then(function (data) {
		$scope.departments = data.data;
	});
	Data.get("lokasikantor").then(function (data) {
		$scope.lokasikantor = data.data;
	});

	$scope.refreshData = function() {
		Data.get("transaksi/gaji", "", "s").then(function (data) {
			var bt;
			for (var a=0; a<data.length; a++) {
				bt = data[a].bln_thn.split("-");
				data.bln_thn = new Date(bt[0], +bt[1] - 1, 1);
			}
			$scope.gaji = data;
		});
	}
	$scope.refreshData();


    $scope.cariKaryawan = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.getKaryawan = function (item, form) {
        form.nik = item.nik;
        form.nama = item.nama;

        var f = $scope.form;
        var k;

        Data.get("karyawan/" + item.nik, "", "s").then(function (data) {
            k = data;
            $scope.form.Karyawan = data;

            f.lokasi_kntr = k.lokasi_kntr;

            Data.get("lokasikantor/" + k.lokasi_kntr, "", "s").then(function (data) {
                f.id_lokasi_kntr = data.data.id_lokasi_kantor;
            });
            Data.get("department/" + k.department, "", "s").then(function (data) {
                f.id_department = data.id_department;
                f.department = data.department;
            });
            Data.get("section/" + k.section, "", "s").then(function (data) {
                f.id_section = data.id_section;
                f.section = data.section;
            });
        });

        Data.get("karyawan/gajiaktif/" + item.nik, "", "s").then(function (data) {
            f.umk = +data.gaji;
            f.t_jab = +data.t_jab;
            f.t_hdr = +data.t_hdr;
            f.t_man = +data.t_man;
            $scope.compute();
        });
    };

    $scope.compute = function () {
    	var f = $scope.form;
	f.tot_tunj = f.umk + f.t_jab + f.t_hdr + f.t_man;
	f.tot_pot = f.bpjs + f.pot_pinj + f.pot_abs;
	f.gb = f.tot_tunj + f.tot_pot;
    }


	$scope.back = function () {
		$scope.isDetail = false;
	}

	$scope.cancel = function () {
		$scope.isEditing = false;
		$scope.isViewing = false;
	}

	$scope.setBlnThn = function () {
		var f = $scope.form;
		f.bln_thn = new Date(f.tahun, f.bulan - 1);
	}

	//Master
	$scope.newGaji = function () {
		$scope.isEditing = true;
		var nd = new Date();
		$scope.form = {id: 0, bulan: nd.getMonth() + 1, tahun: nd.getFullYear(), inc_keh: 0, bpjs: 0, pot_pinj: 0, pot_abs:0};
		$scope.setBlnThn();
	}

	$scope.detailGaji = function (dg) {
		$scope.isDetail = true;
		$scope.filter = dg;

		console.log($scope.filter);
		var bt = dg.bln_thn.split("-");
		$scope.filter.bulan = +bt[1];
		$scope.filter.tahun = +bt[0];

		Data.get("transaksi/gaji/detail/" + dg.id, "", "s").then(function (data){
			$scope.detailList = data;
		});
	}

	$scope.editDetGaji = function (dg) {
		$scope.isEditing = true;

		var fl = $scope.filter;
		var s = fl.bln_thn.split("-");
		dg.bulan = +s[1];
		dg.tahun = +s[0];

		//t_jab, t_hdr, t_man, tot_tunj, inc_keh, bpjs, pot_pinj, pot_abs, tot_pot, gb
		dg.umk = +dg.umk;
		dg.t_jab = +dg.t_jab;
		dg.t_hdr = +dg.t_hdr;
		dg.t_man = +dg.t_man;
		dg.tot_tunj = +dg.tot_tunj;
		dg.inc_keh = +dg.inc_keh;
		dg.bpjs = +dg.bpjs;
		dg.pot_pinj = +dg.pot_pinj;
		dg.pot_abs = +dg.pot_abs;
		dg.tot_pot = +dg.tot_pot;
		dg.gb = +dg.gb;

		$scope.form = dg;
		$scope.setBlnThn();

		$scope.getKaryawan(dg, $scope.form);
		console.log(dg);
	}

	//Save detail
	$scope.save = function (f) {
		f.bln_thn = $filter("date")(f.bln_thn, "yyyy-MM-dd");

		var url = (f.id == 0 ? "transaksi/gaji/insert" : "transaksi/gaji/update/" + f.id);
		Data.post(url, f, "s").then(function (data) {
			if(data.status == 1) {
				toaster.pop("success", "Berhasil", "Data sudah disimpan.");
				$scope.isEditing = false;
				$scope.refreshData();
			} else toaster.pop("error", "Gagal", "Data gagal disimpan.");
		});
	}

	$scope.delete = function (f) {
		if (confirm("Hapus " + "(bulan dan tahun)" + " secara permanen?"))
		Data.post("transaksi/gaji/del/" + f.id, "", "s").then(function (data) {
			if (data.status > 0) toaster.pop("info", "", "Data sudah dihapus.");
		});
	}
})