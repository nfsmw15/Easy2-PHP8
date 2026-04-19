<!-- Page Content -->
<div class="container">

	<!-- Page Heading/Breadcrumbs -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				<i class="fa fa-info-circle"></i> Impressum
			</h1>
			<ol class="breadcrumb">
				<li>
					<a href="?">Home</a>
				</li>
				<li class="active">Impressum</li>
			</ol>
		</div>
	</div>
	<!-- /.row -->

	<div class="row">
		<div class="col-sm-6">
			<u>Angaben gem&auml;&szlig; §5 TMG:</u><br>
			<?php echo nl2br($loginsystem->getMainData('impressum_info')); ?><br>
			<br>
			EASY 2.0 Loginsystem - Copyright by <a href="http://marlight-systems.de" target="_blank">Marlight Systems</a><br>
			PHP8 fork - Copyright (C) by Andreas P. <a href="https://nfsmw15.de" target="_blank">nfsmw15</a><br>
		</div>
		<div class="col-sm-12 mt-4">
			<hr>
			<h2 class="page-title">Haftungsausschluss</h2>
			<hr>
			<p><?php echo $loginsystem->getImpressum(); ?></p>
		</div>
	</div>

</div>
<!-- /.container -->
