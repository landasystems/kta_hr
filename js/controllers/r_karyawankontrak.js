app.controller('karyawanKontrakCtrl', function ($scope, Data, toaster, $modal) {
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.form.tipe = 'kelompok';
    $scope.form.tipe_periode = 'rentang';
    $scope.show_detail = false;
//    $scope.form.tanggal = {
//        'startDate' : new Date(),
//    };

    $scope.print = function (form) {
        if (('tanggal_rentang' in form && (form.tanggal_rentang != null || form.tanggal_rentang != undefined)) || ('tanggal' in form && (form.tanggal != null || form.tanggal != undefined)) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                window.open('api/web/karyawan/excelkontrak?print=true&rekap=karyawankontrak', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.excel = function (form) {
        if (('tanggal_rentang' in form && (form.tanggal_rentang != null || form.tanggal_rentang != undefined)) || ('tanggal' in form && (form.tanggal != null || form.tanggal != undefined)) || ('Karyawan' in form && form.Karyawan != undefined)) {
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                window.location = 'api/web/karyawan/excelkontrak?rekap=karyawankontrak';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.cariDepartment = function ($query) {
        if ($query.length >= 3) {
            Data.get('department/cari', {nama: $query}).then(function (data) {
                $scope.listDepartment = data.data;
            });
        }
    };

    $scope.cariSection = function ($query) {
        if ($query.length >= 3) {
            Data.get('section/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.cariJabatan = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
                $scope.listJabatan = data.data;
            });
        }
    };
    $scope.cariKaryawan = function ($query) {
        if ($query.length >= 3) {
            Data.get('karyawan/cari', {nama: $query}).then(function (data) {
                $scope.listKaryawan = data.data;
            });
        }
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

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if (('tanggal_rentang' in form && (form.tanggal_rentang != null || form.tanggal_rentang != undefined)) || ('tanggal' in form && (form.tanggal != null || form.tanggal != undefined)) || ('Karyawan' in form && form.Karyawan != undefined)) {
            $scope.show_detail = true;
            Data.post('karyawan/rekapkontrak', form).then(function (data) {
                $scope.listSrc = [];
                var urut = 1;
                angular.forEach(data.data, function ($value, $key) {

                    $scope.listSrc.push($value);
                    urut++;
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    };

    $scope.modalPenilaian = function (form, tipe) {
        var data = {
            form: form,
            tipe: tipe
        };

        var modalInstance = $modal.open({//on modal open event;
            templateUrl: 'tpl/r_karyawankontrak/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return data;
                }
            }
        }).result.finally(function () {  //after modal closed event;
            $scope.view($scope.form);
        });
    };
});

app.controller('modalCtrl', function ($scope, Data, toaster, $modalInstance, form) {
    var forms = form.form;
    var tipe = form.tipe;
    $scope.formmodal = {};
    Data.post('penilaiankontrak/view/' + forms.nik, form).then(function (data) {

        if (data.data == false) {
            $scope.formmodal = forms;
            $scope.formmodal.tgl = new Date();
        } else {
            $scope.formmodal = data.data;
            $scope.formmodal.tgl = new Date(data.data.tgl);
        }

    });

    $scope.nilaiTotal = function () {
        var subTotal1 = 0;
        var subTotal2 = 0;
        var subTotal3 = 0;
        var mutu_kerja = ($scope.formmodal.mutu_kerja !== undefined) ? parseInt($scope.formmodal.mutu_kerja) : 0;
        var pengetahuan_teknis = ($scope.formmodal.pengetahuan_teknis !== undefined) ? parseInt($scope.formmodal.pengetahuan_teknis) : 0;
        var tgjawab_pekerjaan = ($scope.formmodal.tgjawab_pekerjaan !== undefined) ? parseInt($scope.formmodal.tgjawab_pekerjaan) : 0;
        var kerjasama_komunikasi = ($scope.formmodal.kerjasama_komunikasi !== undefined) ? parseInt($scope.formmodal.kerjasama_komunikasi) : 0;
        var sikap_kerja = ($scope.formmodal.sikap_kerja !== undefined) ? parseInt($scope.formmodal.sikap_kerja) : 0;
        var inisiatif = ($scope.formmodal.inisiatif !== undefined) ? parseInt($scope.formmodal.inisiatif) : 0;
        var rasa_turut_memiliki = ($scope.formmodal.rasa_turut_memiliki !== undefined) ? parseInt($scope.formmodal.rasa_turut_memiliki) : 0;
        var disiplinitas = ($scope.formmodal.disiplinitas !== undefined) ? parseInt($scope.formmodal.disiplinitas) : 0;
        var kehadiran = ($scope.formmodal.kehadiran !== undefined) ? parseInt($scope.formmodal.kehadiran) : 0;
        var administratif = ($scope.formmodal.administratif !== undefined) ? parseInt($scope.formmodal.administratif) : 0;
        var kepemimpinan = ($scope.formmodal.kepemimpinan !== undefined) ? parseInt($scope.formmodal.kepemimpinan) : 0;
        var pelaksanaan_managerial = ($scope.formmodal.pelaksanaan_managerial !== undefined) ? parseInt($scope.formmodal.pelaksanaan_managerial) : 0;
        var problem_solving = ($scope.formmodal.problem_solving !== undefined) ? parseInt($scope.formmodal.problem_solving) : 0;

        subTotal1 = mutu_kerja + pengetahuan_teknis + tgjawab_pekerjaan + kerjasama_komunikasi + sikap_kerja + inisiatif + rasa_turut_memiliki + disiplinitas;
        subTotal3 = kehadiran + administratif;
        subTotal2 = kepemimpinan + pelaksanaan_managerial + problem_solving;

        $scope.formmodal.sub1 = ((subTotal1 / 100) * 40).toFixed(2);
        $scope.formmodal.sub2 = ((subTotal2 / 100) * 20).toFixed(2);
        $scope.formmodal.sub3 = ((subTotal3 / 100) * 40).toFixed(2);
        $scope.formmodal.penilaian = (parseFloat($scope.formmodal.sub1) + parseFloat($scope.formmodal.sub2) + parseFloat($scope.formmodal.sub3)).toFixed(2);

    };

    $scope.nilaiToString = function (angka) {
        var hasil = '';
        if (angka == 4) {
            hasil = 'A';
        } else if (angka == 3) {
            hasil = 'B';
        } else if (angka == 2) {
            hasil = 'C';
        } else {
            hasil = 'D';
        }

        return hasil;
    };

    $scope.ternilai = function (angka) {
        var hasil = '';
        var ket = '';
        var nilai = parseInt(angka);

        if (nilai <= 4.60 || nilai == 0) {
            hasil = 'D';
            ket = 'Kurang Baik';
        } else if (nilai <= 9.2) {
            hasil = 'C';
            ket = 'Cukup Baik';
        } else if (nilai <= 13.8) {
            hasil = 'B';
            ket = 'Baik';
        } else if (nilai > 13.8) {
            hasil = 'A';
            ket = 'Sangat Baik';
        }
        $scope.formmodal.keterangan = ket;
        return hasil;
    };

    $scope.save = function (form) {
        var data = {
            form: form,
            kontrak: tipe
        };
        var url = ("id" in form && form.id !== null) ? 'penilaiankontrak/update/' + form.id : 'penilaiankontrak/create';
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                $modalInstance.dismiss('cancel');
            }
        });
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});