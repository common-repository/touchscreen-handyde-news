<?php
/*
Plugin Name: touchscreen-handy.de News
Plugin URI: http://wordpress.org/extend/plugins/touchscreen-handyde-news/
Description: Adds a customizeable widget which displays the latest news by http://www.touchscreen-handy.de/
Version: 0.1
Author: Frank Kugler
Author URI: http://www.touchscreen-handy.de/
License: GPL3
*/

function handynews()
{
  $options = get_option("widget_handynews");
  if (!is_array($options)){
    $options = array(
      'title' => 'touchscreen-handy.de News',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://www.touchscreen-handy.de/feed/'); 
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

function widget_handynews($args)
{
  extract($args);
  
  $options = get_option("widget_handynews");
  if (!is_array($options)){
    $options = array(
      'title' => 'touchscreen-handy.de News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  handynews();
  echo $after_widget;
}

function handynews_control()
{
  $options = get_option("widget_handynews");
  if (!is_array($options)){
    $options = array(
      'title' => 'touchscreen-handy.de News',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['handynews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['handynews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['handynews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['handynews-CharCount']);
    update_option("widget_handynews", $options);
  }
?> 
  <p>
    <label for="handynews-WidgetTitle">Widget Title: </label>
    <input type="text" id="handynews-WidgetTitle" name="handynews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="handynews-NewsCount">Max. News: </label>
    <input type="text" id="handynews-NewsCount" name="handynews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="handynews-CharCount">Max. Characters: </label>
    <input type="text" id="handynews-CharCount" name="handynews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="handynews-Submit"  name="handynews-Submit" value="1" />
  </p>
  
<?php
}

function handynews_init()
{
  register_sidebar_widget(__('touchscreen-handy.de News'), 'widget_handynews');    
  register_widget_control('touchscreen-handy.de News', 'handynews_control', 300, 200);
}
add_action("plugins_loaded", "handynews_init");
?>
