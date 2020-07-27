<?php
/**
 * @package SP Page Builder
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('Restricted access');

class SppagebuilderAddonPricelist extends SppagebuilderAddons {

	public function render() {

		$class = (isset($this->addon->settings->class) && $this->addon->settings->class) ? $this->addon->settings->class : '';
		$title = (isset($this->addon->settings->title) && $this->addon->settings->title) ? $this->addon->settings->title : '';

		//Options
		$text = (isset($this->addon->settings->text) && $this->addon->settings->text) ? $this->addon->settings->text : '';

		$price_position = (isset($this->addon->settings->price_position) && $this->addon->settings->price_position) ? $this->addon->settings->price_position : 'right-to-title';
		$price = (isset($this->addon->settings->price) && $this->addon->settings->price) ? $this->addon->settings->price : '$20.00';
		$discount_price = (isset($this->addon->settings->discount_price) && $this->addon->settings->discount_price) ? $this->addon->settings->discount_price : '';
		$add_line = (isset($this->addon->settings->add_line) && $this->addon->settings->add_line) ? $this->addon->settings->add_line : 0;
		$line_style = (isset($this->addon->settings->line_style) && $this->addon->settings->line_style) ? $this->addon->settings->line_style : 'solid';
		$line_position = (isset($this->addon->settings->line_position) && $this->addon->settings->line_position) ? $this->addon->settings->line_position : 'center';

		$add_number_or_image = (isset($this->addon->settings->add_number_or_image) && $this->addon->settings->add_number_or_image) ? $this->addon->settings->add_number_or_image : 0;
		$number_or_image_left = (isset($this->addon->settings->number_or_image_left) && $this->addon->settings->number_or_image_left) ? $this->addon->settings->number_or_image_left : 'image';
		$image = (isset($this->addon->settings->image) && $this->addon->settings->image) ? $this->addon->settings->image : '';
		$image_tag = (isset($this->addon->settings->image_tag) && $this->addon->settings->image_tag) ? $this->addon->settings->image_tag : '';
		$image_tag_text = (isset($this->addon->settings->image_tag_text) && $this->addon->settings->image_tag_text) ? $this->addon->settings->image_tag_text : '';
		$image_tag_radius = (isset($this->addon->settings->image_tag_radius) && $this->addon->settings->image_tag_radius) ? $this->addon->settings->image_tag_radius : '';
		$number_text = (isset($this->addon->settings->number_text) && $this->addon->settings->number_text) ? $this->addon->settings->number_text : 1;
		
		$content_alignment = '';
		if($price_position == "content-bottom"){
			$content_alignment = 'sppb-text-alignment';
		}

		$split_price = preg_split("/[\.]+/", $price);
		$price_text = '';
		$price_zero = '';
		if($split_price){
			$price_text = isset($split_price[0]) ? $split_price[0] : '';
			$price_zero = isset($split_price[1]) ? '.'.$split_price[1] : '';
		}

		$dis_split_price = preg_split("/[\.]+/", $discount_price);
		$dis_price_text = '';
		$dis_price_zero = '';
		if($dis_split_price){
			$dis_price_text = isset($dis_split_price[0]) ? $dis_split_price[0] : '';
			$dis_price_zero = isset($dis_split_price[1]) ? '.'.$dis_split_price[1] : '';
		}

		//Building price
		$price_output = '';
		$dis_price_output = '';
		$line_style_output = '';
		$discount_class = '';
		if($price_position && $discount_price){
			$discount_class = 'discounted-price';
		}
		if($price_position && $price){
			$price_output = '<span class="pricelist-price '.$discount_class.'">'.$price_text.'<span class="pricelist-point-zero">'.$price_zero.'</span></span>';
		}
		if($price_position && $discount_price){
			$dis_price_output = '<span class="pricelist-price">'.$dis_price_text.'<span class="pricelist-point-zero">'.$dis_price_zero.'</span></span>';
		}
		if($add_line && $line_style){
			if($line_position == 'title-bottom'){
				$line_style_output .= '<span class="pricelist-line title-bottom"><span class="pricelist-line-style-'.$line_style.'"></span></span>';
			} else{
				$line_style_output .= '<span class="pricelist-line"><span class="pricelist-line-style-'.$line_style.'"></span></span>';
			}
		}

		//Output
		$output  = '';
		$output  .= '<div class="sppb-addon sppb-addon-pricelist ' . $class . '">';
		if($add_number_or_image){
			$output  .= '<div class="pricelist-left-image">';
				if($number_or_image_left == 'image'){
					$alt_text = ($title) ? $title : '';
					$output  .= '<img src="'.$image.'" alt="'.$alt_text.'">';
					if($image_tag && $image_tag_text){
						$tag_class = ($image_tag_radius) ? 'tag-radius' : '';
						$output  .= '<span class="pricelist-tag '.$tag_class.'">'.$image_tag_text.'</span>';
					}
				} else {
					$output  .= '<div class="pricelist-left-number">';
					$output  .= $number_text;
					$output  .= '</div>';
				}
			$output  .= '</div>';
		}
		$output .= '<div class="pricelist-text-content '.$content_alignment.'">';
		if ($title) {
			$output .= '<div class="sppb-addon-title">';
				$output .= '<span class="pricelist-title-content">';
					$output .= '<span class="pricelist-title">' . $title;
					if($price_position == 'with-title'){
						$output .= '<span class="pricelist-price-with-title">' .$price_output.' '.$dis_price_output.'</span>';
					}
					$output .= '</span>';
					if($line_position != 'title-bottom' && $price_position == 'right-to-title'){
						$output .=  $line_style_output;
					}
					if($price_position == 'right-to-title'){
						$output .= '<span class="pricelist-price-content">'.$price_output.' '.$dis_price_output.'</span>';
					}
				$output .= '</span>';
			$output .= '</div>';
		}
		if($line_position == 'title-bottom'){
			$output .= $line_style_output;
		}
		$output .= '<div class="sppb-addon-content">';
		$output .= $text;
		$output .= '</div>';
		if($price_position == 'content-bottom'){
			$output .= '<span class="pricelist-price-content bottom-of-content">'.$price_output.' '.$dis_price_output.'</span>';
		}
		if($line_position != 'title-bottom' && $price_position == 'content-bottom'){
			$output .=  $line_style_output;
		}
		$output .= '</div>';

		$output .= '</div>';

		return $output;

	}

	public function css() {
		$css = '';

		$style = '';
		$style_sm = '';
		$style_xs = '';

		$style .= (isset($this->addon->settings->text_fontsize) && $this->addon->settings->text_fontsize) ? "font-size: " . $this->addon->settings->text_fontsize . "px;" : "";
		$style_sm .= (isset($this->addon->settings->text_fontsize_sm) && $this->addon->settings->text_fontsize_sm) ? "font-size: " . $this->addon->settings->text_fontsize_sm . "px;" : "";
		$style_xs .= (isset($this->addon->settings->text_fontsize_xs) && $this->addon->settings->text_fontsize_xs) ? "font-size: " . $this->addon->settings->text_fontsize_xs . "px;" : "";

		$style .= (isset($this->addon->settings->text_lineheight) && $this->addon->settings->text_lineheight) ? "line-height: " . $this->addon->settings->text_lineheight . "px;" : "";
		$style_sm .= (isset($this->addon->settings->text_lineheight_sm) && $this->addon->settings->text_lineheight_sm) ? "line-height: " . $this->addon->settings->text_lineheight_sm . "px;" : "";
		$style_xs .= (isset($this->addon->settings->text_lineheight_xs) && $this->addon->settings->text_lineheight_xs) ? "line-height: " . $this->addon->settings->text_lineheight_xs . "px;" : "";

		if($style){
			$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-addon-content { ' . $style . ' }';
		}

		if($style_sm){
			$css .= '@media (min-width: 768px) and (max-width: 991px) {#sppb-addon-' . $this->addon->id . ' .sppb-addon-content { ' . $style_sm . ' }}';
		}

		if($style_xs){
			$css .= '@media (max-width: 767px) {#sppb-addon-' . $this->addon->id . ' .sppb-addon-content { ' . $style_xs . ' }}';
		}

		$add_line = (isset($this->addon->settings->add_line) && $this->addon->settings->add_line) ? $this->addon->settings->add_line : 0;
		$line_size = (isset($this->addon->settings->line_size) && $this->addon->settings->line_size) ? $this->addon->settings->line_size : '';
		$line_color = (isset($this->addon->settings->line_color) && $this->addon->settings->line_color) ? $this->addon->settings->line_color : '';
		$line_position = (isset($this->addon->settings->line_position) && $this->addon->settings->line_position) ? $this->addon->settings->line_position : 'center';
		$line_top_gap = (isset($this->addon->settings->line_top_gap) && $this->addon->settings->line_top_gap) ? $this->addon->settings->line_top_gap : '';
		$line_bottom_gap = (isset($this->addon->settings->line_bottom_gap) && $this->addon->settings->line_bottom_gap) ? $this->addon->settings->line_bottom_gap : '';

		if($add_line){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-line span {';
				if($line_size){
					$css .= 'border-bottom-width:' . $line_size . 'px;';
				}
				if($line_color){
					$css .= 'border-bottom-color:' . $line_color . ';';
				}
			$css .= '}';
			if($line_position){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-line  {';
					$css .= '-webkit-box-align: '.$line_position.';';
					$css .= '-ms-flex-align: '.$line_position.';';
					$css .= 'align-items: '.$line_position.';';
				$css .= '}';
			}
			if($line_position == 'title-bottom'){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-line.title-bottom  {';
					if($line_top_gap){
						$css .= 'margin-top: '.$line_top_gap.'px;';
					}
					if($line_bottom_gap ){
						$css .= 'margin-bottom: '.$line_bottom_gap .'px;';
					}
				$css .= '}';
			}
		}

		$image = (isset($this->addon->settings->image) && $this->addon->settings->image) ? $this->addon->settings->image : '';
		$image_width = (isset($this->addon->settings->image_width) && $this->addon->settings->image_width) ? $this->addon->settings->image_width : '';
		$image_width_sm = (isset($this->addon->settings->image_width_sm) && $this->addon->settings->image_width_sm) ? $this->addon->settings->image_width_sm : '';
		$image_width_xs = (isset($this->addon->settings->image_width_xs) && $this->addon->settings->image_width_xs) ? $this->addon->settings->image_width_xs : '';
		$image_border_radius = (isset($this->addon->settings->image_border_radius) && $this->addon->settings->image_border_radius) ? $this->addon->settings->image_border_radius : '';
		$add_number_or_image = (isset($this->addon->settings->add_number_or_image) && $this->addon->settings->add_number_or_image) ? $this->addon->settings->add_number_or_image : 0;
		$number_or_image_left = (isset($this->addon->settings->number_or_image_left) && $this->addon->settings->number_or_image_left) ? $this->addon->settings->number_or_image_left : 'image';
		$image_gutter = (isset($this->addon->settings->image_gutter) && $this->addon->settings->image_gutter) ? $this->addon->settings->image_gutter : 15;
		$image_gutter_sm = (isset($this->addon->settings->image_gutter_sm) && $this->addon->settings->image_gutter_sm) ? $this->addon->settings->image_gutter_sm : '';
		$image_gutter_xs = (isset($this->addon->settings->image_gutter_xs) && $this->addon->settings->image_gutter_xs) ? $this->addon->settings->image_gutter_xs : '';
		$price_color = (isset($this->addon->settings->price_color) && $this->addon->settings->price_color) ? $this->addon->settings->price_color : '';
		$discount_price_color = (isset($this->addon->settings->discount_price_color) && $this->addon->settings->discount_price_color) ? $this->addon->settings->discount_price_color : '';
		$price_fontsize = (isset($this->addon->settings->price_fontsize) && $this->addon->settings->price_fontsize) ? $this->addon->settings->price_fontsize : '';
		$price_fontsize_sm = (isset($this->addon->settings->price_fontsize_sm) && $this->addon->settings->price_fontsize_sm) ? $this->addon->settings->price_fontsize_sm : '';
		$price_fontsize_xs = (isset($this->addon->settings->price_fontsize_xs) && $this->addon->settings->price_fontsize_xs) ? $this->addon->settings->price_fontsize_xs : '';
		$price_fontweight = (isset($this->addon->settings->price_fontweight) && $this->addon->settings->price_fontweight) ? $this->addon->settings->price_fontweight : 700;
		$price_top_gap = (isset($this->addon->settings->price_top_gap) && $this->addon->settings->price_top_gap) ? $this->addon->settings->price_top_gap : '';
		$price_bottom_gap = (isset($this->addon->settings->price_bottom_gap) && $this->addon->settings->price_bottom_gap) ? $this->addon->settings->price_bottom_gap : '';
		
		$number_style = '';
		$number_style_sm = '';
		$number_style_xs = '';
		
		$number_style .= (isset($this->addon->settings->number_bg_color) && $this->addon->settings->number_bg_color) ? 'background-color:'. $this->addon->settings->number_bg_color .';' : '';
		$number_style .= (isset($this->addon->settings->number_color) && $this->addon->settings->number_color) ? 'color:'. $this->addon->settings->number_color .';' : '';
		$number_style .= (isset($this->addon->settings->number_fontsize) && $this->addon->settings->number_fontsize) ? 'font-size:'. $this->addon->settings->number_fontsize .'px;' : '';
		$number_style .= (isset($this->addon->settings->number_fontweight) && $this->addon->settings->number_fontweight) ? 'font-weight:'. $this->addon->settings->number_fontweight .';' : '';
		$number_style .= (isset($this->addon->settings->number_fontstyle) && $this->addon->settings->number_fontstyle) ? 'font-style:'. $this->addon->settings->number_fontstyle .';' : '';
		$number_style .= (isset($this->addon->settings->number_top_padding) && $this->addon->settings->number_top_padding) ? 'padding-top:'. $this->addon->settings->number_top_padding .'px;' : '';
		$number_style .= (isset($this->addon->settings->number_bottom_padding) && $this->addon->settings->number_bottom_padding) ? 'padding-bottom:'. $this->addon->settings->number_bottom_padding .'px;' : '';
		
		$number_style_sm .= (isset($this->addon->settings->number_fontsize_sm) && $this->addon->settings->number_fontsize_sm) ? 'font-size:' . $this->addon->settings->number_fontsize_sm . 'px;' : '';
		$number_style_xs = (isset($this->addon->settings->number_fontsize_xs) && $this->addon->settings->number_fontsize_xs) ? 'font-size:'. $this->addon->settings->number_fontsize_xs .'px;' : '';
		
		$zero_position = (isset($this->addon->settings->zero_position) && $this->addon->settings->zero_position) ? $this->addon->settings->zero_position : '';
		$discount_price_position = (isset($this->addon->settings->discount_price_position) && $this->addon->settings->discount_price_position) ? $this->addon->settings->discount_price_position : '';
		
		$tag_style = '';
		$tag_style .= (isset($this->addon->settings->image_tag_bg) && $this->addon->settings->image_tag_bg) ? 'background-color:'.$this->addon->settings->image_tag_bg.';' : 'background-color:#000000';
		$tag_style .= (isset($this->addon->settings->image_tag_radius) && $this->addon->settings->image_tag_radius) ? 'border-radius:' .$this->addon->settings->image_tag_radius.'px;' : '';
		$tag_style .= (isset($this->addon->settings->image_tag_top_margin) && $this->addon->settings->image_tag_top_margin) ? 'top:' .$this->addon->settings->image_tag_top_margin.'px;' : '';
		$tag_style .= (isset($this->addon->settings->image_tag_left_margin) && $this->addon->settings->image_tag_left_margin) ? 'left:' .$this->addon->settings->image_tag_left_margin.'px;' : '';

		if($price_top_gap || $price_bottom_gap){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price-content.bottom-of-content {';
				$css .= 'display: block;';
				if($price_top_gap){
					$css .= 'margin-top:' .$price_top_gap. 'px;';
				}
				if($price_bottom_gap){
					$css .= 'margin-bottom:' .$price_bottom_gap. 'px;';
				}
				if($this->addon->settings->font_family){
					$css .= 'font-family: ' .$this->addon->settings->font_family. ';';
				}
			$css .= '}';
		}
		if($zero_position){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-point-zero {';
				$css .= 'vertical-align: ' .$zero_position. ';';
			$css .= '}';
		}
		if($discount_price_position){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price.discounted-price {';
				$css .= 'vertical-align: ' .$discount_price_position. ';';
			$css .= '}';
		}
		if($price_color || $price_fontsize || $price_fontweight || $discount_price_color){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price-content {';
				if($price_color){
					$css .= 'color:' .$price_color. ';';
				}
				if($price_fontsize){
					$css .= 'font-size:' .$price_fontsize. 'px;';
				}
				if($price_fontweight){
					$css .= 'font-weight:' .$price_fontweight. ';';
				}
			$css .= '}';
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price.discounted-price {';
				if($discount_price_color){
					$css .= 'color:' .$discount_price_color. ';';
				}
			$css .= '}';
		}

		if(isset($this->addon->settings->content_position) && $this->addon->settings->content_position){
			$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment {';
				if($this->addon->settings->content_position == 'left'){
					$css .= 'text-align: left;';
				} elseif( $this->addon->settings->content_position == 'right' ){
					$css .= 'text-align: right;';
				} elseif( $this->addon->settings->content_position == 'center' ){
					$css .= 'text-align: center;';
				}
			$css .= '}';

			if($this->addon->settings->content_position == 'left'){
				$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
					-webkit-box-pack: start;
					-ms-flex-pack: start;
					justify-content: flex-start;
				}';
			} elseif( $this->addon->settings->content_position == 'right' ){
				$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
					-webkit-box-pack: end;
					-ms-flex-pack: end;
					justify-content: flex-end;
				}';
			} elseif( $this->addon->settings->content_position == 'center' ){
				$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
					-webkit-box-pack: center;
					-ms-flex-pack: center;
					justify-content: center;
				}';
			}

		}

		if($add_number_or_image){
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-image {';
				if($image_width){
					$css .= '-ms-flex: 0 0 ' .$image_width. '%;';
					$css .= 'flex: 0 0 ' .$image_width. '%;';
					$css .= 'max-width:' .$image_width. '%;';
				}
				if($image_gutter){
					$css .= 'padding-right: '.$image_gutter.'px;';
				}
			$css .= '}';
			$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-text-content {';
				if($image_width){
					$css .= '-ms-flex: 0 0 ' .(100-$image_width). '%;';
					$css .= 'flex: 0 0 ' .(100-$image_width). '%;';
					$css .= 'max-width:' .(100-$image_width). '%;';
				}
			if($image_gutter){
				$css .= 'padding-left: '.$image_gutter.'px;';
			}
			$css .= '}';
			
			if($image_border_radius){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-number,';
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-image img {';
					$css .= 'border-radius:' .$image_border_radius. '%;';
				$css .= '}';
			}
			if($tag_style){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-tag {';
					$css .= $tag_style;
				$css .= '}';
			}
			if($number_style){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-number {';
					$css .= $number_style;
				$css .= '}';
			}
		}

		$css .= '@media (min-width: 768px) and (max-width: 991px) {';
			if($add_number_or_image || $number_style_sm){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-image {';
					if($image_width_sm){
						$css .= '-ms-flex: 0 0 ' .$image_width_sm. '%;';
						$css .= 'flex: 0 0 ' .$image_width_sm. '%;';
						$css .= 'max-width:' .$image_width_sm. '%;';
					}
					if($image_gutter_sm){
						$css .= 'padding-right: '.$image_gutter_sm.'px;';
					}
				$css .= '}';
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-text-content {';
					if($image_width_sm){
						$css .= '-ms-flex: 0 0 ' .(100-$image_width_sm). '%;';
						$css .= 'flex: 0 0 ' .(100-$image_width_sm). '%;';
						$css .= 'max-width:' .(100-$image_width_sm). '%;';
					}
					if($image_gutter_sm){
						$css .= 'padding-left: '.$image_gutter_sm.'px;';
					}
				$css .= '}';

				if($number_style_sm){
					$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-number {';
						$css .= $number_style_sm;
					$css .= '}';
				}
			}
			if(isset($this->addon->settings->content_position_sm) && $this->addon->settings->content_position_sm){
				$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment {';
					if($this->addon->settings->content_position_sm == 'left'){
						$css .= 'text-align: left;';
					} elseif( $this->addon->settings->content_position_sm == 'right' ){
						$css .= 'text-align: right;';
					} elseif( $this->addon->settings->content_position_sm == 'center' ){
						$css .= 'text-align: center;';
					}
				$css .= '}';

				if($this->addon->settings->content_position_sm == 'left'){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
					}';
				} elseif( $this->addon->settings->content_position_sm == 'right' ){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: end;
						-ms-flex-pack: end;
						justify-content: flex-end;
					}';
				} elseif( $this->addon->settings->content_position_sm == 'center' ){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: center;
						-ms-flex-pack: center;
						justify-content: center;
					}';
				}
			}
			if($price_fontsize_sm){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price-content {';
					$css .= 'font-size:' .$price_fontsize_sm. 'px;';
				$css .= '}';
			}
		$css .= '}';
		$css .= '@media (max-width: 767px) {';
			if($add_number_or_image || $number_style_xs){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-image {';
					if($image_width_xs){
						$css .= '-ms-flex: 0 0 ' .$image_width_xs. '%;';
						$css .= 'flex: 0 0 ' .$image_width_xs. '%;';
						$css .= 'max-width:' .$image_width_xs. '%;';
					}
					if($image_gutter_xs){
						$css .= 'padding-right: '.$image_gutter_xs.'px;';
					}
				$css .= '}';
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-text-content {';
					if($image_width_xs){
						$css .= '-ms-flex: 0 0 ' .(100-$image_width_xs). '%;';
						$css .= 'flex: 0 0 ' .(100-$image_width_xs). '%;';
						$css .= 'max-width:' .(100-$image_width_xs). '%;';
					}
					if($image_gutter_xs){
						$css .= 'padding-left: '.$image_gutter_xs.'px;';
					}
				$css .= '}';
				
				if($number_style_xs){
					$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-left-number {';
						$css .= $number_style_xs;
					$css .= '}';
				}
			}
			if(isset($this->addon->settings->content_position_xs) && $this->addon->settings->content_position_xs){
				$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment {';
					if($this->addon->settings->content_position_xs == 'left'){
						$css .= 'text-align: left;';
					} elseif( $this->addon->settings->content_position_xs == 'right' ){
						$css .= 'text-align: right;';
					} elseif( $this->addon->settings->content_position_xs == 'center' ){
						$css .= 'text-align: center;';
					}

				$css .= '}';
				if($this->addon->settings->content_position_xs == 'left'){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
					}';
				} elseif( $this->addon->settings->content_position_xs == 'right' ){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: end;
						-ms-flex-pack: end;
						justify-content: flex-end;
					}';
				} elseif( $this->addon->settings->content_position_xs == 'center' ){
					$css .= '#sppb-addon-' . $this->addon->id . ' .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: center;
						-ms-flex-pack: center;
						justify-content: center;
					}';
				}
			}
			if($price_fontsize_xs){
				$css .= '#sppb-addon-' . $this->addon->id . ' .pricelist-price-content {';
					$css .= 'font-size:' .$price_fontsize_xs. 'px;';
				$css .= '}';
			}
		$css .= '}';

		return $css;
	}

	public static function getTemplate()
	{
		$output = '
		<#
			
			let split_price = _.split(data.price, ".");
			let price_text = "";
			let price_zero = "";
			if(split_price){
				price_text = !_.isEmpty(split_price[0]) ? split_price[0] : "";
				price_zero = !_.isEmpty(split_price[1]) ? "."+split_price[1] : "";
			}

			let dis_split_price = _.split(data.discount_price, ".");
			let dis_price_text = "";
			let dis_price_zero = "";
			if(dis_split_price){
				dis_price_text = !_.isEmpty(dis_split_price[0]) ? dis_split_price[0] : "";
				dis_price_zero = !_.isEmpty(dis_split_price[1]) ? "."+dis_split_price[1] : "";
			}

			let discount_class = "";
			let price_output = "";
			let dis_price_output = "";
			let line_style_output = "";
			
			if(data.price_position && data.discount_price){
				discount_class = "discounted-price";
			}
			if(data.price_position && data.price){
				price_output = \'<span class="pricelist-price \' + discount_class + \'">\'+ price_text + \'<span class="pricelist-point-zero">\'+price_zero+\'</span></span>\';
			}
			if(data.price_position && data.discount_price){
				dis_price_output = \'<span class="pricelist-price">\'+dis_price_text+\'<span class="pricelist-point-zero">\'+dis_price_zero+\'</span></span>\';
			}
			if(data.add_line && data.line_style){
				if(data.line_position == "title-bottom"){
					line_style_output += \'<span class="pricelist-line title-bottom"><span class="pricelist-line-style-\'+data.line_style+\'"></span></span>\';
				} else {
					line_style_output += \'<span class="pricelist-line"><span class="pricelist-line-style-\'+data.line_style+\'"></span></span>\';
				}
			}

			let content_alignment = "";
			if(data.price_position == "content-bottom"){
				content_alignment = "sppb-text-alignment";
			}
		#>
		<style type="text/css">
			#sppb-addon-{{ data.id }} .sppb-addon-content {
				<# if(_.isObject(data.text_fontsize)){ #>
					font-size: {{ data.text_fontsize.md }}px;
				<# } else { #>
					font-size: {{ data.text_fontsize }}px;
				<# } #>

				<# if(_.isObject(data.text_lineheight)){ #>
					line-height: {{ data.text_lineheight.md }}px;
				<# } else { #>
					line-height: {{ data.text_lineheight }}px;
				<# } #>
				<# if(data.text_fontweight){ #>
					font-weight: {{ data.text_fontweight}};
				<# } #>
			}

			@media (min-width: 768px) and (max-width: 991px) {
				#sppb-addon-{{ data.id }} .sppb-addon-content{
					<# if(_.isObject(data.text_fontsize)){ #>
						font-size: {{ data.text_fontsize.sm }}px;
					<# } #>

					<# if(_.isObject(data.text_lineheight)){ #>
						line-height: {{ data.text_lineheight.sm }}px;
					<# } #>
				}
			}
			@media (max-width: 767px) {
				#sppb-addon-{{ data.id }} .sppb-addon-content {
					<# if(_.isObject(data.text_fontsize)){ #>
						font-size: {{ data.text_fontsize.xs }}px;
					<# } #>

					<# if(_.isObject(data.text_lineheight)){ #>
						line-height: {{ data.text_lineheight.xs }}px;
					<# } #>
				}
			}

		<# if(data.add_line || data.price_position){ #>
			#sppb-addon-{{data.id}} .pricelist-line span {
				<# if(data.line_size){ #>
					border-bottom-width:{{data.line_size}}px;
				<# } #>
				<# if(data.line_color){ #>
					border-bottom-color:{{data.line_color}};
				<# } #>
			}

			<# if(data.line_position){ #>
				#sppb-addon-{{data.id}} .pricelist-line  {
					-webkit-box-align: {{data.line_position}};
					-ms-flex-align: {{data.line_position}};
					align-items: {{data.line_position}};
				}
			<# }
			if(data.line_position == "title-bottom"){
			#>
				#sppb-addon-{{data.id}} .pricelist-line.title-bottom  {
					<# if(data.line_top_gap){ #>
						margin-top: {{data.line_top_gap}}px;
					<# }
					if(data.line_bottom_gap){
					#>
						margin-bottom: {{data.line_bottom_gap}}px;
					<# } #>
				}
			<# }
		}

		if(data.price_top_gap || data.price_bottom_gap){ #>
			#sppb-addon-{{data.id}} .pricelist-price-content.bottom-of-content {
				display: block;
				<# if(data.price_top_gap){ #>
					margin-top:{{data.price_top_gap}}px;
				<# } #>
				<# if(data.price_bottom_gap){ #>
					margin-bottom:{{data.price_bottom_gap}}px;
				<# }
				if(data.font_family){
				#>
					font-family: {{data.font_family}};
				<# } #>
			}
		<# }

		if(data.price_color || data.price_fontsize || data.price_fontweight || data.discount_price_color){ #>
			#sppb-addon-{{data.id}} .pricelist-price-content {
				<# if(data.price_color){ #>
					color:{{data.price_color}};
				<# } #>
				<# if(_.isObject(data.price_fontsize)){ #>
					font-size:{{data.price_fontsize.md}}px;
				<# } else {#>
					font-size:{{data.price_fontsize}}px;
				<# } #>
				<# if(data.price_fontweight){ #>
					font-weight:{{data.price_fontweight}};
				<# } #>
			}
			#sppb-addon-{{data.id}} .pricelist-price.discounted-price{
				<# if(data.discount_price_color){ #>
					color:{{data.discount_price_color}};
				<# } #>
			}
		<# }
		if(data.number_text && data.number_or_image_left=="number"){
		#>
			#sppb-addon-{{data.id}} .pricelist-left-number {
				<# if(data.number_bg_color){ #>
					background-color:{{data.number_bg_color}};
				<# } #>
				<# if(data.number_color){ #>
					color:{{data.number_color}};
				<# } #>
				<# if(_.isObject(data.number_fontsize)){ #>
					font-size: {{data.number_fontsize.md}}px;
				<# } else { #>
					font-size: {{data.number_fontsize}}px;
				<# } #>
				<# if(data.number_fontweight){ #>
					font-weight:{{data.number_fontweight}};
				<# } #>
				<# if(data.number_fontstyle){ #>
					font-style:{{data.number_fontstyle}};
				<# } #>
				<# if(data.number_top_padding){ #>
					padding-top:{{data.number_top_padding}}px;
				<# } #>
				<# if(data.number_bottom_padding){ #>
					padding-bottom:{{data.number_bottom_padding}}px;
				<# } #>
			}
		<# }
		if(data.add_number_or_image){
			if(data.image_width){
			#>
				#sppb-addon-{{data.id}} .pricelist-left-image {
					<# if(_.isObject(data.image_width)){ #>
						-ms-flex: 0 0 {{data.image_width.md}}%;
						flex: 0 0 {{data.image_width.md}}%;
						max-width: {{data.image_width.md}}%;
					<# } else { #>
						-ms-flex: 0 0 {{data.image_width}}%;
						flex: 0 0 {{data.image_width}}%;
						max-width: {{data.image_width}}%;
					<# }
					if(_.isObject(data.image_gutter)){
					#>
						padding-right: {{data.image_gutter.md}}px;
					<# } else { #>
						padding-right: {{data.image_gutter}}px;
					<# } #>
				}
				#sppb-addon-{{data.id}} .pricelist-text-content {
					<# if(_.isObject(data.image_width)){ #>
						-ms-flex: 0 0 {{100-data.image_width.md}}%;
						flex: 0 0 {{100-data.image_width.md}}%;
						max-width: {{100-data.image_width.md}}%;
					<# } else { #>
						-ms-flex: 0 0 {{100-data.image_width}}%;
						flex: 0 0 {{100-data.image_width}}%;
						max-width: {{100-data.image_width}}%;
					<# }
					if(_.isObject(data.image_gutter)){
					#>
						padding-left: {{data.image_gutter.md}}px;
					<# } else { #>
						padding-left: {{data.image_gutter}}px;
					<# } #>
				}
			<# }
			if(data.image_border_radius){ #>
				#sppb-addon-{{data.id}} .pricelist-left-number,
				#sppb-addon-{{data.id}} .pricelist-left-image img {
					border-radius:{{data.image_border_radius}}%;
				}
			<# }
			if(data.image_tag_bg || data.image_tag_radius || data.image_tag_top_margin || data.image_tag_left_margin){ #>
				#sppb-addon-{{data.id}} .pricelist-tag {
					background-color:{{data.image_tag_bg}};
					border-radius:{{data.image_tag_radius}}px;
					top:{{data.image_tag_top_margin}}px;
					left:{{data.image_tag_left_margin}}px;
				}
			<# } #>
		<# }
		if(data.content_position){
			if(_.isObject(data.content_position) && data.content_position.md){
		#>
				#sppb-addon-{{data.id}} .sppb-text-alignment {
					<# if(data.content_position.md == "left"){ #>
						text-align: left;
					<# } else if( data.content_position.md == "right" ){ #>
						text-align: right;
					<# } else if( data.content_position.md == "center" ){ #>
						text-align: center;
					<# } #>

				}
				<# if(data.content_position.md == "left"){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
					}
				<# } else if( data.content_position.md == "right" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: end;
						-ms-flex-pack: end;
						justify-content: flex-end;
					}
				<# } else if( data.content_position.md == "center" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: center;
						-ms-flex-pack: center;
						justify-content: center;
					}
				<# }
			}
		} #>
		<# if(data.zero_position){ #>
			#sppb-addon-{{data.id}} .pricelist-point-zero {
				vertical-align: {{data.zero_position}};
			}
		<# } #>
		<# if(data.discount_price_position){ #>
			#sppb-addon-{{data.id}} .pricelist-price.discounted-price {
				vertical-align: {{data.discount_price_position}};
			}
		<# } #>
		@media (max-width: 991px) {
			<# if(data.add_number_or_image){ #>
				<# if((_.isObject(data.image_width) && data.image_width.sm) || _.isObject(data.image_gutter)){ #>
					#sppb-addon-{{data.id}} .pricelist-left-image {
						-ms-flex: 0 0 {{data.image_width.sm}}%;
						flex: 0 0 {{data.image_width.sm}}%;
						max-width:{{data.image_width.sm}}%;
						<# if(_.isObject(data.image_gutter)){ #>
							padding-right: {{data.image_gutter.sm}}px;
						<# } #>
					}
					#sppb-addon-{{data.id}} .pricelist-text-content {
						-ms-flex: 0 0 {{100-data.image_width.sm}}%;
						flex: 0 0 {{100-data.image_width.sm}}%;
						max-width: {{100-data.image_width.sm}}%;
						<# if(_.isObject(data.image_gutter)){ #>
							padding-left: {{data.image_gutter.sm}}px;
						<# } #>
					}
				<# }
				if(_.isObject(data.number_fontsize) && data.number_fontsize.sm){
				#>
					#sppb-addon-{{data.id}} .pricelist-left-number {
						font-size: {{data.number_fontsize.sm}}px;
					}
				<# }
			}
			if(_.isObject(data.content_position) && data.content_position.sm){
			#>
				#sppb-addon-{{data.id}} .sppb-text-alignment {
					<# if(data.content_position.sm == "left"){ #>
						text-align: left;
					<# } else if( data.content_position.sm == "right" ){ #>
						text-align: right;
					<# } else if( data.content_position.sm == "center" ){ #>
						text-align: center;
					<# } #>

				}
				<# if(data.content_position.sm == "left"){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
					}
				<# } else if( data.content_position.sm == "right" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: end;
						-ms-flex-pack: end;
						justify-content: flex-end;
					}
				<# } else if( data.content_position.sm == "center" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: center;
						-ms-flex-pack: center;
						justify-content: center;
					}
				<# }
			} #>
			<# if(_.isObject(data.price_fontsize)){ #>
				#sppb-addon-{{data.id}} .pricelist-price-content {
					font-size:{{data.price_fontsize.sm}}px;
				}
			<# } #>
		}
		@media (max-width: 767px) {
			<# if(data.add_number_or_image){ #>
				<# if((_.isObject(data.image_width) && data.image_width.xs) || _.isObject(data.image_gutter)){ #>
					#sppb-addon-{{data.id}} .pricelist-left-image {
						-ms-flex: 0 0 {{data.image_width.xs}}%;
						flex: 0 0 {{data.image_width.xs}}%;
						max-width: {{data.image_width.xs}}%;
						<# if(_.isObject(data.image_gutter)){ #>
							padding-right: {{data.image_gutter.xs}}px;
						<# } #>
					}
					#sppb-addon-{{data.id}} .pricelist-text-content {
						-ms-flex: 0 0 {{100-data.image_width.xs}}%;
						flex: 0 0 {{100-data.image_width.xs}}%;
						max-width: {{100-data.image_width.xs}}%;
						<# if(_.isObject(data.image_gutter)){ #>
							padding-left: {{data.image_gutter.xs}}px;
						<# } #>
					}
				<# }
				if(_.isObject(data.number_fontsize) && data.number_fontsize.xs){
				#>
					#sppb-addon-{{data.id}} .pricelist-left-number {
						font-size: {{data.number_fontsize.xs}}px;
					}
				<# } #>
			<# }
			if(_.isObject(data.content_position) && data.content_position.xs){
			#>
				#sppb-addon-{{data.id}} .sppb-text-alignment {
					<# if(data.content_position.xs == "left"){ #>
						text-align: left;
					<# } else if( data.content_position.xs == "right" ){ #>
						text-align: right;
					<# } else if( data.content_position.xs == "center" ){ #>
						text-align: center;
					<# } #>

				}
				<# if(data.content_position.xs == "left"){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: start;
						-ms-flex-pack: start;
						justify-content: flex-start;
					}
				<# } else if( data.content_position.xs == "right" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: end;
						-ms-flex-pack: end;
						justify-content: flex-end;
					}
				<# } else if( data.content_position.xs == "center" ){ #>
					#sppb-addon-{{data.id}} .sppb-text-alignment .pricelist-title-content{
						-webkit-box-pack: center;
						-ms-flex-pack: center;
						justify-content: center;
					}
				<# }
			} #>
			<# if(_.isObject(data.price_fontsize)){ #>
				#sppb-addon-{{data.id}} .pricelist-price-content {
					font-size:{{data.price_fontsize.xs}}px;
				}
			<# } #>
		}
		</style>
		
		<div class="sppb-addon sppb-addon-pricelist {{data.class}}">
		<# if(data.add_number_or_image){ #>
			<div class="pricelist-left-image">
				<# if(data.number_or_image_left=="image") {
					if(data.image.indexOf("http://") == -1 && data.image.indexOf("https://") == -1){ #>
						<img class="sppb-img-responsive" src=\'{{ pagebuilder_base + data.image }}\'>
					<# } else { #>
						<img class="sppb-img-responsive" src=\'{{ data.image }}\'>
					<# }
					if(data.image_tag && data.image_tag_text){ 
						let tag_class = "";
						if (data.image_tag_radius !== undefined && data.image_tag_radius) {
							tag_class = "tag-radius";
						}; 
					#>
						<span class="pricelist-tag {{tag_class}}">{{data.image_tag_text}}</span>
					<# }
				} else { #>
					<div class="pricelist-left-number">
						{{data.number_text}}
					</div>
				<# } #>
			</div>
		<# } #>
		<div class="pricelist-text-content {{content_alignment}}">
		<# if (data.title) { #>
			<div class="sppb-addon-title">
				<span class="pricelist-title-content">
					<span class="pricelist-title">
					<span class="sp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{data.title}}}</span>
					<# if(data.price_position=="with-title"){ #>
						<span class="pricelist-price-with-title">{{{price_output}}} {{{dis_price_output}}}</span>
					<# } #>
					</span>
					<# if(data.line_position !== "title-bottom" && data.price_position=="right-to-title"){ #>
						{{{line_style_output}}}
					<# } #>
					<# if(data.price_position=="right-to-title"){ #>
						<span class="pricelist-price-content">{{{price_output}}} {{{dis_price_output}}}</span>
					<# } #>
				</span>
			</div>
		<# } 

		if(data.line_position == "title-bottom"){
		#>
			{{{line_style_output}}}
		<# } #>

		<div class="sppb-addon-content sp-editable-content" id="addon-text-{{data.id}}" data-id={{data.id}} data-fieldName="text">
			{{{data.text}}}
		</div>

		<# if(data.price_position=="content-bottom"){ #>
			<span class="pricelist-price-content bottom-of-content">{{{price_output}}} {{{dis_price_output}}}</span>
		<# } #>
		<# if(data.line_position !== "title-bottom" && data.price_position=="content-bottom"){ #>
			{{{line_style_output}}}
		<# } #>

		</div>
		</div>';
		return $output;
	}
}
