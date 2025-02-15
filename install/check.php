<?php

$debug = false;

if($debug == false) {
    error_reporting(0);
    ini_set('display_errors', '0');
}

if($debug == false) {
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', '1');
}

require('../vendor/classes/class.medoo.php');

try {
    $database = new medoo([
        "database_type"=>"mysql",
        "database_name"=> $_POST['dbname'],
        "server"=> $_POST['dbserver'],
        "username"=> $_POST['dbuser'],
        "password"=> $_POST['dbpassword'],
        "charset"=>"utf8",
        "port"=>3306
    ]);
    $ok = true;
}
catch(Exception $e) {
    $ok = false;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>EyeNet Installer - System Check</title>
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
                <div><b>Eye</b>Net System Check</div>
            </div>

            <div class="login-box-body">
                <div class="progress-indicator">
                    <div class="step active"><i class="fas fa-cog"></i> Settings</div>
                    <div class="step active"><i class="fas fa-check-circle"></i> System Check</div>
                    <div class="step"><i class="fas fa-flag-checkered"></i> Installation</div>
                </div>

                <?php if($ok == true): ?>
                <div class="row"><div class='col-md-12'><div class="alert alert-success alert-auto" role="alert">Succesfully conected to database!</div></div></div>
                <?php endif; ?>

                <?php if($ok == false): ?>
                <div class="row"><div class='col-md-12'><div class="alert alert-danger alert-auto" role="alert">Database Error!</div></div></div>
                <?php endif; ?>

                <div class="system-check-results">
                    <p class="login-box-msg"><i class="fas fa-clipboard-check"></i> System Check Results</p>
                    <div class="check-list">
                        <?php if($ok == true): ?>
                        <div class="check-item success">
                            <i class="fas fa-check"></i>
                            <span>Database connection successful.</span>
                        </div>
                        <?php endif; ?>

                        <?php if($ok == false): ?>
                        <div class="check-item error">
                            <i class="fas fa-times"></i>
                            <span>Failed to connect to database.</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row action-buttons">
                    <div class="col-xs-6">
                        <a href="index.php" class="btn btn-default btn-block"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                    <?php if($ok == true): ?>
                    <div class="col-xs-6">
                        <form method="POST" action="install.php">
                            <?php foreach($_POST as $key => $value): ?>
                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary btn-block">Continue <i class="fas fa-arrow-right"></i></button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script src="../template/assets/plugins/jQuery/jQuery-2.1.3.min.js"></script>
        <script src="../template/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>
