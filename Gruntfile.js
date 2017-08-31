module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        sshconfig: grunt.file.readJSON('/Users/jako/Documents/MODx/partout.json'),
        banner: '/*!\n' +
        ' * <%= modx.name %> - <%= modx.description %>\n' +
        ' * Version: <%= modx.version %>\n' +
        ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
        ' */\n',
        usebanner: {
            css: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/ressavesort/css/mgr/ressavesort.min.css'
                    ]
                }
            },
            js: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/ressavesort/js/mgr/ressavesort.min.js'
                    ]
                }
            }
        },
        uglify: {
            mgr: {
                src: [
                    'source/js/mgr/ressavesort.js',
                    'source/js/mgr/ressavesort.grid.js'
                ],
                dest: 'assets/components/ressavesort/js/mgr/ressavesort.min.js'
            }
        },
        sass: {
            options: {
                outputStyle: 'expanded',
                sourcemap: false
            },
            mgr: {
                files: {
                    'source/css/mgr/ressavesort.css': 'source/sass/mgr/ressavesort.scss'
                }
            }
        },
        postcss: {
            options: {
                processors: [
                    require('pixrem')(),
                    require('autoprefixer')({
                        browsers: 'last 2 versions, ie >= 8'
                    })
                ]
            },
            mgr: {
                src: [
                    'source/css/mgr/ressavesort.css'
                ]
            }
        },
        cssmin: {
            mgr: {
                src: [
                    'source/css/mgr/ressavesort.css'
                ],
                dest: 'assets/components/ressavesort/css/mgr/ressavesort.min.css'
            }
        },
        sftp: {
            css: {
                files: {
                    "./": [
                        'assets/components/ressavesort/css/mgr/ressavesort.min.css',
                        'source/css/mgr/ressavesort.css'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/ressavesort/',
                    srcBasePath: 'develop/ressavesort/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: '<%= sshconfig.privateKey %>',
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            },
            js: {
                files: {
                    "./": [
                        'assets/components/ressavesort/js/mgr/ressavesort.min.js'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/ressavesort/',
                    srcBasePath: 'develop/ressavesort/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: '<%= sshconfig.privateKey %>',
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            scripts: {
                files: [
                    'source/**/*.js'
                ],
                tasks: ['uglify', 'usebanner:js', 'sftp:js']
            },
            css: {
                files: ['source/**/*.scss'],
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner:css', 'sftp:css']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php',
                    dest: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright 2013(-\d{4})? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2013 ? '2013-' : '') + new Date().getFullYear() + ' by'
                    }]
                }
            },
            version: {
                files: [{
                    src: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php',
                    dest: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'sass', 'postcss', 'cssmin', 'usebanner', 'sftp']);
};
