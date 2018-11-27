/*
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */

/**
 * The DeliveryServer/index controller.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'module',
    'core/router',
    'ui/feedback',
    'layout/loading-bar'
], function($, _, module, router, feedback, loadingBar){
    'use strict';

    /**
     * Display a permanent message
     * @param {String} level - in supported feedbacks' levels
     * @param {String} content - the message to display
     */
    var displayPermanentMessage = function displayPermanentMessage(level, content){
        if(level && content){
            feedback($('.permanent-feedback'))[level](content, {
                timeout : -1,
                popup : false
            });
        }
    };

    /**
     * The DeliveryServer/index controller
     */
    return {

        /**
         * Controller entry point
         * @param {Object} [parameters] - controller's data
         * @param {Object} [parameters.message] - message data to display
         */
        start: function start(parameters){
            var deliveryStarted = false;

            /**
             * Run/open the given delivery
             * @param {String} url - the delivery URL
             */
            var runDelivery = function runDelivery (url) {
                if(_.isString(url) && !_.isEmpty(url)){
                    deliveryStarted = true;
                    loadingBar.start();
                    window.location.href = url;
                }
            };

            var config = module.config();


            if (parameters && parameters.messages) {
                _.forEach(parameters.messages, function(message) {
                    displayPermanentMessage(message.level, message.content);
                });
            }

            $('a.entry-point').on('click', function (e) {
                var $elt = $(this);

                e.preventDefault();
                e.stopPropagation();

                if(!deliveryStarted && !$elt.hasClass('disabled')){
                    runDelivery($elt.data().launch_url);
                }
            });

            // dispatch any extra registered routes
            if (config && _.isArray(config.extraRoutes) && config.extraRoutes.length) {
                router.dispatch(config.extraRoutes);
            }
        }
    };
});
