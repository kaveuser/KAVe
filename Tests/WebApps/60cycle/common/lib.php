<?php

// given a title, timestamp, timezone, and body string, and a boolean isPreview,
// output formatted html entry
function getEntryHTML($title, $timestamp, $timezone, $body, $isPreview)
{
	$encTitle = urlencode($title);
	// CUSTOMIZE: put your own entry title HTML here...
	if (!$isPreview)
	{
		$htmlString = '<h2 class="lonelyPost"><a class="titleLink" href="post.php?post=' . $encTitle . '">' . $title . '</a></h2>';
	}
	else
	{
		$htmlString = '<h2 class="lonelyPost"><a class="titleLink" href="#">' . $title . '</a></h2>';
	}		
	// CUSTOMIZE: format the date as you like
	// format Unix timestamp as Monday, March 2, 2009 - 6:32 pm and cat header to html string
	if (intval($timezone) > 0)
		$tzident = 'Etc/GMT+' . $timezone;
	else
		$tzident = 'Etc/GMT' . $timezone;
	date_default_timezone_set($tzident);
	$htmlString .= '<h4>' . date("l, F j, Y - g:i a", $timestamp) . '</h4>';
	// cat body on to html string
	$htmlString .= '<p>' . $body . '</p>';
	return $htmlString;
}

function isValidUnixTimestamp($input)
{
	// don't think this will ba around past Sat, 20 Nov 2286 17:46:39 GMT do you?
	return preg_match('/^\d{1,10}$/', $input);
}
	
function isValidPostName($input)
{
	return preg_match('/[a-zA-Z\-_\d\+%.]+/', $input);
}

function getEntryComments($title)
{
	$title = addslashes($title);
	$query = "SELECT `timestamp`,`author`,`text` FROM `comments` WHERE `entry_id` ='$title' ORDER BY `timestamp` ASC";
	// query MySQL server
	$result=mysql_query($query) or die("MySQL Query fail: $query");	
	$numRows = mysql_num_rows($result);
	$htmlString = '';
	if ($numRows == 0)
	{
		return "<br/><br/><a name=\"comments\"></a><h4>Comments</h4><br/><h3>No comments yet, be the first!</h3><br/>";
	}
	for ($i = 0; $i < $numRows; $i++)
	{
		$row = mysql_fetch_row($result);
		if ($i == 0) $htmlString .= '<br/><br/><a name="comments"></a><h4>Comments</h4><br/>';
		$htmlString .= "<h3>$row[1]</h3>";
		$htmlString .= '<h5>' . getTimeAgo($row[0]) . '</h5>';
		$htmlString .= "<p>$row[2]</p><br/>";
	}
	return $htmlString;		
}

function getCommentsLine($title)
{
	$title = addslashes($title);
	$query = "SELECT `timestamp` FROM `comments` WHERE entry_id= '$title'";
	// query MySQL server
	$result=mysql_query($query) or die("MySQL Query fail: $query");	
	$numComments = mysql_num_rows($result);
	$encTitle = urlencode($title);
	return '<a href="post.php?post=' . $encTitle . '#comments" >' . $numComments . ' comments</a>';	
}

function getTimeAgo($time)
{
	$secondsDiff = time() - $time;
	if ($secondsDiff < 60)
	{
		return "$secondsDiff seconds ago";
	}
	elseif ($secondsDiff < 3600) 
	{
		$minutesDiff = round($secondsDiff / 60);
		return "About $minutesDiff minutes ago";
	}
	elseif ($secondsDiff < 86400)
	{
		$hoursDiff = round($secondsDiff / 3600);
		return "About $hoursDiff hours ago";
	}
	elseif ($secondsDiff < 604800)
	{
		$daysDiff = round($secondsDiff / 86400);
		return "About $daysDiff days ago";
	}
	elseif ($secondsDiff < 2629743.83)
	{
		$weeksDiff = round($secondsDiff / 604800);
		return "About $weeksDiff weeks ago";
	}
	elseif ($secondsDiff < 31556926)
	{
		$monthsDiff = round($secondsDiff / 2629743.83);
		return "About $monthsDiff months ago";
	}
	else
	{
		$yearsDiff = round($secondsDiff / 31556926);
		return "About $yearsDiff years ago";
	}	
}

function getCommentForm($use_recaptcha, $publickey)
{
	$commentForm = "<h4>Comment on this post</h4>";
	if ($_SESSION['result'] == "fail")
		$commentForm .= "<p>CAPTCHA not entered correctly, please try again</p>";
	$commentForm .=	"<form action=\"\" method=\"post\" id=\"commentForm\"><p>
					Name:  <br /><input type=\"text\" size=\"80\" maxlength=\"255\" name=\"name\" value=\"\" /><br /><br />
          <input name=\"blah\" type=\"text\" class=\"h\" value=\"\" />
					Email (not made public):<br /><input type=\"text\" size=\"80\" maxlength=\"255\" name=\"email\" value=\"\" /><br /><br />
					Comment (only &lt;b&gt;, &lt;i&gt;, &lt;a&gt; allowed): <br /><textarea rows=\"10\" cols=\"80\" name=\"comment\" ></textarea><br /><br /></p>";
	if ($use_recaptcha)	$commentForm .= "<script type=\"text/javascript\" src=\"http://api.recaptcha.net/challenge?k=$publickey\"></script><noscript><p>Javascript is required to comment on this entry</p></noscript>";
	$thisURL = curPageURL();
	$commentForm .=	"<p><br/><input type=\"button\" value=\"Submit Comment\" onclick=\"validateComment(this.form)\" />
					<input type=\"button\" value=\"Reset Form\" onclick=\"clearForm(this.form)\" />
					<input type=\"text\" name=\"referrer\" value=\"$thisURL\" style=\"display: none\" />
					</p></form>
					<script type=\"text/javascript\">
					function validateComment(form) {
						
						if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(form.email.value))
							alert('Please enter a valid email address');
						else if (form.comment.innerHTML.length > 2000)
							alert('Seriously?  More than 2000 characters?  Trim it down!');
						else if (form.comment.value.length == 0)
							alert('Please enter a comment');
						else if (form.name.value.length == 0)
							alert('Please enter your name');
						else {
							form.action = \"submitComment.php\";
							form.submit();
						}
					}
					</script>
					<script type=\"text/javascript\">
					function clearForm(form) {
						form.reset();
						form.comment.innerHTML=\"\";
					}					
					</script>";
	if ($_SESSION['result'] == "fail") {
		$commentForm .= "<script type=\"text/javascript\">
						 var name = \"".addslashes($_SESSION['name'])."\";
						 document.getElementById('commentForm').name.value = name;
						 var email = \"".addslashes($_SESSION['email'])."\";
						 document.getElementById('commentForm').email.value = email;
             var blah = \"".addslashes($_SESSION['blah'])."\";
						 document.getElementById('commentForm').blah.value = blah;
						 var comment = \"".$_SESSION['comment']."\";
						 document.getElementById('commentForm').comment.innerHTML = comment;
						 </script>
						 <noscript>
						 <p>Your previous comment was not filled in because you do not have Javascript turned on.  Here it is: ".$_SESSION['comment']."</p></noscript>";
	}	
	
	return $commentForm;
}

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

?>
