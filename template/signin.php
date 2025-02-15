<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo strip_tags(getConfigValue("app_name")); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php if(file_exists($scriptpath . "/assets/icon.png")) { ?>
            <link rel="shortcut icon" href="assets/icon.png"/>
            <link rel="apple-touch-icon" href="assets/icon-large.png"/>
            <link rel="image_src" href="assets/icon-large.png"/>
        <?php } else { ?>
            <link rel="shortcut icon" href="template/assets/icon.png"/>
            <link rel="apple-touch-icon" href="template/assets/icon-large.png"/>
            <link rel="image_src" href="template/assets/icon-large.png"/>
        <?php } ?>

        <!-- New template CSS -->
        <link href="new-template/maxton/vertical-menu/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="new-template/maxton/vertical-menu/assets/plugins/fontawesome/css/all.min.css" rel="stylesheet" type="text/css" />
        <link href="new-template/maxton/vertical-menu/assets/css/app.min.css" rel="stylesheet" type="text/css" />
        
        <style>
            .auth-page {
                background: #f3f3f9;
                display: flex;
                min-height: 100vh;
                align-items: center;
                justify-content: center;
            }
            .auth-card {
                max-width: 450px;
                width: 100%;
                margin: 0 auto;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                padding: 2rem;
            }
            .auth-logo {
                text-align: center;
                margin-bottom: 2rem;
            }
            .auth-logo img {
                max-height: 50px;
                margin-bottom: 1rem;
            }
            .auth-logo h3 {
                font-size: 1.75rem;
                margin: 0;
                color: #1a1a1a;
            }
            .auth-form .form-floating {
                margin-bottom: 1.25rem;
            }
            .auth-form .form-control {
                padding: 0.75rem 1rem;
                height: auto;
                border-radius: 6px;
                border: 1px solid #e2e8f0;
            }
            .auth-form .form-floating label {
                padding: 0.75rem 1rem;
            }
            .auth-form .btn {
                padding: 0.75rem 1.5rem;
                font-weight: 500;
            }
            .auth-footer {
                text-align: center;
                margin-top: 2rem;
            }
            .auth-footer a {
                color: #4f46e5;
                text-decoration: none;
            }
            .auth-footer a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body class="auth-page">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="template/assets/logo.svg" alt="<?php echo htmlspecialchars(getConfigValue("app_name")); ?>" class="img-fluid" style="max-height: 60px; margin-bottom: 1.5rem;">
            </div>

            <div class="auth-form">
                <h5 class="text-center mb-4"><?php _e('Sign in to your account'); ?></h5>

                <?php if(!empty($statusmessage)): ?>
                    <div class="alert alert-<?php print $statusmessage["type"]; ?> alert-dismissible fade show" role="alert">
                        <?php print $statusmessage["message"]; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="?route=signin" method="post">
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required autofocus>
                        <label for="emailInput"><i class="fas fa-envelope me-2"></i><?php _e('Email address'); ?></label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Enter password" required>
                        <label for="passwordInput"><i class="fas fa-lock me-2"></i><?php _e('Password'); ?></label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i><?php _e('Sign In'); ?>
                        </button>
                    </div>

                    <input type="hidden" name="signin"/>
                </form>
            </div>

            <div class="auth-footer">
                <p class="mb-0">
                    <a href="?route=forgot">
                        <i class="fas fa-key me-1"></i><?php _e('Forgot your password?'); ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Scripts -->
        <script src="new-template/maxton/vertical-menu/assets/js/jquery.min.js"></script>
        <script src="new-template/maxton/vertical-menu/assets/js/bootstrap.bundle.min.js"></script>
        <script src="new-template/maxton/vertical-menu/assets/js/app.js"></script>
    </body>
</html>
