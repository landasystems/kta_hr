app.controller('absensiharianCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.status = 'tidakhadir';
    $scope.form.lokasi_kntr = "SUKOREJO";
    $scope.form.tanggal = new Date();
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
    
     Data.get('lokasikantor').then(function (data) {
            $scope.listLokasi = data.data;
        });

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
            $scope.show_detail = true;
            $scope.show_form = form;
            
            if ($scope.show_form.status == 'hadir'){
                $scope.show_form.labelstatus = 'KEHADIRAN';
            } else {
                $scope.show_form.labelstatus = 'TIDAK HADIR';
            }
            
            Data.get('absensi/absensiharian', form).then(function (data) {
                $scope.listSrc = [];
                $scope.show_form.total = data.data.length;
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        
    };
    
    
});
