<?php include'include/header.php'; ?>
<?php include'include/sidebar.php'; ?>
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
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr#</th>
                  <th>Name</th>
                  <th>Membership Number</th>
                  <th>Membership Type</th>
                  <th>Plan</th>
                  <th>Amount</th>
                  <th>Subscription End</th>
                  <th>Miles Covered</th>
                  <th>Status</th>
                  <th>Cancel Subscription</th>
                </tr>
                
                </thead>
                <tbody>
                    <?php if(isset($subscriptions)){
  
                        $count = 0;
                        foreach($subscriptions as $subscription){
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
                  <td><?= $subscription->getUser->name; ?></td>
                  <td><?= $subscription->stripe_id;?> </td>
                  <td><?= $subscription->stripe_plan;?> </td>
                  <td>
                      <?php if($subscription->stripe_plan == 'plan_FEsyKIceDTMp7m' ){
                                echo "50MilesYear";
                      }elseif($subscription->stripe_plan == 'plan_FEsxkqVubAiUZz' ){
                                echo "10MilesYear";
                      }elseif($subscription->stripe_plan == 'plan_FEsvVtxkrrP14v'){
                          echo '6Months';
                      }
                        ?>
                  </td>
                  <?php $payments = $subscription->getUser->getPaymnet;
                  $total =0;
                        foreach ($payments as $payment) {
                          $payment  = $payment->amount;      
                          $total += $payment;                         
                                  
                        }
                                       
                        
                        ?>
                  
                  <td><?= $total/100;?> </td>
                   <td><?= date("d-m-Y", strtotime($subscription->ends_at));?></td>
                    <td><?= ($subscription->miles_covered) ? $subscription->miles_covered : '0.0' ;?> </td>
                    <td>
                        <?php if ($subscription->status == 1){ ?> 
                        <span class="status-active ">Active</span>
                       <?php } else { ?> 
                             <span class="status-inactive ">Inactive</span>
                        <?php }?>
                    </td>
                    <td>
                        <?php if ($subscription->status == 1){ ?> 
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm cancel_now"  onclick='cancelSub(<?=$subscription->getUser->id?>)'>Cancel Now</a>
                       <?php } else { }?>
                    </td>
                    
                </tr>
                        <?php }
                        } ?>
                
                </tbody>
                <tfoot>
                <tr>
                  <th>Sr#</th>
                  <th>Name</th>
                  <th>Membership Number</th>
                  <th>Membership Type</th>
                  <th>Plan</th>
                  <th>Amount</th>
                  <th>Subscription End</th>
                  <th>Miles Covered</th>
                  <th>Status</th>
                  <th>Cancel Subscription</th>
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





<?php include'include/footer.php'; ?>

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