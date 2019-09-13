<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Drive Road Side | Registration Page</title>
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
        <script src="https://script.tapfiliate.com/tapfiliate.js" type="text/javascript" async></script>
        <script type="text/javascript">
            (function(t,a,p){t.TapfiliateObject=a;t[a]=t[a]||function(){
            (t[a].q=t[a].q||[]).push(arguments)}})(window,'tap');

            tap('create', '11066-c05a86', { integration: "stripe" });
            tap('detect');
        </script>
    </head>
    <style>
        .error{
            color:blue;
        }
        .login-box-msg{
            font-size: 16px;
            color: #2481bc;
            font-weight: 600;
        }
        .register-box{
            margin: 2% auto;
        }
        .register-logo{
            margin-left: -28px;
        }
        .register-logo a{
            margin-left: -17px;
        }
        .flex-class{
            display: flex;
        }
        .flex-class .form-group:first-child{
            display: flex;
        }
        .flex-class .form-group:nth-child(2){
            display: flex;
            align-items: center;
        }
        .form-group input{
            margin-right: 8px;
        }
        .form-group label{
            margin-right: 7px;
            margin-bottom: 0;
        }
        .login-box-msg{
            padding: 4px 15px;
        }
        .login-box-msg1{
            padding: 0px 10px 20px 10px;
        }
        .btn-flat.register{
            margin-top: 10px;
        }
        .form-group span{
            display: block;
        }
        .error{
            font-weight: 400;
        }
        .cus_flex{
            display: flex;
            flex-direction: column;
        }
        .flex-class .form-control.error{
            border-color: blue; 
        }
        .has-feedback label~.form-control-feedback{
            top: 0;
        }
        .res_col,
        .res_col button{
            width: 100%;
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
<!--                <a href="#"><img src="https://i.ibb.co/ZY5GT5d/Mobile-Roadside-Logo.png" alt="Mobile-Roadside-Logo"></a>-->
                <a href="http://driveroadside.com?ref=<?=$ref?>&plan=<?=$plan?>"><b>Road</b> Side</a>
            </div>

            <div class="register-box-body">
                <p class="login-box-msg login-box-msg1">Register For a New <?= ucwords($plan) ?> Membership</p>

                <form id="form" action="<?= asset('register_membership') ?>" method ="Post">
                    <?php include resource_path('views/admin/include/messages.php'); ?>
                    <?= csrf_field() ?>
                    <div class="form-group has-feedback">
                        <input type="text" name="name" class="form-control" placeholder="Full name">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" id='password' class="form-control" placeholder="Password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="c_password" class="form-control" placeholder="Retype password">
                        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="text" name="contact_number" class="form-control" placeholder="Contact">
                        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                    </div>
                    <p class="login-box-msg">Payment Info</p>
                    <div class="form-group">
                        <span style="color:red" class="payment-errors"></span>
                        <label>Card Number</label>
                        <input name="cardnumber" class="form-control general-field card-field" size="16" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 16)
                                    return false;" data-stripe="number" type="number" placeholder="4242 4242 4242 4242">
                        <input type="hidden" name="plan" value="<?=$plan?>" id="choose_plan">
                    </div>
                    <div class="form-group">
                        <label>Card Holder</label>
                        <input type="text" class="form-control general-field " name="cardholdername" placeholder="Card holder name">
                    </div>
                    <div class="cart-holder">
                        <label>Exp.Date</label>
                        <div class="flex-class">
                            <div class="form-group ">
                                <div class="cus_flex">
                                    <input size="2" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 2)
                                            return false;" data-stripe="exp-month" name="month" type="number" class="form-control" placeholder="Month" />
                                </div>
                                <input size="4" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 4)
                                            return false;" data-stripe="exp-year" type="number" name="year" class="form-control" placeholder="Year" />
                            </div>
                            <div class="form-group ">
                                <label>CVC</label>
                                <input  size="3" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 3)
                                            return false;" data-stripe="cvc"  type="number" name="cvc" class="form-control general-field" placeholder="CVC">
                            </div>
                        </div>
                    </div>
                    <!--                                <div class="credit-cart-save">
                                                        <button class="btn btn_grey">Save</button>
                                                    </div>-->
                    <div class="row">
                        <div class="col-xs-8">
                            <!--          <div class="checkbox icheck">
                                        <label>
                                          <input type="checkbox"> I agree to the <a href="#">terms</a>
                                        </label>
                                      </div>-->
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4 res_col">
                            <button type="submit" class="btn btn-primary btn-block btn-flat register">Register</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <!--    <div class="social-auth-links text-center">
                      <p>- OR -</p>
                      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
                        Facebook</a>
                      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
                        Google+</a>
                    </div>-->

                <!--    <a href="login.html" class="text-center">I already have a membership</a>-->
            </div>
            <!-- /.form-box -->
        </div>
        <!-- /.register-box -->
    
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <!-- jQuery 3 -->
        <script src="<?= asset('public/bower_components/jquery/dist/jquery.min.js') ?>"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?= asset('public/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
        <!-- iCheck -->
        <!--<script src="../../plugins/iCheck/icheck.min.js"></script>-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
        <script>
            Stripe.setPublishableKey('<?= env('STRIPE_KEY') ?>');
            jQuery(function () {

                $('#form').submit(function (event) {
                    event.preventDefault();     
                    var $form = $(this);

                    $this = $(this);

                    //Form validation
                    $form.validate({
                        rules: {
                            name: {
                                required: true,
                            },
                            email: {
                                required: true,
                                email: true
                            },
                            password: {
                                required: true,
                                minlength: 6
                            },
                            c_password: {
                                required: true,
                                minlength: 6,
                                equalTo: "#password"

                            }, contact_number: {
                                required: true,
                            }, cardnumber: {
                                required: true,
                            }, cardholdername: {
                                required: true,
                            }, cvc: {
                                required: true,
                            }, month: {
                                required: true,
                            }, year: {
                                required: true,
                            }
                        }
                        , messages: {
                            name: {
                                required: "Enter Full name",
                            },
                            password: {
                                required: "",
                                minlength: "Your password must be at least 6 characters long"
                            },
                            email: {
                                required: "Enter email",
                                email: ""
                            },
                            c_password: {
                                required: "",
                                equalTo: "Please enter the same password as above"
                            },
                            contact_number: {
                                required: "Enter Contact Number",
                            }, cvc: {
                                required: "",
                            }, month: {
                                required: "",
                                range: [1, 12]
                            }, year: {
                                required: "",
                            }

                        }, submitHandler: function (form) {
                                


                        }

                    });

                    // Disable the submit button to prevent repeated clicks

                    $form.find('button').prop('disabled', true);


                    Stripe.card.createToken($form, stripeResponseHandler);


                    // Prevent the form from submitting with the default action
//                                                    return false;
                });
            });
            function stripeResponseHandler(status, response) {
                var $form = $('#form');

                if (response.error) {
                    // Show the errors on the form
                    $form.find('.payment-errors').text(response.error.message);
                    $form.find('button').prop('disabled', false);

                } else {
                    // response contains id and card, which contains additional card details
                    //                                                    
//                                                    var token = response.id;
                    // Insert the token into the form so it gets submitted to the server
//                                                    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
//                                                    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
//                                                  
                    $form.get(0).submit();
                }
            }
                                            
</script>
     
    
    </body>
</html>
