/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define([
    'jquery',
    'iframeResizer',
    'context',
    'layout/loading-bar'
], function($, iframeResizer, context, loadingBar){
    
    'use strict';
    
    var $frameContainer,
        $frame,
        $headerHeight,
        $footerHeight;

    function resizeMainFrame() {
        var height = $(window).outerHeight() - $headerHeight - $footerHeight;
        $frameContainer.height(height);
        $frame.height(height);
    }
    
    return {
        start: function(options){

            $frameContainer = $('#outer-delivery-iframe-container');
            $frame = $frameContainer.find('iframe');
            $headerHeight = $('body > .content-wrap > header').outerHeight();
            $footerHeight = $('body > footer').outerHeight();

            $(document).on('serviceforbidden', function() {
                window.location = context.root_url + 'tao/Main/logout';
            });

            var serviceApi = options.serviceApi;

            serviceApi.onFinish(function() {
                $.ajax({
                    url : options.finishDeliveryExecution,
                    data : {
                        'deliveryExecution' : options.deliveryExecution
                    },
                    type : 'post',
                    dataType : 'json',
                    success : function(data) {
                        window.location = data.destination;
                    }
                });
            });
            
            $(document)
                .on('loading', function(e){
                    loadingBar.start();
                })
                .on('unloading', function(){
                    setTimeout(function(){
                        loadingBar.stop();
                    }, 300);
                })
                .on('shutdown-com', function(){
                    //use when we want to stop all exchange between frames
                    $(document).off('heightchange');
                    $frame.off('load.eventHeight')
                           .off('load.cors');
                });
            
            serviceApi.loadInto($frame.get(0));
            
            $(window).bind('resize', function() {
                resizeMainFrame();
            });
            
            resizeMainFrame();
        }
    };
});