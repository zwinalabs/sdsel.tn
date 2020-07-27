window.lsDefaultTr = [{
	transitionin: true,
	delayin: 0,
	durationin: 1000,
	easingin: "easeInOutQuint",
	fadein: true,
	offsetxin: 0,
	offsetyin: 0,
	rotatein: 0,
	rotatexin: 0,
	rotateyin: 0,
	scalexin: 1,
	scaleyin: 1,
	skewxin: 0,
	skewyin: 0,
	transformoriginin: "50% 50% 0",
	transformperspectivein: 500,
	clipin: "",
	widthin: "",
	heightin: "",
	radiusin: "",
	colorin: "",
	bgcolorin: "",
	filterin: "",
	texttransitionin: false,
	texttypein: "", // "chars_asc",
	textstartatin: "transitioninend",
	textdurationin: "", // 1000,
	texteasingin: "easeInOutQuint",
	textfadein: "true",
	textshiftin: 50,
	textoffsetxin: 0,
	textoffsetyin: 0,
	textrotatein: 0,
	textrotatexin: 0,
	textrotateyin: 0,
	textscalexin: 1,
	textscaleyin: 1,
	textskewxin: 0,
	textskewyin: 0,
	texttransformoriginin: "50% 50% 0",
	texttransformperspectivein: 500,
	static: "none"
}, {
	transitionout: true,
	startatout: "slidechangeonly + 0",
	durationout: 1000,
	easingout: "easeInOutQuint",
	fadeout: true,
	offsetxout: 0,
	offsetyout: 0,
	rotateout: 0,
	rotatexout: 0,
	rotateyout: 0,
	scalexout: 1,
	scaleyout: 1,
	skewxout: 0,
	skewyout: 0,
	transformoriginout: "50% 50% 0",
	transformperspectiveout: 500,
	clipout: "",
	widthout: "",
	heightout: "",
	radiusout: "",
	colorout: "",
	bgcolorout: "",
	filterout: ""
}, {
	loop: false,
	loopcount: 1,
	loopstartat: "allinend + 0",
	looprepeatdelay: 0,
	loopduration: 1000,
	loopeasing: "linear",
	loopopacity: 1,
	loopoffsetx: 0,
	loopoffsety: 0,
	looprotate: 0,
	looprotatex: 0,
	looprotatey: 0,
	loopscalex: 1,
	loopscaley: 1,
	loopskewx: 0,
	loopskewy: 0,
	looptransformorigin: "50% 50% 0",
	looptransformperspective: 500,
	loopclip: "",
	loopfilter: "",
	loopyoyo: false
}, {
	hover: false,
	hoverdurationin: 500,
	hovereasingin: "easeInOutQuint",
	hoverdurationout: "",
	hovereasingout: "",
	hovercolor: "",
	hoverbgcolor: "",
	hoverborderradius: "",
	hoveroffsetx: 0,
	hoveroffsety: 0,
	hoveropacity: "",
	hoverrotate: 0,
	hoverrotatex: 0,
	hoverrotatey: 0,
	hoverscalex: 1,
	hoverscaley: 1,
	hoverskewx: 0,
	hoverskewy: 0,
	hovertransformorigin: "50% 50% 0",
	hovertransformperspective: 500,
	hoveralwaysontop: true
}];

jQuery(function($) {

	var $document = $(document),
			$window = $(window),
			$body = $(document.body);

	var root = location.pathname.split('/');
	root.length -= 2;
	root = root.join('/');

	// put admin inside a div#wpwrap
	var $wpwrap = $('<div id="wpwrap">').appendTo($body);
	$wpwrap.siblings().appendTo($wpwrap);

	// remove message param from URL
	var message = location.href.match(/&message=[^&]*/);
	if (message && history.pushState) {
		history.pushState('', '', location.pathname + location.search.replace(message[0], ''));
	}

	// capture preview fix
	$('#system-message-container').parent('.span12').css('margin-left', 0);

	// Update sliders per page fix
	$('#ls-screen-options-form').submit(function(e) {
		e.stopPropagation();
		var options = {};
		$(this).find('input').each(function() {
			if( $(this).is(':checkbox')) {
				options[$(this).attr('name')] = $(this).prop('checked');
			} else {
				options[$(this).attr('name')] = $(this).val();
			}
		});

		// Save settings
		$.post(ajaxurl, $.param({ action : 'ls_save_screen_options', options : options }), function() {
			if(typeof reload != "undefined" && reload === true) {
				location.href = location.href;
			}
		});
	});

	// [post-url] override
	$('#ls-layers > .ls-box').on('click', '.ls-slide-link a', function(e) {
		e.preventDefault();
		e.stopPropagation();
		jQuery(this).closest('.ls-slide-link').find('input').val('[url]').trigger('input').trigger('change');
	});

	// filtering sliders fix
	$('<input>', {name: 'option', value: 'com_layer_slider', type: 'hidden'}).prependTo('#ls-slider-filters');
	$('#ls-slider-filters').on('change', 'select', function() { this.form.submit() });

	// autoplay revisions video fix
	$('#ls-revisions-welcome video[autoplay]').each(function() { this.play() });

	// Import sample sliders
	$('#ls-import-samples-button').click(function(e) {
		e.stopImmediatePropagation();
		e.preventDefault();
		var popup = $('#ls-import-sliders-template');
		if (!popup.length) popup = $('<div id="ls-import-sliders-template">').appendTo($body);
		var top = $('#screen-meta-links').offset().top;
		popup.attr('style', 'position: fixed !important;').css({
			top : top,
			opacity: 0,
			width: '100%',
			height: 'calc(100vh - '+top+'px)',
			zIndex: 9999991,
			overflowY: 'scroll'
		}).show().animate({ opacity: 1 }, 300);

		popup.on('click', '.ls-import-close', function() {
			popup.hide();
			$body.css('overflow', '').off('keyup.lsimport');
		});

		$body.css('overflow', 'hidden').on('keyup.lsimport', function(e) {
			if (e.keyCode == 27) $('.ls-import-close').click();
		});

		if (!popup.children().length) {
			$.ajax({
				url: location.protocol+'//offlajn.com/layerslider/',
				dataType: 'jsonp',
				success: function(data) {
					data.css.forEach(function(src) {
						$('<link type="text/css" rel="stylesheet">')
							.appendTo(document.head)
							.attr('href', src);
					});
					popup.html(data.html);
					popup.children().css('min-height', '100%'); // css fix
					var $x = $('<i class="flaticon-delete ls-import-close">').css({color: '#2f3d46', top: 10}).appendTo(popup);
					popup.scroll(function() { $x.css('top', popup.scrollTop() + 10) });
					$.ajax(data.js[0], {
						dataType: 'script',
						cache: true,
						complete: function() {
							popup.find('.filter-options').css('visibility', ''); // reveal tags
							$.getScript(location.protocol+'//offlajn.com/index2.php?option=com_ls_import&task=request&slider=test'); // check domain
						}
					});
				},
				error: function() {
					console.log(arguments)
				}
			});
			popup.on('click', 'a[data-import]', function onImport() {
				var $figure = $(this).closest('figure').addClass('loading');
				$('.logoload').css({
					position: 'absolute',
					display: 'block',
					opacity: 1
				}).appendTo($figure);
				$.getScript(location.protocol+'//offlajn.com/index2.php?option=com_ls_import&task=request&ver='+lsVersion+'&slider='+$(this).data('import'));
			});
			var slideDur = 400;
			popup.on('click', '.ls-warning-close', function closeWarning() {
				$(this).closest('.ls-warning-cont').slideUp(slideDur, function() { $(this).remove() });
			});
			function scrollUp() {
				var scroll = { y: popup.scrollTop() };
				scroll.y && TweenLite.to(scroll, 0.3, { y: 0, onUpdate: function() { popup.scrollTop(scroll.y) } });
			}
			window.lsImportError = function(msg) {
				scrollUp();
				$('.ls-warning-close').click();
				$('figure.loading').removeClass('loading').find('.logoload').hide();
				$('<div class="ls-warning-cont"><h3 class="flaticon-warning">' +
					'<p>'+msg+'</p><i class="flaticon-delete ls-warning-close"/>')
					.insertBefore('#grid').slideUp(0).slideDown(slideDur);
			};
			window.lsImportRegister = function(domain) {
				scrollUp();
				$('.ls-warning-close').click();
				$('figure.loading').removeClass('loading').find('.logoload').hide();
				$('<div class="ls-warning-cont"><h3 class="flaticon-warning">' +
					'<p>Please register your domain to use the import feature!</p>' +
					'<a class="ls-warning-close" href="http://offlajn.com/register-ls-domain/?domain='+domain+'" target="_blank">Register</a>')
					.insertBefore('#grid').slideUp(0).slideDown(slideDur);
			};
			window.lsImportRenew = function() {
				scrollUp();
				$('.ls-warning-close').click();
				$('figure.loading').removeClass('loading').find('.logoload').hide();
				$('<div class="ls-warning-cont"><h3 class="flaticon-warning">' +
					'<p>Import service access has expired<br/><span>Please renew your subscription to use it!</span></p>' +
					'<a class="ls-warning-close" href="http://offlajn.com/download-area/" target="_blank">Renew</a>')
					.insertBefore('#grid').slideUp(0).slideDown(slideDur);
			};
		}
	});

	// embed slider fix
	var embedSlider = function(a) {
		a.href = 'index.php?option=com_modules&task=module.add&eid='+lsModule.eid+'&params[slider]='+$(a).data('id');
		a.target = '_blank';
	};
	$('.ls-sliders-grid').on('click', 'a.embed', function(e) {
		e.stopImmediatePropagation();
		embedSlider(this);
	});

	// update slider action links
	$('#ls-slider-actions-template').on('click', 'a', function(e) {
		if ($(this).hasClass('embed')) {
			e.stopImmediatePropagation();
			embedSlider(this);
		} else {
			this.href = this.href.replace('page=layerslider', 'option=com_layer_slider');
			this.href = this.href.replace('admin.php?page=ls-revisions', 'index.php?option=com_layer_slider&view=revisions');
		}
	});

	// add module assignements to list view
	var $list = $('.ls-sliders-list');
	if ($list.length && location.search.indexOf('view=sliderlist') < 0) {
		var ids = [];
		$('<td>').html('Module assignments').insertBefore($list.find('thead .center'));
		$list.find('tbody .name').each(function() {
			ids[ids.length] = parseInt(this.parentNode.children[1].textContent);
			$('<td class="modpos">').insertAfter(this);
		});

		$.getJSON(ajaxurl, {action: 'ls_get_modules'}, function(data) {
			$list.find('.modpos').each(function(i) {
				lsModule = data;
				var html = '<a class="create_module_tag" href="index.php?option=com_modules&amp;task=module.add&amp;eid='+data.eid+'&amp;params[slider]='+ids[i]+'">Create module</a>';
				for (var j = 0; j < data.modules.length; j++) {
					var mod = data.modules[j];
					if (mod.params.slider == ids[i]) {
						html += '<a href="index.php?option=com_modules&amp;task=module.edit&amp;id='+mod.id+'" class="module_tag '+(mod.published == 1 ? 'published' : 'unpublished')+'">'+mod.title+'</a>';
					}
				}
				this.innerHTML = html;
			});
		});
	} else if (!window.lsSliderData) {
		$.getJSON(ajaxurl, {action: 'ls_get_modules'}, function(data) {
			lsModule = data;
		});
	}
	// replace [] to {} at shortcode
	$list.find('.ls-shortcode').each(function() {
		this.value = '{creative'+ this.value.slice(6, -1) +'}';
	});

	// open Google fonts tab
	$('.ls-plugin-settings-tabs .active').removeClass('active').next().addClass('active');
	$('.ls-plugin-settings .active').removeClass('active').next().addClass('active');

	// update advanced settings
	var $adv = $('.ls-global-settings');
	if ($adv.length) {
		$.get(ajaxurl, {action: 'ls_get_advanced_settings'}, function($data) {
			$adv.find('tbody').html($data).find(':checkbox').customCheckbox();
		});
	}

	// v5.x compatibility fix: add root to image URLs
	var imgs = '/images/';
	if (root) $('a.preview[style*="('+imgs+'"]').each(function() {
		this.style.backgroundImage = this.style.backgroundImage.replace(/(url\(['"]?)/, '$1'+root);
	});

	if (window.lsSliderData) { // EDIT VIEW
		// Schedule Slide
		var datepicker = $('.ls-slide-options .ls-datepicker-input').datepicker({
			position: 'right top',
			classes: 'ls-datepicker',
			dateFormat: 'yyyy-mm-dd',
			timeFormat: 'hh:ii',
			todayButton: new Date(),
			clearButton: true,
			timepicker: true,
			keyboardNav: false,
			range: false,

			onSelect: function(formattedDate, date, inst) {
				inst.$el.trigger('change');
			}

		}).each(function() {
			var $this = $(this),
					key = $(this).data('schedule-key'),
					startDate = new Date(lsSliderData.layers[LS_activeSlideIndex].properties[ key ]);
			if (startDate.getTime()) {
				$this.data('datepicker').selectDate(startDate);
				$this.trigger('keyup');
			}

		}).attr('pattern', '\\d{4}-\\d\\d?-\\d\\d?( \\d\\d?:\\d\\d?)?|');


		// EASY MODE
		var lsForm = $('#ls-slider-form')[0];
		var $easy = $('<a class="ls-checkbox small"><span>');

		$('#ls-easy-view').on('click', function onClickEasyMode(e) {
			e.stopImmediatePropagation();
			var easyOn = $easy.hasClass('on');
			$easy.addClass(easyOn ? 'off' : 'on').removeClass(!easyOn ? 'off' : 'on');
			window.localStorage && (localStorage.lsEasyMode = easyOn ? 'off' : 'on');
			runEasyMode();
		});

		function runEasyMode() {
			var easyOn = $easy.hasClass('on') && LS_activeLayerDataSet.length == 1;
			$easy.parent().css('display', LS_activeLayerDataSet.length == 1 ? '' : 'none');
			$('.ls-adv').css('display', !easyOn ? '' : 'none');
			$('.ls-easy').css('display', easyOn ? '' : 'none');

			if (easyOn) {
				var tr = LS_activeLayerDataSet[0].transition;
				var $col = $('.ls-easy-tr > ul');
				var include = [
					tr.transitionin === false ? [] : ['delayin', 'durationin', 'fadein'],
					['startatout', 'durationout', 'fadeout'],
					['loopcount', 'loopduration'],
					['hoverdurationin', 'hovercolor', 'hoverbgcolor']
				];
				lsDefaultTr.forEach(function(def, index) {
					if (
						index == 0 && tr.transitionin === false && !tr.texttransitionin ||
						index == 1 && tr.transitionout === false ||
						'loop' in def && !tr.loop || 'hover' in def && !tr.hover
					) {
						// show close icon after transition title / hide turned off transition properties
						return $col.eq(index).parent().addClass('ls-tr-off');
					}
					var easyTr = {};
					include[index].forEach(function(prop) {
						easyTr[prop] = prop in tr ? tr[prop] : def[prop];
					});
					for (var prop in def) {
						if (prop in tr && def[prop] !== (typeof def[prop] === 'number' ? Number(tr[prop]) : tr[prop])) {
							easyTr[prop] = tr[prop];
						}
						// leave cycle when text transition is turned off
						if (prop == 'texttransitionin' && !tr[prop]) break;
					}
					var $ul = $col.eq(index).html('');
					$ul.parent().removeClass('ls-tr-off');
					for (var prop in easyTr) {
						var $li = $(lsForm[prop]).closest('li');
						$li.clone().toggleClass('ls-easy-prop', !/fade|duration|startat|count|yoyo/.test(prop)).appendTo($ul);
						var $clone = $(lsForm[prop]).eq(0).val(easyTr[prop]);
						if ($clone.hasClass('ls-colorpicker')) {
							// init colorpicker
							$clone.parent().before($clone).remove();
							LayerSlider.addColorPicker($clone);
						} else if (~prop.indexOf('startat')) {
							// clone with modifier
							$li.next().clone().appendTo($ul);
						}
					}
					if (!index) {
						// highlight texttypein|parallaxlevel|static property if exists
						$(lsForm.texttypein).closest('.ls-easy-prop').addClass('ls-main-tr');
						if (tr.parallax) {
							$(lsForm.parallaxlevel).closest('li').clone().addClass('ls-easy-prop ls-main-tr').appendTo($ul);
						}
						if (tr.static && tr.static != 'none') {
							var staticLayer = $(lsForm.static.parentNode).prev().text();
							$li = $('<li><div>'+staticLayer+'</div></li>').addClass('ls-easy-prop ls-main-tr').appendTo($ul);
							$(lsForm.static).clone().val( $(lsForm.static).val() ).appendTo($li).wrap('<div>');
						}
					}
				});
			}
		}

		$(function initEasyMode() {
			$easy.addClass(window.localStorage && localStorage.lsEasyMode || 'on').prependTo('#ls-easy-view');

			$(LayerSlider).on('afterStartMultipleSelection', function() {
				$easy.parent().css('display', 'none');
				if (window.localStorage && localStorage.lsEasyMode != 'off') {
					$('.ls-easy').css('display', 'none');
					$('.ls-adv').css('display', '');
				}
			}).on('afterStopMultipleSelection', function() {
				$easy.removeClass('indeterminate').addClass(window.localStorage && localStorage.lsEasyMode || 'on')
					.parent().css('display', '');
			}).on('afterSelectMediaType', runEasyMode);

			$(LS_UndoManager).on('afterExecuteItem', function(e, cmd) {
				if (cmd === 'layer.transition' && $easy.hasClass('on')) {
					runEasyMode();
				}
			});
			runEasyMode();
		});

		$(document).on('click', '.ls-tr-off', function onClickAddTrType() {
			var prop = $(this).data('prop');
			$(lsForm[prop]).next().click();
			runEasyMode();

		}).on('click', '.ls-easy-tr .dashicons-no', function onClickRemoveTrType() {
			var prop = $(this).closest('.ls-easy-tr').data('prop');
			$(lsForm[prop]).next().click();
			runEasyMode();

		}).on('focus', '.ls-add-tr-prop', function onFocusTrPropList() {
			var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
			var $ul = $(this).prev();
			var index = $(this.parentNode).index();
			var group = ([
				{ transitionin: 1, texttransitionin: !/img|media/.test(LS_activeLayerDataSet[0].media) },
				{ transitionout: 1 },
				{ loop: 1 },
				{ hover: 1 }
			])[index];
			this.innerHTML = this.children[0].outerHTML;
			for (var prop in lsDefaultTr[index]) {
				if (group[prop]) {
					var optgroup = $(lsForm[prop]).closest('header').prev().text();
					optgroup && $('<optgroup>', { label: optgroup }).appendTo(this);
				} else if (lsForm[prop].name) {
					var opt = $(lsForm[prop]).closest('li').children(':first').text();
					opt && $('<option>', { value: prop, html: tab + opt }).appendTo(this);
				}
			}
			if (!index) {
				// Other Settings
				$('<optgroup>', { label: $('#ls-layer-transitions+.ls-separator').text() }).appendTo(this);
				lsForm.parallaxlevel.name && $('<option>', {
					value: 'parallaxlevel',
					html: tab + $(lsForm.parallaxlevel.parentNode).prev().text()
				}).appendTo(this);
				lsForm.static.name && $('<option>', {
					value: 'static',
					html: tab + $(lsForm.static.parentNode).prev().text()
				}).appendTo(this);
			}

		}).on('change', '.ls-add-tr-prop', function onAddTrProp() {
			var index = $(this).closest('.ls-easy-tr').index();
			var prop = $(this).val();
			var openingTrs = {transitionin: 0, texttransitionin: 1};
			for (var tr in openingTrs) {
				if (!index && prop.startsWith('text') == openingTrs[tr] && !lsForm[tr].checked) {
					// turn on opening|text transition & insert default properties
					$(lsForm[tr]).next('.off').click();
					runEasyMode();
					if (!lsForm[prop].name) {
						// don't clone property later, it was autoincluded
						var $clone = $(lsForm[prop][0]);
					}
				}
			}
			if (!$clone) {
				var $prev, $li = $(lsForm[prop]).closest('li');
				for (var p in lsDefaultTr[index]) {
					// search for prev property
					if (p == prop) break;
					if (!lsForm[p].name) $prev = $(lsForm[p][0]).closest('li');
				}
				$li.clone().toggleClass('ls-easy-prop', !/fade|duration/.test(prop)).insertAfter($prev)
					.toggleClass('ls-main-tr', /texttypein|parallaxlevel/.test(prop));
				$clone = $(lsForm[prop][0]);
				if ($clone.hasClass('ls-colorpicker')) {
					// init colorpicker
					$clone.parent().before($clone).remove();
					LayerSlider.addColorPicker($clone);
				} else if (~prop.indexOf('startat')) {
					// insert startatoperator & startatvalue too
					$li.next().clone().insertAfter($prev.next());
				}
			}
			if (prop == 'parallaxlevel') {
				$(lsForm.parallax).next('.off').click();
			} else if (prop == 'static') {
				// create static property
				var staticLayer = $(lsForm.static.parentNode).prev().text();
				$li = $('<li><div>'+staticLayer+'</div></li>').addClass('ls-easy-prop ls-main-tr').appendTo(this.previousElementSibling);
				$(lsForm.static).val('forever').change()
					.clone().val('forever').appendTo($li).wrap('<div>');
			}
			this.selectedIndex = 0;
			this.blur();
			$clone[0].select ? $clone[0].select() : $clone[0].focus();

		}).on('click', '.ls-easy-prop', function onRemoveTrProp(e) {
			if (e.target != this) return;
			// click on ::before (x)
			var prop = $(':input[name]', this).prop('name');
			var xProp = { transitionin: 'delayin', texttransitionin: 'texttypein', parallax: 'parallaxlevel' };
			for (var p in xProp) {
				if (prop == xProp[p]) {
					// turn off opening|text|parallax transition when property is removed
					$(lsForm[p]).next('.on').click();
					return runEasyMode();
				}
			}
			var undoObj = {}, redoObj = {};
			var def = lsDefaultTr[ $(this).closest('.ls-easy-tr').index() ];
			undoObj[prop] = $(lsForm[prop][1]).val();
			redoObj[prop] = def[prop];
			if (~prop.indexOf('startat')) {
				// remove startatoperator & startatvalue too
				$(':input[name]', this.nextElementSibling).each(function() {
					undoObj[this.name] = $(lsForm[this.name][1]).val();
					redoObj[this.name] = def[this.name]
				});
			}
			if (undoObj[prop] != redoObj[prop]) {
				var updateInfo = [{
					itemIndex: LS_activeLayerIndexSet[0],
					undo: undoObj,
					redo: redoObj
				}];
				$.extend(LS_activeLayerDataSet[0].transition, redoObj);
				LS_DataSource.buildLayer();
				LS_UndoManager.add('layer.transition', LS_l10n.SBUndoPasteSettings, updateInfo);
			}
			runEasyMode();

		}).on('input', '.ls-easy :input', function onSyncAdvProp() {
			// synchronize easy property value with advanced property
			this.name && $(lsForm[this.name][1]).val( $(this).val() );

		}).on('click', '.ls-easy .ls-checkbox', function onSyncAdvCheckbox() {
			if (this.previousSibling.name) {
				$(lsForm[this.previousSibling.name][1]).next().click();
			}
		});

		$(document).on('click', '.ls-sublayer-options .ls-preset', function onClickOpenPresets() {
			kmUI.modal.open('#tmpl-ls-layer-presets', {
				width: 795,
				height: 482,
				direction: '',
				animate: 'fade',
				theme: 'blue'
			});
			var title = document.createTextNode( $(this).prev().text() );
			kmUI.modal.$window.find('.header i').before(title);

			// check selected layer(s) media type
			var noTextTr = LS_activeLayerDataSet.some(function(layer) {
				return layer.media == 'img' || layer.media == 'media';
			});

			// build accordion menu
			var $menu = kmUI.modal.$window.find('.ls-side-menu');
			var presetIndex = $(this.parentNode).index();
			var presets = ([lsOpeningPresets, lsClosingPresets, lsLoopPresets, lsHoverPresets])[presetIndex];
			presets.layers.forEach(function(slide, i) {
				if (noTextTr && slide.sublayers[0].transition.texttransitionin) return;
				var $item = $('<div class="ls-menu-wrapper"><a class="ls-menu-item">'+slide.properties.title+'</a>').appendTo($menu);
				if (slide.sublayers.length > 1) {
					$item.children().append('<i class="dashicons dashicons-arrow-right">');
					var $submenu = $('<div class="ls-submenu">').appendTo($item);
				}
				slide.sublayers.forEach(function(layer, j) {
					if (~layer.subtitle.indexOf('color') && noTextTr) return;
					layer.image = lsTrImgPath + (layer.transition.hover ? 'sample_slide_2_hover.png' : 'sample_slide_2.png');
					layer.media = noTextTr ? 'img' : 'text';
					noTextTr ? layer.styles.padding = 0 : delete layer.styles.padding;
					if (slide.sublayers.length > 1) {
						$('<div class="ls-menu-wrapper"><a class="ls-menu-item">'+layer.subtitle+'</a>').appendTo($submenu);
					}
				});
			});

			// build sample slider
			var $slider = kmUI.modal.$window.find('.ls-container');
			LayerSlider.populateSliderPreview($slider, [], presets);
			$slider.layerSlider({
				type: 'responsive',
				width: 530,
				height: 360,
				autoStart: false,
				pauseOnHover: false,
				navButtons: false,
				navStartStop: false,
				showCircleTimer: false
			}).one('pageTimelineDidStart', function() {
				$menu.find('.ls-menu-wrapper:first').click();
			});

			var ls = _layerSliders[$slider.data('lsSliderUID')];
			$menu.on('click', '>.ls-menu-wrapper', function onClickPresetMainItem() {
				var $item = $(this).addClass('active');
				var $prev = $item.siblings('.active').removeClass('active');
				if ($item[0] != $prev[0]) {
					$item.find('.ls-submenu').slideDown(300);
					$prev.find('.ls-submenu').slideUp(300);
				}
				$item.find('.ls-menu-wrapper:first').addClass('active').siblings('.active').removeClass('active');

			}).on('click', '.ls-submenu .ls-menu-wrapper', function onClickPresetSubItem(e) {
				e.stopPropagation();
				$(this).addClass('active').siblings('.active').removeClass('active');

			}).on('click', '.ls-menu-wrapper', function onClickPresetMenuItem() {
				var $parent = $(this.parentNode).closest('.ls-menu-wrapper');
				var index = $parent.length ? $parent.index() : $(this).index();
				var subindex = $parent.length ? $(this).index() : 0;
				$slider.one('slideChangeDidStart', function() {
					var $layers = ls.layers.$all.filter('[data-ls-slidein='+index+']');
					var wrapper = $layers.data()._LS.hover.enabled ? '.ls-wrapper' : '*';
					$layers.closest(wrapper).addClass('ls-hidden');
					$layers.eq($layers.length - 1 - subindex).closest(wrapper).removeClass('ls-hidden');
					ls.functions.resetSlideTimelines();
					ls.transitions.layers.timeline.create(true);
				}).layerSlider(index);
				ls.slides.current.index == index && $slider.trigger('slideChangeDidStart');
			});

			$('#ls-choose-tr').click(function onClickChoosePreset() {
				var $active = $menu.find('>.active, >.active .active');
				var index = $active.eq(0).index() - 1;
				var subindex = $active.length > 1 ? $active.eq(1).index() : 0;
				var tr = $.extend({}, lsDefaultTr[presetIndex], presets.layers[index].sublayers[subindex].transition);
				// don't override start at options except textstartatin
				delete tr.delayin, delete tr.startatout, delete tr.loopstartat;

				var updateInfo = [];
				LS_activeLayerIndexSet.forEach(function(layerIndex, i) {
					var layerData = LS_activeLayerDataSet[i];
					var undoObj = {};
					var redoObj = $.extend({}, tr);
					for (var prop in redoObj) {
						undoObj[prop] = layerData.transition[prop];
					}

					updateInfo.push({
						itemIndex: layerIndex,
						undo: undoObj,
						redo: redoObj
					});

					$.extend(layerData.transition, redoObj);
					LS_DataSource.buildLayer();
				});

				LS_UndoManager.add('layer.transition', LS_l10n.SBUndoPasteSettings, updateInfo);

				$slider.layerSlider('destroy');
				kmUI.overlay.$element.click();
				runEasyMode();
			});
		});


		if (window.LS_img_path_fix) {
			$('<div class="ls-notification large error">').html('<div>'+LS_img_path_fix+'</div>').insertAfter('#ls-screen-options');
			$('.ls-img-path-fix').click(function(e) {
				e.preventDefault();
				kmUI.modal.open( '#tmpl-ls-img-path-fix', { width: 900, height: 1500 } );
			});
			$(document.body).on('click', '.ls-correct-img-path', function() {
				location.href += '&ls-img-path-fix=1';
			});
		}

		// v5.x compatibility fix: add root to image URLs, init thumbs, position fix
		var posFix = false, parallax = {};
		if (lsSliderData.properties) {
			var props = lsSliderData.properties;
			if (props.pauseonhover === true) $('select[name=pauseonhover]').val(props.pauseonhover = 'enabled');
			if (props.pauseonhover === false) $('select[name=pauseonhover]').val(props.pauseonhover = 'disabled');
			if (root && props.backgroundimage && props.backgroundimage.indexOf(imgs) == 0) {
				props.backgroundimage = root + props.backgroundimage;
			}
			props.backgroundimageThumb = props.backgroundimage;
			// slider background compatibility fixes
			if (props.background_size) props.globalBGSize = props.background_size, delete props.background_size;
			if (props.background_repeat) props.globalBGRepeat = props.background_repeat, delete props.background_repeat;
			if (props.background_position) props.globalBGPosition = props.background_position, delete props.background_position;
			if (props.background_behaviour) props.globalBGAttachment = props.background_behaviour, delete props.background_behaviour;
			// fullscreen compatibility fix
			if (props.fullpage && props.forceresponsive && !props.responsive) props.type = 'fullsize', delete props.fullpage;
			if (props.forceresponsive) $('input[name=maxRatio]').val(props.maxRatio = posFix = 1), delete props.forceresponsive;
			// parallax compatibility fix
			if (props.parallaxtype) {
				parallax.event = props.parallaxtype;
				delete props.parallaxtype;
				props.parallaxScrollReverse = true;
				$('input[name=parallaxScrollReverse]').prop('checked', true).next().addClass('on');
			}
			if ('parallaxscrollduration' in props) parallax.scrollduration = props.parallaxscrollduration, delete props.parallaxscrollduration;
		}
		for (var i = 0; i < lsSliderData.layers.length; i++) {
			var slide = lsSliderData.layers[i];
			if (root && slide.properties && slide.properties.background && slide.properties.background.indexOf(imgs) == 0) {
				slide.properties.background = root + slide.properties.background;
			}
			if (parallax.event) {
				slide.properties.parallaxdistance = 40;
				if (parallax.event == 'scroll') {
					slide.properties.parallaxevent = parallax.event;
					slide.properties.parallaxdurationmove = parallax.scrollduration;
				} else {
					slide.properties.parallaxevent = 'cursor';
					slide.properties.parallaxdurationmove = 450;
					slide.properties.parallaxdurationleave = 450;
				}
			}
			slide.properties.backgroundThumb = slide.properties.background;
			for (var j = 0; j < slide.sublayers.length; j++) {
				var layer = slide.sublayers[j];
				if (root && layer.image && layer.image.indexOf(imgs) == 0) {
					layer.image = root + layer.image;
				}
				if (posFix && layer.styles.top.indexOf('%') > 0 && layer.styles.left.indexOf('%') > 0) {
					layer.transition.position = 'fixed';
				}
				layer.imageThumb = layer.image;
			}
		}

		// wrap slideprops title to span
		$('.ls-slide-options .row-helper :input:first-child').each(function() {
			if (this.previousSibling && !this.previousElementSibling && this.previousSibling.textContent.trim()) {
				$(this.previousSibling).wrap('<span class="slideproptitle">');
			}
		});

		// Generate preview immediately
		LS_PostOptions._update = LS_PostOptions.update;
		LS_PostOptions.update = function(el, parsed) {
			clearTimeout(LayerSlider.timeout);
			LayerSlider.generatePreview();
			this._update(el, parsed);
		};

		// dynamic content generator
		var $post = $('#ls-post-options').on('change', '.ls-post-filters select', function() {
			lsSliderData.properties[ $(this).data('param') ] = $(this).val();
			LS_PostOptions.change(this);
		});
		if (~location.search.indexOf('action=edit')) {
			$.getJSON(ajaxurl, {action: 'ls_get_generators'}, function(data) {
				var $header = $post.find('.header');
				$header.contents()[0].textContent = data.header;
				var val, opts = '', type = 'generator_type';
				for (val in data.options) {
					opts += '<option value="'+ val +'">'+ data.options[val] + '</option>';
				}
				var $type = $('<select>', {'class': type, 'data-param': type, 'name': type})
					.html(opts)
					.insertAfter($header)
					.val(lsSliderData.properties.generator_type || 'imagesfromfolder')
					.wrap('<div class="inner clearfix">');
				$('<h3 class="subheader preview-subheader">').html(data.subheader).insertAfter($type.parent());

				$type.change(function() {
					// Get options
					var params = {};
					$post.find('select').each(function() {
						params[ $(this).data('param') ] = $(this).val();
					});
					// update generator type
					lsSliderData.properties.generator_type = $(this).val() || 'imagesfromfolder';

					$.post(ajaxurl, $.param({ action: 'ls_get_dynamic_filters', params: params }), function(data) {
						data = JSON.parse(data);
						// update selects
						$post.find('.ls-post-filters').html(data.filters);
						var $adv = $post.find('.ls-post-adv-settings select');
						$adv[0].innerHTML = $(data.orderby).html();
						var $tpl = $('#ls-layer-template');
						$tpl.html( $tpl.html().replace(/(ls-post-placeholders.*?>)[^]*?<\/ul>/, '$1'+data.taglist+'</ul>') );
						$('.ls-post-placeholders').html(data.taglist);
						// update select values
						$post.find('select').each(function(index) {
							if (index) $(this).val(lsSliderData.properties[ $(this).data('param') ]);
							if (this.selectedIndex < 0) this.selectedIndex = 0;
						});
						// update post options
						LS_PostOptions.change($post[0]);
					});
				}).change();
			});
		}

		// bugfix: zoom slider disappears
		var $zoom = $('.ls-editor-slider').on('slide.bugfix', function() {
			$zoom.parent('.ls-editor-zoom').length || $zoom.off('slide.bugfix').attr('style', '').unwrap();
		});

		// replace [] to {} at shortcode
		$('.ls-save-shortcode span:last-child').each(function() {
			this.innerHTML = '{creative'+ this.innerHTML.slice(6, -1) +'}';
			return false;
		});

		// session check on save
		$(document).ajaxComplete(function(e, xhr, settings) {
			if (settings.data && settings.data.length && settings.data.indexOf('action=ls_save_slider') === 0) {
				try {
					var resp = JSON.parse(xhr.responseText);
				} catch (ex) {
					adminLogin('Please login at the following popup window, to save your slider', function() { LayerSlider.save() });
				}
			}
		});

		var $prev = $('<div class="ls-prev-slide button"><span class="dashicons dashicons-controls-skipback">');
		var $next = $('<div class="ls-next-slide button"><span class="dashicons dashicons-controls-skipforward">');
		var $preview = $('<div class="ls-preview-btn button">\
			<svg viewbox="40 40 60 60" class="ls-preview-icon" style="width:16px; height: 16px;">\
				<polygon points="50,40 100,70 100,70 50,100, 50,40" fill="#fff">\
					<animate begin="indefinite" fill="freeze" attributeName="points" dur="500ms" calcMode="spline"\
						to="45,45 95,45 95,95, 45,95 45,45" keyTimes="0; 0.22; 0.33; 0.55; 0.66; 0.88; 1"\
						keySplines="0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1" />\
					<animate begin="indefinite" fill="freeze" attributeName="points" dur="500ms" calcMode="spline"\
						to="50,40 100,70 100,70 50,100, 50,40" keyTimes="0; 0.22; 0.33; 0.55; 0.66; 0.88; 1"\
						keySplines="0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1; 0.1 0.8 0.2 1" />');
		$('<div class="ls-preview-nav">').append($prev, $preview, $next).appendTo('.ls-preview-td');
		var $animate = $preview.find('animate');

		$preview.click(function() {
			$('.ls-preview-button').click();
		});

		$prev.click(function() {
			if ($preview.hasClass('playing')) {
				$('#ls-preview-timeline .ls-nav-prev').click();
			} else {
				$('#ls-layer-tabs .active').prev().click().length || $('#ls-layer-tabs a:not(.unsortable):last').click();
			}
		});

		$next.click(function() {
			if ($preview.hasClass('playing')) {
				$('#ls-preview-timeline .ls-nav-next').click();
			} else {
				$('#ls-layer-tabs .active').next('a:not(.unsortable)').click().length || $('#ls-layer-tabs :first').click();
			}
		});

		new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (mutation.attributeName == 'class') {
					var playing = $preview.hasClass('playing') ? 1 : 0;
					setTimeout(function() {
						$animate[playing].beginElement();
						TweenLite.fromTo($preview[0].children[0], 0.3, {rotation: -90 * playing}, {rotation: '+=90'});
					}, 100 * playing + 1);
					$preview.toggleClass('playing');
				}
			});
		}).observe($('.ls-preview-button')[0], {attributes: true});

	}

	// Skin/CSS Editor
	if(location.href.indexOf('view=skineditor') != -1) {
		$('select[name="skin"]').change(function(e) {
			e.stopImmediatePropagation();
			location.href = 'index.php?option=com_layer_slider&view=skineditor&skin=' + $(this).children(':selected').val();
		});
	}

	/**
	 * Screen Options tab
	 */
	screenMeta = {
		element: null, // #screen-meta
		toggles: null, // .screen-meta-toggle
		page:    null, // #wpcontent

		init: function() {
			this.element = $('#screen-meta');
			this.toggles = $( '#screen-meta-links' ).find( '.show-settings' );
			this.page    = $('#wpcontent');

			this.toggles.click( this.toggleEvent );
		},

		toggleEvent: function() {
			var panel = $( '#' + $( this ).attr( 'aria-controls' ) );

			if ( !panel.length )
				return;

			if ( panel.is(':visible') )
				screenMeta.close( panel, $(this) );
			else
				screenMeta.open( panel, $(this) );
		},

		open: function( panel, button ) {

			$( '#screen-meta-links' ).find( '.screen-meta-toggle' ).not( button.parent() ).css( 'visibility', 'hidden' );

			panel.parent().show();
			panel.slideDown( 'fast', function() {
				panel.focus();
				button.addClass( 'screen-meta-active' ).attr( 'aria-expanded', true );
			});

			$document.trigger( 'screen:options:open' );
		},

		close: function( panel, button ) {
			panel.slideUp( 'fast', function() {
				button.removeClass( 'screen-meta-active' ).attr( 'aria-expanded', false );
				$('.screen-meta-toggle').css('visibility', '');
				panel.parent().hide();
			});

			$document.trigger( 'screen:options:close' );
		}
	};

	$(function() { screenMeta.init() });

	/**
	 * Help tabs.
	 */
	$('.contextual-help-tabs').delegate('a', 'click', function(e) {
		var link = $(this),
			panel;

		e.preventDefault();

		// Don't do anything if the click is for the tab already showing.
		if ( link.is('.active a') )
			return false;

		// Links
		$('.contextual-help-tabs .active').removeClass('active');
		link.parent('li').addClass('active');

		panel = $( link.attr('href') );

		// Panels
		$('.help-tab-content').not( panel ).removeClass('active').hide();
		panel.addClass('active').show();
	});

	// blur fix for combobox
	if (window.kmComboBox) {
		kmComboBox._show = kmComboBox.show;
		kmComboBox.show = function($input) {
			kmComboBox._show.call(this, $input);
			var $wrapper = $input.next();
			var pos = $wrapper.attr('style').match(/translate3d\(([-\d.]+px), ([-\d.]+px)/i);
			$wrapper.css('transform', '')[0].attributes.style.value += ' margin: '+(parseInt(pos[2]) - 6)+'px '+pos[1]+' !important;';
		};
	}

	// add skin chooser
	$('<select name="adminSkin"><option value="light">Light Skin</option><option value="dark">Dark Skin</option>').change(function() {
		$.get(ajaxurl, { action: 'ls_save_admin_skin', skin: $(this).val() });
		$(document.body).removeClass('layout-light layout-dark').addClass('layout-'+$(this).val());
	}).val($(document.body).hasClass('layout-light') ? 'light' : 'dark')
		.prependTo('#ls-screen-options-form')
		.before('<h5>Backend skin</h5>');

});

adminLogin = function(msg, callback) {
	if (SqueezeBox.isOpen) SqueezeBox.close();
	SqueezeBox.removeEvents('open');
	SqueezeBox.open('', {
		size: {x: 400, y: 80},
		onOpen: function() {
			this.options.closable = false;
			this.closeBtn.style.display = 'none';
			this.content.style.textAlign = 'center';
			this.content.innerHTML = '<strong>Your session has expired!</strong><br>'+ msg +'.<br><br>';
			jQuery('<input>').attr({
				type: 'button',
				value: '    OK    '
			}).click(function() {
				window.open(document.location.href.split('?')[0]+"?option=com_layer_slider&view=autologin", "backend",
					"width=630,height=434,screenX="+(screen.width/2 - 315)+",screenY="+(screen.height/2 - 217)).focus();
			}).appendTo(this.content);
		},
		onClose: function() {
			this.options.closable = true;
			this.closeBtn.style.display = '';
			this.content.style.textAlign = '';
			jQuery(window).off('.mediamanager');
			if (typeof callback == 'function') callback();
		}
	});
};

/**
 * Fake object to simulate WP's media manager
 */
wpMediaFrame = {
	folder: '',
	open: function() {
		$imgInput = jQuery(uploadInput).prev();
		$imgNode = jQuery(uploadInput).find('img');
		var folder = $imgInput.val().match(/\/images\/(.*\/|)/);
		this.folder = folder ? folder[1] : this.folder;
		SqueezeBox.removeEvents('open');
		SqueezeBox.open('index.php?option=com_media&view=images&tmpl=component&folder='+ this.folder +'&e_name=image', {
			handler: 'iframe',
			size: {x:800, y:653},
			onOpen: function() {
				var iframe = this.asset;
				iframe.contentWindow.self = iframe.contentWindow.top; // fix for expired session
				iframe.onload = function() {
					var doc = iframe.contentDocument || iframe.contentWindow.document;
					doc.documentElement.style.overflowY = doc.body.style.overflowY = 'hidden';
					// session check
					if (jQuery('input[name=username]', doc).length) return adminLogin('Please login at the following popup window');
					// hide unnessesry fields
					var $a = jQuery('label[for=f_align]', doc);
					if ($a.parent().is('td')) {	// J!2.5
						$a.parent().css('display', 'none').next().css('display', 'none').next().css('display', 'none');
						if ($imgInput.attr('name') == 'background')
							jQuery('#f_title', doc).parent().parent().css('display', 'none');
						jQuery('label[for=f_caption]', doc).parent().css('display', 'none').next().css('display', 'none').next().css('display', 'none');
					} else {	// J!3.X
						$a.parent().parent().css('display', 'none');
						if ($imgInput.attr('name') == 'background')
							jQuery('#f_title', doc).parent().parent().css('display', 'none');
						jQuery('#f_caption', doc).parent().parent().css('display', 'none');
						jQuery('#f_caption_class', doc).parent().parent().css('display', 'none');
					}
					// add transparent bg
					var imgframe = doc.getElementById('imageframe');
					imgframe.onload = function() {
						jQuery('<style>')
							.html('.img-preview .height-50 img, .item a img {width:100%; height:100%; object-fit:contain}'+
								'.img-preview .height-50, .item a {background: #ccc url(data:image/svg+xml;base64,'+
								'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0'+
								'PSIxNiI+PHJlY3QgZmlsbD0iI2ZmZiIgd2lkdGg9IjgiIGhlaWdodD0iOCIvPjxyZWN0IGZpbGw9'+
								'IiNmZmYiIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHg9IjgiIHk9IjgiLz48L3N2Zz4=) repeat !important}')
							.appendTo((imgframe.contentDocument || imgframe.contentWindow.document).head);
					};
					imgframe.onload();
				};
			}
		});
	},
	on: function(event, handler) {
		this['on'+event] = handler;
	},
	trigger: function(event) {
		this['on'+event]();
	},
	state: function() {
		return this._state;
	},
	_state: {
		get: function(name) {
			return this[name];
		},
		selection: {
			first: function() {
				this._first = true;
				return this;
			},
			toJSON: function() {
				var first = this._first;
				this._first = false;
				return first ? this.data[0] : this.data;
			},
			data: []
		}
	}
};

function jInsertEditorText(tag, name) {
	var $tag = jQuery(tag);
	var src = userSettings.url + $tag.attr('src');
	wpMediaFrame._state.selection.data = [{
		id: '',
		url: src,
		sizes: {
			full: { url: src }
		}
	}];

	var folder = src.match(/\/images\/(.*\/|)/);
	if (folder) wpMediaFrame.folder = folder[1];

	wpMediaFrame.trigger('select');
	// TODO
	// var $tr = $imgInput.parents('tr'),
	// var alt = $tag.attr('alt');
	// var title = $tag.attr('title');
	// if (alt) $tr.find('input[name$=alt]').val(alt);
	// if (title) $tr.find('input[name=title]').val(title);
	// LayerSlider.willGeneratePreview( jQuery('.ls-box.active').index() );
}

wp = {
	media: function() {
		return wpMediaFrame;
	}
};