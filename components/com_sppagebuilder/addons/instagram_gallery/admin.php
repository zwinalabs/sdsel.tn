<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2018 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('_JEXEC') or die ('Restricted access');

SpAddonsConfig::addonConfig(
	array(
		'type'=>'content',
		'addon_name'=>'sp_instagram_gallery',
		'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GALLERY'),
		'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GALLERY_DESC'),
		'category'=>'Media',
		'attr'=>array(
			'general' => array(
				'admin_label'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_ADMIN_LABEL_DESC'),
					'std'=> ''
				),

				'title'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_DESC'),
					'std'=>  ''
				),

				'heading_selector'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_DESC'),
					'values'=>array(
						'h1'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H1'),
						'h2'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H2'),
						'h3'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H3'),
						'h4'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H4'),
						'h5'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H5'),
						'h6'=>JText::_('COM_SPPAGEBUILDER_ADDON_HEADINGS_H6'),
					),
					'std'=>'h3',
					'depends'=>array(array('title', '!=', '')),
				),

				'title_font_family'=>array(
					'type'=>'fonts',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_FAMILY'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_FAMILY_DESC'),
					'depends'=>array(array('title', '!=', '')),
					'selector'=> array(
						'type'=>'font',
						'font'=>'{{ VALUE }}',
						'css'=>'.sppb-addon-title { font-family: "{{ VALUE }}"; }'
					)
				),

				'title_fontsize'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_SIZE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_SIZE_DESC'),
					'std'=>'',
					'depends'=>array(array('title', '!=', '')),
					'max'=>400,
					'responsive'=>true
				),

				'title_lineheight'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_LINE_HEIGHT'),
					'std'=>'',
					'depends'=>array(array('title', '!=', '')),
					'max'=>400,
					'responsive'=>true
				),

				'title_font_style'=>array(
					'type'=>'fontstyle',
					'title'=> JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_FONT_STYLE'),
					'depends'=>array(array('title', '!=', '')),
				),

				'title_letterspace'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_GLOBAL_LETTER_SPACING'),
					'values'=>array(
						'0'=> 'Default',
						'1px'=> '1px',
						'2px'=> '2px',
						'3px'=> '3px',
						'4px'=> '4px',
						'5px'=> '5px',
						'6px'=>	'6px',
						'7px'=>	'7px',
						'8px'=>	'8px',
						'9px'=>	'9px',
						'10px'=> '10px'
					),
					'std'=>'0',
					'depends'=>array(array('title', '!=', '')),
				),

				'title_text_color'=>array(
					'type'=>'color',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_TEXT_COLOR'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_TEXT_COLOR_DESC'),
					'depends'=>array(array('title', '!=', '')),
				),

				'title_margin_top'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_TOP'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_TOP_DESC'),
					'placeholder'=>'10',
					'depends'=>array(array('title', '!=', '')),
					'max'=>400,
					'responsive'=>true
				),

				'title_margin_bottom'=>array(
					'type'=>'slider',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_MARGIN_BOTTOM_DESC'),
					'placeholder'=>'10',
					'depends'=>array(array('title', '!=', '')),
					'max'=>400,
					'responsive'=>true
				),

				'access_token'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_ACCESS_TOKEN'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_ACCESS_TOKEN_DESC'),
					'std'=>  ''
				),

				'item_resource'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GET_IMAGES_BY'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GET_IMAGES_BY_DESC'),
					'values'=>array(
						'userid' =>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GET_IMAGES_BY_USERID'),
						'hashtag'	 =>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GET_IMAGES_BY_HASHTAG'),
					),
					'std'=>'userid',
				),

				'user_id'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_USER_ID'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_USER_ID_DESC'),
					'std'=>  '',
					'depends'=>array(
						array('item_resource', '=', 'userid'),
					),
				),

				'hashtag'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_GET_IMAGES_BY_HASHTAG'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_HASHTAG_DESC'),
					'std'=>  '',
					'depends'=>array(
						array('item_resource', '=', 'hashtag'),
					),
				),

				'limit'=>array(
					'type'=>'number',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_ITEM_LIMIT'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_ITEM_LIMIT_DESC'),
					'std'=>  '4'
				),

				'thumb_per_row'=>array(
					'type'=>'number',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_THUMB_PER_ROW'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_TITLE_THUMB_PER_ROW_DESC'),
					'std'=>  '4'
				),

				'show_stats'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_SHOW_STATS'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_SHOW_STATS_DESC'),
					'values'=>array(
						'author'	=>JText::_('COM_SPPAGEBUILDER_FIELD_CREATED_BY_LABEL'),
						'caption'	=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_SHOW_STATS_CAPTION'),
						'likes'		=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_SHOW_STATS_LIKES_COUNT'),
						'comments'	=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_SHOW_STATS_COMMENTS_COUNT'),
					),
					'multiple'=>true,
				),

				'layout_type'=>array(
					'type'=>'select',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_LAYOUT_TYPE'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_LAYOUT_TYPE_DESC'),
					'values'=>array(
						'default'	=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_LAYOUT_TYPE_DEFAULT'),
						'classic'	=>JText::_('COM_SPPAGEBUILDER_ADDON_INSTAGRAM_LAYOUT_TYPE_CLASSIC'),
					),
					'std'=>'default',
				),

				'class'=>array(
					'type'=>'text',
					'title'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS'),
					'desc'=>JText::_('COM_SPPAGEBUILDER_ADDON_CLASS_DESC'),
					'std'=> ''
				),
			)
		)
	)
);
