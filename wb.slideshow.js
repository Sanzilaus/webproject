/*
 * jQuery Slideshow Plugin for WYSIWYG Web Builder v7.0
 * Copyright Pablo Software solutions 2010
 * http://www.wysiwygwebbuilder.com/
 *
 */

(function($)
{
   $.fn.slideshow = function(options) 
   {
      return this.each(function() 
      {   
         $.slideshow(this, options);
      });
   };

   $.slideshow = function(container, options) 
   {
      var settings = 
      {
         effect: 'none',
         effectlength: 2000,
         direction: '',
         interval: 2000,
         type: 'sequence',
         dataSource: 'local',
         param: null,
         maxItems: 10,
         current: 1,
         last: 0
      };

      if (options && typeof(options) == 'string') 
      { 
         if (options == 'nextimage') 
         { 
            $.slideshow.nextimage(container); 
         } 
         else
         if (options == 'previmage') 
         { 
            $.slideshow.previmage(container); 
         } 
         return; 
      }
	
      if (options)
         state = $.extend(settings, options);

      if (state.dataSource == 'local')
      {
         $.slideshow.init(container, state); 
      }
      else 
      if (state.dataSource == 'flickr')
      {
         $container = $(container);
         $container.empty();

         var $loadingIndicator = $('<img/>')
           .attr({
           'src': 'wb.slideshow.gif', 
           'alt': 'Loading. Please wait.'
         })
         .addClass('slideshow-wait')
         .appendTo($container);

         var url = "http://api.flickr.com/services/feeds/photos_public.gne?format=json&";

         if (state.param != null)
         {
            url += state.param;
            url += "&";
         }

         url += "jsoncallback=?";

         $.getJSON(url, function(data)
         {
             $loadingIndicator.remove();

            $.each(data.items, function(index, item)
            {
               $('<img>')
                  .attr("src", item.media.m)
                  .css('left', '0')
                  .css('top', '0')
                  .appendTo($container);

               if (index == state.maxItems) 
                  return false;
            });
            $.slideshow.init(container, state);  
         });
      }
   };

   $.slideshow.init = function(container, state)
   {
//      $(container).css('position', 'relative');
      state.images = $(container).find('img').get();
      $.each(state.images, function(i)
      {
         $(state.images[i]).css('zIndex', state.images.length - i).css('position', 'absolute').css('top', '0').css('left', '0');
      });
  
      $(container).data('state', state);

      if (state.interval != 0)
      {
         setTimeout(function()
         {
            $.slideshow.imagetransition(container);
         }, state.interval);
      }
   };

   $.slideshow.nextimage = function(container)
   {
      state = $(container).data('state');
      $.slideshow.next(state);
      $.slideshow.imagetransition(container);
   };


   $.slideshow.previmage = function(container)
   {
      state = $(container).data('state');

      if (( state.current - 1 ) >= 0) 
      {
         state.current = state.current - 1;
         state.last = state.current + 1;
      }
      else 
      {
         state.current = state.images.length - 1;
         state.last = 0;
      }
      $.slideshow.imagetransition(container);
   };

   $.slideshow.blockEffectStep1 = function(arrBlocks, num, speed, callback)
   {	
      if(num < arrBlocks.length) 
      {
         $(arrBlocks[num]).animate({opacity:1}, speed, 'linear', function() 
         {
            $.slideshow.blockEffectStep1(arrBlocks, num + 1, speed, callback);
         });
      } 
      else 
      {
         if(typeof callback == 'function')
         { 
            callback.call(); 
         }
      }
   };

   $.slideshow.blockEffectStep2 = function(arrBlocks, num, speed, callback)
   {	
      if(num < arrBlocks.length) 
      {
         $(arrBlocks[num]).animate({opacity:0}, speed, 'linear', function() 
         {
            $.slideshow.blockEffectStep2(arrBlocks, num + 1, speed, callback);
         });
      } 
      else 
      {
         if(typeof callback == 'function')
         { 
            callback.call(); 
         }
      }
   };

   $.slideshow.next = function(state)
   {
      if (state.type == 'sequence') 
      {
         if (( state.current + 1 ) < state.images.length) 
         {
            state.current = state.current + 1;
            state.last = state.current - 1;
         }
         else 
         {
            state.current = 0;
            state.last = state.images.length - 1;
         }
      }
      else 
      if (state.type == 'random') 
      {
         state.last = state.current;
         while (state.current == state.last) 
         {
            state.current = Math.floor(Math.random() * (state.images.length));
         }
      }
   };

 	
   $.slideshow.imagetransition = function(container)
   {
         state = $(container).data('state');

         if (state.effect != 'blocks') 
         {
            for (var i = 0; i < state.images.length; i++) 
            {
               $(state.images[i]).css('display', 'none');
            }
            $(state.images[state.last]).css('display', 'block').css('zIndex', '0');
         }

         if (state.effect == 'none') 
         {
	    $(state.images[state.current]).css('zIndex', '1').show();
         }
         else
         if (state.effect == 'fade') 
         {
            $(state.images[state.current]).css('zIndex', '1').fadeIn(state.effectlength);
         }
         else
         if (state.effect == 'slide') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('slide', { direction: state.direction }, state.effectlength);
         }
         else
         if (state.effect == 'puff') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('puff', { }, state.effectlength);
         }
         else
         if (state.effect == 'blind') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('blind', { direction: state.direction } , state.effectlength);
         }
         else
         if (state.effect == 'clip') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('clip', { direction: state.direction }, state.effectlength);
         }
         else
         if (state.effect == 'drop') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('drop', { direction: state.direction }, state.effectlength);
         } 
         else
         if (state.effect == 'fold') 
         {
            $(state.images[state.current]).css('zIndex', '1').show('fold', { }, state.effectlength);
         } 
         else
         if (state.effect == 'zoom') 
         {
            var nWidth = $(state.images[state.last]).outerWidth();
            var nHeight = $(state.images[state.last]).outerHeight();

            var animIn = { top: 0, left: 0, width: nWidth, height: nHeight };
            var animOut   = { width: 0, height: 0, top: nHeight/2, left: nWidth/2 };

            $(state.images[state.current]).animate(animOut, 0, null);
            $(state.images[state.current]).css('zIndex', '1').animate(animIn, state.effectlength, null);
         } 
         else
         if (state.effect == 'zoominout') 
         {
            var nWidth = $(state.images[state.last]).outerWidth();
            var nHeight = $(state.images[state.last]).outerHeight();

            var animIn = { top: 0, left: 0, width: nWidth, height: nHeight };
            var animOut   = { width: 0, height: 0, top: nHeight/2, left: nWidth/2 };

            $(state.images[state.last]).animate(animOut, state.effectlength, null);
            $(state.images[state.current]).animate(animOut, 0, null);
            $(state.images[state.current]).css('zIndex', '1').animate(animIn, state.effectlength, null);
         } 
         else
         if (state.effect == 'blocks') 
         {
            var nWidth = $(state.images[state.last]).outerWidth()/5;
            var nHeight = $(state.images[state.last]).outerHeight()/5;

            $(state.images[state.current]).parent().append('<div style="position:absolute;left:0px;top:0px;z-index:'+state.images.length+'" id="block-effect"></div>');

            var arrBlocks = new Array();
            for(var i = 0; i < 25; i++)
            {
               arrBlocks[i] = '#block-' + i;
               $('#block-effect').append('<div style="float:left;background-color:#000000;width:'+nWidth+'px;height:'+nHeight+'px;opacity:0.00;-moz-opacity:0.00;-khtml-opacity:0.00;filter:alpha(opacity=0)" id="block-' + i + '"></div>');
            }
            var i = arrBlocks.length;
            if (i != 0)
            {
               while (--i) 
               {
                  var j = Math.floor(Math.random() * (i + 1));
                  var tempi = arrBlocks[i];
                  var tempj = arrBlocks[j];
                  arrBlocks[i] = tempj;
                  arrBlocks[j] = tempi;
               }
            }
	
            $.slideshow.blockEffectStep1(arrBlocks, 0, 50, function() 
            {
               for (var i = 0; i < state.images.length; i++) 
               {
                  $(state.images[i]).css('display', 'none');
               }
               $(state.images[state.last]).css('display', 'block').css('zIndex', '0');
               $(state.images[state.current]).css('zIndex', '1').show();

               $.slideshow.blockEffectStep2(arrBlocks, 0, 50, function() 
               {
                   $('#block-effect').remove();

                   if (state.interval != 0)
                   {
                      setTimeout(function()
                      {
                         $.slideshow.next(state);
                         $.slideshow.imagetransition(container);
                      }, state.interval);
                   }
               });
            });
            return;
         } 
         else
         {
            $(state.images[state.current]).css('zIndex', '1').show(state.effect, { }, state.effectlength);
         } 

         if (state.interval != 0)
         {       
            $.slideshow.next(state);   

            setTimeout(function()
            {
               $.slideshow.imagetransition(container);
            }, state.interval);
         }
   };
 
})(jQuery);