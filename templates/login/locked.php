	<!-- begin #page-container -->
    <div class="container">

            <!-- end brand -->
      	<!-- Marketing Icons Section -->
      	<div class="row mt30px">
        	<div class="col-sm-4 col-sm-offset-4">
                <form action="./?c=unlock" method="POST">
                    <div class="form-group text-center text-white">
						<img src="<?php echo $loginsystem->getUserAvatar(); ?>" class="round_pic">
                        <p class="locked-name"><?php echo $loginsystem->getUser('fullname'); ?></p>
                    </div>
                	<?php echo $error; ?>
                    <div class="form-group">
                   		<input type="password" class="form-control input-lg input-special" name="locked-passwd" placeholder="Passwort" required />
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block">Entsperren</button>
                    </div>
                    <div class="mt5px">
						<a href="./?c=logout&csrf=<?php echo $loginsystem->getData('csrfToken'); ?>" class="text-success">Nicht <?php echo $loginsystem->getUser('fullname'); ?>?</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- end login -->
	</div>
	<!-- end page container -->

