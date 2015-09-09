app.controller('kpbCtrl', function($scope, Data, toaster, $modal) {
    $scope.jabatan = [];
    $scope.form = [];
    $scope.detail = [];
    $scope.status = 0;
    $scope.msg = '';

    Data.get('pengguna/profile').then(function(data) {
        $scope.user = data.data;
    });

    $scope.simpanPrint = function(no_wo, kd_jab) {
        var data = {
            no_wo: no_wo,
            kd_jab: kd_jab,
        }
        Data.post('kpb/simpanprint', data).then(function(data) {
            $scope.status = data.print;
            $scope.msg = data.msg;

        });
    }

    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
                $scope.status = 1;
            });
        }
    }

    $scope.listBagian = function($query) {
        $scope.jabatan = [];
        $scope.detail = [];
        if ($query.length >= 3) {
            Data.get('kpb/jabkpb', {key: $query}).then(function(data) {
                $scope.jabatan = data.data;
            });
        }
    }

    $scope.listBahan = function(kd_bom, kd_jab) {
        var dt = {
            kd_bom: kd_bom,
            kd_jab: kd_jab,
        };
        Data.post('kpb/listbahan', dt).then(function(data) {
            $scope.detail = data.data;
            $scope.status = data.print;
            $scope.msg = data.msg;
        });
    }

    $scope.modal = function(form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_kpb/modal.html',
            controller: 'modalCtrl',
            size: 'md',
            resolve: {
                form: function() {
                    return form;
                }
            }
        });
    };
});

app.controller('modalCtrl', function($scope, Data, $modalInstance, form, toaster) {

    $scope.form = {
        no_wo: '',
        kd_jabatan: '',
    }

    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
                $scope.status = 1;
            });
        }
    }

    $scope.listBagian = function($query) {
        $scope.jabatan = [];
        $scope.detail = [];
        if ($query.length >= 3) {
            Data.get('kpb/jabkpb', {key: $query}).then(function(data) {
                $scope.jabatan = data.data;
            });
        }
    }

    $scope.bukaPrint = function(no_wo, kd_jab) {
        var data = {
            no_wo: no_wo,
            kd_jab: kd_jab,
        }
        Data.post('kpb/bukaprint', data).then(function(data) {
            if (data.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", data.errors);
            } else {
                $scope.status = data.print;
                $scope.msg = data.msg;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
            $modalInstance.dismiss('cancel');
        });
    }

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };

})


