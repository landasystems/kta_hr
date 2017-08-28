app.controller('jadwalPenilaianCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if (form.tanggal != null && form.semester != null) {
            Data.post('jpenilaian/rekap', form).then(function (data) {
                window.open('api/web/jpenilaian/excelrekap?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Tentukan Periode dan Semester terlebih dahulu!!");
        }
    };

    $scope.excel = function (form) {
        if (form.tanggal != null && form.semester != null) {
            Data.post('jpenilaian/rekap', form).then(function (data) {
                window.location = 'api/web/jpenilaian/excelrekap';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Tentukan Periode dan Semester terlebih dahulu!!");
        }
    };

//    $scope.cariBarang = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('jpenilaian/cari', {jpenilaian: $query}).then(function (data) {
//                $scope.resultsjpenilaian = data.data;
//            });
//        }
//    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if (form.tanggal != undefined && form.semester != undefined) {
            $scope.show_detail = true;
            Data.post('jpenilaian/rekap', form).then(function (data) {
                $scope.listSrc = [];
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Tentukan Periode dan Semester terlebih dahulu!!");
        }
    };
});
