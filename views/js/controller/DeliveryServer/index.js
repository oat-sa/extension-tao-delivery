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
    'i18n',
    'module',
    'core/router',
    'ui/feedback',
    'core/logger',
    'layout/loading-bar'
], function($, _, __, module, router, feedback, loggerFactory, loadingBar){
    'use strict';

    const logger = loggerFactory('deliveryServer');

    /**
     * Display a permanent message
     * @param {String} level - in supported feedbacks' levels
     * @param {String} content - the message to display
     */
    const displayPermanentMessage = function displayPermanentMessage(level, content){
        if(level && content){
            feedback($('.permanent-feedback'))[level](content, {
                timeout : -1,
                popup : false
            });
        }
    };

    /**
     * Extract standard LTI error parameters from query string
     * @returns {Object} LTI error parameters
     */
    const getLTIErrorParameters = function getLTIErrorParameters() {
        const { searchParams } = new URL(window.location.href);

        return ['lti_errormsg', 'lti_errorlog'].reduce((params, paramName) => {
            if (searchParams.has(paramName)) {
                params[paramName] = searchParams.get(paramName);
            }
            return params;
        }, {});
    };

    /**
     * The DeliveryServer/index controller
     */
    return {

        /**
         * Controller entry point
         * @param {Object} [parameters] - controller's data
         * @param {Object} [parameters.messages] - message data to display
         */
        start(parameters){
            let deliveryStarted = false;

            /**
             * Run/open the given delivery
             * @param {String} url - the delivery URL
             */
            const runDelivery = function runDelivery (url) {
                if(_.isString(url) && !_.isEmpty(url)){
                    deliveryStarted = true;
                    loadingBar.start();
                    window.location.href = url;
                }
            };

            const config = module.config();

            // display as feedbacks any messages in parameters
            if (parameters && parameters.messages) {
                _.forEach(parameters.messages, message => {
                    displayPermanentMessage(message.level, message.content);
                });
            }

            // display as feedbacks any LTI error messages from query string
            const { lti_errormsg: ltiErrorMsg, lti_errorlog: ltiErrorLog } = getLTIErrorParameters();

            if (ltiErrorMsg) {
                displayPermanentMessage('error', ltiErrorMsg.length ? ltiErrorMsg : __('An error occurred!'));
            };
            if (ltiErrorLog) {
                logger.error(`${ltiErrorMsg} ${ltiErrorLog}`);
            };

            $('a.entry-point').on('click', function (e) {
                const $elt = $(this);

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
