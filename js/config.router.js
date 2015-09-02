'use strict';

/**
 * Config for the router
 */
angular.module('app')
        .run(
                ['$rootScope', '$state', '$stateParams', 'Data',
                    function ($rootScope, $state, $stateParams, Data) {
                        $rootScope.$state = $state;
                        $rootScope.$stateParams = $stateParams;
                        //pengecekan login
                        $rootScope.$on("$stateChangeStart", function (event, toState) {
                            var globalmenu = ['app.dashboard', 'master.userprofile', 'access.signin', 'transaksi.coba'];
                            Data.get('site/session').then(function (results) {
                                if (typeof results.data.user != "undefined") {
                                    $rootScope.user = results.data.user;
                                    if (results.data.user.akses[(toState.name).replace(".", "_")]) { // jika punya hak akses, return true

                                    } else {
                                        if (globalmenu.indexOf(toState.name) >= 0) { //menu global menu tidak di redirect

                                        } else {
                                            $state.go("access.forbidden");
                                        }
                                    }
                                } else {
                                    $state.go("access.signin");
                                }
                            });
                        });
                    }
                ]
                )
        .config(
                ['$stateProvider', '$urlRouterProvider',
                    function ($stateProvider, $urlRouterProvider) {

                        $urlRouterProvider
                                .otherwise('/app/dashboard');
                        $stateProvider
                                .state('app', {
                                    abstract: true,
                                    url: '/app',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('app.dashboard', {
                                    url: '/dashboard',
                                    templateUrl: 'tpl/dashboard.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                            }]
                                    }
                                })
                                // others
                                .state('access', {
                                    url: '/access',
                                    template: '<div ui-view class="fade-in-right-big smooth"></div>'
                                })
                                .state('access.signin', {
                                    url: '/signin',
                                    templateUrl: 'tpl/page_signin.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/site.js').then(
                                                        );
                                            }]
                                    }
                                })
                                .state('access.forbidden', {
                                    url: '/forbidden',
                                    templateUrl: 'tpl/page_forbidden.html'
                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
                                })
                                //master roles
                                .state('master', {
                                    url: '/master',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('master.barang', {
                                    url: '/barang',
                                    templateUrl: 'tpl/m_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/barang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //jenis barang
                                .state('master.jenisbrg', {
                                    url: '/jenisbrg',
                                    templateUrl: 'tpl/m_jenisbrg/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/jenisbrg.js');
                                            }]
                                    }
                                })
                                .state('master.customer', {
                                    url: '/customer',
                                    templateUrl: 'tpl/m_customer/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/customer.js');
                                            }]
                                    }
                                })
                                .state('master.modelkendaraan', {
                                    url: '/modelkendaraan',
                                    templateUrl: 'tpl/m_modelkendaraan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/modelkendaraan.js');
                                            }]
                                    }
                                })
                                .state('master.jnskomplain', {
                                    url: '/jnskomplain',
                                    templateUrl: 'tpl/m_jnskomplain/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/jnskomplain.js');
                                            }]
                                    }
                                })
                                .state('master.chassis', {
                                    url: '/chassis',
                                    templateUrl: 'tpl/m_chassis/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/chassis.js');
                                            }]
                                    }
                                })
                                // supplier
                                .state('master.supplier', {
                                    url: '/supplier',
                                    templateUrl: 'tpl/m_supplier/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/supplier.js');
                                            }]
                                    }
                                })
                                // kalender
                                .state('master.kalender', {
                                    url: '/kalender',
                                    templateUrl: 'tpl/m_kalender/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/kalender.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // lokasi
                                .state('master.lokasi', {
                                    url: '/lokasi-kantor',
                                    templateUrl: 'tpl/m_lokasikantor/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/lokasikantor.js');

                                            }]
                                    }
                                })
                                // jabatan
                                .state('master.jabatan', {
                                    url: '/jabatan',
                                    templateUrl: 'tpl/m_jabatan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jabatan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // subsection
                                .state('master.subsection', {
                                    url: '/subsection',
                                    templateUrl: 'tpl/m_subsection/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/subsection.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // section
                                .state('master.section', {
                                    url: '/section',
                                    templateUrl: 'tpl/m_section/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/section.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //jamsostek
                                .state('master.jamsostek', {
                                    url: '/jamsostek',
                                    templateUrl: 'tpl/m_jamsostek/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jamsostek.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //alat pelindung diri
                                .state('master.apd', {
                                    url: '/apd',
                                    templateUrl: 'tpl/m_apd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/apd.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // umk
                                .state('master.umk', {
                                    url: '/umk',
                                    templateUrl: 'tpl/m_umk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/umk.js');

                                            }]
                                    }
                                })
                                // departement
                                .state('master.departement', {
                                    url: '/department',
                                    templateUrl: 'tpl/m_department/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                                return $ocLazyLoad.load('js/controllers/departement.js');

                                            }]
                                    }
                                })

                                // user
                                .state('master.user', {
                                    url: '/pengguna',
                                    templateUrl: 'tpl/m_user/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna.js');
                                            }]
                                    }
                                })
                                .state('master.userprofile', {
                                    url: '/profile',
                                    templateUrl: 'tpl/m_user/profile.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna_profile.js');
                                            }]
                                    }
                                })
                                .state('master.roles', {
                                    url: '/roles',
                                    templateUrl: 'tpl/m_roles/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/roles.js');
                                            }]
                                    }})
                                // Transaksi
                                .state('transaksi', {
                                    url: '/transaksi',
                                    templateUrl: 'tpl/app.html'
                                })
                                //BOM
                                .state('transaksi.bom', {
                                    url: '/bom',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_bom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bom.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //Rubah Bentuk
                                .state('transaksi.rubahbentuk', {
                                    url: '/rubah-bentuk',
                                    templateUrl: 'tpl/t_rubahbentuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/rubahbentuk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //BSTK
                                .state('transaksi.bstk', {
                                    url: '/bstk',
                                    templateUrl: 'tpl/t_bstk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bstk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('transaksi.sti', {
                                    url: '/sti',
                                    templateUrl: 'tpl/t_sti/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/sti.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //Validasi bom
                                .state('transaksi.validasibom', {
                                    url: '/validasibom',
                                    templateUrl: 'tpl/t_validasibom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/validasibom.js');
                                            }]
                                    }})
                                // SPK
                                .state('transaksi.spk', {
                                    url: '/spk',
                                    templateUrl: 'tpl/t_spk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/spk.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.spesanankaroseri', {
                                    url: '/suratpesanankaroseri',
                                    templateUrl: 'tpl/t_s-pesanankaroseri/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/s-pesanankaroseri.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('transaksi.sperintahkaroseri', {
                                    url: '/suratperintahkaroseri',
                                    templateUrl: 'tpl/t_s-perintahkaroseri/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/s-perintahkaroseri.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.valbarangkeluar', {
                                    url: '/val-barangkeluar',
                                    templateUrl: 'tpl/t_val-barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/validasibkk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.bktbarangkeluar', {
                                    url: '/bkt-barangkeluar',
                                    templateUrl: 'tpl/t_bkt-barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bkt-barangkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.bktbarangmasuk', {
                                    url: '/bkt-barangmasuk',
                                    templateUrl: 'tpl/t_bkt-barangmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bkt-barangmasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.buktiterima', {
                                    url: '/bukti-terima',
                                    templateUrl: 'tpl/t_bukti-terima/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/bkt-terima.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.pembatalanchasis', {
                                    url: '/pembatalanchassis',
                                    templateUrl: 'tpl/t_pembatalanchassis/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pembatalanchassis.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.claimunit', {
                                    url: '/claimunit',
                                    templateUrl: 'tpl/t_claimunit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/claimunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.deliveryunit', {
                                    url: '/deliveryunit',
                                    templateUrl: 'tpl/t_deliveryunit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/deliveryunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.kpb', {
                                    url: '/kpb',
                                    templateUrl: 'tpl/t_kpb/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/kpb.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.purchaseorder', {
                                    url: '/purchase-order',
                                    templateUrl: 'tpl/t_purchase-order/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/purchase-order.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.returbuktibarangkeluar', {
                                    url: '/retur-buktibarangkeluar',
                                    templateUrl: 'tpl/t_retur-buktibarangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/retur-buktibarangkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('transaksi.returbuktibarangmasuk', {
                                    url: '/retur-buktibarangmasuk',
                                    templateUrl: 'tpl/t_retur-buktibarangmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/retur-buktibarangmasuk.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.sppnonrutin', {
                                    url: '/spp-nonrutin',
                                    templateUrl: 'tpl/t_spp-nonrutin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/spp-nonrutin.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.spprutin', {
                                    url: '/spp-rutin',
                                    templateUrl: 'tpl/t_spp-rutin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/spp-rutin.js');
                                                        });
                                            }]
                                    }})
                                //
                                .state('transaksi.tambahitem', {
                                    url: '/tambahitem',
                                    templateUrl: 'tpl/t_tambahitem/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/tambahitem.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.ujimutu', {
                                    url: '/ujimutu',
                                    templateUrl: 'tpl/t_ujimutu/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/ujimutu.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('transaksi.winprogress', {
                                    url: '/workinprogress',
                                    templateUrl: 'tpl/t_w-inprogress/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/wip.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.worderkeluar', {
                                    url: '/workorderkeluar',
                                    templateUrl: 'tpl/t_w-orderkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/wokeluar.js');
                                            }]
                                    }})
                                //
                                .state('transaksi.wordermasuk', {
                                    url: '/workordermasuk',
                                    templateUrl: 'tpl/t_w-ordermasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/womasuk.js');
                                                        }
                                                );
                                            }]
                                    }})

                                // Rekap
                                .state('rekap', {
                                    url: '/rekap',
                                    templateUrl: 'tpl/app.html'
                                })
                                //
                                .state('rekap.purchaseorder', {
                                    url: '/purchase-order',
                                    templateUrl: 'tpl/r_purchase-order/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_purchase-order.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.supplier', {
                                    url: '/supplier',
                                    templateUrl: 'tpl/r_supplier/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.barang', {
                                    url: '/barang',
                                    templateUrl: 'tpl/r_purchase-order/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.barangmasuk', {
                                    url: '/barang-masuk',
                                    templateUrl: 'tpl/r_barang-masuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_barangmasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.barangkeluar', {
                                    url: '/barang-keluar',
                                    templateUrl: 'tpl/r_barang-keluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_barangkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                ///
                                .state('rekap.pergerakanbarang', {
                                    url: '/pergerakan-barang',
                                    templateUrl: 'tpl/r_pergerakan-barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_pergerakan_brg.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.spp', {
                                    url: '/spp',
                                    templateUrl: 'tpl/r_spp/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.rubahbentuk', {
                                    url: '/rubah-bentuk',
                                    templateUrl: 'tpl/r_rubah-bentuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/rubahbentuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.claimunit', {
                                    url: '/claim-unit',
                                    templateUrl: 'tpl/r_claim-unit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_claimunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.customer', {
                                    url: '/customer',
                                    templateUrl: 'tpl/r_customer/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.bom', {
                                    url: '/bom',
                                    templateUrl: 'tpl/r_bom/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/bom.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.chassisin', {
                                    url: '/chassis-in',
                                    templateUrl: 'tpl/r_chassis-in/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_chassisin.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.bstk', {
                                    url: '/bstk',
                                    templateUrl: 'tpl/r_bstk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_bstk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.suratpesanan', {
                                    url: '/surat-pesanan',
                                    templateUrl: 'tpl/r_surat-pesanan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/s-pesanankaroseri.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.ujimutu', {
                                    url: '/uji-mutu',
                                    templateUrl: 'tpl/r_uji-mutu/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_ujimutu.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.deliveryunit', {
                                    url: '/delivery-unit',
                                    templateUrl: 'tpl/r_delivery-unit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_deliveryunit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.womasuk', {
                                    url: '/wo-masuk',
                                    templateUrl: 'tpl/r_wo-masuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_womasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.wokeluar', {
                                    url: '/wo-keluar',
                                    templateUrl: 'tpl/r_wo-keluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_wokeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.retbarangmasuk', {
                                    url: '/ret-barangmasuk',
                                    templateUrl: 'tpl/r_ret-barangmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_returbarangmasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.retbarangkeluar', {
                                    url: '/ret-barangkeluar',
                                    templateUrl: 'tpl/r_ret-barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_returbarangkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.wip', {
                                    url: '/wip',
                                    templateUrl: 'tpl/r_wip/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.schedule', {
                                    url: '/schedule',
                                    templateUrl: 'tpl/r_schedule/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/wip.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.historybarang', {
                                    url: '/history-barang',
                                    templateUrl: 'tpl/r_history-barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.historyunit', {
                                    url: '/rhistory-unit',
                                    templateUrl: 'tpl/r_history-unit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})
                                //
                                .state('rekap.historywip', {
                                    url: '/rhistory-wip',
                                    templateUrl: 'tpl/r_history-wip/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/isidewe.js');
                                                        }
                                                );
                                            }]
                                    }})

                                //notifikasi
                                .state('notif', {
                                    url: '/notif',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('notif.barang', {
                                    url: '/barang',
                                    templateUrl: 'tpl/n_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/n_barang.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('notif.unit', {
                                    url: '/unit',
                                    templateUrl: 'tpl/n_unit/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/n_unit.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('notif.barangkeluar', {
                                    url: '/barangkeluar',
                                    templateUrl: 'tpl/n_barangkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/n_barangkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})

                    }
                ]);

