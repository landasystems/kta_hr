app.controller('rptLemburCtrl', function ($scope, Data, toaster, $filter) {
	$scope.semuaBulan = [{num: 1, name: "Januari"}, {num: 2, name: "Februari"}, {num: 3, name: "Maret"}, {num: 4, name: "April"}, {num: 5, name: "Mei"}, {num: 6, name: "Juni"}, {num: 7, name: "Juli"}, {num: 8, name: "Agustus"}, {num: 9, name: "September"}, {num: 10, name: "Oktober"}, {num: 11, name: "November"}, {num: 12, name: "Desember"}];

	$scope.getMonthName = function (n) {
		angular.forEach($scope.semuaBulan, function (val, key) {
			if (val.num == n) { return val.name; }
		});
	};

	Data.get("departement").then(function (data) {
		$scope.departments = data.data;
	});

	$scope.filter = {};
	var dn = new Date();
	//$scope.filter.$setPristine();
	$scope.filter.akhir_bulan = $scope.filter.awal_bulan = dn.getMonth() + 1;
	$scope.filter.akhir_tahun = $scope.filter.awal_tahun = dn.getFullYear();

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
    };

    $scope.applyFilter = function (filter) {
        $scope.showFiltered = true;
        console.log(filter);
        filter.awal_bln_thn = $filter("date")(new Date(filter.awal_tahun, +filter.awal_bulan - 1, 1), "yyyy-MM-dd");
        filter.akhir_bln_thn = $filter("date")(new Date(filter.akhir_tahun, +filter.akhir_bulan - 1, 1), "yyyy-MM-dd");

        Data.get("laporan/lembur", filter, "s").then( function(data){
            console.log(data);
        });
    }

    //Todo: implement applyFilter

})