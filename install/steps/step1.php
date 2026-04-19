        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Step 1 &ndash; Voraussetzungen</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Einleitung</a></li>
                            <li class="breadcrumb-item"><a href="index.php?p=terms_of_use">Nutzungsbedingungen</a></li>
                            <li class="breadcrumb-item active actual">Step 1 - Voraussetzungen</li>
                            <li class="breadcrumb-item active">Step 2 - MySQL-Daten</li>
                            <li class="breadcrumb-item active">Step 3 - Grundeinstellungen</li>
                            <li class="breadcrumb-item active">Step 4 - Accounts</li>
                           <li class="breadcrumb-item active">Fertig</li>
              	        </ol>
                    </div>
                    <div class="col-lg-12">
                        <p>Bitte vergewissere dich das folgende Bedingungen erf&uuml;llt sind:</p>
                        <table class="table table-striped">
                        	<thead>
                            	<tr>
                            		<th>Bedingung</th>
                                	<th>Soll</th>
                                	<th>Ist</th>
                            	</tr>
                            </thead>
                            <tbody>
                            	<tr>
                                	<td colspan="3"><strong>Schreibrechte auf:</strong></td>
                                </tr>
                            	<tr>
                                	<td>avatare/</td>
                                	<td>Ja</td>
                                	<td><?php echo $install->conditions(0); ?></td>
                                </tr>
                            	<tr>
                                	<td>system/</td>
                                	<td>Ja</td>
                                	<td><?php echo $install->conditions(1); ?></td>
                                </tr>
                             	<tr>
                                	<td colspan="3"><strong>Voraussetzungen:</strong></td>
                                </tr>                               
                             	<tr>
                                	<td>PHP 8.0 oder h&ouml;her</td>
                                	<td>&gt;= 8.0</td>
                                	<td><?php echo $install->conditions(3); ?></td>
                                </tr>
                             	<tr>
                                	<td>PHP Funktion "imagettftext" vorhanden</td>
                                	<td>true</td>
                                	<td><?php echo function_exists('imagettftext') ? '<span class="text-success">true</span>' : '<span class="text-danger">false</span>'; ?></td>
                                </tr>
                             	<tr>
                                	<td>PHP Erweiterung "GD" vorhanden</td>
                                	<td>true</td>
                                	<td><?php echo extension_loaded('gd') ? '<span class="text-success">true</span>' : '<span class="text-danger">false</span>'; ?></td>
                                </tr>
                                <tr>
                                    <td>PHP Erweiterung "pdo_mysql" vorhanden</td>
                                    <td>true</td>
                                    <td><?php echo extension_loaded('pdo_mysql') ? '<span class="text-success">true</span>' : '<span class="text-danger">false</span>'; ?></td>
                                </tr>
                             	<tr>
                                	<td>MySQL / MariaDB Server</td>
                                	<td>&gt;= 5.7 / &gt;= 10.3</td>
                                	<td><?php
                                	    global $pdo;
                                	    if ($pdo instanceof \PDO) {
                                	        echo '<span class="text-success">' . htmlspecialchars($pdo->getAttribute(\PDO::ATTR_SERVER_VERSION)) . '</span>';
                                	    } else {
                                	        echo '<span class="text-muted">Wird in Step 2 geprüft</span>';
                                	    }
                                	?></td>
                                </tr>
                           </tbody>
                        </table>
                    	<a class="btn btn-default" href="index.php?p=terms_of_use">zur&uuml;ck</a>
                    	<?php if($install->condition){ ?>
                        	<a class="btn btn-success" href="index.php?p=step2">Weiter</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>