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
      background: #fff;
      color: #333;
      font: 87.5%/1.5 Calibri, sans-serif; /* 14px */
      width: 640px;
      margin: 50px auto;
      padding: 20px;
    }
    a, a:link, a:visited { color: #333; }
    a:hover, a:focus, a:active { color: #666; }
  </style> 
</head> 
<body> 

<?php
  require_once('twitter-caching.inc');
  
  // Here's the simple way to get a user's 5 most recent tweets
  parse_cache_feed("spaceninja");
  
  // // Here's the more verbose method, with all the options explained
  // $usernames = "vosechu spaceninja"; // Space delimited list of handles
  // $limit = "10"; // Number of tweets to pull in
  // $display_username = TRUE; // Uses display name instead of the @handle
  // // File cache directory location
  // // Must be writable by your webserver
  // $cache_dir = '/tmp/'; // Absolute path to cache directory. Include trailing slash.
  // parse_cache_feed($usernames, $limit, $display_username, $cache_dir);
?>

</body> 
</html>