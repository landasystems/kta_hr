app.controller('auditSemesterCtrl', function ($scope, Data, toaster) {
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
        Data.get('jauditsemester', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excel = function() {
        Data.get('jauditsemester', paramRef).then(function(data) {
            window.location = 'api/web/jauditsemester/excel';
        });
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cariAuditee = function (nama) {
        if (nama.length > 2) {
            var data = {
                nama: nama
            };
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detAuditee = data.data;
            });
        }
    };
    $scope.getAuditee = function (det, item) {
        det.auditee = item.nik;
        det.dept_auditee = item.department;
    };

    $scope.cariAuditor = function (nama) {
        if (nama.length > 2) {
            var data = {
                nama: nama
            };
            Data.get('karyawan/cari', data).then(function (data) {
                $scope.detAuditor = data.data;
            });
        }
    };
    $scope.getAuditor = function (det, item) {
        det.auditor = item.nik;
        det.dept_auditor = item.department;
    };
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Audit Semester";
        $scope.form = {};
        $scope.detAudit = [{}];
        $scope.form.tgl = new Date();
        Data.get('jauditsemester/kode', form).then(function (data) {
            $scope.form.no_audit = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form.tgl = new Date(form.tgl);
        $scope.formtitle = "Edit Data : " + form.no_audit;
        $scope.retDetail(form.no_audit);
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_audit;
        $scope.retDetail(form.no_audit);
    };
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };

        var url = ($scope.is_create == true) ? 'jauditsemester/create' : 'jauditsemester/update/' + form.no_audit;
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
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
            Data.delete('jauditsemester/delete/' + row.no_audit).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.addrow = function () {
        $scope.detAudit.unshift({
            id: 0,
            auditee: '',
            dept_auditee: '',
            teraudit: [],
            auditor: '',
            dept_auditor: '',
            pengaudit: []
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detAudit);
        if (comArr.length > 1) {
            $scope.detAudit.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.retDetail = function (no) {
        Data.get('jauditsemester/view/'+no).then(function(data){
            $scope.detAudit = data.data;
        });
    };
});
