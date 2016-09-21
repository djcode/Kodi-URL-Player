<!DOCTYPE html>
<html lang="en">
<head>
<title>Kodi URL Player</title>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<div class="row">
  <h1>Kodi URL Player</h1>
</div>
  <div class="row">
<?php
if (isset($_POST['URL'])) {
  $url = parse_url($_POST['URL']);
  if ($url) {
    $kodi_url = '';
    if (preg_match('/youtu.?be(\.com)?/',$url['host'])) {     // Handle YouTube URLs
      if ($url['host']=='youtu.be') {
        $vid = substr($url['path'],1);
      } else {
        parse_str($url['query'],$q);
        $vid = $q['v'];
      }
      $kodi_url = 'plugin://plugin.video.youtube/?action=play_video&videoid='.$vid;
    } elseif ($url['host']=='vimeo.com') {
        $vid = substr($url['path'],1);
        $kodi_url = 'plugin://plugin.video.vimeo/play/?video_id='.$vid;
    } elseif (preg_match('/(dailymotion\.com|dai\.ly)/',$url['host'])) {
        if ($url['host']=='dai.ly') {
          $vid = substr($url['path'],1);
        } else {
          $path = str_replace('/video','',$url['path']);
          $vid  = substr($path,1,strpos($path,'_')-1);
        }
        $kodi_url = 'plugin://plugin.video.dailymotion/?mode=playVideo&url='.$vid;
    } else {
      $kodi_url = $_POST['URL'];
    }

    $instance = $_POST['instance'];
    //open connection
    $ch = curl_init();
    $data = "{\"jsonrpc\":\"2.0\",\"method\":\"Player.Open\",\"params\":{\"item\":{\"file\":\"$kodi_url\"}}}";
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, "http://$instance:8080/jsonrpc");
    #curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)));

    //execute post
    $result = curl_exec($ch);
    var_dump($result);
  } else {
    echo 'Invalid URL!';
  }
}
?>
</div>
<div class="row">
<form method="post">
  <div class="form-group">
    <label for="instance">Instance</label>
    <select name="instance" class="form-control">
      <option value="htpc-main">htpc-main</option>
    </select>
  </div>
<div class="form-group">
  <label for="URL">URL</label>
  <input name="URL" type="text"  class="form-control" />
</div>
</form>
  </div>
</div>
</body>
</html>
