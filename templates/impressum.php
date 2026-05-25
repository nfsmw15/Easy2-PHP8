<!-- Page Content -->
<div class="container">

	<!-- Page Heading/Breadcrumbs -->
	<h1 class="mt-4 mb-3">Impressum</h1>

	<div class="bg-body-tertiary rounded-2 px-3 py-2 mb-3">
<ol class="breadcrumb mb-0">
		<li class="breadcrumb-item"><a href="?">Home</a></li>
		<li class="breadcrumb-item active">Weiteres</li>
		<li class="breadcrumb-item active">Impressum</li>
	</ol>
</div>

	<div class="row">
		<div class="col-sm-6">
			<u>Angaben gem&auml;&szlig; §5 TMG:</u><br>
			<?php echo nl2br($loginsystem->getMainData('impressum_info')); ?><br>
			
			EASY 2.0 Loginsystem - Copyright by <a href="http://marlight-systems.de" target="_blank">Marlight Systems</a>
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
