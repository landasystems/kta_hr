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
                                .state('master.karyawan', {
                                    url: '/karyawan',
                                    templateUrl: 'tpl/m_karyawan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['angularFileUpload', ]).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/karyawan.js');
                                                        }
                                                );
                                            }]
                                    }})
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
                                .state('monitoring', {
                                    url: '/monitoring',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('monitoring.filelegalitas', {
                                    url: '/filelegalitas',
                                    templateUrl: 'tpl/mo_filelegalitas/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/mo_filelegalitas.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('monitoring.asuransikendaraan', {
                                    url: '/asuransikendaraan',
                                    templateUrl: 'tpl/mo_asuransikendaraan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/mo_asuransikendaraan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('monitoring.servicekendaraan', {
                                    url: '/servicekendaraan',
                                    templateUrl: 'tpl/mo_servicekendaraan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/mo_servicekendaraan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('monitoring.stnk', {
                                    url: '/stnk',
                                    templateUrl: 'tpl/mo_stnk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/mo_stnk.js');
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
                                .state('transaksi.kecelakaankerja', {
                                    url: '/kecelakaankerja',
                                    params: {'form': null},
                                    templateUrl: 'tpl/t_kecelakaankerja/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/kecelakaankerja.js');
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
                                .state('rekap.ijazahmasuk', {
                                    url: '/ijazahmasuk',
                                    templateUrl: 'tpl/r_ijazahmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_ijazahmasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.ijazahkeluar', {
                                    url: '/ijazahkeluar',
                                    templateUrl: 'tpl/r_ijazahkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_ijazahkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.penilaiankontrak', {
                                    url: '/penilaiankontrak',
                                    templateUrl: 'tpl/r_penilaiankontrak/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_penilaiankontrak.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.lamarkerjaperpribadi', {
                                    url: '/lamarkerjaperpribadi',
                                    templateUrl: 'tpl/r_lamarkerjaperpribadi/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_lamarkerjaperpribadi.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.lamarkerjaperpend', {
                                    url: '/lamarkerjaperpend',
                                    templateUrl: 'tpl/r_lamarkerjaperpend/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_lamarkerjaperpend.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.jamsostek', {
                                    url: '/jamsostek',
                                    templateUrl: 'tpl/r_jamsostek/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_jamsostek.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawankeluar', {
                                    url: '/karyawankeluar',
                                    templateUrl: 'tpl/r_karyawankeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawankeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanmasukpertunjangan', {
                                    url: '/karyawanmasukpertunjangan',
                                    templateUrl: 'tpl/r_karyawanmasukpertunjangan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanmasukpertunjangan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanmasukpergaji', {
                                    url: '/karyawanmasukpergaji',
                                    templateUrl: 'tpl/r_karyawanmasukpergaji/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanmasukpergaji.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanmasukperdata', {
                                    url: '/karyawanmasukperdata',
                                    templateUrl: 'tpl/r_karyawanmasukperdata/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanmasukperdata.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanmasukperpend', {
                                    url: '/karyawanmasukperpend',
                                    templateUrl: 'tpl/r_karyawanmasukperpend/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanmasukperpend.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanmasukperpekerjaan', {
                                    url: '/karyawanmasukperpekerjaan',
                                    templateUrl: 'tpl/r_karyawanmasukperpekerjaan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanmasukperpekerjaan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.kecelakaankerja', {
                                    url: '/kecelakaankerja',
                                    templateUrl: 'tpl/r_kecelakaankerja/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_kecelakaankerja.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.laporanapd', {
                                    url: '/laporanapd',
                                    templateUrl: 'tpl/r_laporanapd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_laporanapd.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.pemasukanapd', {
                                    url: '/pemasukanapd',
                                    templateUrl: 'tpl/r_pemasukanapd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_pemasukanapd.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.jadwalhsetalk', {
                                    url: '/jadwalhsetalk',
                                    templateUrl: 'tpl/r_jadwalhsetalk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_jadwalhsetalk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.jadwalauditsemester', {
                                    url: '/jadwalauditsemester',
                                    templateUrl: 'tpl/r_jadwalauditsemester/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_jadwalauditsemester.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.jadwalpelatihan', {
                                    url: '/jadwalpelatihan',
                                    templateUrl: 'tpl/r_jadwalpelatihan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_jadwalpelatihan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.laporanstokapd', {
                                    url: '/laporanstokapd',
                                    templateUrl: 'tpl/r_laporanstokapd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_laporanstokapd.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.jadwalapenilaian', {
                                    url: '/jadwalapenilaian',
                                    templateUrl: 'tpl/r_jadwalpenilaian/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_jadwalpenilaian.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.pemakianlat', {
                                    url: '/pemakianlat',
                                    templateUrl: 'tpl/r_pemakianlat/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_pemakianlat.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.karyawanspd', {
                                    url: '/karyawanspd',
                                    templateUrl: 'tpl/r_karyawanspd/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_karyawanspd.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.filelegalitas', {
                                    url: '/filelegalitas',
                                    templateUrl: 'tpl/r_filelegalitas/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_filelegalitas.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.atkkeluar', {
                                    url: '/atkkeluar',
                                    templateUrl: 'tpl/r_atkkeluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_atkkeluar.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.atkmasuk', {
                                    url: '/atkmasuk',
                                    templateUrl: 'tpl/r_atkmasuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_atkmasuk.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.agendapelatihan', {
                                    url: '/agendapelatihan',
                                    templateUrl: 'tpl/r_agendapelatihan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_agendapelatihan.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.agendaumum', {
                                    url: '/agendaumum',
                                    templateUrl: 'tpl/r_agendaumum/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_agendaumum.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.siswaprakerin', {
                                    url: '/siswaprakerin',
                                    templateUrl: 'tpl/r_siswaprakerin/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_siswaprakerin.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.rekapmagang', {
                                    url: '/rekapmagang',
                                    templateUrl: 'tpl/r_rekapmagang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_rekapmagang.js');
                                                        }
                                                );
                                            }]
                                    }})
                                .state('rekap.rpenilaiankontrak', {
                                    url: '/rpenilaiankontrak',
                                    templateUrl: 'tpl/r_rpenilaiankontrak/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function () {
                                                            return $ocLazyLoad.load('js/controllers/r_rpenilaiankontrak.js');
                                                        }
                                                );
                                            }]
                                    }})
                        //

                    }
                ]);

