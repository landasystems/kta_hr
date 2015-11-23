app.controller('absensipenggajianproduksiCtrl', function ($scope, Data, toaster, $http) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tanggal = new Date();
    $scope.form.lokasi_kntr = "SUKOREJO";
    $scope.form.tanggal_sampai = new Date();
    $scope.show_detail = false;
    $scope.show_form = [];

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('karyawan/rekapkeluar', form).then(function (data) {
                window.open('api/web/karyawan/excelkeluar?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function () {
        Data.post('absensi/penggajianexcel', $scope.listSrc).then(function (data) {
            saveExcel(data, 'gaji.xls');
        });
    };
    $scope.excelslip = function () {
        Data.post('absensi/slipgajiexcel', $scope.listSrc).then(function (data) {
            saveExcel(data, 'slipgaji.xls');
        });
    };
    
    $scope.cariSection = function ($query) {

        if ($query.length >= 3) {
            Data.get('section/cari',{nama: $query}).then(function (data) {
                $scope.listSection = data.data;
            });
        }
    };
    
    Data.get('lokasikantor').then(function (data) {
            $scope.listLokasi = data.data;
        });

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        $scope.show_detail = true;
        $scope.show_form = form;

        Data.post('absensi/penggajian', form).then(function (data) {
            $scope.tahun = data.tahun; 
            $scope.listSrc = [];
            angular.forEach(data.data, function ($value, $key) {
                $scope.listSrc.push($value);
            });
        });

    };


});
