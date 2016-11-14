app.controller('moStnkCtrl', function ($scope, Data, toaster) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
         if ('tahun' in form && form.tahun != null) {
            Data.post('mstnk/rekap', form).then(function (data) {
                window.open('api/web/mstnk/excelrekap?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih tahun terlebih dahulu");
        }
    };

    $scope.excelkeluar = function (form) {
         if ('tahun' in form && form.tahun != null) {
            Data.post('mstnk/rekap', form).then(function (data) {
                window.location = 'api/web/mstnk/excelrekap';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih tahun terlebih dahulu");
        }
    };

    var date = new Date,
            years = [],
            year = date.getFullYear();

    for (var i = year-2; i < year + 3; i++) {
        years.push(i);
    }
    
    $scope.year = year;
    $scope.years = years;
    console.log(year);

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tahun' in form && form.tahun != null) {
            $scope.show_detail = true;
            Data.post('mstnk/rekap', form).then(function (data) {
                $scope.listSrc = [];
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih tahun terlebih dahulu");
        }
    };
});
