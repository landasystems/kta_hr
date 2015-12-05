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
        $scope.cariDepartment();
        $scope.formtitle = "Form Tambah Data Karyawan";
        $scope.form = {};
        $scope.form.tgl_masuk_kerja = new Date();
        $scope.form.tgl_selesai = new Date();
        $scope.form.tgl_lahir = new Date();
//        $scope.form.Kontrak_1 = new Date();
//        $scope.form.Kontrak_11 = new Date();
//        $scope.form.Kontrak_2 = new Date();
//        $scope.form.Kontrak_21 = new Date();
        $scope.form.nik = '';
        $scope.setCode($scope.form);
        Data.get('ijazah/kode', form).then(function (data) {
            $scope.form.no = data.kode;
        });

    };

    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_newcopy = true;
        $scope.cariDepartment();
        $scope.form.tgl_lahir = new Date(form.tgl_lahir);
        $scope.form.tgl_masuk_kerja = new Date(form.tgl_masuk_kerja);
        $scope.form.Kontrak_1 = (form.Kontrak_1 != undefined) ? new Date(form.Kontrak_1) : null;
        $scope.form.Kontrak_11 = (form.Kontrak_1 != undefined) ? new Date(form.Kontrak_11) : null;
        $scope.form.Kontrak_2 = (form.Kontrak_2 != undefined) ? new Date(form.Kontrak_2) : null;
        $scope.form.Kontrak_21 = (form.Kontrak_21 != undefined) ? new Date(form.Kontrak_21) : null;
        $scope.formtitle = "Edit Data : " + form.nik;

        $scope.loadDetail(form.nik);
        $scope.nikSkarang = form.nik;
        if ($scope.form.no === undefined) {
            Data.get('ijazah/kode', form).then(function (data) {
                $scope.form.no = data.kode;
            });
        }
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
        if ($scope.is_create == true && form.status_karyawan !== undefined) {
            var data = {status: form.status_karyawan};
            Data.get('karyawan/kode', {status: form.status_karyawan}).then(function (data) {
                form.nik = data.kode;
            });
        }
    };

    $scope.loadDetail = function (nik) {
        Data.get('karyawan/view/' + nik).then(function (data) {
            $scope.form.Department = data.department;
            $scope.form.Section = data.section;
            $scope.form.SubSection = data.subSection;
            $scope.form.Jabatan = data.jabatan;
            $scope.form.Ketua = data.ketua;
            $scope.form.no = data.ijazah.no;
            $scope.form.tgl_ijazah = (data.ijazah.tgl_ijazah !== undefined) ? new Date(data.ijazah.tgl_ijazah) : null;
            $scope.form.tgl_masuk = (data.ijazah.tgl_masuk !== undefined) ? new Date(data.ijazah.tgl_masuk) : null;
        });
    };

    $scope.inisial = function (form) {
        if (form.nama.length) {
            var slr = form.nama;
            var ini = slr.split(" ");
            if (ini.length >= 2) {
                var pertama = ini[0];
                var kedua = ini[1];
                form.initial = pertama.substring(2, 0) + kedua.substring(1, 0);
            } else {
                var pertama = ini[0];
                form.initial = pertama.substring(3, 0);
            }
        } else {
            form.initial = undefined;
        }
    };

    $scope.cariDepartment = function () {
        Data.get('departement/listdepartment').then(function (data) {
            $scope.listDepartment = data.data;
        });
    };

    $scope.cariSection = function (nama) {
        Data.get('section/list', {nama: nama}).then(function (data) {
            $scope.listSection = data.data;
        });
    };

    $scope.cariSubSection = function (nama) {
        var data = {nama: nama};
        Data.get('subsection/list', data).then(function (data) {
            $scope.listSubSection = data.data;
        });
    };

    $scope.cariJabatan = function (nama) {
        var data = {nama: nama};
        Data.get('jabatan/list', data).then(function (data) {
            $scope.listJabatan = data.data;
        });
    };
    $scope.retDepartment = function (item, form) {
        form.department = item.id_department;
        $scope.cariSection(item.id_department);
    };
    $scope.retSection = function (item, form) {
        form.section = item.id_section;
        $scope.cariSubSection(item.id_section);
    };
    $scope.retSubSection = function (item, form) {

        form.sub_section = item.kd_kerja;
        $scope.cariJabatan(item.kd_kerja);
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

    $scope.cariKetua = function (nama) {
        if (nama.length > 2) {
            var data = {nama: nama};
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.listKetua = data.data;
            });
        }
    };
    $scope.retKetua = function (item, form) {
        form.nik_ketua = item.nik;
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
                }
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
