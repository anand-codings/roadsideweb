<?php include'includes/header.php'; ?>
<?php include'includes/sidebar.php'; ?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Subscriptions
            <small>Road Side</small>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Subscriptions Details</h3>
                    </div>
                    <!-- /.box-header -->


                    <div class="row">
                        <div class="col-md-4">

                            <!-- Profile Image -->
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <img class="profile-user-img img-responsive img-circle" src="<?= asset('public/images/admin/profile_pic/demo.png') ?>">

                                    <h3 class="profile-username text-center">10 Miles Two Events</h3>



                                    <ul class="list-group list-group-unbordered">

                                        <li class="list-group-item">
                                            <b>Plan</b> <a class="pull-right"> 6 Month Membership</a>
                                        </li>

                                        <li class="list-group-item">
                                            <b>Amount</b> <a class="pull-right">$ 49</a>
                                        </li>
                                          <li class="list-group-item">
                                                <b>Road Side Event Available</b> <a class="pull-right">2</a>
                                            </li>
                                        <li class="list-group-item">
                                            <b>Service</b> <a class="pull-right">Jumpstart</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Service</b> <a class="pull-right">Locksmith</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Service</b> <a class="pull-right">Tire Change</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Service</b> <a class="pull-right">Fuel Delievery</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Service</b> <a class="pull-right">Towing</a>
                                        </li>

                                    </ul>
                                    <?php if (empty($subscription->getSubscription)) {
                                        if (!empty($subscription->stripe_id)) { ?>
                                            <a href="<?= asset('create_subscription/' . '6Months'); ?>" class="btn btn-success btn-block"><b>Join now</b></a>
                                        <?php } else { ?>
                                            <button data-target="#payment" data-toggle="modal" type="button" class="btn btn_md_green first_next " id="third_next" value="6Months" >Join Now</button>
                                            <?php } 
                                            
                                        } else if($subscription->getSubscription->stripe_plan == 'plan_FEsvVtxkrrP14v' && $subscription->getSubscription->counter == '2'){
                                                ?>
                                                    <a href="#" class="btn btn-danger btn-block"><b>Already Subscribed</b></a>
                                                <?php    
                                        } else { ?>
                                            <a href="<?= asset('upgrade_subscription/' . '6Months'); ?>" class="btn btn-primary btn-block"><b>Upgrade now</b></a>
                                    <?php } ?>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <div class="col-md-4">

                                <!-- Profile Image -->
                                <div class="box box-primary">
                                    <div class="box-body box-profile">
                                        <img class="profile-user-img img-responsive img-circle" src="<?= asset('public/images/admin/profile_pic/demo.png') ?>" alt="User profile picture">

                                        <h3 class="profile-username text-center">10 Miles Four Events</h3>



                                        <ul class="list-group list-group-unbordered">

                                            <li class="list-group-item">
                                                <b>Plan</b> <a class="pull-right">1 Year Membership</a>
                                            </li>

                                            <li class="list-group-item">
                                                <b>Amount</b> <a class="pull-right">$ 89</a>
                                            </li>
                                              <li class="list-group-item">
                                                <b>Road Side Event Available</b> <a class="pull-right">4</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Service</b> <a class="pull-right">Jumpstart</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Service</b> <a class="pull-right">Locksmith</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Service</b> <a class="pull-right">Tire Change</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Service</b> <a class="pull-right">Fuel Delievery</a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Service</b> <a class="pull-right">Towing</a>
                                            </li>


                                        </ul>
                                        <?php if (empty($subscription->getSubscription)) {
                                            if (!empty($subscription->stripe_id)) { ?>
                                                <a href="<?= asset('create_subscription/' . '10MilesYear'); ?>" class="btn btn-success btn-block"><b>Join now</b></a>
                                            <?php } else { ?>
                                                <button data-target="#payment" data-toggle="modal" type="button" class="btn btn_md_green second_next " id="third_next" value="10MilesYear" >Join Now</button>
                                                <?php }
                                                
                                            } else if($subscription->getSubscription->stripe_plan == 'plan_FEsxkqVubAiUZz' && $subscription->getSubscription->counter == '4'){
                                                ?>
                                                    <a href="#" class="btn btn-danger btn-block"><b>Already Subscribed</b></a>
                                                <?php    
                                            } else { ?>
                                                <a href="<?= asset('upgrade_subscription/' . '10MilesYear'); ?>" class="btn btn-primary btn-block"><b>Upgrade now</b></a>
                                        <?php } ?>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                
                                    <!-- Profile Image -->
                                    <div class="box box-primary">
                                        <div class="box-body box-profile">
                                            <img class="profile-user-img img-responsive img-circle" src="<?= asset('public/images/admin/profile_pic/demo.png') ?>">

                                            <h3 class="profile-username text-center">10 Miles Six Events</h3>



                                            <ul class="list-group list-group-unbordered">

                                                <li class="list-group-item">
                                                    <b>Plan</b> <a class="pull-right">1 Year Plus Membership </a>
                                                <li class="list-group-item">
                                                    <b>Amount</b> <a class="pull-right">$ 129</a>
                                                </li>
                                                 <li class="list-group-item">
                                                <b>Road Side Event Available</b> <a class="pull-right">6</a>
                                            </li>
                                                <li class="list-group-item">
                                                    <b>Service</b> <a class="pull-right">Jumpstart</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Service</b> <a class="pull-right">Locksmith</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Service</b> <a class="pull-right">Tire Change</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Service</b> <a class="pull-right">Fuel Delievery</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <b>Service</b> <a class="pull-right">Towing</a>
                                                </li>

                                            </ul>
                                            <?php if (empty($subscription->getSubscription)) {
                                                if (!empty($subscription->stripe_id)) { ?>
                                                    <a href="<?= asset('create_subscription/' . '50MilesYear'); ?>" class="btn btn-success btn-block"><b>Join now</b></a>
                                                <?php } else { ?>
                                                    <button data-target="#payment" data-toggle="modal" type="button" class="btn btn_md_green third_next " id="third_next" value="50MilesYear" >Join Now</button>
                                                <?php }
                
                                                } else if($subscription->getSubscription->stripe_plan == 'plan_FEsyKIceDTMp7m' && $subscription->getSubscription->counter == '6'){
                                                ?>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-block" title="Disabled"><b>Already Subscribed</b></a>
                                                <?php    
                                                } else { ?>
                                                    <a href="<?= asset('upgrade_subscription/' . '50MilesYear'); ?>" class="btn btn-primary btn-block"><b>Upgrade now</b></a>
                                            <?php } ?>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>
                                    </div>
                                </div>

                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </section>
                <!-- /.content -->
            </div>
            <div class="modal fade" id="payment">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <div class="modal-title border-bottom">
                                <h6>Payment</h6>
                            </div>
                            <button type="button" class="close-modal" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form id="form" action="<?= asset('new_subscription') ?>" method ="Post">
            <?= csrf_field(); ?>
                                <div class="credit-cart">
                                    <h6>Credit Card</h6>
                                    <div class="form-group">
                                        <span style="color:red" class="payment-errors"></span>
                                        <label>Card Number</label>
                                        <input type="hidden" name="plan" id="plan"/>
                                        <input  class="form-control general-field card-field" size="16" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 16)
                                                           return false;" data-stripe="number" name="number" type="number" placeholder="4242 4242 4242 4242">
                                        <input type="hidden" name="choose_plan" id="choose_plan">
                                    </div>
                                    <div class="form-group">
                                        <label>Card Holder</label>
                                        <input type="text" class="form-control general-field " name="name" placeholder="Card holder name">
                                    </div>
                                    <div class="cart-holder">

                                        <div class="form-group ">
                                            <label>Exp.Date</label>
                                            <input size="2" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 2)
                                                                return false;" data-stripe="exp-month" name="date" type="number" class="form-control" placeholder="Month" />
                                            <input size="4" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 4)
                                                                                                                               return false;" data-stripe="exp-year" name="year" type="number" class="form-control" placeholder="Year" />
                                        </div>
                                        <div class="form-group ">
                                            <label>CVC</label>
                                            <input  size="3" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 3)
                                                                return false;" data-stripe="cvc" name="cvc"  type="number" class="form-control general-field" placeholder="CVC">
                                        </div>
                                    </div>
                                    <div class="credit-cart-save">
                                        <button class="btn btn_grey">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <?php include'includes/footer.php'; ?>
 <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
            <script>
                $(document).ready(function(){
                    $('body').on('click','.first_next',function(){
                        $plan=$(this).val();
                        $('#plan').val($plan);
                    });
                    $('body').on('click','.second_next',function(){
                         $plan=$(this).val();
                         $('#plan').val($plan);
                    });
                    $('body').on('click','.third_next',function(){
                         $plan=$(this).val();
                         $('#plan').val($plan);
                    });
                });
                $(function () {
                    $('#example1').DataTable({
                        'responsive': true
                    })
                    $('#example2').DataTable({
                        'paging': true,
                        'lengthChange': false,
                        'searching': false,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'responsive': true
                    })
                })
                function cancelSub(id)
                {
                    if (confirm('Are you sure you want to cancel this ?')) {
                        console.log(id);
                        $.ajax({
                            url: '<?= asset('cancel_sub'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                "_token": "<?= csrf_token() ?>",
                    "id": id
                },
                success: function (data) {
                    if (data == 1) {
                        location.reload();
                    }

                }

            });
        } else {
            // Do nothing!
        }


    }
</script>
     <script>
           
                                              Stripe.setPublishableKey('<?= env('STRIPE_KEY') ?>');
                                            jQuery(function()  {
                                                
                                                $('#form').submit(function (event) {
                                                    var $form = $(this);

                                                    // Disable the submit button to prevent repeated clicks
                                                    $form.find('button').prop('disabled', true);

                                                    Stripe.card.createToken($form, stripeResponseHandler);

                                                    // Prevent the form from submitting with the default action
                                                    return false;
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
                                                    var token = response.id;
                                                    // Insert the token into the form so it gets submitted to the server
                                                    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                                                    $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                   

                                                    // and submit
                                                    $form.get(0).submit();
                                                }
                                            }
        </script>