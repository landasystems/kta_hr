app.controller("asuransiCtrl", function ($scope, Data, toaster) {
	$scope.isEdit = false;
	$scope.isView = false;
	$scope.asuransi = [];
	$scope.form = {};

	$scope.refreshData = function() {
		Data.get("asuransi").then(function(data) {
		$scope.asuransi = data.data;
		console.log($scope.asuransi);
		console.log(data.data);
	}); };
	$scope.refreshData();

	$scope.new = function () {
		$scope.form = {};
		$scope.form.id = 0;
		$scope.isEdit = true;
	};

	$scope.view = function (f) {
		$scope.form = fl;
		$scope.isView = true;
	}

	$scope.edit = function (f) {
		$scope.form = f;
		$scope.isEdit = true;
	};

	$scope.save = function (f) {
		var url = (f.id == 0) ? "asuransi/insert" : "asuransi/update";
		Data.post(url, f).then(function(result) {
			if(result.status == 0)
				toaster.pop("error", "Galat", "Data gagal disimpan.");
			else {
				toaster.pop("success", "Berhasil", "Data sudah disimpan.");
				$scope.isEdit = false;
				$scope.refreshData();
			}
		});
	};

	$scope.delete = function (f) {
		if (confirm("Hapus permanen " + f.nama + "?")) {
			Data.post("asuransi/delete", f).then( function (result) {
				if(result.status > 0) {
				 toaster.pop("success", "", f.nama + "sudah dihapus.");
				 $scope.asuransi.splice($scope.asuransi.indexOf(f));
				}
			});
		}
	};

	$scope.cancel = function() {
		$scope.isEdit = false;
		$scope.isView = false;
	};
});