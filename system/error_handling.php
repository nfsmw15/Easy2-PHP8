<?php
/********************************************
*
* System:   EASY 2.0 Loginsystem
* Author:   Marius Rasche (aka Marlight)
* File:     error_handling.php
* FVersion: 0.1 (this file)
* SVersion: 0.9.8.1 BETA (complete System)
* Date:     12.12.2017
*
* Created by www.marlight-systems.de
* Copyright by Marlight Systems (www.marlight-systems.de)
* All rights reserved.
*
*********************************************/

/* Error Verarbeitung
 **********************************************/

if(!empty($error)){
	$error = '
        <div class="row">
          <div class="col-lg-12">
            <div class="alert alert-danger alert-dismissable">
            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-warning"></i> '.$error.'
			</div>
          </div>
        </div><!-- /.row -->';
}

if(!empty($success)){
	$error = '
        <div class="row">
          <div class="col-lg-12">
            <div class="alert alert-success alert-dismissable">
            	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				'.$success.'
			</div>
          </div>
        </div><!-- /.row -->';
}

