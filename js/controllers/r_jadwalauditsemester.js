app.controller('jadwalAuditSemesterCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('jauditsemester/rekap', form).then(function (data) {
                window.open('api/web/jauditsemester/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('jauditsemester/rekap', form).then(function (data) {
                window.location = 'api/web/jauditsemester/excel';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

//    $scope.cariBarang = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('jauditsemester/cari', {jauditsemester: $query}).then(function (data) {
//                $scope.resultsjauditsemester = data.data;
//            });
//        }
//    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('jauditsemester/rekap', form).then(function (data) {
                $scope.listSrc = [];
                var nomor = 1;
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                    $scope.listSrc[0].no = nomor;
                });
                var nomor = 2;
                angular.forEach($scope.listSrc, function (val, key) {
                    var au1 = ($scope.listSrc[key-1] != undefined)? $scope.listSrc[key-1] : [];
                    var au2 = ($scope.listSrc[key] != undefined)? $scope.listSrc[key]: [];
                    
                    if (au1.no_audit != undefined) {
                        if (au1.no_audit == au2.no_audit) {
                            val.tgl = undefined;
                            val.jam = undefined;
                            $scope.listSrc[key].no = '';
                        }else{
                            $scope.listSrc[key].no = nomor;
                            nomor++;
                        }
                    }
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };
});
