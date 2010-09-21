<?php

require_once('relative-time.inc');

/*
	Parse Twitter Feeds
	based on code from http://spookyismy.name/old-entries/2009/1/25/latest-twitter-update-with-phprss-part-three.html
	and cache code from http://snipplr.com/view/8156/twitter-cache/
	and other cache code from http://wiki.kientran.com/doku.php?id=projects:twitterbadge
*/
function parse_cache_feed($usernames, $limit, $display_handle = FALSE) {
  // TODO: Allow people to specify the cache directory path
  // Generate an array based on a comma or space seperated list
  $users = explode(" ", $usernames);
  
  // Assemble the GET request parameters
	$username_for_feed = implode('+OR+from%3A', $users);
	$feed = "http://search.twitter.com/search.atom?q=from%3A" . $username_for_feed . "&rpp=" . $limit;
	
	// Build the file path for the file cache
	$usernames_for_file = str_replace(" ", "-", $usernames);
	$cache_dir = dirname(__FILE__).'/cache/';
	$cache_file = $cache_dir . $usernames_for_file . '-twitter-cache';
	
	// Check for directory existence and directory permissions
	if (!file_exists($cache_dir)) {
	  if(!mkdir($cache_dir)){
	    trigger_error("Cache director cannot be created.", E_USER_WARNING);
	  }
	}
	if (!is_writable($cache_dir)) {
	  trigger_error("Cache directory cannot be written to.", E_USER_WARNING);
	}
	
	// Find out the last time the cache file was written to
	$last = null;
	if (file_exists($cache_file)) {
	  $last = filemtime($cache_file);
	}
	
	$now = time();
	$interval = 600; // ten minutes
	// check the cache file
	if ( !$last || (( $now - $last ) > $interval) ) {
		// cache file doesn't exist, or is old, so refresh it
		$cache_rss = file_get_contents($feed);
		if (!$cache_rss) {
			// we didn't get anything back from twitter
			echo "<!-- ERROR: Twitter feed was blank! Using cache file. -->";
		} else {
			// we got good results from twitter
			echo "<!-- SUCCESS: Twitter feed used to update cache file -->";
			
			// Open the file, serialize the code, then write it out to disk.
			$cache_static = fopen($cache_file, 'wb');
			fwrite($cache_static, serialize($cache_rss));
			fclose($cache_static);
		}
	}
	// TODO: Find a way to fix errors without @
	// read from the cache file even if we got new results from the feed
	$rss = unserialize(file_get_contents($cache_file));
	$xml = new SimpleXMLElement($rss);

	if (count($xml->entry) > 0) { // are there any tweets?
	  foreach ($xml->entry as $tweet) {
      // User url
      $user_uri = $tweet->author->uri;
      
      // User handle
      $matches = array();
      preg_match('/^[^ ]+/', $tweet->author->name, $matches);
      $user_handle = $matches[0];
      
      // User display name
      $matches = array();
      preg_match('/\((.*)\)/', $tweet->author->name, $matches);
      $user_display_name = $matches[1];
      
      // Time a tweet was published. ie: 2 days ago, 1 minute ago
      $time = relativeTime(strtotime($tweet->published));
      
      // Try to be smart about whether to use the handle or the display name
      // Defaults to handle.
      $name = null;
      if ($display_handle) {
        $name = "@" . $user_handle;
      }
      else {
        $name = !empty($user_display_name) ? $user_display_name : "@" . $user_handle;
      }
			?>
				<blockquote>
					<p class="tweet">
						<?php echo $tweet->content; ?>
						<br /><small>
						  <a href="<?php echo $user_uri ?>"><?php echo $name; ?></a>
							<?php echo $time; ?>
						</small>
					</p>
				</blockquote>
			<?php
	  }
	} else { // if there aren't any tweets
		?>
			<blockquote>
				<p class="tweet">
					I have been terribly busy recently shoveling pixels and clearing out the tubes that make up the Internet, so I haven't had a chance to tweet recently. I am truly very sorry about this, so with just a bit more prodding I'll update as soon as possible.
				</p>
			</blockquote>
		<?php
	}
}
?>