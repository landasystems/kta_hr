app.controller('t_lemburCtrl', function ($scope, Data, toaster, $filter) {
    $scope.form = {};
    $scope.isEditing = false;
    $scope.isViewing = false;
    $scope.dataLembur = [];
    $scope.preventSave = false;

    $scope.opened1 = false;

//Todo: show what day is it & show on the right panel
    $scope.refreshTable = function () {
        Data.get("transaksi/lembur", "", "s").then(function(data) {
	var ja = [], jak = [], dat;
	for(var a=0; a<data.data.length; a++) {
		dat = data.data[a];

		dat.tgl = new Date(dat.tgl);
		ja = dat.jam_awal.split(":");
		jak = dat.jam_akhir.split(":");
		dat.jam_awal = new Date(1970,0,1, ja[0], ja[1], ja[2]);
		dat.jam_akhir = new Date(1970,0,1, jak[0], jak[1], jak[2]);

		dat.istirahat = parseInt(dat.istirahat);
		dat.jam_lembur = parseInt(dat.jam_lembur);
		dat.jml_lembur = parseInt(dat.jml_lembur);
		dat.harilibur = (dat.harilibur == 1) ? true : false;

		dat.Karyawan = {nik: dat.nik, nama: dat.nama};
		//Todo: insentif hanya yg punya hak_akses 2,3, & 4

		dat.jadwal = $.map(JSON.parse(dat.jadwal), function(value, index) { return [value]; });
		for(var b=0; b<dat.jadwal.length; b++) {
			dat.jadwal[b].jam_mulai = new Date(dat.jadwal[b].jam_mulai); 
			dat.jadwal[b].jam_selesai = new Date(dat.jadwal[b].jam_selesai); 
		}
	}

	//console.log(data.data);
	$scope.dataLembur = data.data;

        });
    };
    $scope.refreshTable();


    $scope.cariKaryawan = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.refreshJadwal = function () {
        Data.get('lokasikantor', param).then(function (data) {
        for(var b=0; b<data.data.length; b++) {//Parse object to array then time data from date
            if(data.data[b].jadwal.length == 0) continue;
            data.data[b].jadwal = $.map(JSON.parse(data.data[b].jadwal), function(value, index) { return [value]; });

            for(var a=0; a<data.data[b].jadwal.length; a++) {
                data.data[b].jadwal[a].jam_mulai = new Date(data.data[b].jadwal[a].jam_mulai); 
                data.data[b].jadwal[a].jam_selesai = new Date(data.data[b].jadwal[a].jam_selesai); 
            }
        }
        $scope.displayed = data.data;
    });
    }

    $scope.getKaryawan = function (item, form) {
        form.nik = item.nik;
        form.nama = item.nama;

        Data.get("karyawan/" + item.nik, "", "s").then (function (data) {
            $scope.form.lokasi_kantor = data.lokasi_kntr;
            switch(+data.hak_akses) {
            	case 2: case 3: case 4: $scope.form.status = 1; break;
            	default: $scope.form.status = 0; break;
            }

            Data.get('lokasikantor/' + data.lokasi_kntr, "", "s").then(function (data) {
                d = data.data.jadwal;
                //console.log("d:", d);
            
                //Parse object to array then time data from date
                d = $.map(JSON.parse(d), function(value, index) { return [value]; });
            
                for(var a=0; a<d.length; a++) {
                    d[a].jam_mulai = new Date(d[a].jam_mulai);
                    d[a].jam_selesai = new Date(d[a].jam_selesai);
                }
                
                //console.log(d);
                $scope.form.jadwal = d;
            });
        });

        Data.get("karyawan/gajiaktif/" + item.nik, "", "s").then(function (data) {
            form.umr = data.gaji;
        });
    };


    $scope.newKaryLembur = function () {
    	$scope.form = {id_lembur: 0, tgl: (new Date()), istirahat: 0, preventSave: true};
    	$scope.isEditing = true;
    };

    $scope.editKaryLembur = function (k) {
    	$scope.form = k;
    	$scope.computeOvertime();
    	$scope.isEditing = true;
    };

    $scope.viewKaryLembur = function (k) {
    	$scope.form = k;
    	$scope.computeOvertime();
    	$scope.isViewing = true;
    };

    $scope.cancel = function () {
    	$scope.isEditing = false;
    	$scope.isViewing = false;
    };

    $scope.computeOvertime = function () {
    	var umr = $scope.form.umr;
    	var tgl = $scope.form.tgl;
    	var jaw = $scope.form.jam_awal;
    	var jakh = $scope.form.jam_akhir;

    	if (jaw === undefined || jakh === undefined || umr == 0) return;

//Todo: Cek jam mulai & jam selesai
	//$scope.preventSave = (jaw.getTime() <= j.jam_selesai.getTime() || jaw.getTime() >= jakh.getTime());
	//((jaw.getTime() <= j.jam_mulai.getTime() && j.jam_mulai.getTime() <= jakh.getTime()) || (jaw.getTime() <= j.jam_selesai.getTime() && j.jam_selesai.getTime() <= jakh.getTime()));

//Cek hari libur
	var tgd = tgl.getDay();
	//console.log("Hari ini:", tgd);
	
	if (tgd == 0) {
		$scope.form.harilibur = true;
		$scope.ket = "Libur hari Minggu";
		$scope.jam_mulai = new Date(1970,0,1,0,0);
		$scope.jam_selesai = new Date(1970,0,1,0,0);
	} else {
		var j = $scope.form.jadwal[tgd - 1];
		$scope.form.harilibur = false;
		$scope.jam_mulai = j.jam_mulai;
		$scope.jam_selesai = j.jam_selesai;
		$scope.ket = "Hari ini masuk kerja.";

		Data.get("transaksi/lembur/libur/" + $filter("date")(tgl, "yyyy-MM-dd"), "", "s").then(function (data) {
			var d = data.data;

			if(d) { 
				$scope.form.harilibur = true;
				$scope.jam_mulai = new Date(1970,0,1,0,0);
				$scope.jam_selesai = new Date(1970,0,1,0,0);
				$scope.ket = "Libur " + d.ket;
			}

			if (jaw.getTime() < $scope.jam_selesai.getTime())
				$scope.preventSave = true;
			else $scope.preventSave = false;
		});

	}

	//Done: check jaw & jakh to prevent corruption  >:3
	//Done: query on lokasi kantor > jadwal kantor
	//Done: add a new field for jadwal kantor in lokasi kantor
	//Done: Cek hari libur

	var hrs = (jakh - jaw) / 3600000;

	var jl = hrs - $scope.form.istirahat;
	$scope.form.jam_lembur = jl;

	var libur = $scope.form.harilibur;
	var insentif = ($scope.form.status == 1) ? true : false;
	var _u = umr / 173;

	//Penghitungan gaji lembur/insentif
	var tot = 0;
	if (!libur) {
		//Waktu hari kerja
		if (!insentif) {
			if (jl > 0) { jl--; tot += 1.5 * _u; }
			if (jl > 0) tot += jl * 2 * _u;
		} else {
			if (jl <= 2) tot += 2;
			else if (jl > 2 && jl <= 4) tot += 3;
			else if (jl > 4) tot += 15;
			else if (jl > 6) tot += 16;

			tot *= 10000;
		}

		//Todo: Use checkbox or check hak_akses
	} else {
		//Waktu hari libur
		if (!insentif) {
			if (jl > 0) { 
				tot += (jl < 7) ? jl * 2 * _u : 14 * _u;
				jl -= (jl < 7) ? jl : 7;
			}
			if (jl > 0) { jl--; tot += 3 * _u; }
			if (jl > 0) tot += jl * 4 * _u;
		} else {
			if (jl >= 1 && jl < 5) tot += 12;
			else if (jl >= 5 && jl < 7) tot += 16;
			else if (jl >= 7) tot += 17;

			tot *= 10000;
		}
	}

	$scope.form.jml_lembur = ~~tot;

	//console.log("insentif:" + insentif, "total: " + ~~tot);
};

    $scope.save = function (f) {
    	f.tgl = $filter("date")(f.tgl, "yyyy-MM-dd");
    	f.jam_awal = $filter("date")(f.jam_awal, "HH:mm:ss");
    	f.jam_akhir = $filter("date")(f.jam_akhir, "HH:mm:ss");
    	f.harilibur = f.harilibur ? 1 : 0;

    	var url = (f.id_lembur == 0) ? "transaksi/lembur/insert" : "transaksi/lembur/update/" + f.id_lembur;
    	Data.post(url, f, "s").then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.isEditing = false;
                //$scope.callServer(tableStateRef); //reload grid ulang
                $scope.refreshTable();
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
    	});
    };

    $scope.del = function (f) {
    	if (confirm("Hapus detail lembur untuk " + f.nama + " secara permanen?")) {
    		Data.post("transaksi/lembur/delete/" + f.id_lembur, "", "s").then(function (result) {
    			if (result.status == 1) {
    				toaster.pop("info", "Sudah dihapus");
    				$scope.refreshTable();
    			}
    		});
    	}
    };

    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };
})