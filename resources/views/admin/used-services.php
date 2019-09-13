
<?php include'include/header.php'; ?>
<?php include'include/sidebar.php'; ?>
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Used Services
            <small>Road Side</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">All Used Services List</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-12">
                            <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Job ID </th>
                                    <th>Type </th>
                                    <th>Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php
                                if (isset($user)) {
                                    $counter = 1;
                                    foreach ($user[0]->getJob as $job) {
                                        
                                        
                                        $type = $job->type;
                                        if($type == '0'){
                                           $type = 'Jump';
                                        }elseif($type == '1'){
                                            $type = 'Lockout';
                                        }elseif($type == '2'){
                                            $type = 'Tow';
                                        }elseif($type == '3'){
                                            $type = 'Tire';
                                        }elseif($type == '4'){
                                            $type = 'Fuel';
                                        }else{
                                            $type = '';
                                        }
                                        ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><?= $job->job_id ?></td>
                                    <td><?= $type ?></td>
                                    <td><?= $job->status ?></td>
                                </tr>
                                <?php
                                    $counter++;
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Sr#</th>
                                    <th>Job ID </th>
                                    <th>Type </th>
                                    <th>Status </th>
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
