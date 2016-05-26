<?php

// error out with a failed, closed out html and close file
function cronErrorOut()
{
  $text_out .= '<b>failed!</b></p></body></html>';
  fwrite($output_file, $text_out);
  fclose($output_file);

  echo('Script failed!<br>See: <a href="cron-count.html">output file</a>');
  die;
}

// variables
$fail_flag = FALSE;
$output_file = fopen("cron-count.html", "w") or
               die("Script failed!<br>Failed to open cron-count.html file!");

// start html ouput
$text_out = '<html>
  <head>
    <title>Cron Status</title>
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <style>#updates { float: left; width: 40% } #counts { float: right; width: 60%; } body{ font-family: monospace } th, td { border: 1px solid black; } th, td { padding: 0.5em; }</style>
    <meta http-equiv="refresh" content="300">
  </head>
<body>
  <h1>Social Count Status</h1>
  v2.0 | <a href="cron-count.php">Re-run count job</a>
  <div>
    <div id="updates">
    <h2>Status:</h2>
    <p>Cron job started at ' . date('Y-m-d H:i:s') . '</p>';

// try to load the library
$text_out .= '<p>Loading library... ';
if((include('../lib/lib.php')) == false)
{
  cronErrorOut();
}
else
{
  include_once('../lib/lib.php');
  $text_out .= '<b>done!</b></p>';
}

// try to load the model
$text_out .= '<p>Loading model... ';
if((include('../model/model.php')) == false)
{
  cronErrorOut();
}
else
{
  include_once('../model/model.php');
  $text_out .= '<b>done!</b></p>';
}

$results = getAllTitleIDs();

$text_out .= '<p>Getting all share counts for all blog posts... ';
$table_out = '<div id="counts"><h2>Counts/Database:</h2>
<table>
  <tr>
    <th><i class="fa fa-fw fa-link"></i></th>
    <th><i class="fa fa-fw fa-facebook"></i></th>
    <th><i class="fa fa-fw fa-twitter"></i></th>
    <th><i class="fa fa-fw fa-google-plus"></i></th>
    <th><i class="fa fa-fw fa-linkedin"></i></th>
    <th><i class="fa fa-fw fa-stumbleupon"></i></th>
    <th><i class="fa fa-fw fa-reddit-alien"></i></th>
    <th><i class="fa fa-fw fa-database"></i></th>
  </tr>';

foreach ($results as $blog_post)
{
  $title_id = $blog_post["title_id"];

  $permalink = getBlogPermalink($title_id);

  $facebook_count = (int)getFacebookLikes($permalink);
  // $twitter_count = (int)getTweets($permalink);
  $twitter_count = 0;  // Twitter couning is currently broken
  $google_count= (int)getGooglePlusOnes($permalink);
  $linkedin_count = (int)getLinkedInShares($permalink);
  $stumbleupon_count = (int)getStumbles($permalink);
  $reddit_count = (int)0;

  $success = setSocialCounts($title_id, $facebook_count, $twitter_count, $google_count, $linkedin_count, $reddit_count, $stumbleupon_count);

  if ($success)
  {
    $ok = '<i class="fa fa-fw fa-check"></i>';
  }
  else
  {
    $ok = '<i class="fa fa-fw fa-times"></i>';
    $fail_flag = TRUE;
  }


  $table_out .= '<tr><td><a href="' . $permalink . '">' . $title_id . '</a></td><td align="right">' . $facebook_count . '</td><td align="right">' . $twitter_count . '</td><td align="right">' . $google_count . '</td><td align="right">' . $linkedin_count . '</td><td align="right">' . $stumbleupon_count .'</td><td align="right">' . $reddit_count .'</td><td>' . $ok .  '</td></tr>';
}

$text_out .= '<b>done</b>!</p><p>Adding share counts to database... ';

if($fail_flag)
{
  $text_out .= '<b>failed</b>!</p></div';
}
else
{
  $text_out .= '<b>done</b>!</p><p>Writing to HTML file... <b>done</b>!</p><p><em>Cron job complete!</em></p></div>';
}

$table_out .= '</table></div></div></body></html>';

fwrite($output_file, $text_out);
fwrite($output_file, $table_out);

fclose($output_file);

echo('<h2>Script complete!</h1>See: <a href="cron-count.html">output file</a>');

?>
