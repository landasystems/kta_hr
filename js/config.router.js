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
                                //potongan
                                .state('master.potongan', {
                                    url: '/potongan',
                                    templateUrl: 'tpl/m_potongan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/potongan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //barang atk
                                .state('master.barangatk', {
                                    url: '/barangatk',
                                    templateUrl: 'tpl/m_barangatk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/barangatk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //stok atk
                                .state('master.stokatk', {
                                    url: '/stokatk',
                                    templateUrl: 'tpl/m_stokatk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/stokatk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //barang atk
                                .state('master.filelegalitas', {
                                    url: '/filelegalitas',
                                    templateUrl: 'tpl/m_filelegalitas/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/filelegalitas.js');
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

                                // Pegawai
                                .state('pegawai', {
                                    url: '/pegawai',
                                    templateUrl: 'tpl/app.html'
                                })
                                //Ijazah
                                .state('pegawai.ijazah', {
                                    url: '/ijazah',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_ijazah/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/ijazah.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //karyawan
                                .state('pegawai.karyawan', {
                                    url: '/karyawan',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_karyawan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/karyawan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('pegawai.lamarankerja', {
                                    url: '/lamarankerja',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_lamarankerja/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('angularFileUpload').then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/lamarankerja.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('pegawai.penilaiankontrak', {
                                    url: '/penilaiankontrak',
                                    params: {'form': null},
                                    templateUrl: 'tpl/p_penilaiankontrak/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/penilaiankontrak.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('pegawai.magang', {
                                    url: '/magang',
                                    params: {'form': null},
                                    templateUrl: 'tpl/p_magang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/magang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('pegawai.prakerin', {
                                    url: '/prakerin',
                                    params: {'form': null},
                                    templateUrl: 'tpl/p_prakerin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/prakerin.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('pegawai.pendaftaranjamsostek', {
                                    url: '/pendaftaranjamsostek',
                                    params: {'form': null},
                                    templateUrl: 'tpl/p_pendaftaranjamsostek/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/pendaftaranjamsostek.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // Jadwal
                                .state('jadwal', {
                                    url: '/jadwal',
                                    templateUrl: 'tpl/app.html'
                                })

                                .state('jadwal.jpelatihan', {
                                    url: '/jpelatihan',
                                    templateUrl: 'tpl/t_jpelatihan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jpelatihan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('jadwal.penilaian', {
                                    url: '/penilaian',
                                    templateUrl: 'tpl/j_penilaian/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jadwalpenilaian.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('jadwal.auditsemester', {
                                    url: '/auditsemester',
                                    templateUrl: 'tpl/j_auditsemester/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jadwalauditsemester.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('jadwal.hsetalk', {
                                    url: '/hsetalk',
                                    templateUrl: 'tpl/j_hse/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/jadwalhse.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('transaksi', {
                                    url: '/transaksi',
                                    templateUrl: 'tpl/app.html'
                                })

                                .state('transaksi.hsetalk', {
                                    url: '/hsetalk',
                                    templateUrl: 'tpl/t_hsetalk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/hsetalk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('transaksi.agendapelatihan', {
                                    url: '/agendapelatihan',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_agendapelatihan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/agendapelatihan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.agendaumum', {
                                    url: '/agendaumum',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_agendaumum/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/agendaumum.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.atkkeluar', {
                                    url: '/atkkeluar',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_atkkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/atkkeluar.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.atkmasuk', {
                                    url: '/atkmasuk',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_atkmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/atkmasuk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.karyawankeluar', {
                                    url: '/karyawankeluar',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_karyawankeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/karyawankeluar.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.karyawanspd', {
                                    url: '/karyawanspd',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_karyawanspd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/karyawanspd.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.lamarankerja', {
                                    url: '/lamarankerja',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_lamarankerja/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/lamarankerja.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.p2k3', {
                                    url: '/p2k3',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_p2k3/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/p2k3.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.pengeluaranapd', {
                                    url: '/pengeluaranapd',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_pengeluaranapd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/pengeluaranapd.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.pemasukanapd', {
                                    url: '/pemasukanapd',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_pemasukanapd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/pemasukanapd.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.pemakaianlistrikair', {
                                    url: '/pemakaianlistrikair',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_pemakaianlistrikair/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/pemakaianlistrikair.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                //
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
                                    }});

                    }
                ]);

