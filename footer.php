			<footer role="contentinfo">
			
				<div id="inner-footer" class="clearfix">
		          <hr />
		          <div id="widget-footer" class="clearfix row-fluid">
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer1') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer2') ) : ?>
		            <?php endif; ?>
		            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer3') ) : ?>
		            <?php endif; ?>
		          </div>
					
					<nav class="clearfix">
						<?php bones_footer_links(); // Adjust using Menus in Wordpress Admin ?>
					</nav>
					
					<p class="pull-right"><a href="http://320press.com" id="credit320" title="By the dudes of 320press">320press</a></p>
			
					<p class="attribution">&copy; <?php bloginfo('name'); ?></p>
				
				</div> <!-- end #inner-footer -->
				
			</footer> <!-- end footer -->
		
		</div> <!-- end #container -->
				
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->
		
		<?php wp_footer(); // js scripts are inserted using this function ?>
		<script>/**
 *  jquery plugin pinbox
 *  
 *  usecase:
 *   $('#categoryProductContainer').pinbox().hide(400).fadeIn(1000);
 *   var options: {
 *                 newitemindicator : "new", //only the classname this class will remove by the plugin!
                   subcontainer : ".prodcont"  //classname selector for all subcontainer
                  };
 *    $('#categoryProductContainer').pinbox(options);
 *    
 *    //after an ajax call, place the new container into the container:
 *    $('#categoryProductContainer').append(ajaxResult);
 *    then call:
 *     $('#categoryProductContainer').pinbox(options);
 *     //now only the new items wich have the new class indicator: "new" will be placed directly
 *  author: Kay Schneider <kayoliver82@gmail.com>
 *  version: 1.0 
 *  date: 2012.11.19
 */
(function( $ ){
    
    var methods = {
        afterLoad : function( options  ) {
           
            if( $.fn.pinbox.staticInfo.isRun  == true ) {
                return 1;
            }
            $.fn.pinbox.staticInfo.isRun = true;
            container = $(options.subcontainer + "." + options.newitemindicator, $(this));

            var matrix  =  methods['buildMatrix'].apply( this, [container, options]);
            methods.setPositions.apply(this, [ matrix , options ]);
       
            return container;
        },
        
        buildMatrix: function (containerArrs, options) {

            var matrix = new Array();
            var subMatrix = new Array();
            var matrixWidth = options.rowsize;
            var counter = 0;
            $(containerArrs).each(function () {
                counter++;
                var pushObject = this;
                subMatrix.push(pushObject);

                if(counter === matrixWidth) {
                    counter = 0;
                    matrix.push(subMatrix);
                    subMatrix = new Array();
                }
       
            });
            if(subMatrix.length > 0) {
                matrix.push(subMatrix);
            }
            
            return matrix;
        },
        
        setPositions : function (matrix, options) {
            var CFlexObj = this;
            var staticInfo = $.fn.pinbox.staticInfo;
            var tools = methods;
            $(matrix).each(function (pos) {

                $(this).each(function (subPos) {
             
                    if(pos > 0) {
                        if(staticInfo.nextFillMatrixId !== false ) {
                            subPos = staticInfo.nextFillMatrixId;
                        }
                 
                        var parent = pos -1;
                        var parentSubPos = subPos;
                        var parentObject = tools.getParentItem(subPos);
                        var topPos = tools.builPosPositionFromMaxParent( 
                            parentObject.top,
                            $(parentObject.height).outerHeight(true)
                            );
                        
                        left = ( subPos * $(this).outerWidth(true) ) + 10;
                        
                        $(this).attr('subpos',subPos );
                        $(this).css( {
                            'position':'absolute',
                            'top': topPos + 'px', 
                            'left':left + 'px'
                        });
                
                        $(this).removeClass(options.newitemindicator);
                 
                    } else {
                        if(staticInfo.nextFillMatrixId != false) {
                            subPos = staticInfo.nextFillMatrixId;
                        }
                        if( tools.getLastPosition(subPos) != false ) {
                            topPos = tools.getLastPosition(subPos);
                        } else {
                            topPos = 0;
                        }
                
                        left = ( subPos * $(this).outerWidth(true) ) + 10;
             
                        $(this).css( {
                            'position':'absolute',
                            'top':topPos + 'px', 
                            'left':left + 'px'
                        });
                        $(this).removeClass(options.newitemindicator);
                    }
                    
                    tools.setParentItem(subPos, this, topPos);
                    tools.setLastPosition(subPos, topPos  + $(this).outerHeight(true) + 30);

                    if(staticInfo.firstRun != true) {
                        tools.checkV();
                    }
                
                });
                
                staticInfo.firstRun = false;
         
            });

            staticInfo.isRun = false; 
            
            return this;
        },

        getParentItem : function (subPosition) {
            return $.fn.pinbox.staticInfo.parentPosition[subPosition];
        },

        setParentItem : function (subPosition, outerHeight, positionTop) {
            $.fn.pinbox.staticInfo.parentPosition[subPosition] = {
                height:outerHeight, 
                top: positionTop
            };
        },

        checkV : function () {
            var minItem =  this.minItem($.fn.pinbox.staticInfo.lastScrollIndex);
            var maxItem =  this.maxItem($.fn.pinbox.staticInfo.lastScrollIndex);
            var diff = maxItem[1] - minItem[1];
       
            if($.fn.pinbox.staticInfo.containerMaxDiff <= diff ) {
                $.fn.pinbox.staticInfo.nextFillMatrixId = minItem[0];
            } else {
                $.fn.pinbox.staticInfo.nextFillMatrixId = false;
            }
            
  
        },

        maxItem : function(ar) {
            var max  = ar[0];
            var maxi = 0
            for (var i = 1; i < ar.length; i++) {
                if (ar[i] > max) {
                    max = ar[i];
                    maxi=i;
                }
            }
            return Array(maxi,max);
        },

        minItem : function(ar) {
            var max  = ar[0];
            var maxi = 0
            for (var i = 1; i < ar.length; i++) {
                if (ar[i] < max) {
                    max = ar[i];
                    maxi=i;
                }
            }
            return Array(maxi,max);
        },

        getLastPosition : function (index) {
            if($.fn.pinbox.staticInfo.lastScrollIndex == false || $.fn.pinbox.staticInfo.lastScrollIndex[index] === undefined) {
                return false;
            } else {
                return $.fn.pinbox.staticInfo.lastScrollIndex[index]; 
            }
     
        },

        setLastPosition : function (index,position) {

            if($.fn.pinbox.staticInfo.lastScrollIndex == false) {
                $.fn.pinbox.staticInfo.lastScrollIndex = new Array();
            }
    
            $.fn.pinbox.staticInfo.lastScrollIndex[index] = position;
        },

        builPosPositionFromMaxParent : function (top, height) {
            return top + height + 30;
        },
        
        destroy : function( ) {

            return this.each(function(){

                var $this = $(this),
                data = $this.data('tooltip');

                // Namespacing FTW
                $(window).unbind('.tooltip');
                data.tooltip.remove();
                $this.removeData('tooltip');

            })

        }
   
    };
    
    

    $.fn.pinbox = function (options) {
        var opts = jQuery.extend({}, $.fn.pinbox.defaults, options);

        return methods.afterLoad.apply(this, [ opts ]);
    };

    $.fn.pinbox.staticInfo = {
        isRun : false,
        lastScrollIndex : false,
        containerMaxDiff : 150,
        nextFillMatrixId : false,
        parentPosition :new Array(),
        firstRun :true
    };
    
    // $.fn.pinbox.defaults.newitemindicator 

    $.fn.pinbox.defaults =  {
        newitemindicator : "new",
        subcontainer : ".prodcont",
        rowsize : 5
    };
})( jQuery );



            $(document).ready(function () {
                /**
                 *  create a new pinboxes! ;)
                 *  
                 *  avaiable parameters in the options:
                 *   every new item in the boxes uses the new item Indicator
                 *   
                 *   newitemindicator : "new", 
                 *   subcontainer : ".prodcont" 
                 */

                $('#categoryProductContainer').pinbox({subcontainer:'.actioninside'}).hide(0).fadeIn(1000);
                
                $('#ajaxtrigger').bind('click', function () {
                    
                    $('#ajax').children().each(function () {
                        //add an ajax class so we can see where are the new boxes placed
                        $(this).addClass('ajax');
                    })
                    
                    var ajaxResult = $('#ajax').html();
                    //set the result into the container:
                    $('#categoryProductContainer').append(ajaxResult);
                    //update the pinbox view
                    $('#categoryProductContainer').pinbox({subcontainer:'.actioninside'}).hide(0).fadeIn(1500);
                });
                
                
            });
   
 </script>

	</body>

</html>
