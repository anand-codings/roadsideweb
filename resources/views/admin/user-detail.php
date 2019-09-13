<?php include'include/header.php'; ?>
<?php include'include/sidebar.php'; ?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            User Detail
            <small>Road Side</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">All Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body user_detail_row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Name: </label> <?= $user[0]->name ?>
                            </div>
                            <div class="col-md-6">
                                <label>Phone: </label> <?= $user[0]->contact_number ?>
                            </div>
                            <div class="col-md-6">
                                <label>Email: </label> <?= $user[0]->email ?>
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                                $status = '<span class="status-active btn btn-success">Active</span>';
                            }else{
                                $status = '<span class="status-inactive ">Inactive</span>';
                            } ?>
                            <div class="col-md-6">
                                <label>Current Membership Status: </label> <?= $status ?>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Stripe Customer ID: </label> <?= ($user[0]->stripe_id) ? $user[0]->stripe_id : 'None' ?>
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                                if($user[0]->getSubscription->stripe_plan == 'plan_FEsyKIceDTMp7m' ){
                                    $plan_name = "50MilesYear";
                                }elseif($user[0]->getSubscription->stripe_plan == 'plan_FEsxkqVubAiUZz' ){
                                    $plan_name = "10MilesYear";
                                }elseif($user[0]->getSubscription->stripe_plan == 'plan_FEsvVtxkrrP14v'){
                                    $plan_name = '6Months';
                                }
                            }else{
                                $plan_name = 'None';
                            } ?>
                            <div class="col-md-6">
                                <label>Plan Name: </label> <?= $plan_name; ?>
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                                $miles_covered = ($user[0]->getSubscription->miles_covered) ? $user[0]->getSubscription->miles_covered : '0.00';
                            }else{
                                $miles_covered= '0.00';
                            } ?>
                            <div class="col-md-6">
                                <label>Mile Cover: </label> <?= $miles_covered ?> KM
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                               $stripe_plan_id = $user['0']->getSubscription->stripe_id;
                            }else{
                                $stripe_plan_id = 'None';
                            } ?>
                            <div class="col-md-6">
                                <label>Stripe Plan Subscription ID: </label> <?= $stripe_plan_id ?>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                               $sub_end_at = $user['0']->getSubscription->ends_at;
                               $sub_end_at = date("d-m-Y", strtotime($sub_end_at));
                            }else{
                                $sub_end_at = 'None';
                            } ?>
                            <div class="col-md-6">
                                <label>Subscription End's Date: </label> <?= $sub_end_at ?>
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                              $payments = $user[0]->getPaymnet;
                                $total =0;
                                      foreach ($payments as $payment) {
                                        $payment  = $payment->amount;      
                                        $total += $payment;                         

                                      }
                                      $total = $total/100;
                            }else{
                                $total = 'None';
                            } ?>
                            <div class="col-md-6">
                                <label>Total Charge Amount: </label> <?= $total ?>
                            </div>
                            <?php if($user[0]->getSubscription && $user[0]->getSubscription->status == 1){
                               $counter = $user['0']->getSubscription->counter;
                            }else{
                                $counter = '0';
                            } ?>
                            <div class="col-md-6">
                                <label>Number of Renewals: </label> <?= $counter ?>
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

<?php include'include/footer.php'; ?>
