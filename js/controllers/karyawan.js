app.controller('karyawanCtrl', function ($scope, Data, toaster, FileUploader, $modal) {
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
    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_newcopy = false;
    $scope.nikSkarang = '';

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
        Data.get('karyawan', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    $scope.openkntrk1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedkntrk1 = true;
    };
    $scope.openkntrk11 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedkntrk11 = true;
    };
    $scope.openkntrk2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedkntrk2 = true;
    };
    $scope.openkntrk21 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedkntrk21 = true;
    };
    $scope.openTglIjz= function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedijz= true;
    };
    $scope.openTglMasuk= function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedtglmasuk= true;
    };

    $scope.excel = function () {
        Data.get('karyawan', paramRef).then(function (data) {
            window.location = 'api/web/karyawan/excel';
        });
    };
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_newcopy = false;
        $scope.formtitle = "Form Tambah Data Karyawan";
        $scope.form = {};
        $scope.form.tgl_masuk_kerja = new Date();
        $scope.form.tgl_selesai = new Date();
        $scope.form.tgl_lahir = new Date();
        $scope.form.Kontrak_1 = new Date();
        $scope.form.Kontrak_11 = new Date();
        $scope.form.Kontrak_2 = new Date();
        $scope.form.Kontrak_21 = new Date();
        $scope.form.nik = '';
        $scope.setCode($scope.form);
        Data.get('ijazah/kode',form).then(function(data){
            $scope.form.no = data.kode;
        });

    };

    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_newcopy = true;
        $scope.form.tgl_lahir = new Date(form.tgl_lahir);
        $scope.form.tgl_masuk_kerja = new Date(form.tgl_masuk_kerja);
        $scope.form.Kontrak_1 = new Date(form.Kontrak_1);
        $scope.form.Kontrak_11 = new Date(form.Kontrak_11);
        $scope.form.Kontrak_2 = new Date(form.Kontrak_2);
        $scope.form.Kontrak_21 = new Date(form.Kontrak_21);
        $scope.formtitle = "Edit Data : " + form.nik;
        $scope.loadDetail(form.nik);
        $scope.nikSkarang = form.nik;

    };

    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_newcopy = false;
        $scope.formtitle = "Lihat Data : " + form.nik;
        $scope.form.Kontrak_1 = new Date(form.Kontrak_1);
        $scope.form.Kontrak_11 = new Date(form.Kontrak_11);
        $scope.form.Kontrak_2 = new Date(form.Kontrak_2);
        $scope.form.Kontrak_21 = new Date(form.Kontrak_21);
        $scope.loadDetail(form.nik);
    };

    $scope.setCode = function (form) {
        var nik = '';
        Data.get('karyawan/kode').then(function (data) {
            nik = data.kode;
            if ($scope.is_create == true) {
                if (form.status_karyawan == 'Borongan') {
                    form.nik = 'B' + nik;
                } else {
                    form.nik = '0' + nik;
                }
            }
        });

    };

    $scope.loadDetail = function (nik) {
        Data.get('karyawan/view/' + nik).then(function (data) {
            $scope.form.Department = data.department;
            $scope.form.Section = data.section;
            $scope.form.SubSection = data.subSection;
            $scope.form.Jabatan = data.jabatan;
            $scope.form.no = data.ijazah.no;
            $scope.form.tgl_ijazah = new Date(data.ijazah.tgl_ijazah);
            $scope.form.tgl_masuk = new Date(data.ijazah.tgl_masuk);
            
        });
    };

    $scope.cariDepartment = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('departement/cari', data).then(function (data) {
                $scope.listDepartment = data.data;
            });
        }
    };

    $scope.cariSection = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('section/cari', data).then(function (data) {
                $scope.listSection = data.data;
            });
        }
    };
    $scope.cariSubSection = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('subsection/cari', data).then(function (data) {
                $scope.listSubSection = data.data;
            });
        }
    };

    $scope.cariJabatan = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('jabatan/cari', data).then(function (data) {
                $scope.listJabatan = data.data;
            });
        }
    };
    $scope.retDepartment = function (item, form) {
        form.department = item.id_department;
    };
    $scope.retSection = function (item, form) {
        form.section = item.id_section;
    };
    $scope.retSubSection = function (item, form) {
        form.sub_section = item.id_kerja;
    };
    $scope.retJabatan = function (item, form) {
        form.jabatan = item.id_jabatan;
    };
    $scope.save = function (form) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }

        var url = ($scope.is_create == true) ? 'karyawan/create/' : 'karyawan/update/' + form.nik;
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
    $scope.saveNew = function (form) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }

        var url = 'karyawan/create/';
        if ($scope.nikSkarang == form.nik) {
            toaster.pop('error', "Terjadi Kesalahan", 'Ubah ke NIK Baru!!');
        } else {
            Data.post(url, form).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan", result.errors);
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                }
            });
        }
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
            Data.delete('karyawan/delete/' + row.nik).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.modalKeluar = function (form) {
        var modalInstance = $modal.open({//on modal open event;
            templateUrl: 'tpl/m_karyawan/modal.html',
            controller: 'modalCtrl',
            size: 'md',
            resolve: {
                form: function () {
                    return form;
                },
            }
        }).result.finally(function () {  //after modal closed event;
            $scope.callServer(tableStateRef);
        });
    };
    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'karyawan/upload/?folder=barang',
        formData: [],
        removeAfterUpload: true,
    });

    $scope.uploadGambar = function (form) {
        $scope.uploader.uploadAll();
    };

    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            var x = '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            if (!x) {
                toaster.pop('error', "Jenis gambar tidak sesuai");
            }
            return x;
        }
    });

    uploader.filters.push({
        name: 'sizeFilter',
        fn: function (item) {
            var xz = item.size < 2097152;
            if (!xz) {
                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 2 MB");
            }
            return xz;
        }
    });

    $scope.gambar = [];

    uploader.onSuccessItem = function (fileItem, response) {
        if (response.answer == 'File transfer completed') {
            $scope.gambar.unshift({name: response.name});
            $scope.form.foto = $scope.gambar;
        }
    };

    uploader.onBeforeUploadItem = function (item) {
        item.formData.push({
            nik: $scope.form.nik,
        });
    };

    $scope.removeFoto = function (paramindex, namaFoto) {
        var comArr = eval($scope.gambar);
        Data.post('karyawan/removegambar', {nik: $scope.form.nik, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modal = function (kd_barang, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/barang/' + kd_barang + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };
    /* sampe di sini*/

});

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.confirmAction = function (form) {
        Data.post('karyawan/keluar/' + form.nik, {form: form}).then(function (data) {
            $modalInstance.dismiss('cancle');
        });
    };

    $scope.open4 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened4 = true;
    };

    $scope.formmodal = form;
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});