app.controller('absensilaporanproduksiCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
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

    $scope.excelkeluar = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('karyawan/rekapkeluar', form).then(function (data) {
                window.location = 'api/web/karyawan/excelkeluar';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
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

    $scope.listSrc = [];
    $scope.list = [];
    $scope.ttl_hadir = [];
    $scope.view = function (form) {

        if ('bulan' in form && 'tahun' in form) {
            $scope.show_detail = true;
            $scope.show_form = form;
            Data.get('absensi/absensiproduksi', form).then(function (data) {
                $scope.listSrc = [];
                $scope.ttl_hadir = [];
                $scope.show_form.tanggal_startDate = new Date(data.start);
                $scope.show_form.tanggal_endDate = new Date(data.end);
                var jml = data.jmlhr;

                var listbln = [];
                for (var i = 1; i <= jml; i++) {
                    listbln.push(i);
                }
                $scope.ttl_hadir= data.jmlhdr;
                $scope.listbn = listbln;
                $scope.colsp = jml + 1;

                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih bulan dan Tahun terlebih dahulu");
        }
    };


});
