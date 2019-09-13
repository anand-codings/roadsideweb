<!DOCTYPE html>
<html>
<head>
	<title>Location</title>
</head>
<body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.5.1/socket.io.min.js"></script> 
<script>
    var socket = io('<?= env('SOCKETS')?>');
    base_url = "<?php echo asset('/'); ?>";
</script>
<script type="text/javascript">
	socket.emit('location_get', {
        "job_id": '<?php echo $job_id; ?>',
        "user_id": '<?php echo '123'; ?>',
        
    });

    socket.on('location_send', function (data) { 
    	console.log(data); 
    });
</script>
</body>
</html>