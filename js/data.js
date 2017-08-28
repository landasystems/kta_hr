app.factory("Data", ['$http', '$location',
    function ($http, $q, $location) {
        var serviceBase = function(s) { 
            return (s == "s") ? "api_slim/" : (s == "" || s == undefined) ? 'api/web/' : "";
        }

        var obj = {};
        
        obj.base = serviceBase("");

        obj.get = function (q, object, r) {
            return $http.get(serviceBase(r) + q, {
                params: object
            }).then(function (results) {
                return results.data;
            });
        };
        obj.post = function (q, object, r) {
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
            return $http.post(serviceBase(r) + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.put = function (q, object, r) {
            return $http.put(serviceBase(r) + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.delete = function (q, r) {
            return $http.delete(serviceBase(r) + q).then(function (results) {
                return results.data;
            });
        };
        return obj;
    }]);
