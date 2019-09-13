<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 offset-sm-3">
                <h1>Change Password</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 offset-sm-3">
                <p class="text-center">Use the form below to change your password.</p>
                <form method="get" id="passwordForm" action="{{ asset('/reset') }}">
                	@csrf
                        
                        <?php //if (Session::has('success')) { ?>
                            @if(session()->has('success'))
                                        <div class="alert alert-success">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                            <?php echo Session::get('success') ?>
                                        </div>
                                    <?php// } ?>
                            @elseif(session()->has('error')) 
                                    <?php // if (Session::has('error')) { ?>
                                        <div class="alert alert-danger">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times</a>
                                            <?php echo Session::get('error') ?>
                                        </div>
                                        @endif
                                    <?php // } ?>
                                    <?php //if ($errors->any()) { ?>
                                        @if( count( $errors ) > 0 )
                                        <div class="alert alert-danger">
                                            <ul>
                                                 @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    <?php //} ?>
                                        @endif  
                    <input type="password" class="input-lg form-control" name="password" id="password1" placeholder="New Password" autocomplete="off">
                    <br>
                    <div class="row">
                    </div>
                    <input type="password" class="input-lg form-control" name="password_confirmation" id="password2" placeholder="Repeat Password" autocomplete="off">
                    <div class="row">
                    </div>
                    <input type="hidden" name="token" id="passtoken" value="">
                    <br>
                    <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" value="Change Password">
                </form>
            </div><!--/col-sm-6-->
        </div><!--/row-->
    </div>
    <script>
    	var url = window.location.href;
		var array = url.split("/");
		var num = array.length;
		$('#passtoken').val(array[num-1]);
    </script>
    
</body>
</html>

