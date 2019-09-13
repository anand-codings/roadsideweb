<?php include'includes/header.php'; ?>
<?php include'includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Services
            <small>Road Side</small>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Services Details</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>Service Name</th>
<!--                                <th>Job</th>-->
                                <th>Plan</th>
                                <th>Covered Miles</th>
                                <th>Total Miles</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Start Date</th>
                            </tr>

                            </thead>
                            <tbody>
                            <?php if(isset($services )){

                                $count = 0;
                                foreach($services as $service){
                                    ?>


                                    <?php /*
                 <tr>
                  <td><?=++$count;?></td>
                  <td><?= subscribed_user($subscription->id);       ?> </td>
                  <td><?=$subscription['plan']->nickname;?></td>
                  <td><?php printf("%.2f",$subscription['plan']->amount/100);?></td>
                  <td><?=date('F d, Y',$subscription->current_period_end);?></td>
                  <td><?= subscribed_mile_coverd($subscription->id) ? subscribed_mile_coverd($subscription->id)->miles_covered :'0.00' ;?></td>
                </tr>
                    */ ?>
                                    <tr>
                                        <td><?=++$count;?></td>
                                        <td><?= $service->name; ?></td>

                                        <td>
                                            <?php if(!empty($services->getSubscription)){
                                            $plan = $service->getSubscription->stripe_plan;
                                            if($plan == 'plan_FEsyKIceDTMp7m' ){
                                                echo "50MilesYear";
                                            }elseif($plan == 'plan_FEsxkqVubAiUZz' ){
                                                echo "10MilesYear";
                                            }elseif($plan == 'plan_FEsvVtxkrrP14v'){
                                                echo '6Months';
                                            }
                                            } ?>
                                        </td>

                                        <td><?= ($service->miles_covered) ? $service->miles_covered : '0.0' ;?></td>
                                        <td><?= ($service->total_miles) ? $service->total_miles : '0.0' ;?></td>
                                        <td>
                                            <?php if ($service->status == 1){ ?>
                                                <span class="status-active ">Completed</span>
                                            <?php } else { ?>
                                                <span class="status-inactive ">On Progress</span>
                                            <?php }?>
                                        </td>
                                        <td><?= $service->amount  ?></td>
                                        <td><?= date("d-m-Y", strtotime($service->created_at));?></td>
                                    </tr>
                                <?php }
                            } ?>

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Sr#</th>
                                <th>Service Name</th>
                                <!--                                <th>Job</th>-->
                                <th>Plan</th>
                                <th>Covered Miles</th>
                                <th>Total Miles</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Start Date</th>
                            </tr>
                            </tfoot>
                        </table>
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





<?php include'includes/footer.php'; ?>

<script>
    $(function () {
        $('#example1').DataTable({
            'responsive'  : true
        })
        $('#example2').DataTable({
            'paging'      : true,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'responsive'  : true
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
                data:{
                    "_token": "<?= csrf_token() ?>",
                    "id":id
                },
                success: function (data) {
                    if(data == 1){
                        location.reload();
                    }

                }

            });
        } else {
            // Do nothing!
        }


    }
</script>
