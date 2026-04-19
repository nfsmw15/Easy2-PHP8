	<!-- begin #page-container -->
    <div class="container row-fullheight clearfix">

            <!-- end brand -->
      	<!-- Marketing Icons Section -->
      	<div class="row justify-content-center align-items-center mt-5 row-100">
        	<div class="col-lg-4 mb-5">
                <form action="?c=unlock" method="POST">
                    <div class="form-group text-center text-white">
						<img src="<?php echo $loginsystem->getUserAvatar(); ?>" class="round_pic">
                        <p class="mt-1"><?php echo $loginsystem->getUser('fullname'); ?></p>
                    </div>
                	<?php echo $error; ?>
                    <div class="form-group">
                   		<input type="password" class="form-control input-lg input-special" name="locked-passwd" placeholder="Passwort" required />
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block">Entsperren</button>
                    </div>
                    <div class="mt-1">
						<a href="?c=logout&csrf=<?php echo $loginsystem->getData('csrfToken'); ?>" class="text-success">Nicht <?php echo $loginsystem->getUser('fullname'); ?>?</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- end login -->
	</div>
	<!-- end page container -->

