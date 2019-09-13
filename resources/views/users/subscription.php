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
                    <?php if (empty($subscription->getSubscription)) { ?>
                        <a href="<?= asset('add_subscription_view'); ?>" class="btn btn-success "><b>Add subscription</b></a>
                    <?php } ?>
                    <?php if (isset($subscription->getSubscription)) { ?>         
                        <div class="row">
                            <div class='col-md-4 col-sm-12'></div>

                            <div class="col-md-4 col-sm-12">

                                <!-- Profile Image -->
                                <div class="box box-primary">
                                    <div class="box-body box-profile">
                                        <img class="profile-user-img img-responsive img-circle" src="<?= asset('public/images/admin/profile_pic/demo.png') ?>">


                                        <?php if ($subscription->getSubscription->stripe_plan == 'plan_FEsyKIceDTMp7m') { ?>
                                            <h3 class="profile-username text-center">50 Miles Per Event</h3>
                                        <?php } elseif ($subscription->getSubscription->stripe_plan == 'plan_FEsxkqVubAiUZz') { ?>
                                            <h3 class="profile-username text-center">10 Miles Per Event</h3>
                                            <?php } elseif ($subscription->getSubscription->stripe_plan == 'plan_FEsvVtxkrrP14v') { ?>
                                            <h3 class="profile-username text-center">10 Miles Per Event</h3>
                                        <?php }
                                        ?>


                                        <ul class="list-group list-group-unbordered">

                                            <li class="list-group-item">
                                                <b>Plan</b> <a class="pull-right"> <?php
                                    if ($subscription->getSubscription->stripe_plan == 'plan_FEsyKIceDTMp7m') {
                                        echo "1 Year +";
                                    } elseif ($subscription->getSubscription->stripe_plan == 'plan_FEsxkqVubAiUZz') {
                                        echo "1 Year";
                                    } elseif ($subscription->getSubscription->stripe_plan == 'plan_FEsvVtxkrrP14v') {
                                        echo '6 Months';
                                    }
                                        ?></a>
                                            </li>
                                            <?php
                                            $payments = $subscription->getPaymnet;
                                            $total = 0;
                                            foreach ($payments as $payment) {
                                                if($payment->charge_id == $subscription->getSubscription->stripe_id ){
                                                    $total = $payment->amount;
                                                }
                                            }
                                            ?>
                                            <li class="list-group-item">
                                                <b>Subscriptions Amount Spend</b> <a class="pull-right">$ <?= $total / 100 ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Subscription End</b> <a class="pull-right"><?=$subscription->getSubscription->ends_at; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Road Side Event Available</b> <a class="pull-right"><?= $subscription->getSubscription->counter; ?></a>
                                            </li>
<!--                                            <li class="list-group-item">
                                                <b>Miles Covered</b> <a class="pull-right"><?php // ($subscription->getSubscription->miles_covered) ? $subscription->getSubscription->miles_covered : 0; ?> </a>
                                            </li>-->
                                            <li class="list-group-item">
                                                <b>Miles Per Event</b> <a class="pull-right"><?= (isset($subscription->getSubscription->total_miles)) ? $subscription->getSubscription->total_miles : 0; ?> </a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Status</b> <a class="pull-right"><?=($subscription->getSubscription->status == '1')? 'Active' : 'InActive' ?></a>
                                            </li>

                                        </ul>

                                        <a href="<?= asset('cancel_subscription'); ?>" class="btn btn-danger btn-block"><b>Cancel</b></a>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                        </div>
                    <?php } ?>
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





<?php include'includes/footer.php'; ?>

<script>
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