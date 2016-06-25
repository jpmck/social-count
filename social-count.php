<?php

class SocialCount
{
  // PUBLIC VARIABLES -----------------------------------------------------------------------------

  public $url;
  public $counts;

  // PUBLIC FUNCTIONS -----------------------------------------------------------------------------

  // CONSTRUCTOR FUNCTION
  //
  public function __construct($url_in)
  {
    if (filter_var($new_url, FILTER_VALIDATE_URL))
    {
      $url_in = $this->$url;
      updateCounts();
    }
    else
    {
      echo 'The string given as a URL (' . $new_url . ') for this object is not a valid URL.';
    }
  }

  // UPDATE COUNTS FUNCTION
  // queries each social media site and updates each portion of the array
  public function updateCounts()
  {
    $counts['facebook'] = getFacebookLikes($url);
    $counts['google_plus'] = getGooglePlusOnes($url);
    $counts['linkedin'] = getLinkedInShares($url);
    $counts['stumble_upon'] = getStumbles($url);
    $counts['tweets'] = getTweets($url);
  }

  // UPDATE URL FUNCTION
  // updates the array that is entered and updates the counts
  public function updateUrl($new_url)
  {
    if (filter_var($new_url, FILTER_VALIDATE_URL))
    {
      $new_url = $this->$url;
      updateCounts();
    }
    else
    {
      echo 'The string given for a new URL (' . $new_url . ') is not a valid URL.';
    }
  }

  // PRIVATE FUNCTIONS ----------------------------------------------------------------------------

  // GET FACEBOOK LIKES FUNCTION
  // returns number of Facebook likes for provided URL
  private function getFacebookLikes()
  {
    $json_string = file_get_contents('http://graph.facebook.com/?ids=' . $this->$url);
    $json = json_decode($json_string, true);
    return intval($json[$this->$url]['shares']);
  }

  // GET GOOGLE PLUS ONES FUNCTION
  // returns number of Google+ +1s for provided URL
  private function getGooglePlusOnes()
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $this->$url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    $curl_results = curl_exec ($curl);
    curl_close ($curl);
    $json = json_decode($curl_results, true);
    return intval( $json[0]['result']['metadata']['globalCounts']['count'] );
  }

  // GET LINKEDIN SHARES FUNCTION
  // returns number of LinkedIn shares for provided URL
  private function getLinkedInShares()
  {
    $json_string = file_get_contents('http://www.linkedin.com/countserv/count/share?url=' . $this->$url . '&format=json');
    $json = json_decode($json_string, true);
    return intval( $json['count'] );
  }

  // GET STUMBLES FUNCTION
  // returns number of StumbleUpon Stumbles for provided URL
  private function getStumbles()
  {
    $json_string = file_get_contents('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $this->$url);
    $json = json_decode($json_string, true);
    return intval($json['result']['views']);
  }
  // GET TWEETS FUNCTION
  // returns number of Twitter tweets for provided URL
  private function getTweets()
  {
    $json_string = file_get_contents('https://api.twitter.com/1.1/search/tweets.json?q=' . $this->$url);
    $json = json_decode($json_string, true);
    return intval( $json['count'] );
  }
}
?>
