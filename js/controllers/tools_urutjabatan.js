app.controller('urutJabatanCtrl', function ($scope, $timeout, Data, toaster, $http) {
    var apple_selected, tree, treedata_avm, treedata_geography;
    $scope.data_karyawan = [];
    Data.get('karyawan/urut-jabatan').then(function (data) {        
        angular.forEach(data.data,function(val,key){
            var aa = val;
            $scope.data_karyawan.push(aa);
        });
    });
    
    $scope.my_tree_handler = function (branch) {
        
        var _ref;
        $scope.output = "Anda Memilih: " + branch.label;
        if ((_ref = branch.data) != null ? _ref.description : void 0) {
            return $scope.output += '(' + branch.data.description + ')';
        }
        
        Data.get('karyawan/det-karyawan',branch).then(function(data){
           $scope.detailKaryawan = data.data; 
        });
    };
    
    treedata_avm = [];

    treedata_geography = $scope.data_karyawan;
    
    $scope.my_data = treedata_geography;
    $scope.try_changing_the_tree_data = function () {
        if ($scope.my_data === treedata_avm) {
            return $scope.my_data = treedata_geography;
        } else {
            return $scope.my_data = treedata_avm;
        }
    };
    $scope.my_tree = tree = {};
    $scope.try_async_load = function () {
        $scope.my_data = [];
        $scope.doing_async = true;
        return $timeout(function () {
            if (Math.random() < 0.5) {
                $scope.my_data = treedata_avm;
            } else {
                $scope.my_data = treedata_geography;
            }
            $scope.doing_async = false;
            return tree.expand_all();
        }, 1000);
    };
    return $scope.try_adding_a_branch = function () {
        var b;
        b = tree.get_selected_branch();
        return tree.add_branch(b, {
            label: 'New Branch',
            data: {
                something: 42,
                "else": 43
            }
        });
    };
});