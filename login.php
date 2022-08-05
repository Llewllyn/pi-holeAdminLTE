<?php
/*
*    Pi-hole: A black hole for Internet advertisements
*    (c) 2017 Pi-hole, LLC (https://pi-hole.net)
*    Network-wide ad blocking via your own hardware.
*
*    This file is copyright under the latest version of the EUPL.
*    Please see LICENSE file for your rights under this license.
*/

require "scripts/pi-hole/php/password.php";

// Go directly to index, if authenticated.
if ($_SESSION["auth"]) {
    header("Location: index.php");
    exit;
}

$setupVars = parse_ini_file("/etc/pihole/setupVars.conf");

require "scripts/pi-hole/php/theme.php";

// Retrieve layout setting from setupVars
if (isset($setupVars['WEBUIBOXEDLAYOUT']) && !($setupVars['WEBUIBOXEDLAYOUT'] === "boxed")) {
    $boxedlayout = false;
} else {
    $boxedlayout = true;
}

// Override layout setting if layout is changed via Settings page
if (isset($_POST["field"])) {
    if ($_POST["field"] === "webUI" && isset($_POST["boxedlayout"])) {
        $boxedlayout = true;
    } elseif ($_POST["field"] === "webUI" && !isset($_POST["boxedlayout"])) {
        $boxedlayout = false;
    }
}

// Create cache busting version
$cacheVer = filemtime(__FILE__);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pi-hole login</title>

<?php if ($theme == "default-light") { ?>
    <meta name="theme-color" content="#367fa9">
<?php } elseif ($theme == "default-dark") { ?>
    <meta name="theme-color" content="#272c30">
<?php } elseif ($theme == "default-darker") { ?>
    <meta name="theme-color" content="#2e6786">
<?php } elseif ($theme == "lcars") { ?>
    <meta name="theme-color" content="#4488FF">
    <link rel="stylesheet" href="style/vendor/fonts/ubuntu-mono/ubuntu-mono.css?v=<?=$cacheVer?>">
    <link rel="stylesheet" href="style/vendor/fonts/antonio/antonio.css?v=<?=$cacheVer?>">
<?php } ?>
<?php if ($darkmode) { ?>
    <style>
        html { background-color: #000; }
    </style>
<?php } ?>

    <link rel="stylesheet" href="style/vendor/SourceSansPro/SourceSansPro.css?v=<?=$cacheVer?>">
    <link rel="stylesheet" href="style/vendor/bootstrap/css/bootstrap.min.css?v=<?=$cacheVer?>">
    <link rel="stylesheet" href="style/vendor/icheck-bootstrap.min.css?v=<?=$cacheVer?>">
    <link rel="stylesheet" href="style/vendor/AdminLTE.min.css?v=<?=$cacheVer?>">

    <link rel="stylesheet" href="style/pi-hole.css">
    <link rel="stylesheet" href="style/themes/<?php echo $theme; ?>.css">

    <script src="scripts/vendor/jquery.min.js?v=<?=$cacheVer?>"></script>
    <script src="style/vendor/bootstrap/js/bootstrap.min.js?v=<?=$cacheVer?>"></script>
    <script src="scripts/vendor/adminlte.min.js?v=<?=$cacheVer?>"></script>
    <script src="scripts/vendor/bootstrap-notify.min.js?v=<?=$cacheVer?>"></script>
    <script src="style/vendor/font-awesome/js/all.min.js?v=<?=$cacheVer?>"></script>
    <script src="scripts/pi-hole/js/utils.js?v=<?=$cacheVer?>"></script>
    <script src="scripts/pi-hole/js/footer.js?v=<?=$cacheVer?>"></script>

    <link rel='shortcut icon' href='/admin/img/favicons/favicon.ico' type='image/x-icon'>
</head>
<body class="hold-transition layout-boxed login-page">
<div class="box login-box">
    <section style="padding: 15px;">
        <div class="login-logo">
            <div class="text-center">
                <img src="img/logo.svg" alt="Pi-hole logo" class="loginpage-logo">
            </div>
            <div class="panel-title text-center"><span class="logo-lg" style="font-size: 25px;">Pi-<b>hole</b></span></div>
        </div>
        <!-- /.login-logo -->

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <div id="cookieInfo" class="panel-title text-center text-red" style="font-size: 150%" hidden>Verify that cookies are allowed for <code><?php echo $_SERVER['HTTP_HOST']; ?></code></div>
                <?php if ($wrongpassword) { ?>
                <div class="form-group has-error login-box-msg">
                    <label class="control-label"><i class="fa fa-times-circle"></i> Wrong password!</label>
                </div>
                <?php } ?>

                <form action="" id="loginform" method="post">
                    <div class="form-group login-options has-feedback<?php if ($wrongpassword) { ?> has-error<?php } ?>">
                        <div class="pwd-field">
                            <input type="password" id="loginpw" name="pw" class="form-control" placeholder="Password" autocomplete="current-password" autofocus>
                            <span class="fa fa-key form-control-feedback"></span>
                        </div>
                        <div>
                            <input type="checkbox" id="logincookie" name="persistentlogin">
                            <label for="logincookie">Remember me for 7 days</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;&nbsp;Log in</button>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-<?php if (!$wrongpassword) { ?>info collapsed-box<?php } else { ?>danger<?php }?>">
                            <div class="box-header with-border pointer no-user-select" data-widget="collapse">
                                <h3 class="box-title">Forgot password?</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool">
                                        <i class="fa <?php if ($wrongpassword) { ?>fa-minus<?php } else { ?>fa-plus<?php } ?>"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <p>After installing Pi-hole for the first time, a password is generated and displayed
                                    to the user. The password cannot be retrieved later on, but it is possible to set
                                    a new password (or explicitly disable the password by setting an empty password)
                                    using the command
                                </p>
                                <pre>sudo pihole -a -p</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.login-card-body -->
            <div class="login-footer" style="margin-top: 15px; display: flex; justify-content: space-between;">
                <a class="btn btn-default btn-sm" role="button" href="https://docs.pi-hole.net/" target="_blank"><i class="fas fa-question-circle"></i> Documentation</a>
                <a class="btn btn-default btn-sm" role="button" href="https://github.com/pi-hole/" target="_blank"><i class="fab fa-github"></i> Github</a>
                <a class="btn btn-default btn-sm" role="button" href="https://discourse.pi-hole.net/" target="_blank"><i class="fab fa-discourse"></i> Pi-hole Discourse</a>
            </div>
        </div>
    </section>
</div>

<div class="login-donate">
    <div class="text-center" style="font-size:125%">
        <strong><a href="https://pi-hole.net/donate/" rel="noopener" target="_blank"><i class="fa fa-heart text-red"></i> Donate</a></strong> if you found this useful.
    </div>
</div>
</body>
</html>
