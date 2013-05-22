<?php
/* 
Description:  Add popular, recent, popular in category and random search terms widget
Version: 1.5 (1.01228)
Author: Purwedi Kurniawan
*/
/**
* ---------- POPULAR TERMS WIDGET ----------
**/
function widget_popular_terms($args) {
  extract($args);
  $options = get_option("widget_popular_terms");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Popular Search Terms',
	  'limit' => 10,
	  'list' => '1',
		'search' => '0'
      );
  }      
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  echo stt_popular_terms_widget($options);
  echo $after_widget;
}
function popular_terms_control()
{
  $options = get_option("widget_popular_terms");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Popular Search Terms',
	  'limit' => 10,
	  'list' => '1',
			'search' => '0'
      );
  }    
  if ($_POST['popularterms-submit'])
  {
    $options['title'] = htmlspecialchars($_POST['popularterms-widgettitle']);
	$options['limit'] = intval($_POST['popularterms-widgetlimit']);
	$options['list'] = $_POST['popularterms-widgetlist'];	
	$options['search'] = $_POST['popularterms-widgetsearch'];	
    update_option("widget_popular_terms", $options);
  }
    ?>
    <p>
        <label for="popularterms-widgettitle">
            Title: 
        </label>	
        <input type="text" id="popularterms-widgettitle" name="popularterms-widgettitle" value="<?php echo $options['title'];?>" size="35"/>
        </p><p>
        <label for="popularterms-widgetlimit">
            Number of terms to show: 
        </label>
        <input type="text" id="popularterms-widgetlimit" name="popularterms-widgetlimit" value="<?php echo $options['limit'];?>"  size="3"/>
		</p><p>
		<label>Display format:</label> 
		<ul><li><Input type="radio" name="popularterms-widgetlist" value="1" <?php if ($options['list'] == 1){ echo 'checked'; } ;?> /> List</li>
		<li><Input type="radio" name="popularterms-widgetlist" value="0" <?php if ($options['list'] == 0){ echo 'checked'; } ;?> /> Comma separated value</li></ul>	</p><p>
		<label>Link to search (not recommended):</label> 
		<Input type="radio" name="popularterms-widgetsearch" value="0" <?php if ($options['search'] == 0){ echo 'checked'; } ;?> />No&nbsp;&nbsp;		
		<Input type="radio" name="popularterms-widgetsearch" value="1" <?php if ($options['search'] == 1){ echo 'checked'; } ;?> />Yes
	</p><p>
        <input type="hidden" id="popularterms-submit" name="popularterms-submit" value="1" />
    </p>
    <?php
}
function popular_terms_init()
{
  register_sidebar_widget( 'Popular Search Terms', 'widget_popular_terms');
  register_widget_control( 'Popular Search Terms', 'popular_terms_control', 300, 200 );    
}
add_action("plugins_loaded", "popular_terms_init");
/**
* ---------- RECENT TERMS WIDGET ----------
**/
function widget_recent_terms($args) {
  extract($args);
  $options = get_option("widget_recent_terms");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Recent Search Terms',
	  'limit' => 10,
	  'list' => '1',
			'search' => '0'
      );
  }      
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  echo stt_recent_terms_widget($options);
  echo $after_widget;
}
function recent_terms_control()
{
  $options = get_option("widget_recent_terms");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Recent Search Terms',
	  'limit' => 10,
	  'list' => '1',
			'search' => '0'
      );
  }    
  if ($_POST['recentterms-submit'])
  {
    $options['title'] = htmlspecialchars($_POST['recentterms-widgettitle']);
	$options['limit'] = intval($_POST['recentterms-widgetlimit']);
	$options['list'] = $_POST['recentterms-widgetlist'];
	$options['search'] = $_POST['recentterms-widgetsearch'];	
    update_option("widget_recent_terms", $options);
  }
    ?>
    <p>
        <label for="recentterms-widgettitle">
            Title: 
        </label>
        <input type="text" id="recentterms-widgettitle" name="recentterms-widgettitle" value="<?php echo $options['title'];?>" size="35"/>
        </p><p>
        <label for="recentterms-widgetlimit">
            Number of terms to show: 
        </label>
        <input type="text" id="recentterms-widgetlimit" name="recentterms-widgetlimit" value="<?php echo $options['limit'];?>"  size="3"/>
		</p><p>
		<label>Display format:</label> 
		<ul><li><Input type="radio" name="recentterms-widgetlist" value="1" <?php if ($options['list'] == 1){ echo 'checked'; } ;?> /> List </li>
		<li><Input type="radio" name="recentterms-widgetlist" value="0" <?php if ($options['list'] == 0){ echo 'checked'; } ;?> /> Comma separated value</li></ul>	
		</p><p>
		<label>Link to search (not recommended):</label> 
		<Input type="radio" name="recentterms-widgetsearch" value="0" <?php if ($options['search'] == 0){ echo 'checked'; } ;?> />No&nbsp;&nbsp;		
		<Input type="radio" name="recentterms-widgetsearch" value="1" <?php if ($options['search'] == 1){ echo 'checked'; } ;?> />Yes
	</p><p>
        <input type="hidden" id="recentterms-submit" name="recentterms-submit" value="1" />
    </p>
    <?php
}
function recent_terms_init()
{
  register_sidebar_widget( 'Recent Search Terms', 'widget_recent_terms');
  register_widget_control( 'Recent Search Terms', 'recent_terms_control', 300, 200 );    
}
add_action("plugins_loaded", "recent_terms_init");
/**
* ---------- POPULAR SEARCH TERMS IN CATEGORY WIDGET ----------
**/
function widget_popular_terms_cat($args) {
  extract($args);
  $options = get_option("widget_popular_terms_cat");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Popular Terms in Category',
	  'limit' => 10,
	  'list' => '1',
			'search' => '0'
      );
  }      
  if (is_category()) {
	  echo $before_widget;
	  echo $before_title;
	  echo $options['title'];
	  echo $after_title;
	  echo stt_popular_terms_in_category_widget( $options );
	  echo $after_widget;
  }
}
function popular_terms_cat_control()
{
  $options = get_option("widget_popular_terms_cat");
  if (!is_array( $options ))
        {
                $options = array(
      'title' => 'Popular Terms in Category',
	  'limit' => 10,
	  'list' => '1',
			'search' => '0'
      );
  }    
  if ($_POST['populartermscat-submit'])
  {
    $options['title'] = htmlspecialchars($_POST['populartermscat-widgettitle']);
	$options['limit'] = intval($_POST['populartermscat-widgetlimit']);
	$options['list'] = $_POST['populartermscat-widgetlist'];
	$options['search'] = $_POST['populartermscat-widgetsearch'];	
    update_option("widget_popular_terms_cat", $options);
  }
    ?>
    <p>
        <label for="populartermscat-widgettitle">
            Title: 
        </label>
        <input type="text" id="populartermscat-widgettitle" name="populartermscat-widgettitle" value="<?php echo $options['title'];?>" size="35"/>
        </p><p>
        <label for="populartermscat-widgetlimit">
            Number of terms to show: 
        </label>
        <input type="text" id="populartermscat-widgetlimit" name="populartermscat-widgetlimit" value="<?php echo $options['limit'];?>"  size="3"/>
		</p><p>
		<label>Display format:</label> 
		<ul><li><Input type="radio" name="populartermscat-widgetlist" value="1" <?php if ($options['list'] == 1){ echo 'checked'; } ;?> /> List</li>
		<li><Input type="radio" name="populartermscat-widgetlist" value="0" <?php if ($options['list'] == 0){ echo 'checked'; } ;?> /> Comma separated value</li></ul>		
			</p><p>
		<label>Link to search (not recommended):</label> 
		<Input type="radio" name="populartermscat-widgetsearch" value="0" <?php if ($options['search'] == 0){ echo 'checked'; } ;?> />No&nbsp;&nbsp;		
		<Input type="radio" name="populartermscat-widgetsearch" value="1" <?php if ($options['search'] == 1){ echo 'checked'; } ;?> />Yes
	</p><p>
        <input type="hidden" id="populartermscat-submit" name="populartermscat-submit" value="1" />
    </p>
    <?php
}
function popular_terms_cat_init()
{
  register_sidebar_widget( 'Popular Terms in Category', 'widget_popular_terms_cat');
  register_widget_control( 'Popular Terms in Category', 'popular_terms_cat_control', 300, 200 );    
}
add_action("plugins_loaded", "popular_terms_cat_init");
/**
* ---------- RANDOM TERMS WIDGET ----------
* Display random search terms in the sidebar
**/
function pk_widget_random_terms($args) {
  extract($args);
  $options = get_option("pk_widget_random_terms");
  if (!is_array( $options )){
		$options = array(
			'title' => 'Random Search Terms',
			'limit' => 10,
			'list' => '1',
			'search' => '0'
		);
  }      
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  echo stt_random_terms_widget($options);
  echo $after_widget;
}
function pk_random_terms_control()
{
  $options = get_option("pk_widget_random_terms");
  if (!is_array( $options )){
		$options = array(
			'title' => 'Random Search Terms',
			'limit' => 10,
			'list' => '1',
			'search' => '0'
		);
  }    
  if ($_POST['randomterms-submit'])
  {
    $options['title'] = htmlspecialchars($_POST['randomterms-widgettitle']);
	$options['limit'] = intval($_POST['randomterms-widgetlimit']);
	$options['list'] = $_POST['randomterms-widgetlist'];	
	$options['search'] = $_POST['randomterms-widgetsearch'];	
    update_option("pk_widget_random_terms", $options);
  }
    ?>
	
	<p>Random search terms using a lot of MySQL resources, so it is <i>not recommended</i> to activate this widget.</p>
    <p>
        <label for="randomterms-widgettitle">
            Title: 
        </label>	
        <input type="text" id="randomterms-widgettitle" name="randomterms-widgettitle" value="<?php echo $options['title'];?>" size="35"/>
    </p>
	<p>
        <label for="randomterms-widgetlimit">
            Number of terms to show: 
        </label>
        <input type="text" id="randomterms-widgetlimit" name="randomterms-widgetlimit" value="<?php echo $options['limit'];?>"  size="3"/>
    </p>
	<p>
		<label>Display format:</label> 
		<ul><li><Input type="radio" name="randomterms-widgetlist" value="1" <?php if ($options['list'] == 1){ echo 'checked'; } ;?> /> List</li>
		<li><Input type="radio" name="randomterms-widgetlist" value="0" <?php if ($options['list'] == 0){ echo 'checked'; } ;?> /> Comma separated value</li></ul>	
	</p>
	<p>
		<label>Link to search (not recommended):</label> 
		<Input type="radio" name="randomterms-widgetsearch" value="0" <?php if ($options['search'] == 0){ echo 'checked'; } ;?> />No&nbsp;&nbsp;		
		<Input type="radio" name="randomterms-widgetsearch" value="1" <?php if ($options['search'] == 1){ echo 'checked'; } ;?> />Yes
	</p>
		<p>
        <input type="hidden" id="randomterms-submit" name="randomterms-submit" value="1" />
    </p>
    <?php
}
function pk_random_terms_init()
{
  register_sidebar_widget( 'Random Search Terms', 'pk_widget_random_terms');
  register_widget_control( 'Random Search Terms', 'pk_random_terms_control', 300, 200 );    
}
add_action("plugins_loaded", "pk_random_terms_init");
/*--------- MAIN FUNCTION FOR WIDGET ----- */
/**
 * print the search terms widget
 * */ 
function pk_stt2_function_prepare_searchterms_widget( $searchterms, $list=true, $search=false ){
	global $post;
	$toReturn = ( $list ) ? '<ul>' : '';
	foreach($searchterms as $term){			
			$toReturn .= ( $list ) ? '<li>' : '';
			if ( !$search ) {
				$permalink = ( 0 == $term->post_id ) ? get_bloginfo('url') : get_permalink($term->post_id);
			} else {
				$permalink = get_bloginfo( 'url' ).'/search/'.user_trailingslashit(pk_stt2_function_sanitize_search_link($term->meta_value));
			}
			$toReturn .= "<a href=\"$permalink\" title=\"$term->meta_value\">$term->meta_value</a>";
			$toReturn .= ( $list ) ? '</li>' : ', ';
		}		
	$toReturn = trim($toReturn,', ');		
	$toReturn .= ( $list ) ? '</ul>' : '';
	//$toReturn = htmlspecialchars_decode($toReturn);
	//$toReturn .= PK_WATERMARK;
	return $toReturn;
}
/**
 * display popular search terms via widget
 * */ 
function stt_popular_terms_widget( $options ){
	$searchterms = pk_stt2_db_get_popular_terms($options['limit']);
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms_widget($searchterms,$options['list'],$options['search']);		
	  return $result;	  
    } else {
    	return false;
    }
}
/** 
 * display recent search terms via widget
 * */ 
function stt_recent_terms_widget( $options ){
	global $wpdb;	   
	$searchterms = pk_stt2_db_get_recent_terms( $options['limit'] );
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms_widget($searchterms,$options['list'],$options['search']);		
	  return $result;	  
    } else {
    	return false;
    }
}
/**
* get popular search terms on coresponding category via widget
**/
function stt_popular_terms_in_category_widget( $options ){
	if (is_category()) {
		$searchterms = pk_stt2_db_get_popular_searchterms_in_category( $options['limit'] );		
		if(!empty($searchterms)) {
		  $result = pk_stt2_function_prepare_searchterms_widget($searchterms,$options['list'],$options['search']);		
		  return $result;	  
		} else {
			return false;
		}
	}
}
/**
 * display random search terms via widget
 * */ 
function stt_random_terms_widget( $options ){
	$searchterms = pk_stt2_db_get_random_terms($options['limit']);
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms_widget($searchterms,$options['list'],$options['search']);		
	  return $result;	  
    } else {
    	return false;
    }
}
?>