app.controller('agendaUmumCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('agendaumum/rekap', form).then(function (data) {
                window.open('api/web/agendaumum/excel?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('agendaumum/rekap', form).then(function (data) {
                window.location = 'api/web/agendaumum/excel';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('agendaumum/rekap', form).then(function (data) {
                $scope.listSrc = [];
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };
});
