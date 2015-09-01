app.controller('umkCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = true;
    $scope.is_view = false;
    $scope.is_create = false;

    Data.get('umk/').then(function (data) {

    $scope.form = data.dataumk[0];
  
    });

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_umk;
        $scope.form = form;
    };
    $scope.save = function(form) {
//        console.log(form.no_umk);
        var url = 'umk/update/' + form.no_umk;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = true;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });

    };

})
