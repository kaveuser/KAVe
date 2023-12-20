<?php
	// print out the entry(ies)
	// conditionally add "Newest", "Newer" links (not if we're already at the latest)
	if ($post == '')
	{
		// this is an index page
		if (! $atLatestPost)
		{
			echo '<p class="textright">';
			echo '<a class="navLink" href="index.php">|&lt;&lt; Newest</a>';
			echo '<a class="navLink" href="index.php?ltsu=' . $ltsu . '">&lt; Newer</a>';
			echo '</p>';
		}
	}

	// print the entries
	echo $entriesHTML;

	// conditionally add "Oldest" and "Older" links (not if we're already at the earliest)
	if ($post == '')
	{
		// this is an index page
		if (! $atEarliestPost)
		{
			echo '<p class="textright">';
			echo '<a class="navLink" href="index.php?etsu=' . $etsu . '">Older &gt;</a>';
			echo '<a class="navLink" href="index.php?ltsu=' . 0 . '">Oldest &gt;&gt;|</a>';
			echo '</p>';
		}
	}
?>
