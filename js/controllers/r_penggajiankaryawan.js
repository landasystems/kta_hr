app.controller('absensipenggajiankaryawanCtrl', function ($scope, Data, toaster, $http) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tanggal = new Date();
    $scope.form.lokasi_kntr = "SUKOREJO";
    $scope.form.tanggal_sampai = new Date();
    $scope.show_detail = false;
    $scope.show_form = [];

 $scope.exceln = function () {
//        Data.get(controller_link + '/view/' + id).then(function (data) {
            window.open('api/web/absensi/gjkryexcel?excel=ex');
//        });
    };

    $scope.printn = function () {
//        Data.get(controller_link + '/view/' + id).then(function (data) {
            window.open('api/web/absensi/gjkryexcel?excel=print');
//        });
    };

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

    var myDate = new Date();
    var year = myDate.getFullYear();
    var month = ("0" + (myDate.getMonth() + 1)).slice(-2);
    var list = [];
    for (var i = year - 3; i < year + 3; i++) {
        list.push(i);
    }
    $scope.form.tahun = year;
    $scope.form.bulan = month;
//    console.log(myDate);

    $scope.listth = list;
    $scope.listbln = [
        {key: "01", value: "Januari"},
        {key: "02", value: "Februari"},
        {key: "03", value: "Maret"},
        {key: "04", value: "April"},
        {key: "05", value: "Mei"},
        {key: "06", value: "Juni"},
        {key: "07", value: "Juli"},
        {key: "08", value: "Agustus"},
        {key: "09", value: "September"},
        {key: "10", value: "Oktober"},
        {key: "11", value: "November"},
        {key: "12", value: "Desember"}
    ];

    $scope.cariSection = function ($query) {

        if ($query.length >= 3) {
            Data.get('section/cari', {nama: $query}).then(function (data) {
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
        $scope.list = [];
        Data.post('absensi/penggajiankaryawan', form).then(function (data) {
            $scope.tahun = data.tahun;
            $scope.tanggal_sampai = data.end;
            $scope.mulai_tanggal = data.start;
            $scope.listSrc = [];
            angular.forEach(data.data, function ($value, $key) {
                $scope.listSrc.push($value);
            });
//            console.log($scope.listSrc);
        });

    };


});
