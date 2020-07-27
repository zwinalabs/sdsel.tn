/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2018 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/

(function($){

	window._layerSlider.plugins.popup = function( ls, $slider, sliderUID, userSettings ){

		var pu = 	this,
					forceSliderSettings = {
						startInViewport: false,
						playByScroll: false,
						allowFullscreen: false,
						insertSelector: null
					};

		pu.pluginData = {
			name: 'Popup Slider Plugin for CreativeSlider',
			version: '1.2',
			requiredLSVersion: '6.6',
			authorName: 'Kreatura',
			releaseDate: '2017. 10. 04.'
		};

		pu.defaults = {

			plugin: {
				eventNamespace: 'PU'
			},

			keys: {
				popupShowOnClick:				['plugin','settings'],
				popupShowOnScroll: 				['plugin','settings'],
				popupShowOnLeave: 				['plugin','settings'],
				popupShowOnIdle: 				['plugin','settings'],
				popupShowOnTimeout: 			['plugin','settings'],
				popupCloseOnScroll: 			['plugin','settings'],
				popupCloseOnTimeout: 			['plugin','settings'],
				popupCloseOnSliderEnd: 			['plugin','settings'],

				popupShowOnce: 					['plugin','settings'],
				popupDisableOverlay: 			['plugin','settings'],
				popupShowCloseButton: 			['plugin','settings'],
				popupCloseButtonStyle: 			['plugin','settings'],
				popupOverlayClickToClose: 		['plugin','settings'],
				popupStartSliderImmediately: 	['plugin','settings'],
				popupResetOnClose: 				['plugin','settings'],

				popupWidth: 					['popup','styleSettings'],
				popupHeight: 					['popup','styleSettings'],
				popupFitWidth: 					['popup','styleSettings'],
				popupFitHeight:					['popup','styleSettings'],
				popupCustomStyle: 				['popup','styleSettings'],
				popupPositionHorizontal: 		['popup','styleSettings'],
				popupPositionVertical:			['popup','styleSettings'],
				popupDistanceLeft: 				['popup','styleSettings'],
				popupDistanceRight: 			['popup','styleSettings'],
				popupDistanceTop: 				['popup','styleSettings'],
				popupDistanceBottom: 			['popup','styleSettings'],

				popupDurationIn: 				['popup','transitionSettings'],
				popupDurationOut: 				['popup','transitionSettings'],
				popupDelayIn: 					['popup','transitionSettings'],
				popupEaseIn: 					['popup','transitionSettings'],
				popupEaseOut: 					['popup','transitionSettings'],
				popupTransitionIn: 				['popup','transitionSettings'],
				popupTransitionOut: 			['popup','transitionSettings'],
				popupCustomTransitionIn: 		['popup','transitionSettings'],
				popupCustomTransitionOut: 		['popup','transitionSettings'],

				popupOverlayBackground: 		['overlay','styleSettings'],

				popupOverlayDurationIn: 		['overlay','transitionSettings'],
				popupOverlayDurationOut: 		['overlay','transitionSettings'],
				popupOverlayEaseIn: 			['overlay','transitionSettings'],
				popupOverlayEaseOut: 			['overlay','transitionSettings'],
				popupOverlayTransitionIn: 		['overlay','transitionSettings'],
				popupOverlayTransitionOut: 		['overlay','transitionSettings']
			}
		};

		pu.init = function(){

			// SET: plugin options
			var keyPath,
				keyInside,
				keyInsideLower;

			for( var key in pu.defaults.keys ){
				keyPath = pu[pu.defaults.keys[key][0]][pu.defaults.keys[key][1]];
				if( ls.o.hasOwnProperty( key ) ){
					keyInside = key.split('popup')[1];
					keyInside = keyInside.substr(0,1).toLowerCase() + keyInside.substr(1);

					if( ls.o[key] !== keyPath[keyInside] ){

						keyInsideLower = keyInside.toLowerCase();

						if( keyInsideLower.indexOf( 'ease' ) !== -1 ){
							ls.o[key] = ls.functions.convert.easing( ls.o[key] );
						}
						if( ( keyInsideLower.indexOf( 'duration' ) !== -1 || keyInsideLower.indexOf( 'delay' ) !== -1 ) && typeof ls.o[key] === 'number' ){
							ls.o[key] /= 1000;
						}
						keyPath[keyInside] = ls.o[key];
					}
				}
			}

			pu.plugin.setOptions();
			pu.slider.setOptions();
			pu.popup.createMarkup();
			pu.popup.setStyles();
			pu.popup.setTransitions();
			pu.slider.setStyles();
			pu.overlay.createMarkup();
			pu.overlay.setTransitions();
			pu.events.set();
		};

		pu.plugin = {

			settings: {
				showOnClick: false,
				showOnScroll: false,
				showOnLeave: false,
				showOnIdle: false,
				showOnTimeout: false,
				closeOnScroll: false,
				closeOnTimeout: false,
				closeOnSliderEnd: false,
				showOnce: true,
				disableOverlay: false,
				showCloseButton: true,
				closeButtonStyle: '',
				overlayClickToClose: true,
				startSliderImmediately: false,
				resetOnClose: 'slide'
			},

			setOptions: function(){

				// SET: trigger elements for click event
				this.settings.$clickTriggerElements = $( this.settings.showOnClick ).add( '[data-ls-popup-trigger-for="' + $slider.attr( 'id' ) + '"], [href="#' + $slider.attr( 'id' ) + '"], .' + $slider.attr( 'id' ) );

				$( '[href="#' + $slider.attr( 'id' ) + '"]' ).on( 'click.' + pu.defaults.plugin.eventNamespace + sliderUID, function( e ){
					e.preventDefault();
				});

				// SET: popup initial width
				if( $slider[0].style.width && $slider[0].style.width.indexOf( 'px' ) !== -1 && !ls.o.popupWidth ){
					pu.popup.styleSettings.width = parseInt( $slider[0].style.width );
				}

				// SET: popup initial height
				if( $slider[0].style.height && $slider[0].style.height.indexOf( 'px' ) !== -1 && !ls.o.popupHeight ){
					pu.popup.styleSettings.height = parseInt( $slider[0].style.height );
				}

				// GET: horizontal position from slider user init options
				if( ls.o.popupPositionHorizontal &&
					( ls.o.popupPositionHorizontal === 'left' || ls.o.popupPositionHorizontal === 'right' )
				){
				// SET: horizontal position
					pu.popup.styleSettings.positionHorizontal = ls.o.popupPositionHorizontal;
				}
				if( ls.o.type === 'fullwidth' ){
					pu.popup.styleSettings.positionHorizontal = 'left';
				}

				// GET: vertical position from slider user init options
				if( ls.o.popupPositionVertical &&
					( ls.o.popupPositionVertical === 'top' || ls.o.popupPositionVertical === 'bottom' )
				){
				// SET: vertical position
					pu.popup.styleSettings.positionVertical = ls.o.popupPositionVertical;
				}

				// GET: horizontal distances from slider user init options
				if( ls.o.popupDistanceLeft ){
					pu.popup.styleSettings.distanceLeft = ls.o.popupDistanceLeft;
				}
				if( ls.o.popupDistanceRight ){
					pu.popup.styleSettings.distanceRight = ls.o.popupDistanceRight;
				}

				// GET: vertical distances from slider user init options
				if( ls.o.popupDistanceTop ){
					pu.popup.styleSettings.distanceTop = ls.o.popupDistanceTop;
				}
				if( ls.o.popupDistanceBottom ){
					pu.popup.styleSettings.distanceBottom = ls.o.popupDistanceBottom;
				}

				// SET: popup close on slider end event
				if( pu.plugin.settings.closeOnSliderEnd ){
					$slider.on( 'slideChangeWillStart', function( event, slider ){
						if( slider.slides.current.index === slider.slides.count ){
							pu.events.hide();
							return false;
						}
					});
				}
			}
		};

		pu.overlay = {

			styleSettings: {
				overlayBackground: 'rgba(0,0,0,.85)'
			},

			transitionSettings: {
				overlayDurationIn: 0.4,
				overlayDurationOut: 0.4,
				overlayEaseIn: ls.gsap.Quint.easeIn,
				overlayEaseOut: ls.gsap.Quint.easeIn,
				overlayTransitionIn: 'fade',
				overlayTransitionOut: 'fade'
			},

			createMarkup: function(){

				// APPEND: overlay element into body
				pu.$overlay = $( '<div>' ).addClass( 'ls-popup-overlay' ).css({
					background: this.styleSettings.overlayBackground
				}).insertBefore( pu.$popup );
			},

			setTransitions: function(){

				// SET: transition in
				this.transitionSettings.styleInFrom = {
					opacity: 1,
					display: 'block'
				};
				this.transitionSettings.styleInTo = {
					ease: this.transitionSettings.overlayEaseIn,
					opacity: 1,
					x: 0,
					y: 0,
					z: 0,
					rotation: 0,
					rotationX: 0,
					rotationY: 0,
					scaleX: 1,
					scaleY: 1,
					skewX: 0,
					skewY: 0,
					borderRadius: 0
				};

				// SET: transition out
				this.transitionSettings.styleOutTo = {
					ease: this.transitionSettings.overlayEaseOut,
					onComplete: function(){
						ls.gsap.TweenMax.set( pu.$overlay[0], {
							display: 'none',
							x: 0,
							y: 0,
							opacity: 0
						});
					}
				};
			},

			show: function(){

				// SET: transition in
				switch( this.transitionSettings.overlayTransitionIn.toLowerCase() ){
					default:
					case 'fade':
						this.transitionSettings.styleInFrom.opacity = 0;
					break;
					case 'slidefromtop':
						this.transitionSettings.styleInFrom.y = -ls.device.viewportHeight;
					break;
					case 'slidefrombottom':
						this.transitionSettings.styleInFrom.y = ls.device.viewportHeight;
					break;
					case 'slidefromleft':
						this.transitionSettings.styleInFrom.x = -ls.device.viewportWidth;
					break;
					case 'slidefromright':
						this.transitionSettings.styleInFrom.x = ls.device.viewportWidth;
					break;
					case 'fadefromtopleft':
						this.transitionSettings.styleInFrom.x = -ls.device.viewportWidth;
						this.transitionSettings.styleInFrom.y = -ls.device.viewportHeight;
						this.transitionSettings.styleInFrom.opacity = 0;
					break;
					case 'fadefromtopright':
						this.transitionSettings.styleInFrom.x = ls.device.viewportWidth;
						this.transitionSettings.styleInFrom.y = -ls.device.viewportHeight;
						this.transitionSettings.styleInFrom.opacity = 0;
					break;
					case 'fadefrombottomleft':
						this.transitionSettings.styleInFrom.x = -ls.device.viewportWidth;
						this.transitionSettings.styleInFrom.y = ls.device.viewportHeight;
						this.transitionSettings.styleInFrom.opacity = 0;
					break;
					case 'fadefrombottomright':
						this.transitionSettings.styleInFrom.x = ls.device.viewportWidth;
						this.transitionSettings.styleInFrom.y = ls.device.viewportHeight;
						this.transitionSettings.styleInFrom.opacity = 0;
					break;
					case 'scale':
						this.transitionSettings.styleInFrom.scaleX = 0;
						this.transitionSettings.styleInFrom.scaleY = 0;
					break;
				}

				// START: transition in
				ls.gsap.TweenMax.fromTo( pu.$overlay[0], this.transitionSettings.overlayDurationIn, this.transitionSettings.styleInFrom, this.transitionSettings.styleInTo );
			},

			hide: function(){

				// SET: transition out
				switch( this.transitionSettings.overlayTransitionOut.toLowerCase() ){
					default:
					case 'fade':
						this.transitionSettings.styleOutTo.opacity = 0;
					break;
					case 'slidetotop':
						this.transitionSettings.styleOutTo.y = -ls.device.viewportHeight;
					break;
					case 'slidetobottom':
						this.transitionSettings.styleOutTo.y = ls.device.viewportHeight;
					break;
					case 'slidetoleft':
						this.transitionSettings.styleOutTo.x = -ls.device.viewportWidth;
					break;
					case 'slidetoright':
						this.transitionSettings.styleOutTo.x = ls.device.viewportWidth;
					break;
					case 'fadetotopleft':
						this.transitionSettings.styleOutTo.x = -ls.device.viewportWidth;
						this.transitionSettings.styleOutTo.y = -ls.device.viewportHeight;
						this.transitionSettings.styleOutTo.opacity = 0;
					break;
					case 'fadetotopright':
						this.transitionSettings.styleOutTo.x = ls.device.viewportWidth;
						this.transitionSettings.styleOutTo.y = -ls.device.viewportHeight;
						this.transitionSettings.styleOutTo.opacity = 0;
					break;
					case 'fadetobottomleft':
						this.transitionSettings.styleOutTo.x = -ls.device.viewportWidth;
						this.transitionSettings.styleOutTo.y = ls.device.viewportHeight;
						this.transitionSettings.styleOutTo.opacity = 0;
					break;
					case 'fadetobottomright':
						this.transitionSettings.styleOutTo.x = ls.device.viewportWidth;
						this.transitionSettings.styleOutTo.y = ls.device.viewportHeight;
						this.transitionSettings.styleOutTo.opacity = 0;
					break;
					case 'scale':
						this.transitionSettings.styleOutTo.scaleX = 0;
						this.transitionSettings.styleOutTo.scaleY = 0;
					break;
				}

				// START: transition out
				ls.gsap.TweenMax.to( pu.$overlay[0], this.transitionSettings.overlayDurationOut, this.transitionSettings.styleOutTo );
			}
		};

		pu.popup = {

			styleSettings: {
				width: 640,
				height: 360,
				fitWidth: false,
				fitHeight: false,
				customStyle: '',
				positionHorizontal: 'center',
				positionVertical: 'middle',
				distanceLeft: 10,
				distanceRight: 10,
				distanceTop: 10,
				distanceBottom: 10
			},

			transitionSettings: {
				durationIn: 1,
				durationOut: 0.5,
				delayIn: 0.2,
				easeIn: ls.gsap.Quint.easeInOut,
				easeOut: ls.gsap.Quint.easeIn,
				transitionIn: 'fade',
				transitionOut: 'fade',
				customTransitionIn: false,
				customTransitionOut: false
			},

			createMarkup: function(){

				var $popupMarkup;

				// CREATE: markup
				if( $slider.parent().is( '.ls-popup' ) ){
					$popupMarkup = $slider.parent();
				}else{
					$popupMarkup = $( '<div class="ls-popup">' );
				}

				// APPEND: popup to body
				$( 'body' ).append( $popupMarkup.append( $( '<div class="ls-popup-inner">' ).append( $slider) ) );

				$slider.attr( 'style', $slider.attr( 'style' ) + '; ' + pu.popup.styleSettings.customStyle );
				pu.$inner = $slider.parent();
				pu.$popup = pu.$inner.parent();

				if( pu.plugin.settings.showCloseButton ){
					$slider.append( '<div class="ls-popup-close-button" style="' + pu.plugin.settings.closeButtonStyle + '"></div>' );
				}
			},

			setStyles: function(){

				// SET: styles of the .ls-popup element
				pu.$popup.css({
					left: pu.popup.styleSettings.distanceLeft,
					right: pu.popup.styleSettings.distanceRight,
					top: pu.popup.styleSettings.distanceTop,
					bottom: pu.popup.styleSettings.distanceBottom
				});

				// SET: styles of the .ls-popup-inner element
				pu.$inner.css({
					maxWidth: ls.slider.initial ? ls.slider.initial.maxWidth : $slider[0].style.maxWidth || $slider.css( 'max-width' ),
					maxHeight: ls.slider.initial && ls.slider.initial.maxHeight ? ls.slider.initial.maxHeight : $slider[0].style.maxHeight || $slider.css( 'max-height' ),
					width: pu.popup.styleSettings.width,
					height: pu.popup.styleSettings.height
				});
			},

			setTransitions: function(){

				// SET: transition in
				this.transitionSettings.styleInFrom = {
					transformPerspective: 500,
					transformOrigin: '50% 50%'
				};

				if( typeof this.transitionSettings.customTransitionIn === 'object' ){
					this.transitionSettings.styleInFrom = $.extend( {}, this.transitionSettings.styleInFrom, this.transitionSettings.customTransitionIn );
				}

				this.transitionSettings.styleInTo = {
					opacity: 1,
					x: 0,
					y: 0,
					z: 0,
					rotation: 0,
					rotationX: 0,
					rotationY: 0,
					scaleX: 1,
					scaleY: 1,
					skewX: 0,
					skewY: 0,
					delay: this.transitionSettings.delayIn,
					ease: this.transitionSettings.easeIn,
					onStart: function(){
						// START: slider
						if( pu.plugin.settings.startSliderImmediately ){
							$slider.layerSlider( 'resumePopup' );
						}
					},
					onComplete: function(){

						// API CALL: popupDidOpen
						if( ls.api.hasEvent( 'popupDidOpen' ) ){
							$slider.triggerHandler( 'popupDidOpen', ls.api.eventData() );
						}

						// START: slider
						ls.slider.state.popupShouldStart = true;
						if( !pu.plugin.settings.startSliderImmediately ){
							$slider.layerSlider( 'resumePopup' );
						}
						if( typeof pu.plugin.settings.closeOnTimeout === 'number' ){
							setTimeout( function(){ pu.events.hide() }, pu.plugin.settings.closeOnTimeout * 1000 );
						}
						if( pu.plugin.settings.showOnScroll ||  pu.plugin.settings.closeOnScroll ){
							pu.events.checkScrollPositions();
						}
					}
				};

				// SET: transition out
				this.transitionSettings.styleOutTo = {
					ease: this.transitionSettings.easeOut,
					transformOrigin: '50% 50%',
					onComplete: function(){
						if( pu.plugin.settings.startSliderImmediately ){
							$slider.layerSlider( 'pause' );
						}
						pu.$popup.removeClass( 'ls-popup-visible' );
						ls.slider.state.popupIsVisible = false;
						ls.slider.state.popupShouldStart = false;

						if( pu.plugin.settings.showOnce ){
							// API CALL: popupDidClose
							if( ls.api.hasEvent( 'popupDidClose' ) ){
								$slider.triggerHandler( 'popupDidClose', ls.api.eventData() );
							}
							// REMOVE: popup & events
							pu.events.destroy();
						}else{
							ls.gsap.TweenMax.set( $slider[0], {
								opacity: 1,
								x: 0,
								y: 0,
								z: 0,
								rotation: 0,
								rotationX: 0,
								rotationY: 0,
								scaleX: 1,
								scaleY: 1,
								skewX: 0,
								skewY: 0
							});
							// if( typeof pu.plugin.settings.showOnTimeout === 'number' ){
							// 	pu.events.startTimeout();
							// }
							if( pu.plugin.settings.resetOnClose === 'slide' ){
								$slider.layerSlider( 'resetSlide' );
							}
							if( pu.plugin.settings.showOnScroll ||  pu.plugin.settings.closeOnScroll ){
								pu.events.checkScrollPositions();
							}
							// API CALL: popupDidClose
							if( ls.api.hasEvent( 'popupDidClose' ) ){
								$slider.triggerHandler( 'popupDidClose', ls.api.eventData() );
							}
						}
					}
				};

				if( typeof this.transitionSettings.customTransitionOut === 'object' ){
					this.transitionSettings.styleOutTo = $.extend( {}, this.transitionSettings.styleOutTo, this.transitionSettings.customTransitionOut );
				}
			},

			show: function(){

				// SET: options, classes
				ls.slider.state.popupIsVisible = true;
				ls.slider.state.popupShouldStart = pu.plugin.settings.startSliderImmediately ? true : false;
				pu.$popup.addClass( 'ls-popup-visible' );

				// TRIGGER: resize events
				$( window ).trigger( 'resize.' + sliderUID );
				pu.events.resize();

				if( typeof this.transitionSettings.customTransitionIn !== 'object' ){

					// SET: tranition in
					switch( this.transitionSettings.transitionIn.toLowerCase() ){
						default:
						case 'fade':
							this.transitionSettings.styleInFrom.opacity = 0;
						break;
						case 'slidefromtop':
							this.transitionSettings.styleInFrom.y = ls.device.winScrollTop - ( ls.slider.height + ls.slider.offsetTop );
						break;
						case 'slidefrombottom':
							this.transitionSettings.styleInFrom.y = ls.device.winScrollTop + ls.device.viewportHeight - ls.slider.offsetTop;
						break;
						case 'slidefromleft':
							this.transitionSettings.styleInFrom.x = ls.device.winScrollLeft - ( ls.slider.width + ls.slider.offsetLeft );
						break;
						case 'slidefromright':
							this.transitionSettings.styleInFrom.x = ls.device.winScrollLeft + ls.device.viewportWidth - ls.slider.offsetLeft;
						break;
						case 'rotatefromtop':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.y = -( ls.slider.height / 2 );
							this.transitionSettings.styleInFrom.rotationX = 30;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quint.easeOut;
						break;
						case 'rotatefrombottom':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.y = ls.slider.height / 2;
							this.transitionSettings.styleInFrom.rotationX = -30;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quint.easeOut;
						break;
						case 'rotatefromleft':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.x = -( ls.slider.width / 2 );
							this.transitionSettings.styleInFrom.rotationY = -30;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quint.easeOut;
						break;
						case 'rotatefromright':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.x = ls.slider.width / 2;
							this.transitionSettings.styleInFrom.rotationY = 30;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quint.easeOut;
						break;
						case 'scalefromtop':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleY = 1.5;
							this.transitionSettings.styleInFrom.y = -( ls.slider.height / 4 );
							this.transitionSettings.styleInFrom.transformOrigin = '50% 100%';
							this.transitionSettings.styleInTo.ease = ls.gsap.Back.easeOut;
							// this.transitionSettings.durationIn = 0.5;
						break;
						case 'scalefrombottom':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleY = 1.5;
							this.transitionSettings.styleInFrom.y = ls.slider.height / 4;
							this.transitionSettings.styleInFrom.transformOrigin = '50% 0';
							this.transitionSettings.styleInTo.ease = ls.gsap.Back.easeOut;
							// this.transitionSettings.durationIn = 0.5;
						break;
						case 'scalefromleft':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleX = 1.5;
							this.transitionSettings.styleInFrom.x = -( ls.slider.width / 4 );
							this.transitionSettings.styleInFrom.transformOrigin = '100% 50%';
							this.transitionSettings.styleInTo.ease = ls.gsap.Back.easeOut;
							// this.transitionSettings.durationIn = 0.5;
						break;
						case 'scalefromright':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleX = 1.5;
							this.transitionSettings.styleInFrom.x = ls.slider.width / 4;
							this.transitionSettings.styleInFrom.transformOrigin = '0 50%';
							this.transitionSettings.styleInTo.ease = ls.gsap.Back.easeOut;
							// this.transitionSettings.durationIn = 0.5;
						break;
						case 'scale':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleX = 0.5;
							this.transitionSettings.styleInFrom.scaleY = 0.5;
						break;
						case 'spin':
							this.transitionSettings.styleInFrom.rotation = 360;
							this.transitionSettings.styleInFrom.scaleX = 0;
							this.transitionSettings.styleInFrom.scaleY = 0;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quart.easeOut;
						break;
						case 'spinx':
							this.transitionSettings.styleInFrom.rotationX = 360;
							this.transitionSettings.styleInFrom.scaleX = 0;
							this.transitionSettings.styleInFrom.scaleY = 0;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quart.easeOut;
						break;
						case 'spiny':
							this.transitionSettings.styleInFrom.rotationY = 360;
							this.transitionSettings.styleInFrom.scaleX = 0;
							this.transitionSettings.styleInFrom.scaleY = 0;
							this.transitionSettings.styleInTo.ease = ls.gsap.Quart.easeOut;
						break;
						case 'elastic':
							this.transitionSettings.styleInFrom.opacity = 0;
							this.transitionSettings.styleInFrom.scaleX = 1.2;
							this.transitionSettings.styleInFrom.scaleY = 0.8;
							this.transitionSettings.styleInTo.ease = ls.gsap.Elastic.easeOut;
						break;
					}
				}

				// START: transition in
				ls.gsap.TweenMax.fromTo( $slider[0], this.transitionSettings.durationIn, this.transitionSettings.styleInFrom, this.transitionSettings.styleInTo );
			},

			hide: function(){

				if( typeof this.transitionSettings.customTransitionOut !== 'object' ){

					// SET: transition out
					switch( this.transitionSettings.transitionOut.toLowerCase() ){
						default:
						case 'fade':
							this.transitionSettings.styleOutTo.opacity = 0;
						break;
						case 'slidetotop':
							this.transitionSettings.styleOutTo.y = ls.device.winScrollTop - ( ls.slider.height + ls.slider.offsetTop );
						break;
						case 'slidetobottom':
							this.transitionSettings.styleOutTo.y = ls.device.winScrollTop + ls.device.viewportHeight - ls.slider.offsetTop;
						break;
						case 'slidetoleft':
							this.transitionSettings.styleOutTo.x = ls.device.winScrollLeft - ( ls.slider.width + ls.slider.offsetLeft );
						break;
						case 'slidetoright':
							this.transitionSettings.styleOutTo.x = ls.device.winScrollLeft + ls.device.viewportWidth - ls.slider.offsetLeft;
						break;
						case 'rotatetotop':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.y = -( ls.slider.height / 2 );
							this.transitionSettings.styleOutTo.rotationX = 30;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'rotatetobottom':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.y = ls.slider.height / 2;
							this.transitionSettings.styleOutTo.rotationX = -30;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'rotatetoleft':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.x = -( ls.slider.width / 2 );
							this.transitionSettings.styleOutTo.rotationY = -30;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'rotatetoright':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.x = ls.slider.width / 2;
							this.transitionSettings.styleOutTo.rotationY = 30;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'scale':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleX = 0.5;
							this.transitionSettings.styleOutTo.scaleY = 0.5;
						break;
						case 'scaletotop':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleY = 1.5;
							this.transitionSettings.styleOutTo.y = -( ls.slider.height / 4 );
							this.transitionSettings.styleOutTo.transformOrigin = '50% 100%';
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'scaletobottom':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleY = 1.5;
							this.transitionSettings.styleOutTo.y = ls.slider.height / 4;
							this.transitionSettings.styleOutTo.transformOrigin = '50% 0';
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'scaletoleft':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleX = 1.5;
							this.transitionSettings.styleOutTo.x = -( ls.slider.width / 4 );
							this.transitionSettings.styleOutTo.transformOrigin = '100% 50%';
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'scaletoright':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleX = 1.5;
							this.transitionSettings.styleOutTo.x = ls.slider.width / 4;
							this.transitionSettings.styleOutTo.transformOrigin = '0 50%';
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quint.easeIn;
						break;
						case 'spin':
							this.transitionSettings.styleOutTo.rotation = 360;
							this.transitionSettings.styleOutTo.scaleX = 0;
							this.transitionSettings.styleOutTo.scaleY = 0;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quart.easeIn;
						break;
						case 'spinx':
							this.transitionSettings.styleOutTo.rotationX = 360;
							this.transitionSettings.styleOutTo.scaleX = 0;
							this.transitionSettings.styleOutTo.scaleY = 0;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quart.easeIn;
						break;
						case 'spiny':
							this.transitionSettings.styleOutTo.rotationY = 360;
							this.transitionSettings.styleOutTo.scaleX = 0;
							this.transitionSettings.styleOutTo.scaleY = 0;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Quart.easeIn;
						break;
						case 'elastic':
							this.transitionSettings.styleOutTo.opacity = 0;
							this.transitionSettings.styleOutTo.scaleX = 1.2;
							this.transitionSettings.styleOutTo.scaleY = 0.8;
							this.transitionSettings.styleOutTo.ease = ls.gsap.Back.easeInOut;
						break;
					}
				}

				// STOP: slider
				if( !pu.plugin.settings.startSliderImmediately ){
					$slider.layerSlider( 'pause' );
				}

				// START: transition out
				ls.gsap.TweenMax.to( $slider[0], this.transitionSettings.durationOut, this.transitionSettings.styleOutTo );
			}
		};

		pu.slider = {

			setOptions: function(){

				if( ls.o.popupFitWidth || ls.o.popupFitHeight ){
					ls.o.type = 'fullsize';
					ls.o.fullSizeMode = 'fitheight';

					if( ls.o.popupFitWidth && !ls.o.popupFitHeight ){
						$slider.css({
							maxHeight: pu.popup.styleSettings.height
						});
					}

					if( ls.o.popupFitHeight && !ls.o.popupFitWidth ){
						$slider.css({
							maxWidth: pu.popup.styleSettings.width
						});
					}
				}else{
					ls.o.type = 'responsive';
					$slider.css({
						maxWidth: pu.popup.styleSettings.width,
						maxHeight: pu.popup.styleSettings.height
					});
				}

				ls.slider.isPopup = true;
				$.extend( ls.o, forceSliderSettings );
			},

			setStyles: function(){

				var sliderStyles = {};

				// SET: styles of the slider element
				switch( pu.popup.styleSettings.positionHorizontal ){
					case 'left':
						sliderStyles.left = 0;
						sliderStyles.right = 'auto';
					break;
					case 'right':
						sliderStyles.left = 'auto';
						sliderStyles.right = 0;
					break;
					case 'center':
						sliderStyles.left = '50%';
						sliderStyles.right = 'auto';
					break;
				}
				switch( pu.popup.styleSettings.positionVertical ){
					case 'top':
						sliderStyles.top = 0;
						sliderStyles.bottom = 'auto';
					break;
					case 'bottom':
						sliderStyles.top = 'auto';
						sliderStyles.bottom = 0;
					break;
					case 'middle':
						sliderStyles.top = '50%';
						sliderStyles.bottom = 'auto';
					break;
				}
				$slider.css( sliderStyles );
			}
		};

		pu.events = {

			set: function(){

				$slider.on( 'sliderDidLoad', function( event, data ){

					// CLICK EVENT
					if( pu.plugin.settings.$clickTriggerElements && pu.plugin.settings.$clickTriggerElements.length > 0 ){

						pu.plugin.settings.$clickTriggerElements.on( 'click.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
							if( pu.$popup.hasClass( 'ls-popup-visible' ) ){
								pu.events.hide();
							}else{
								pu.events.show();
							}
						});
					}

					// SCROLL EVENT
					if( pu.plugin.settings.showOnScroll || pu.plugin.settings.closeOnScroll ){

						// SET: starting scroll direction  & scroll position
						pu.events.scrollDirection = 'down';
						pu.events.lastScrollPosition = ls.device.winScrollTop;
						pu.events.checkScrollPositions();

						// SET: starting scroll direction
						$( window ).on( 'scroll.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){

							if(
								( ls.device.winScrollTop >= pu.events.lastScrollPosition && pu.events.scrollDirection === 'up' ) ||
								( ls.device.winScrollTop <= pu.events.lastScrollPosition && pu.events.scrollDirection === 'down' && ls.device.winScrollTop !== 0 )
							){
								pu.events.switchScrollDirection();
							}

							pu.events.lastScrollPosition = ls.device.winScrollTop;

							if( pu.events.scrollDirection === pu.events.popupShouldShowTriggerAnEventByScrolling ){
								if( pu.plugin.settings.scrollPosition === 'auto' ){
									if( ls.slider.offsetTop > ls.device.winScrollTop && ls.slider.offsetTop < ( ls.device.winScrollTop +  ls.device.viewportHeight ) ){
										if( pu.events.scrollDirection === 'down' ){
											if( ls.slider.state.popupIsVisible ){
												pu.events.popupShouldHide = true;
											}else{
												pu.events.popupShouldShow = true;
											}
										}
									}else if( pu.events.scrollDirection === 'up' ){
										if( ls.slider.state.popupIsVisible ){
											pu.events.popupShouldHide = true;
										}else{
											pu.events.popupShouldShow = true;
										}
									}
								}else if( typeof pu.plugin.settings.scrollPosition === 'number' ){
									if( ls.device.winScrollTop >= pu.plugin.settings.scrollPosition ){
										if( pu.events.scrollDirection === 'down' ){
											if( ls.slider.state.popupIsVisible ){
												pu.events.popupShouldHide = true;
											}else{
												pu.events.popupShouldShow = true;
											}
										}
									}else if( pu.events.scrollDirection === 'up' ){
										if( ls.slider.state.popupIsVisible ){
											pu.events.popupShouldHide = true;
										}else{
											pu.events.popupShouldShow = true;
										}
									}
								}else if( pu.plugin.settings.scrollPosition.indexOf( '%' ) !== -1 ){
									if( parseInt( ls.device.winScrollTop / ( ( ls.device.docHeight - ls.device.viewportHeight ) / 100 ) ) > parseInt( pu.plugin.settings.scrollPosition ) ){
										if( pu.events.scrollDirection === 'down' ){
											if( ls.slider.state.popupIsVisible ){
												pu.events.popupShouldHide = true;
											}else{
												pu.events.popupShouldShow = true;
											}
										}
									}else if( pu.events.scrollDirection === 'up' ){
										if( ls.slider.state.popupIsVisible ){
											pu.events.popupShouldHide = true;
										}else{
											pu.events.popupShouldShow = true;
										}
									}
								}
							}

							if( pu.events.popupShouldShow ){
								pu.events.popupShouldShow = false;
								pu.events.show();
								pu.events.checkScrollPositions();
							}else if( pu.events.popupShouldHide ){
								pu.events.popupShouldHide = false;
								pu.events.hide();

							}
						});

						$( window ).trigger( 'scroll.' + pu.defaults.plugin.eventNamespace + sliderUID );
					}

					// LEAVE EVENT
					if( pu.plugin.settings.showOnLeave ){

						$( document ).on( 'mousemove.' + pu.defaults.plugin.eventNamespace + sliderUID, function( event ) {
							pu.events.mousePositionTop = event.pageY - ls.device.winScrollTop;
						});

						$( 'html' ).on( 'mouseleave.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
							if( pu.events.mousePositionTop < 100 ){
								pu.events.show();
							}
						});
					}

					// IDLE EVENT
					if( pu.plugin.settings.showOnIdle && typeof pu.plugin.settings.showOnIdle === 'number' ){

						pu.events.idleTime = 0;
						pu.events.startIdleTimer();

						$( window ).on( 'load.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
							pu.events.startIdleTimer();
						});

						$( document ).on( 'mousemove.' + pu.defaults.plugin.eventNamespace + sliderUID + ' keypress.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
							pu.events.startIdleTimer();
						});
					}

					// TIMEOUT EVENT
					if( pu.plugin.settings.hasOwnProperty( 'showOnTimeout' ) && typeof pu.plugin.settings.showOnTimeout === 'number' ){
						pu.events.startTimeout();
					}

					$( window ).on( 'resize.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
						if( ls.slider.state.popupIsVisible ){
							pu.events.resize();
						}
					});

					$( window ).on( 'orientationchange.' + pu.defaults.plugin.eventNamespace + sliderUID, function(){
						if( ls.slider.state.popupIsVisible ){
							setTimeout(function(){
								ls.resize.all();
								pu.events.resize();
							}, 750 );
						}
					});
				});

				if( pu.plugin.settings.overlayClickToClose ){
					pu.$overlay.on( 'click.' + pu.defaults.plugin.eventNamespace + sliderUID, function() {
						pu.events.hide();
					});
				}

				$slider.on( 'click.' + pu.defaults.plugin.eventNamespace + sliderUID, '[href="#popupclose"], [href="#closepopup"], .ls-popup-close, .ls-close-popup, .ls-popup-close-button, .ls-close-popup-button', function( event ) {
					event.preventDefault();
					pu.events.hide();
				});

				$( document ).on( 'click.' + pu.defaults.plugin.eventNamespace + sliderUID, '.ls-close-all-popups-button', function( event ){
					event.preventDefault();
					pu.events.hide();
				});
			},

			checkScrollPositions: function(){

				// CHECK: if the slider should show or hide by scrolling
				if( pu.plugin.settings.closeOnScroll && ls.slider.state.popupIsVisible ){
					pu.plugin.settings.scrollPosition = pu.plugin.settings.closeOnScroll;
				}else if( pu.plugin.settings.showOnScroll && !ls.slider.state.popupIsVisible ){
					pu.plugin.settings.scrollPosition = pu.plugin.settings.showOnScroll;
				}else{
					pu.plugin.settings.scrollPosition = '';
				}

				// CHECK: the direction of scrolling & position of the slider regarding to the viewport
				if( pu.plugin.settings.scrollPosition === 'auto' ){
					if( ls.slider.offsetTop > ls.device.winScrollTop && ls.slider.offsetTop < ( ls.device.winScrollTop + ls.device.viewportHeight ) ){
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'up';
					}else{
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'down';
					}
				}else if( typeof pu.plugin.settings.scrollPosition === 'number' ){
					if( ls.device.winScrollTop >= pu.plugin.settings.scrollPosition ){
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'up';
					}else{
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'down';
					}
				}else if( pu.plugin.settings.scrollPosition.indexOf( '%' ) !== -1 ){
					if( parseInt( ls.device.winScrollTop / ( ( ls.device.docHeight - ls.device.viewportHeight ) / 100 ) ) > parseInt( pu.plugin.settings.scrollPosition ) ){
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'up';
					}else{
						pu.events.popupShouldShowTriggerAnEventByScrolling = 'down';
					}
				}
			},

			switchScrollDirection: function(){

				if( pu.plugin.settings.scrollPosition ){
					if( pu.events.scrollDirection === 'up' ){
						pu.events.scrollDirection = 'down';
					}else{
						pu.events.scrollDirection = 'up';
					}
				}
			},

			startIdleTimer: function(){

				if( pu.events.idleTimer ){ clearTimeout( pu.events.idleTimer ); }

				pu.events.idleTimer = setTimeout( function(){
					pu.events.show();
				}, pu.plugin.settings.showOnIdle * 1000 );
			},

			startTimeout: function(){

				if( pu.events.timeout ){ clearTimeout( pu.events.timeout ); }

				pu.events.timeout = setTimeout( function(){
					pu.events.show();
				}, pu.plugin.settings.showOnTimeout * 1000 );
			},

			show: function(){

				if( !ls.slider.state.popupIsVisible ){

					// API CALL: popupWillOpen
					if( ls.api.hasEvent( 'popupWillOpen' ) ){
						$slider.triggerHandler( 'popupWillOpen', ls.api.eventData() );
					}

					if( !pu.plugin.settings.disableOverlay ){
						pu.overlay.show();
					}
					pu.popup.show();
				}
			},

			hide: function(){

				if( ls.slider.state.popupIsVisible ){

					// API CALL: popupWillClose
					if( ls.api.hasEvent( 'popupWillClose' ) ){
						$slider.triggerHandler( 'popupWillClose', ls.api.eventData() );
					}

					if( !pu.plugin.settings.disableOverlay ){
						pu.overlay.hide();
					}
					pu.popup.hide();
				}
			},

			resize: function(){

				var sliderStyles = {};

				if( pu.popup.styleSettings.positionHorizontal === 'center' ){
					sliderStyles.marginLeft = -ls.slider.width / 2;
				}
				if( pu.popup.styleSettings.positionVertical === 'middle' ){
					sliderStyles.marginTop = -ls.slider.height / 2;
				}

				$slider.css( sliderStyles );

				// SET: slider offset after poritioning it
				var sliderOffset = $slider.offset();
				ls.slider.offsetLeft = sliderOffset.left;
				ls.slider.offsetTop = sliderOffset.top;
			},

			destroy: function(){

				$slider.on( 'sliderDidRemove', function(){
					$( [ window, document, $('body')[0], $('html')[0], pu.plugin.settings.$clickTriggerElements ] ).off( pu.defaults.plugin.eventNamespace + sliderUID );
					pu.$overlay.remove();
					pu.$popup.remove();
				}).layerSlider( 'destroy', true );
			}
		};
	};

})( jQuery, undefined );
