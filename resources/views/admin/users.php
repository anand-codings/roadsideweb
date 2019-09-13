<?php include'include/header.php'; ?>
<?php include'include/sidebar.php'; ?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Users
            <small>Road Side</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Users Details</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Name </th>
                                    <th>Number </th>
                                    <th>Email </th>
                                    <th>Details</th>
                                    <th>Used Services</th>
<!--                                    <th>Charge Amount</th>
                                    <th>Charge Date</th>
                                    <th>Subscription Plan</th>
                                    <th>Subscription End</th>
                                    <th>Mile Covered</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($users)) {
                                    $count = 0;
                                    foreach ($users as $user) {
                                        ?>
                                        <tr>
                                            <td><?= ++$count; ?></td>
                                            <td><?= $user->name; ?> </td>
                                            <td><?= $user->contact_number; ?></td>
                                            <td><?= $user->email; ?></td>
                                            <td> <a href="<?= asset('user_detail/'.$user->id); ?>" class="btn btn-primary btn-sm">View Details</a></td>
                                            <?php /*
                                            <td><?= (get_user_charge($user->id)) ? get_user_charge($user->id)->amount/100 : get_subscription($user->id)['plan']['amount']; ?></td>
                                            <td><?= (get_user_charge($user->id)) ? get_user_charge($user->id)->created_at : date('F d,Y h:i:s', get_subscription($user->id)['current_period_start']); ?></td>
                                            <td><?= (get_subscription($user->id)) ? get_subscription($user->id)['plan']->nickname : ''; ?></td>
                                            <td><?= (get_subscription($user->id)) ? date('F d, Y h:i:s', get_subscription($user->id)->current_period_end) : ''; ?></td>
                                            <td><?php
                                                if (get_subscription($user->id) == false) {
                                                    echo ' ';
                                                } else {
                                                    echo ($user->getSubscription['miles_covered']) ? $user->getSubscription['miles_covered'] : '0.00';
                                                    echo ' km';
                                                }
                                                ?></td>
                                             
                                             */
                                            ?>
                                            <td>
                                                <?php 
                                                $obj = $user->getJob;
//                                                dd($obj);
                                                if($obj->count() > 0){
                                                    ?>
                                                <a href="<?= asset('used_services/'.$user->id); ?>" class="btn bg-aqua btn-sm">View Details</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Name </th>
                                    <th>Number </th>
                                    <th>Email </th>
                                    <th>Details</th>
                                    <th>Used Services</th>
<!--                                    <th>Charge Amount</th>
                                    <th>Charge Date</th>
                                    <th>Subscription Plan</th>
                                    <th>Subscription End</th>
                                    <th>Mile Covered</th>-->
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
            'responsive':true
        });
        $('#example2').DataTable({
            'responsive':true,
            'paging': true,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false
        })
    })
</script>