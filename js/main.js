'use strict';

/* Controllers */

angular.module('app')
        .controller('AppCtrl', ['$scope', '$window', 'Data', '$state',
            function($scope, $window, Data, $state) {
                // add 'ie' classes to html
                var isIE = !!navigator.userAgent.match(/MSIE/i);
                isIE && angular.element($window.document.body).addClass('ie');
                isSmartDevice($window) && angular.element($window.document.body).addClass('smart');

                // config
                $scope.app = {
                    name: 'KTA HRD',
                    version: '1.1',
                };

                //cek warna di session
                Data.get('site/session').then(function(data) {
                    if (typeof data.data.user != "undefined" && data.data.user.settings!=null) {
                        $scope.app.settings = data.data.user.settings;
                    } else { //default warna jika tidak ada setingan
                        $scope.app.settings = {
                            themeID: 11,
                            navbarHeaderColor: 'bg-primary',
                            navbarCollapseColor: 'bg-primary',
                            asideColor: 'bg-dark',
                        };
                    }
                });

                function isSmartDevice($window)
                {
                    // Adapted from http://www.detectmobilebrowsers.com
                    var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
                    // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
                    return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
                }

                $scope.pencarian = function($query) {
                    if ($query.length >= 3) {
                        Data.get('bom/cari', {nama: $query}).then(function(data) {
                            $scope.results = data.data;
                        });
                    }
                };

                $scope.pencarianDet = function($query) {
                    $state.go('transaksi.bom', {form: $query});
                };

                $scope.logout = function() {
                    Data.get('site/logout').then(function(results) {
                        $state.go('access.signin');
                    });
                };

            }]);
        
$(document).ready(function () {
    
    $("body").on("keypress", ".angka", function (s) {
        var i = s.which ? s.which : event.keyCode;
        return i > 31 && (48 > i || i > 57) && 45 != i && 46 != i ? !1 : !0
    }), $("body").on("focus", ".angka", function () {
        0 == $(this).val() && $(this).val("")
    }), $("body").on("blur", ".angka", function () {
        "" == $(this).val() && $(this).val(0)
    }), $("input").keypress(function (s) {
        13 == s.keyCode && s.preventDefault()
    })
});