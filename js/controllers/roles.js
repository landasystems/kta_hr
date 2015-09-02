app.controller('rolesCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};

        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('roles', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('roles', paramRef).then(function (data) {
            window.location = 'api/web/roles/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.akses = {};
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
    };
    $scope.save = function (form) {
        var url = (form.id > 0) ? 'roles/update/' + form.id : 'roles/create';
        form.akses = JSON.stringify(form.akses);
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.trash = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
            row.is_deleted = 1;
            Data.post('roles/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function (row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('roles/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('roles/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.checkAll = function (module, valueCheck) {
        var akses = {
            "master_roles": false,
            "master_user": false,
            "master_barang": false,
            "master_jenisbrg": false,
            "master_customer": false,
            "master_supplier": false,
            "master_modelkendaraan": false,
            "master_chassis": false,
            "master_kalender": false,
            "master_lokasi": false,
            "master_jabatan": false,
            "master_section": false,
            "master_subsection": false,
            "master_umk": false,
            "master_departement": false,
            "master_jnskomplain": false,
            "master_jamsostek": false,
            "master_apd": false,
            "transaksi_bom": false,
            "transaksi_validasibom": false,
            "transaksi_tambahitem": false,
            "transaksi_sti": false,
            "transaksi_pembatalanchasis": false,
            "transaksi_spesanankaroseri": false,
            "transaksi_sperintahkaroseri": false,
            "transaksi_claimunit": false,
            "transaksi_transaksi_deliveryunitbstk": false,
            "transaksi_rubahbentuk": false,
            "transaksi_ujimutu": false,
            "transaksi_deliveryunit": false,
            "transaksi_buktiterima": false,
            "transaksi_spprutin": false,
            "transaksi_sppnonrutin": false,
            "transaksi_purchaseorder": false,
            "transaksi_bktbarangmasuk": false,
            "transaksi_bktbarangkeluar": false,
            "transaksi_valbarangkeluar": false,
            "transaksi_returbuktibarangmasuk": false,
            "transaksi_returbuktibarangkeluar": false,
            "transaksi_wordermasuk": false,
            "transaksi_worderkeluar": false,
            "transaksi_winprogress": false,
            "transaksi_spk": false,
            "transaksi_kpb": false,
            "rekap_supplier": false,
            "rekap_barang": false,
            "rekap_customer": false,
            "rekap_barangmasuk": false,
            "rekap_barangkeluar": false,
            "rekap_pergerakanbarang": false,
            "rekap_spp": false,
            "rekap_purchaseorder": false,
            "rekap_rubahbentuk": false,
            "rekap_claimunit": false,
            "rekap_deliveryunit": false,
            "rekap_bom": false,
            "rekap_chassisin": false,
            "rekap_bstk": false,
            "rekap_suratpesanan": false,
            "rekap_ujimutu": false,
            "rekap_womasuk": false,
            "rekap_wokeluar": false,
            "rekap_retbarangmasuk": false,
            "rekap_retbarangkeluar": false,
            "rekap_wip": false,
            "rekap_schedule": false,
            "rekap_historybarang": false,
            "rekap_historyunit": false,
            "rekap_historywip": false,
            "notif_bom": false,
            "notif_chassismasuk": false,
            "notif_wo": false,
            "notif_wip": false,
            "notif_barang": false,
            "notif_unit": false,
            "notif_abk": false,
            "notif_monitoring": false,
        }
        angular.forEach($scope.form.akses, function ($value, $key) {
            if ($key.indexOf(module) >= 0)
                $scope.form.akses[$key] = valueCheck;
        });
    };

})
