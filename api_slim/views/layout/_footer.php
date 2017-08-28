<footer class="main-footer sec-padd-top" style="background-image: url(<?= site_url() ?>img/bg_footer.jpg)"><!--color: #1c2437;-->
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="about-widget">
		<a href="<?= site_url() ?>">
		<div class="row">
		<div class="col-md-3">
		<figure class="footer-logo"><img src="<?= site_url() ?>img/system/logo.png" alt="<?= $setting->nama ?>"></figure>
		</div>
			<div class="col-md-9 text-box">
			<h3 style="color: #fff">BKPSDM</h3>
			<h4 style="color: #04a018">Kabupaten Sampang</h4>
			</div>
		</a>
		</div>

                    <div class="row">
                        <p class="text" style="color:white">Melaksanakan urusan Pemerintahan Daerah dalam penyusunan dan pelaksanaan kebijakan daerah bidang kepegawaian.</p>
                    </div>

                    <ul class="contact-infos">
                        <li>
                            <div class="icon_box">
                                <i class="fa fa-home"></i>
                            </div><!-- /.icon-box -->
                            <div class="text-box">
                                <h5>Alamat</h5>
                                <p style="color:white"><?= $setting->alamat ?></p>
                            </div><!-- /.text-box -->
                        </li>
                        <li>
                            <div class="icon_box">
                                <i class="fa fa-phone"></i>
                            </div><!-- /.icon-box -->
                            <div class="text-box">
                                <h5>Telepon</h5>
                                <p style="color:white"><?= $setting->telepon ?></p>
                            </div><!-- /.text-box -->
                        </li>
                        <li>
                            <div class="icon_box">
                                <i class="fa fa-envelope"></i>
                            </div><!-- /.icon-box -->
                            <div class="text-box">
                                <h5>E-mail</h5>
                                <p style="color:white"><a href="mailto:<?= $setting->email ?>"><?= $setting->email ?></a></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
	<div class="latest-post">
	<div class="section-title">
		<h3>BERITA TERPOPULER</h3>
		</div>
		<?php $i=site_url()."img/articles-70p.jpg"; foreach ($populer as $pop) { $a = url("d/".$pop->alias); ?>
		<div class="post">
		<figure class="post-thumb">
                    <?= get_first_image($pop->content, 'small') ?>
			<a href="<?= $a ?>" class="overlay-link"><span class="fa fa-link"></span></a></figure>
			<h5><a href="<?= $a ?>"><?= $pop->title ?></a></h5>
			<div class="link">
				<a href="<?= $a ?>" class="default_link">Selengkapnya <i class="fa fa-angle-right"></i></a>
			</div>
		</div>
		<?php } ?>
	</div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
	     <div class="footer-link-widget">
                    <div class="section-title">
                        <h3>Link Sistem Informasi</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-sx-6">
                            <ul class="list">
                                <li><a href="http://e-bkd.sampangkab.go.id/eformasi" target="_blank">EFORMASI</a></li>
                                <li><a href="http://e-bkd.sampangkab.go.id/index.php" target="_blank">SIMPEG</a></li>
                                <li><a href="http://e-bkd.sampangkab.go.id/skp" target="_blank">SKP</a></li>
                                <li><a href="http://e-bkd.sampangkab.go.id/pengajuan" target="_blank">Pengajuan</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="opening-hour">
                        <h3>Jam Kerja</h3>
                        <p style="color:white">Senin - Jumat: 07.00 - 16.00 WIB<br/>Sabtu &amp; Minggu: Libur</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



</footer>

<div class="footer-bottom">
    <div class="container">
        <div class="float_left copy-text">
            © Hak cipta 2017. <a href="<?= site_url() ?>"><?= $setting->nama; ?></a>. Hak cipta dilindungi.
        </div>
    </div>
</div>
