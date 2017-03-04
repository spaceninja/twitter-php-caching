:warning: **Attention:** This code is _very_ old and no longer supported. Use at your own risk!

# How to Get Your Most Recent Twitter Posts Using PHP with Caching

When we started redesigning the Pop Art blog, one of the chief requirements was to integrate everyone’s Twitter feeds into the site. In addition to the Pop Art Twitter feed in the sidebar, we wanted to add individual twitter feeds on the profile pages. The problem is that the javascript code that Twitter provides can only be called once in a single page, or it gets confused.

Since we were switching to WordPress, I checked out a bunch of Twitter plugins, but ultimately found them all to be unreliable or just missing features. In the end, I hacked together one of my own, based heavily on code by Ryan Barr. His PHP script was very nearly perfect, but I ran into three problems.

First, his script echoed out the exact date and time of the tweet, but I wanted the fancy “2 hours ago” style dates. To do that, I used a chunk of code from Stack Overflow. Now, I’m far from an advanced PHP programmer, so I’m sure that this code could be cleaned up and condensed down to something like 12 characters, but I like this because it works, and it’s very easy for a mid-level programmer like myself to understand.

[relativeTime()](https://github.com/spaceninja/twitter-php-caching/blob/master/relative-time.inc)

Secondly, and most important, Ryan’s code didn’t cache the results. That’s obviously bad form, but I didn’t really think about trying to fix it until Apple’s WWDC traffic drove Twitter to a standstill while I was testing our new site. Realizing that our Twitter feeds would die anytime anything big happened on the internet motivated me, and I was able to integrate some caching code from Kien Tran and Snipplr that meshed well with Ryan’s existing code.

When I was done, I had a fully functional script that created a cache file for each twitter feed, and only tried to update it every ten minutes. I was especially pleased to discover that it worked when Twitter again ground to a halt due to the Iran elections and Michael Jackson’s death in the last few weeks.

So here’s the final code that we’re using on the Pop Art blog. It looks pretty overwhelming, but it’s basically broken down into three sections. First, it checks to see if the cache file exists, and whether it needs to be updated. Second, it parses the XML from the cache file to create a series of variables. Finally, it echos out a chunk of HTML for each tweet, or an error message if there aren’t any.

[parse_cache_feed()](https://github.com/spaceninja/twitter-php-caching/blob/master/twitter-caching.inc)

You’ll notice that I’m creating more variables than I actually need, noticeably the ones for the user names. On the Pop Art blog, we’re always showing just one person’s tweets at a time, but the code is built to allow you to pass a list of twitter accounts, and it’ll pull them all in. We used that code on another website, and I just left it in place here in case I wanted to use it at some point in the future. Since the variables already exist, I would just need to add them to the output HTML and they would show up.

Which brings me to the final problem I ran into. Ryan’s script uses the Twitter search API, which is great because it gives you the option of pulling in multiple twitter accounts. The downside of the search API is that it will only return recent tweets (the documentation says 7 days, but it looks like it actually returns two weeks).

I did some digging into the API documentation, and there’s no way around this. Twitter has two APIs, the search API and the REST API. The search API is more full-featured, including search operators and automatic link highlighting, with the downside that it only searches recent tweets. The REST API will return all tweets for a user, but it won’t highlight links, merge multiple twitter feeds, and worst of all, it requires a login.

Given those restrictions, I left the code using the search API, despite the restriction. To make the best of the situation, we wrote a funny error message for users with no tweets, and asked everyone linking their Twitter account to post at least once a week.
