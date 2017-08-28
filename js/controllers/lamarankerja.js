app.controller('lamaranKerjaCtrl', function ($scope, Data, toaster, FileUploader) {
    
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=barang&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true,
    });
    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
    
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
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
        Data.get('lamarankerja', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('lamarankerja', paramRef).then(function (data) {
            window.open('api/web/lamarankerja/lapexcel?render=rekaplamaran');
        });
    };
    $scope.print = function () {
        Data.get('lamarankerja', paramRef).then(function (data) {
            window.open('api/web/lamarankerja/lapexcel?print=true&render=rekaplamaran');
        });
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Lamaran Kerja";
        $scope.form = {};
        $scope.detRiwayat = [{}];
        $scope.form.tgl = new Date();
//        $scope.form.tanggal_lahir = new Date();
        Data.get('lamarankerja/kode', form).then(function (data) {
            $scope.form.no_lamaran = data.kode;
        });
    };


    $scope.cariJabatan = function (nama) {
        if (nama.length > 2) {
            var data = {
                nama: nama
            };
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detAuditee = data.data;
            });
        }
    };
    $scope.getJabatan = function (form, item) {
        form.posisi = item.jabatan;
    };

    $scope.update = function (form) {
        $scope.form = form;
        $scope.detRiwayat = form.detail;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.form.tanggal_lahir = new Date(form.tanggal_lahir);
        $scope.formtitle = "Edit Data : " + form.no_lamaran;
        $scope.getDetail(form);
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_lamaran;
        $scope.getDetail(form);
    };
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };

        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }


        var url = ($scope.is_create == true) ? 'lamarankerja/create/' : 'lamarankerja/update/' + form.no_lamaran;
        Data.post(url, data).then(function (result) {
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
        $scope.is_edit = false;
        $scope.is_view = false;
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('lamarankerja/delete/' + row.no_lamaran).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
                toaster.pop('success', "Berhasil", "Data berhasil dihapus");
            });
        }
    };
    $scope.getDetail = function (form) {
        Data.get('lamarankerja/view/' + form.no_lamaran).then(function (data) {
            if ((data.data).length > 0) {

                $scope.detRiwayat = data.data;
            }else{
                $scope.detRiwayat =[{}];
            }
        });
    };


    $scope.addrow = function () {
        $scope.detRiwayat.unshift({
            id: 0,
//            barang: [],
//            jmlh_brng: 0,

            perusahaan: '',
            bagian: '',
            periode_awal: '',
            periode_akhir: ''
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detRiwayat);
        if (comArr.length > 1) {
            $scope.detRiwayat.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

//    ------


    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };

    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.clear1 = function () {
        $scope.form.Section = undefined;
        $scope.form.tanggal = undefined;
    };
    $scope.clear2 = function () {
        $scope.form.Karyawan = undefined;
    };

    $scope.clearTgl = function () {
        $scope.form.tanggal = undefined;
    };
    $scope.clearRentang = function () {
        $scope.form.tanggal_rentang = undefined;
    };
});
