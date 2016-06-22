<?php

class SocialCount
{
  // variables
  public $url;

  public $facebook_likes;
  public $google_plus_ones;
  public $linkedin_shares;
  public $stumbles;
  public $tweets;

  // returns number of Facebook likes for provided URL
  function getFacebookLikes($url)
  {
    $json_string = file_get_contents('http://graph.facebook.com/?ids=' . $url);
    $json = json_decode($json_string, true);
    $facebook_likes = intval( $json[$url]['shares'] );
  }

  // returns number of Google+ +1s for provided URL
  function getGooglePlusOnes($url)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec ($curl);
    curl_close ($curl);
    $json = json_decode($curl_results, true);
    $google_plus_ones = intval( $json[0]['result']['metadata']['globalCounts']['count'] );
  }

  // returns number of LinkedIn shares for provided URL
  function getLinkedInShares($url)
  {
    $json_string = file_get_contents('http://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json');
    $json = json_decode($json_string, true);
    $linkedin_shares = intval( $json['count'] );
  }

  // returns number of StumbleUpon Stumbles for provided URL
  function getStumbles($url)
  {
    $json_string = file_get_contents('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url);
    $json = json_decode($json_string, true);
    $stumbles = intval($json['result']['views']);
  }
  // returns number of Twitter tweets for provided URL
  function getTweets($url)
  {
    $json_string = file_get_contents('https://api.twitter.com/1.1/search/tweets.json?q=' . $url);
    $json = json_decode($json_string, true);
    $tweets = intval( $json['count'] );
  }
}
?>
