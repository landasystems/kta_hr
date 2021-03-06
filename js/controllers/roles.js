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
            "master_karyawan": false,
            "master_barang": false,
            "master_jenisbrg": false,
            "master_customer": false,
            "master_supplier": false,
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
            "master_potongan": false,
            "master_barangatk": false,
            "master_stokatk": false,
            "master_filelegalitas": false,
            "master_kendaraan": false,
            "master_sekolah": false,
            "transaksi_karyawanspd": false,
            "transaksi_agendapelatihan": false,
            "transaksi_agendaumum": false,
            "transaksi_atkkeluar": false,
            "transaksi_atkmasuk": false,
            "transaksi_karyawankeluar": false,
            "transaksi_lamarankerja": false,
            "transaksi_p2k3": false,
            "transaksi_kecelakaankerja": false,
            "transaksi_pengeluaranapd": false,
            "transaksi_pemasukanapd": false,
            "transaksi_pemakaianlistrikair": false,
            "transaksi_potongan": false,
            "jadwal_jpelatihan": false,
            "jadwal_auditsemester": false,
            "jadwal_penilaian": false,
            "jadwal_hsetalk": false,
            "jadwal_workplan": false,
            "monitoring_filelegalitas": false,
            "monitoring_asuransikendaraan": false,
            "monitoring_servicekendaraan": false,
            "monitoring_stnk": false,
            "tools_absennama": false,
            "tools_cekminggu": false,
            "tools_urutjabatan": false,
            "pegawai_ijazah": false,
            "pegawai_karyawan": false,
            "pegawai_lamarankerja": false,
            "pegawai_pendaftaranjamsostek": false,
            "pegawai_penilaiankontrak": false,
            "pegawai_magang": false,
            "pegawai_prakerin": false,
            "pegawai_absentmasuk": false,
            "rekap_ijazahmasuk": false,
            "rekap_ijazahkeluar": false,
            "rekap_lamarkerjaperpribadi": false,
            "rekap_lamarkerjaperpend": false,
            "rekap_penilaiankontrak": false,
            "rekap_jamsostek": false,
            "rekap_karyawankeluar": false,
            "rekap_karyawankontrak": false,
            "rekap_ulangtahun": false,
            "rekap_karyawaniso": false,
            "rekap_karyawanmasuk": false,
            "rekap_karyawanmasukpertunjangan": false,
            "rekap_karyawanmasukpergaji": false,
            "rekap_karyawanmasukperdata": false,
            "rekap_karyawanmasukperpend": false,
            "rekap_karyawanmasukperpekerjaan": false,
            "rekap_kecelakaankerja": false,
            "rekap_laporanapd": false,
            "rekap_pemasukanapd": false,
            "rekap_jadwalhsetalk": false,
            "rekap_jadwalauditsemester": false,
            "rekap_jadwalpelatihan": false,
            "rekap_laporanstokapd": false,
            "rekap_jadwalapenilaian": false,
            "rekap_pemakianlat": false,
            "rekap_karyawanspd": false,
            "rekap_filelegalitas": false,
            "rekap_stockatk": false,
            "rekap_atkkeluar": false,
            "rekap_atkmasuk": false,
            "rekap_agendapelatihan": false,
            "rekap_agendaumum": false,
            "rekap_rekapmagang": false,
            "rekap_siswaprakerin": false,
            "rekap_rpenilaiankontrak": false,
            "rekap_moasuransi": false,
            "rekap_monitoringlegalitas": false,
            "rekap_moservice": false,
            "rekap_mostnk": false,
            "rekap_absensiharian": false,
            "rekap_lemburharian": false,
            "rekap_penggajianproduksi": false,
            "rekap_penggajiankaryawan": false,
            "rekap_laporanabsensiproduksi": false,
            "rekap_absensi": false,
            "rekap_lembur": false,
            "rekap_absensiproduksi": false,
            "rekap_absensioperator": false
        };
        angular.forEach(akses, function ($value, $key) {
            if ($key.indexOf(module) >= 0)
                $scope.form.akses[$key] = valueCheck;
        });
    };

})
