module.exports = function(grunt) {

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';

    /**
     * Remove bundled and bundling files
     */
    clean.taodeliverybundle = [out];

    /**
     * Compile tao files into a bundle
     */
    requirejs.taodeliverybundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoDelivery' : root + '/taoDelivery/views/js' },
            modules : [{
                name: 'taoDelivery/controller/routes',
                include : ext.getExtensionsControllers(['taoDelivery']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }, {
                name: 'taoDelivery/controller/runtime/deliveryExecution',
                include: ['lib/require', 'taoDelivery/deliveryExecution'],
                exclude : ['json!i18ntr/messages.json']
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taodeliverybundle = {
        files: [
            { src: [out + '/taoDelivery/controller/routes.js'],  dest: root + '/taoDelivery/views/js/controllers.min.js' },
            { src: [out + '/taoDelivery/controller/routes.js.map'],  dest: root + '/taoDelivery/views/js/controllers.min.js.map' },
            { src: [out + '/taoDelivery/controller/runtime/deliveryExecution.js'],  dest: root + '/taoDelivery/views/js/deliveryExecution.min.js' },
            { src: [out + '/taoDelivery/controller/runtime/deliveryExecution.js.map'],  dest: root + '/taoDelivery/views/js/deliveryExecution.min.js.map' },
        ],
        options : {
            process: function (content, srcpath) {
                //because we change the bundle names during copy
                if(/routes\.js$/.test(srcpath)){
                    return content.replace('routes.js.map', 'controllers.min.js.map');
                }
                if(/deliveryExecution\.js$/.test(srcpath)){
                    return content.replace('deliveryExecution.js.map', 'deliveryExecution.min.js.map');
                }

                return content;
            }
        }
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taodeliverybundle', ['clean:taodeliverybundle', 'requirejs:taodeliverybundle', 'copy:taodeliverybundle']);
};
