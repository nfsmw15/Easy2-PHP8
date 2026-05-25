<?php

if (!empty($error)) {
    $error = '
        <div class="row">
          <div class="col-lg-12">
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
                <i class="fa fa-warning"></i> ' . $error . '
            </div>
          </div>
        </div>';
}

if (!empty($success)) {
    $error = '
        <div class="row">
          <div class="col-lg-12">
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
                ' . $success . '
            </div>
          </div>
        </div>';
}
