<?php
/*
	Relative Time Function
	based on code from http://stackoverflow.com/questions/11/how-do-i-calculate-relative-time/501415#501415
	For use in the "Parse Twitter Feeds" code below
*/
define("SECOND", 1);
define("MINUTE", 60 * SECOND);
define("HOUR", 60 * MINUTE);
define("DAY", 24 * HOUR);
define("MONTH", 30 * DAY);
function relativeTime($time)
{
	$delta = strtotime('+2 hours') - $time;
	if ($delta < 2 * MINUTE) {
		return "1 min ago";
	}
	if ($delta < 45 * MINUTE) {
		return floor($delta / MINUTE) . " min ago";
	}
	if ($delta < 90 * MINUTE) {
		return "1 hour ago";
	}
	if ($delta < 24 * HOUR) {
		return floor($delta / HOUR) . " hours ago";
	}
	if ($delta < 48 * HOUR) {
		return "yesterday";
	}
	if ($delta < 30 * DAY) {
		return floor($delta / DAY) . " days ago";
	}
	if ($delta < 12 * MONTH) {
		$months = floor($delta / DAY / 30);
		return $months <= 1 ? "1 month ago" : $months . " months ago";
	} else {
		$years = floor($delta / DAY / 365);
		return $years <= 1 ? "1 year ago" : $years . " years ago";
	}
}

/*
	Parse Twitter Feeds
	based on code from http://spookyismy.name/old-entries/2009/1/25/latest-twitter-update-with-phprss-part-three.html
	and cache code from http://snipplr.com/view/8156/twitter-cache/
	and other cache code from http://wiki.kientran.com/doku.php?id=projects:twitterbadge
*/
function parse_cache_feed($usernames, $limit) {
  // Generate an array based on a comma or space seperated list
  $users = explode(" ", explode(", ", $usernames));
  
  // Assemble the GET request parameters
	$username_for_feed = implode('+OR+from%3A', $users);
	$feed = "http://search.twitter.com/search.atom?q=from%3A" . $username_for_feed . "&rpp=" . $limit;
	
	// Build the file path for the file cache
	$usernames_for_file = str_replace(" ", "-", $usernames);
	$cache_file = dirname(__FILE__).'/cache/' . $usernames_for_file . '-twitter-cache';
	
	// TODO: Check for directory existence and directory permissions
	
	$last = filemtime($cache_file);
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

	 // // clean up and output the twitter feed
	 // $feed = str_replace("&amp;", "&", $rss);
	 // $feed = str_replace("&lt;", "<", $feed);
	 // $feed = str_replace("&gt;", ">", $feed);
	 // $clean = explode("<entry>", $feed);
	 // $clean = str_replace("&quot;", "'", $clean);
	 // $clean = str_replace("&apos;", "'", $clean);
	$tweets = htmlentities($rss);
	
	if (count($tweets) > 0) { // are there any tweets?
	  foreach ($tweets as $tweet) {
	    $entry_close = explode("</entry>", $clean[$i]);
			$clean_content_1 = explode("<content type=\"html\">", $entry_close[0]);
			$clean_content = explode("</content>", $clean_content_1[1]);
			$clean_name_2 = explode("<name>", $entry_close[0]);
			$clean_name_1 = explode("(", $clean_name_2[1]);
			$clean_name = explode(")</name>", $clean_name_1[1]);
			$clean_user = explode(" (", $clean_name_2[1]);
			$clean_lower_user = strtolower($clean_user[0]);
			$clean_uri_1 = explode("<uri>", $entry_close[0]);
			$clean_uri = explode("</uri>", $clean_uri_1[1]);
			$clean_time_1 = explode("<published>", $entry_close[0]);
			$clean_time = explode("</published>", $clean_time_1[1]);
			$unix_time = strtotime($clean_time[0]);
			$pretty_time = relativeTime($unix_time);
			?>
				<blockquote>
					<p class="tweet">
						<?php echo $clean_content[0]; ?>
						<br /><small>
							<?php echo $pretty_time; ?>
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