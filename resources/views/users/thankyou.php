<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Drive Road Side | Membership Page</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="<?= asset('public/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="<?= asset('bower_components/font-awesome/css/font-awesome.min.css') ?>">
        <!-- Ionicons -->
        <link rel="stylesheet" href="<?= asset('public/bower_components/Ionicons/css/ionicons.min.css') ?>">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?= asset('public/dist/css/AdminLTE.min.css') ?>">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    
    </head>
    <style>
        .error{
            color:blue;
        }
    </style>
    <?php
    $plan = ''; 
    $ref = '';
    if (isset($_GET['plan']) && !empty($_GET['plan'])) {
        $plan = $_GET['plan'];
    }
    if (isset($_GET['ref']) && !empty($_GET['ref'])) {
        $ref = $_GET['ref'];
    }
    ?>
    <body class="hold-transition register-page">
        <div class="register-box">
            <div class="register-logo">
                <a href="http://driveroadside.com"><b>Road</b> Side</a>
            </div>
            <h1>You have Successfully Subscribed for the Membership</h1>
        </div>
        <!-- /.register-box -->
    
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <!-- jQuery 3 -->
        <script src="<?= asset('public/bower_components/jquery/dist/jquery.min.js') ?>"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?= asset('public/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>

    </body>
</html>
