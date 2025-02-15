<?php

$debug = true;

if($debug == false) {
    error_reporting(0);
    ini_set('display_errors', '0');
}

if($debug == true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Check if accessed directly without POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Validate required POST data
$required_fields = ['dbname', 'dbserver', 'dbuser', 'password', 'email', 'name', 'app_url'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    $ok = false;
    $error_message = "Missing required fields: " . implode(", ", $missing_fields);
} else {
    try {
        // Check if database exists or can be created
        $test_conn = @mysqli_connect(
            $_POST['dbserver'],
            $_POST['dbuser'],
            $_POST['dbpassword']
        );

        if (!$test_conn) {
            throw new Exception("Could not connect to MySQL server: " . mysqli_connect_error());
        }

        // Try to create database if it doesn't exist
        if (!mysqli_select_db($test_conn, $_POST['dbname'])) {
            $create_db = mysqli_query($test_conn, "CREATE DATABASE IF NOT EXISTS " . mysqli_real_escape_string($test_conn, $_POST['dbname']));
            if (!$create_db) {
                throw new Exception("Could not create database: " . mysqli_error($test_conn));
            }
        }
        mysqli_close($test_conn);

        function randomString($chars=10) { 
            $characters = '0123456789abcdef';
            $randstring = '';
            for ($i = 0; $i < $chars; $i++) { 
                $randstring .= $characters[rand(0, strlen($characters) -1)];
            }
            return $randstring;
        }

        $encryption_key = randomString(64);

        require('../vendor/classes/class.medoo.php');
        $database = new medoo([
            "database_type"=>"mysql",
            "database_name"=> $_POST['dbname'],
            "server"=> $_POST['dbserver'],
            "username"=> $_POST['dbuser'],
            "password"=> isset($_POST['dbpassword']) ? $_POST['dbpassword'] : '',
            "charset"=>"utf8",
            "port"=>3306
        ]);

        $sql = file_get_contents('sql/db.sql');
        $database->query($sql);

        sleep(2); 

        $password = sha1($_POST['password']);
        $email = strtolower($_POST['email']);
        $name = $_POST['name'];

        $database = new medoo([
            "database_type"=>"mysql",
            "database_name"=> $_POST['dbname'],
            "server"=> $_POST['dbserver'],
            "username"=> $_POST['dbuser'],
            "password"=> isset($_POST['dbpassword']) ? $_POST['dbpassword'] : '',
            "charset"=>"utf8",
            "port"=>3306
        ]);

        $database->insert("core_users", [
            "roleid" => "1",
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "groups" => 'a:1:{i:0;s:1:"0";}',
            "theme" => "skin-green",
            "sidebar" => "opened",
            "layout" => "",
            "notes" => "",
            "sessionid" => "",
            "resetkey" => "",
            "lang" => "en",
            "autorefresh" => 0,
        ]);

        $database->insert("app_contacts", [
            "groupid" => 1,
            "status" => 1,
            "name" => $name,
            "email" => $email,
            "mobilenumber" => "",
            "pushbullet" => "",
            "twitter" => "",
            "pushover" => "",
        ]);

        $database->update("core_config", ["value" => rtrim($_POST['app_url'], '/') . '/'], ["name" => "app_url"]);

        $data = '<?php $config = array(
        "database_type"=>"mysql",
        "database_name"=>"'.$_POST['dbname'].'",
        "server"=>"'.$_POST['dbserver'].'",
        "username"=>"'.$_POST['dbuser'].'",
        "password"=>"'.(isset($_POST['dbpassword']) ? $_POST['dbpassword'] : '').'",
        "charset"=>"utf8",
        "port"=>3306,
        "encryption_key"=>"'.$encryption_key.'" ); ?>';
        
        $config_file = "../config.php";
        if (is_writable(dirname($config_file))) {
            $file = fopen($config_file, "w+");
            if ($file) {
                fwrite($file, $data);
                fclose($file);
                $ok = true;
            } else {
                throw new Exception("Could not open config file for writing");
            }
        } else {
            throw new Exception("Config directory is not writable");
        }
    } catch (Exception $e) {
        $ok = false;
        $error_message = "Installation Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>EyeNet Installer</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="shortcut icon" href="../template/assets/icon.png"/>
        <link rel="apple-touch-icon image_src" href="../template/assets/icon-large.png"/>
        <link href="../template/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
		<link href="../template/assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <b>Eye</b>Net Installer
      </div><!-- /.login-logo -->
      <div class="login-box-body">

          <?php if($ok == true): ?>
                  <div class="row"><div class='col-md-12'><div class="alert alert-success" role="alert">Installation Succesfull!</div></div></div>
                        <p class="login-box-msg">Please delete the "install" folder before signing in.</p>
                        <p>
                            <b>Admin Email </b><?php echo $_POST['email']; ?><br>
                            <b>Admin Password </b><?php echo $_POST['password']; ?><br>
                        </p>
                        <p class="login-box-msg">Click <a href="../">here</a> to login.</p>
          <?php endif; ?>

          <?php if($ok == false): ?>
                  <div class="row"><div class='col-md-12'><div class="alert alert-danger" role="alert">Installation Error!</div></div></div>
                        <p class="login-box-msg"><?php echo $error_message; ?></p>
                        <div class="row">
                          <div class="col-xs-6"><button onclick="window.history.back()" class="btn btn-default btn-block btn-flat">Back</button></div><!-- /.col -->
                          <div class="col-xs-6"></div><!-- /.col -->
                        </div>
          <?php endif; ?>


      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->



    <!-- jQuery 2.1.3 -->
    <script src="../template/assets/plugins/jQuery/jQuery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="../template/assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

  </body>


</html>
