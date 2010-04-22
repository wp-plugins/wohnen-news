<?php
/*
Plugin Name: Wohnen News
Plugin URI: http://wordpress.org/extend/plugins/wohnen-news/
Description: Adds a widget which displays the latest information by http://www.wohnen.de/
Version: 1.0
Author: Peter Schmidt
Author URI: http://www.wohnen.de/
License: GPL3
*/

function wohnennews()
{
  $options = get_option("widget_wohnennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Wohnen News',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=wohnen&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_wohnennews($args)
{
  extract($args);
  
  $options = get_option("widget_wohnennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Wohnen News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  wohnennews();
  echo $after_widget;
}

function wohnennews_control()
{
  $options = get_option("widget_wohnennews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Wohnen News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['wohnennews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['wohnennews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['wohnennews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['wohnennews-CharCount']);
    update_option("widget_wohnennews", $options);
  }
?> 
  <p>
    <label for="wohnennews-WidgetTitle">Widget Title: </label>
    <input type="text" id="wohnennews-WidgetTitle" name="wohnennews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="wohnennews-NewsCount">Max. News: </label>
    <input type="text" id="wohnennews-NewsCount" name="wohnennews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="wohnennews-CharCount">Max. Characters: </label>
    <input type="text" id="wohnennews-CharCount" name="wohnennews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="wohnennews-Submit"  name="wohnennews-Submit" value="1" />
  </p>
  
<?php
}

function wohnennews_init()
{
  register_sidebar_widget(__('Wohnen News'), 'widget_wohnennews');    
  register_widget_control('Wohnen News', 'wohnennews_control', 300, 200);
}
add_action("plugins_loaded", "wohnennews_init");
?>