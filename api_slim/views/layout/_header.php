<header class="header-area">
    <div class="top-bar">
        <div class="container">
        	<div class="clearfix">
	<div class="top-bar-text float_left">
                    <marquee style="width:75%; color:white" scrolldelay="100" onmouseover="this.stop();" onmouseout="this.start();">
                    	<?php foreach($randarticles as $r) { ?> 
                        <a href="<?=url("d/".$r->alias)?>" style="color: #fff"><?= $r->title ?></a> |
                    	<?php } ?>
                    </marquee>
		<ul class="float_right" style="margin-right: -60px">
			<li><i class="fa fa-clock-o"></i> {{clock | date:"dd MMM yyyy - HH:mm:ss"}} (GMT+7)</li>
		</ul>
	</div>

        </div>
    </div>

    <div class="header-bottom">
        <div class="container">
            <div class="header-bottom-bg clearfix">
                <div class="main-logo float_left">
		<a href="<?= site_url() ?>">
			<div class="col-md-2" align="center">
				<img src="<?= site_url().'img/logo.png' ?>" alt="<?= $setting->nama ?>" style="height:50px" />
			</div>
			<div class="col-md-10" style="margin-top:6px">
				<h5 style="font-weight: 600">Badan Kepegawaian dan Pengembangan SDM</h5>
				<h5 style="font-weight: 500">Kabupaten Sampang</h5>
			</div>
		</a>
                </div>
                <div class="top-info float_right">
                    <ul>
                        <?php if (!empty($setting->telepon)) { ?>
                            <li class="single-info-box">
                                <div class="icon-holder">
                                    <span class="fa fa-phone"></span>
                                </div>
                                <div class="text-holder">
                                    <p style="font-size:60%"><span>Telepon</span><br><?= $setting->telepon ?></p>
                                </div>
                            </li>
                            <?php } ?>
                        <?php if (!empty($setting->email)) { ?>
                            <li class="single-info-box">
                                <div class="icon-holder">
                                    <span class="fa fa-envelope"></span>
                                </div>
                                <div class="text-holder">
                                    <p style="font-size:60%"><span>Email</span> <br><a href="mailto:<?= $setting->email ?>"><?= $setting->email ?></a></p>
                                </div>
                            </li>
                            <?php } ?>
                        <li class="link_btn">
                            <a href="<?= url('tanya-jawab') ?>" class="thm-btn-tr">Tanya Jawab</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>  
</header>

<!-- Menu ******************************* -->
<section class="theme_menu stricky">
    <div class="container">
        <div class="menu-bg">
            <div class="row">
                <div class="col-md-10 menu-column">
                    <nav class="menuzord" id="main_menu">
                        <ul class="menuzord-menu">
                            <li class="home"><a href="<?= site_url() ?>"><span class="fa fa-home"></span></a></li>

                            <li><a href="#">Profil</a>
                                <ul class="dropdown">
                                    <li><a href="<?= url('d/' . "profil-bkd-kabupaten-sampang") ?>">Tentang Kami</a></li>
                                    <li><a href="<?= url('d/' . "visi-dan-misi") ?>">Visi &amp; Misi</a></li>
                                    <li><a href="<?= url('d/' . "struktur-organisasi") ?>">Struktur Organisasi</a></li>
                                    <li><a href="<?= url('d/' . "sambutan-kepala-bkd") ?>">Kepala BKD</a></li>
                                </ul>
                            </li>

                            <li><a href="<?= site_url() ?>persyaratan.html">Persyaratan</a>
                            <ul class="dropdown">
                            	<li><a href="#">Pembuatan Kartu</a>
                            		<ul class="dropdown">
                            		<li><a href="<?=url("d/persyaratan-pembuatan-kartu-taspen")?>">TASPEN</a></li>
                            		<li><a href="<?=url("d/persyaratan-pembuatan-kartu-pegawai")?>">Kartu Pegawai</a></li>
                            		<li><a href="<?=url("d/persyaratan-pembuatan-kartu-istrisuami")?>">Kartu Istri/Suami</a></li>
                            		</ul>
                            	</li>
                            	<li><a href="<?=url("d/persyaratan-pensiun")?>">Pensiun</a></li>
                            	<li><a href="<?=url("d/persyaratan-cuti")?>">Cuti</a></li>
                            	<li><a href="<?=url("d/persyaratan-izin-belajar")?>">Izin belajar</a></li>
                            </ul>
                            </li>

                            <li><a href="#">Tupoksi</a>
                                <ul class="dropdown">
                                    <li><a href="<?= url("d/". "layanan-kepala-badan") ?>">Kepala Badan</a></li>
                                    <li>
                                        <a href="<?= url("d/". "kesekretariatan") ?>">Sekretariat</a>
                                        <ul class="dropdown">
                                            <li><a href="<?= url("d/bagian-umum") ?>">Bagian Umum</a></li>
                                            <li><a href="<?= url("d/bagian-keuangan-dan-program") ?>">Bagian Keuangan dan Program</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                    	<a href="<?=url("d/bidang-informasi-dan-pembinaan-aparatur")?>">Informasi dan Pembinaan Aparatur</a>
                                    	<ul class="dropdown">
                                    		<li><a href="<?=url("d/bidang-formasi-dan-pengadaan")?>">Formasi dan Pengadaan</a></li>
                                    		<li><a href="<?=url("d/bidang-pengolahan-data-dan-sistem-informasi")?>">Pengolahan Data dan Sistem Informasi</a></li>
                                    		<li><a href="<?=url("d/bidang-pembinaan-aparatur")?>">Pembinaan Aparatur</a></li>
                                    	</ul>
                                    </li>

                                    <li><a href="<?= url("d/bidang-mutasi") ?>">Bidang Mutasi</a>
                                        <ul class="dropdown">
                                            <li><a href="<?= url("d/bidang-jabatan") ?>">Bidang Jabatan</a></li>
                                            <li><a href="<?= url("d/bidang-pindah-dan-pangkat") ?>">Bidang Pindah dan Pangkat</a></li>
                                            <li><a href="<?= url("d/bidang-kesejahteraan-aparatur") ?>">Bidang Kesejahteraan Aparatur</a></li>
                                        </ul>    
                                    </li>
                                    <li><a href="<?= url("d/bidang-pendidikan-pelatihan-dan-pengembangan-karier") ?>">Bidang Pendidikan, Pelatihan dan Pengembangan Karier</a>
                                        <ul class="dropdown">
                                            <li><a href="<?= url("d/bidang-pendidikan-dan-pelatihan-struktural") ?>">Bidang Pendidikan dan Pelatihan Struktural</a></li>
                                            <li><a href="<?= url("d/bidang-pendidikan-dan-pelatihan-teknis-dan-fungsional") ?>">Bidang Pendidikan dan Pelatihan Teknis dan Fungsional</a></li>
                                            <li><a href="<?= url("d/bidang-pengembangan-karier")?>">Bidang Pengembangan Karier</a></li>
                                        </ul>    
                                    </li>
                                    <li>
                                    	<a href="<?= url("d/upt-badan") ?>">UPT Badan</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="<?= url("berita") ?>">berita</a></li>
                            <li><a href="<?= url('download') ?>">download</a></li>
                            <li><a href="<?= url('grafik') ?>">grafik</a></li>
                            <li><a href="<?= url('contact-us') ?>">kontak</a></li>
                            <li><a href="<?= url('registrasi') ?>">registrasi</a></li>
                        </ul><!-- End of .menuzord-menu -->
                    </nav> <!-- End of #main_menu -->
                </div>
                <div class="right-column">
                    <div class="right-area">
                        <div class="nav_side_content">
                            <div class="search_option">
                                <button class="search tran3s dropdown-toggle color1_bg" id="searchDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-search" aria-hidden="true"></i></button>
                                <form action="<?= site_url().'search' ?>" class="dropdown-menu" aria-labelledby="searchDropdown">
                                    <input type="text" placeholder="Cari..." name="q" />
                                    <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>      

    </div> <!-- End of .conatiner -->
</section> <!-- End of .theme_menu -->
<!--Start rev slider wrapper-->     