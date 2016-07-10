module.exports = function(grunt) {
    'use strict';

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoDelivery/views/';

    sass.taodelivery = { };
    sass.taodelivery.files = { };
    sass.taodelivery.files[root + 'css/delivery.css'] = root + 'scss/delivery.scss';
    sass.taodelivery.files[root + 'css/web-browsers-form.css'] = root + 'scss/web-browsers-form.scss';

    watch.taodeliverysass = {
        files : [root + 'scss/**/*.scss'],
        tasks : ['sass:taodelivery', 'notify:taodeliverysass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taodeliverysass = {
        options: {
            title: 'Grunt SASS',
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taodeliverysass', ['sass:taodelivery']);
};
