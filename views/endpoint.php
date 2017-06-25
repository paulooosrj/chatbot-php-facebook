<?php 

	unset($_GET["url"]);

	function filter($data) { //Filters data against security risks.
    	if (is_array($data)) {
        	foreach ($data as $key => $element) {
           	 	$data[$key] = filter($element);
        	}
    	} else {
        	$data = trim(strip_tags($data));
        	if(get_magic_quotes_gpc()) $data = stripslashes($data);
        	$data = addslashes($data);
    	}
    	return $data;
	}

	    $_GET["canal"] = filter($_GET["canal"]);
      $_GET["event"] = filter($_GET["event"]);
      $_GET["msg"] = filter($_GET["msg"]);

		  $options = array(
    		'cluster' => 'us2',
    		'encrypted' => true
 	 	  );

  		$pusher = new Pusher($_GET['key'],$_GET['secret'],$_GET['app_id'],$options);

  		$data = array();
  		$data['message'] = $_GET['msg'];
  		$pusher->trigger($_GET['canal'], $_GET['event'], $data);