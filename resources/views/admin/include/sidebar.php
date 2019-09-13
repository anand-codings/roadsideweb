
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
            <?php
                    $img = $admin->profile_pic ;
                    if($img){
                      ?>
                      <div class="user_profile_img" style="background-image:url('<?php echo asset('public/images/'.$admin->profile_pic);?>')"></div>
                      <?php  
                     
                    }else{
                      ?>
                      <div class="user_profile_img" style="background-image:url('<?php echo asset('public/images/admini/profile_pic/demo.png');?>')"></div>
                      <?php
                        // $img = asset('public/images/admini/profile_pic/demo.png');
                    }
                    ?>
          <!-- <img src="<?= $img;?>" class="img-circle" alt="User Image"> -->
        </div>
        <div class="pull-left info">
          <h4>Admin </h4>
          <a href="#"></a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          
          <span class="input-group-btn">
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="<?= ($tab == 'dashboard' ? 'active treeview menu-open' : '') ?>">
            <a href="<?= asset('dashboard') ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
     
        </li>
        <li class="<?= ($tab == 'users' ? 'active treeview menu-open' : '') ?>">
            <a href="<?= asset('users') ?>">
            <i class="fa fa-laptop"></i> <span>Users</span>
          </a>
     
        </li>
        <li class="<?= ($tab == 'subscriptions' ? 'active treeview menu-open' : '') ?>">
            <a href="<?= asset('subscriptions') ?>">
            <i class="fa fa-cc-stripe"></i> <span>Subscriptions</span>
          </a>
     
        </li>
        <li class="<?= ($tab == 'edit_profile' ? 'active ' : '') ?>">
            <a href="<?= asset('edit_admin_profile_view') ?>">
                <i class="fa   fa-edit"></i> <span>Edit Profile</span>
            </a>
        </li>
         <li class="<?= ($tab == 'register_profile' ? 'active ' : '') ?>">
            <a href="<?= asset('register_profile') ?>">
                <i class="fa   fa-edit"></i> <span>Register</span>
            </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
