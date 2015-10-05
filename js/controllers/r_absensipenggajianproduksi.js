app.controller('absensipenggajianproduksiCtrl', function ($scope, Data, toaster, $http) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tanggal = new Date();
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

    $scope.excelslip = function () {
        $http({
            url: 'api/web/absensi/penggajianexcel',
            method: "POST",
            data: $scope.listSrc, //this is your json data string
            headers: {
                'Content-type': 'application/json'
            },
            responseType: 'arraybuffer'
        }).success(function (data, status, headers, config) {
            var blob = new Blob([data], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            saveAs(blob, 'aa.xlsx');
        }).error(function (data, status, headers, config) {

        });
    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        $scope.show_detail = true;
        $scope.show_form = form;

        Data.get('absensi/penggajian', form).then(function (data) {
            $scope.listSrc = [];
            angular.forEach(data.data, function ($value, $key) {
                $scope.listSrc.push($value);
            });
        });

    };


});
