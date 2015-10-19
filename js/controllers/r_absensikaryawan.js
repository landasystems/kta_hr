app.controller('absensikaryawanCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.show_form = [];

    

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        $scope.show_detail = true;
        $scope.show_form = form;

        Data.get('absensi/rekap', form).then(function (data) {
            $scope.listSrc = [];
            $scope.show_form.tanggal_startDate = new Date(data.start);
            $scope.show_form.tanggal_endDate = new Date(data.end);
            angular.forEach(data.data, function ($value, $key) {
                $scope.listSrc.push($value);

            });
        });


    };
    
    $scope.print = function (form) {
//        console.log(form);
        if ('tanggal' in form && form.tanggal_startDate != null) {
            Data.post('absensi/karyawanexcel', $scope.listSrc).then(function (data) {
                window.open('api/web/absensi/karyawanexcel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if ('tanggal' in form && form.tanggal_startDate != null) {
            Data.post('absensi/karyawanexcel', $scope.listSrc).then(function (data) {
                saveExcel(data, 'absenkaryawan.xls');
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

});
