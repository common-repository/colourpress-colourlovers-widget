<?php
/*
 * Plugin Name: ColourPress
 * Version: 0.1
 * Plugin URI: http://www.codenamecuttlefish.com/project-files/ColourPress-colourlovers-wordpress-widget/
 * Description: A customizable wordpress widget that displays top or new COLOURLovers palettes or patterns on your WordPress site.
 * Author: Ryan Ludwig
 * Author URI: http://www.serostar.com/
 */

//check if the the stylesheet exists, then enqueue it into the header
$myStyleUrl = WP_PLUGIN_URL . '/ColourPressStyle.css';
$myStyleFile = WP_PLUGIN_DIR . '/ColourPressStyle.css';
if ( file_exists($myStyleFile) ) {
    wp_register_style('ColourPressStylesheet', $myStyleUrl);
    wp_enqueue_style( 'ColourPressStylesheet');
}


class ColourPressWidget extends WP_Widget
{

	function ColourPressWidget(){
		$widget_ops = array('classname' => 'ColourPress_widget', 'description' => __( "Display your COLOURLovers.com palettes and patterns") );
		$this->WP_Widget('clwidget', __('ColourPress'), $widget_ops);
	}
	
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
		$lover = empty($instance['lover']) ? '' : $instance['lover'];
		$numresults = empty($instance['numresults']) ? '7' : $instance['numresults'];
		$type = empty($instance['type']) ? 'palette' : $instance['type'];
		$topornew = empty($instance['topornew']) ? 'top' : $instance['topornew'];
		$displaytype = empty($instance['displaytype']) ? 'badge' : $instance['displaytype'];
		$total_width = empty($instance['total_width']) ? '160' : $instance['total_width'];
		$total_height = empty($instance['total_height']) ? '90' : $instance['total_height'];
			
		# Before the widget
		echo $before_widget;
		
		
		# The title
		if ( $lover )
			echo '<h2 class="widgettitle"><a href="http://www.colourlovers.com/lover/' . $lover . '/">' . ucwords($lover) . '</a>&#39;s ' . ucwords($topornew) . ' ' . ucwords($type) . 's</h2>';
		

		
		# Echo out the widget's contents
		echo '<div style="text-align:center;">';

?>

<div class="cl_results">
		
	<?php
	if ($type=="palette")
		{
			$cl_query = 'http://www.colourlovers.com/api/' . $type . 's/' . $topornew . '?lover=' . $lover . '&numResults=' . $numresults . '&showPaletteWidths=1';
		}
	else 
		{
			$cl_query = 'http://www.colourlovers.com/api/' . $type . 's/' . $topornew . '?lover=' . $lover . '&numResults=' . $numresults . '';
		}
	


		$feed_xml=simplexml_load_file("$cl_query");

		foreach($feed_xml->$type as $result) :
			$content = $result->content;
			$title = $result->title;
			$url = $result->url;
			$badgeurl = $result->badgeUrl;
			$imageurl = $result->imageUrl;
			$width_array = explode(",", $result->colorWidths);
			$color_array = array($result->colors->hex[0],$result->colors->hex[1],$result->colors->hex[2],$result->colors->hex[3],$result->colors->hex[4]);

			echo '<div>';
								
			if ($type == "palette"){
				
				if ($displaytype=="badge"){
						echo '<a href="' . $url . '" title="' . $title . ' on COLOURLovers.com" style="width:240px;"><img src="' . $badgeurl . '" alt="' . $title . '" />';					
					}
					else {

						echo '<a href="' . $url . '" title="' . $title . ' on COLOURLovers.com" style="width: ' . round(($total_width+2),1) . 'px;"><span class="wrapper" style="background-color: #' . $color_array[4] . '; height: ' . $total_height . 'px;"><span class="colorblock" style="width:' . round(($total_width*$width_array[0]),1) . 'px; background-color: #' . $color_array[0] . '"></span><span class="colorblock" style="width:' . round(($total_width*$width_array[1]),1) . 'px; background-color: #' . $color_array[1] . '"></span><span class="colorblock" style="width:' . round(($total_width*$width_array[2]),1) . 'px; background-color: #' . $color_array[2] . '"></span><span class="colorblock" style="width:' . round(($total_width*$width_array[3]),1) . 'px; background-color: #' . $color_array[3] . '"></span><span class="colorblock" style="width:' . round(($total_width*$width_array[4]),1) . 'px; background-color: #' . $color_array[4] . '"></span></span>';
						}
				}
			
				else {	
					
					if ($displaytype=="badge"){
							echo '<a href="' . $url . '" title="' . $title . ' on COLOURLovers.com" style="width:240px;"><img src="' . $badgeurl . '" alt="' . $title . '" />';					
					}
						
					else {
					echo '<a href="' . $url . '" title="' . $title . ' on COLOURLovers.com" style="width: ' . ($total_width+2) . 'px;"><span class="wrapper" style="background: url(' . $imageurl . ') center center; height: ' . $total_height . 'px;"></span>';	
					}
				}
			
			
			if ($displaytype == "showtitle"){
				echo '<span class="name">' . $title . '</span>';
			}
			
			echo '</a></div>';
			
		endforeach;
	
	?>

	</div>
	
			
	<?php	
	
		# After the widget
		echo $after_widget;

	}
	
	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['lover'] = strip_tags(stripslashes($new_instance['lover']));
		$instance['numresults'] = strip_tags(stripslashes($new_instance['numresults']));
		$instance['type'] = strip_tags(stripslashes($new_instance['type']));
		$instance['topornew'] = strip_tags(stripslashes($new_instance['topornew']));
		$instance['displaytype'] = strip_tags(stripslashes($new_instance['displaytype']));
		$instance['total_width'] = strip_tags(stripslashes($new_instance['total_width']));
		$instance['total_height'] = strip_tags(stripslashes($new_instance['total_height']));
		
		return $instance;
	}
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('lover'=>'', 'numresults'=>'7', 'type'=>'palette', 'topornew'=>'top', 'displaytype'=>'showtitle', 'total_height'=>'90', 'total_width'=>'160') );
		
		$lover = htmlspecialchars($instance['lover']);
		$numresults = htmlspecialchars($instance['numresults']);
		$type = htmlspecialchars($instance['type']);
		$topornew = htmlspecialchars($instance['topornew']);
		$displaytype = htmlspecialchars($instance['displaytype']);
		$total_height = htmlspecialchars($instance['total_height']);
		$total_width = htmlspecialchars($instance['total_width']);
			
		# Output the options
		echo '<p><label for="' . $this->get_field_name('lover') . '">' . __('Your COLOURLovers Username:') . ' <input style="width: 200px;" id="' . $this->get_field_id('lover') . '" name="' . $this->get_field_name('lover') . '" type="text" value="' . $lover . '" /></label></p>';
		
		
		echo '<p>Show my ';
		# topornew
		echo '<select style="display:inline" name="' . $this->get_field_name('topornew') . '">';
		echo '<option value="top" ';
		if ($topornew == "top"){echo 'selected="true"';}
		echo '>top </option>';
		echo '<option value="new" ';
		if ($topornew == "new"){echo 'selected="true"';}
		echo '>new </option></select> ';
		
		# type
		echo '<select style="display:inline" name="' . $this->get_field_name('type') . '">';
		echo '<option value="palette" ';
		if ($type == "palette"){echo 'selected="true"';}
		echo '>palettes </option>';
		echo '<option value="pattern" ';
		if ($type == "pattern"){echo 'selected="true"';}
		echo '>patterns </option></select></p>';
		
		
		
		# displaytype
		echo '<p>Display Style:</p>';
		echo '<p><input type="radio" id="' . $this->get_field_name('displaytype') . '-notitle" name="' . $this->get_field_name('displaytype') . '" value="notitle" ';
		if ($displaytype == "notitle"){echo "checked";}
		echo '/><label for="' . $this->get_field_name('displaytype') . '-notitle">' . __('No Title') . '</label></p>';
		
		echo '<p><input type="radio" id="' . $this->get_field_name('displaytype') . '-showtitle" name="' . $this->get_field_name('displaytype') . '" value="showtitle" ';
		if ($displaytype == "showtitle"){echo "checked";}
		echo '/><label for="' . $this->get_field_name('displaytype') . '-showtitle">' . __('Show Title') . '</label></p>';
		
		echo '<p><input type="radio" id="' . $this->get_field_name('displaytype') . '-badge" name="' . $this->get_field_name('displaytype') . '" value="badge" ';
		if ($displaytype == "badge"){echo "checked";}
		echo '/><label for="' . $this->get_field_name('displaytype') . '-badge">' . __('Complete Badge') . '</label></p>';
				
			
		# numresults
		echo '<p><label for="' . $this->get_field_name('numresults') . '">' . __('Number of Results:') . ' <input style="width: 40px;" id="' . $this->get_field_id('numresults') . '" name="' . $this->get_field_name('numresults') . '" type="text" value="' . $numresults . '" /></label> <small>(max 99)</small></p>';
		
		# width
		echo '<p><label for="' . $this->get_field_name('total_width') . '">' . __('Palette Width:') . ' <input style="width: 50px;" id="' . $this->get_field_id('total_width') . '" name="' . $this->get_field_name('total_width') . '" type="text" value="' . $total_width . '" /></label><small>(default 160)</small></p>';
		
		# height
		echo '<p><label for="' . $this->get_field_name('total_height') . '">' . __('Palette Height:') . ' <input style="width: 50px;" id="' . $this->get_field_id('total_height') . '" name="' . $this->get_field_name('total_height') . '" type="text" value="' . $total_height . '" /></label><small>(default 90)</small></p>';
		
	}
	


}// END class
	
	/**
	* Register widget.
	*
	* Calls 'widgets_init' action after the widget has been registered.
	*/
	function ColourPressWidgetInit() {
	register_widget('ColourPressWidget');
	}	
	add_action('widgets_init', 'ColourPressWidgetInit');
?>