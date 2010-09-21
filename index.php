<?php include('twitter-caching.php'); ?>
<!DOCTYPE html> 
<html> 
<head> 
  <title>Twitter PHP Caching Script Demo Page</title> 
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" /> 
  <style type="text/css"> 
    html {
      background: #f1f1f1;
    }
    body {
      color: #333;
      font: 87.5%/1.5 Calibri, sans-serif; /* 14px */
      width: 640px;
      margin: 50px auto;
    }
    a, a:link, a:visited { color: #333; }
    a:hover, a:focus, a:active { color: #666; }
  </style> 
</head> 
<body> 

<?php
  $usernames = "vosechu spaceninja";
  $limit = "10"; // Number of tweets to pull in
  parse_cache_feed( $usernames, $limit );
?>

</body> 
</html>