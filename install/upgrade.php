<?php

$debug = false;

if($debug == false) {
    error_reporting(0);
    ini_set('display_errors', '0');
}

if($debug == true) {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', '1');
}


$latestversion = 1.11;
$status = 'ok';

# LOAD CONFIGURAGION FILE
if(file_exists("../config.php")) {
	require('../config.php');
}
else { $status = 'noconfig'; }


if($status == 'ok') {
    # INITIALIZE MEDOO
    require('../vendor/classes/class.medoo.php');
    $database = new medoo($config);
    $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    // UPGRADE to 1.1
    if($currentversion == 1.0) {

        $sql = file_get_contents('sql/db_1.0-1.1.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.1"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }


    // UPGRADE to 1.2
    if($currentversion == 1.1) {

        $sql = file_get_contents('sql/db_1.1-1.2.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.2"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.3
    if($currentversion == 1.2) {

        $sql = file_get_contents('sql/db_1.2-1.3.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.3"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.4
    if($currentversion == 1.3) {

        $sql = file_get_contents('sql/db_1.3-1.4.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.4"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.5
    if($currentversion == 1.4) {

        $database->update("core_config", ["value" => "1.5"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.6
    if($currentversion == 1.5) {

        $database->update("core_config", ["value" => "1.6"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.7
    if($currentversion == 1.6) {

        $database->update("core_config", ["value" => "1.7"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.8
    if($currentversion == 1.7) {

        $sql = file_get_contents('sql/db_1.7-1.8.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.8"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }


    // UPGRADE to 1.9
    if($currentversion == 1.8) {

        $sql = file_get_contents('sql/db_1.8-1.9.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.9"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }

    // UPGRADE to 1.10
    if($currentversion == 1.9) {

        $sql = file_get_contents('sql/db_1.9-1.10.sql');
        $database->query($sql);
        sleep(1);

        $database->update("core_config", ["value" => "1.10"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }


    // UPGRADE to 1.11
    if($currentversion == 1.10) {


        $database->update("core_config", ["value" => "1.11"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }
    

    // UPGRADE to 1.12
    if($currentversion == 1.11) {


        $database->update("core_config", ["value" => "1.12"], ["name" => "db_version"]);
        $status = 'updated';
        sleep(1);

        $currentversion = $database->get("core_config", "value", [ "name" => "db_version" ]);

    }



}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>EyeNet - Upgrade</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="shortcut icon" href="../template/assets/icon.png"/>
        <link rel="apple-touch-icon image_src" href="../template/assets/icon-large.png"/>
        <link href="../template/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/installer.css" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>

    <body class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <img src="../docs/assets/images/EyeNet-Light-Mode.svg" alt="EyeNet Logo" style="height: 50px; margin-bottom: 1rem;">
                <div><b>Eye</b>Net Upgrade</div>
            </div>

            <div class="login-box-body">
                <div class="upgrade-status">
                    <p class="login-box-msg"><i class="fas fa-arrow-circle-up"></i> Upgrade Status</p>
                    
                    <?php if($status == "updated"): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Upgrade Complete!</strong>
                        <p>Your EyeNet installation has been successfully upgraded to the latest version.</p>
                    </div>

                    <div class="version-info">
                        <div class="version-item">
                            <i class="fas fa-code-branch"></i>
                            <div>
                                <strong>Previous Version:</strong>
                                <span><?php echo $currentversion; ?></span>
                            </div>
                        </div>
                        <div class="version-item">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="version-item">
                            <i class="fas fa-code-branch"></i>
                            <div>
                                <strong>New Version:</strong>
                                <span><?php echo $latestversion; ?></span>
                            </div>
                        </div>
                    </div>

                    <a href="../" class="btn btn-primary btn-block">
                        <i class="fas fa-home"></i> Return to Dashboard
                    </a>
                    <?php endif; ?>

                    <?php if($status == "noconfig"): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Configuration file is missing.</strong>
                        <p>Please ensure the configuration file is present and try again.</p>
                    </div>

                    <div class="action-buttons">
                        <a href="../" class="btn btn-default btn-block">
                            <i class="fas fa-home"></i> Return to Dashboard
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="../template/assets/plugins/jQuery/jQuery-2.1.3.min.js"></script>
        <script src="../template/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>
