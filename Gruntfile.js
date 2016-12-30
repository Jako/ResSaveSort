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
            ressavesort: {
                src: [
                    'source/js/mgr/ressavesort.js',
                    'source/js/mgr/ressavesort.grid.js'
                ],
                dest: 'assets/components/ressavesort/js/mgr/ressavesort.min.js'
            }
        },
        sftp: {
            js: {
                files: {
                    "./": ['assets/components/ressavesort/js/mgr/ressavesort.min.js']
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
            js: {
                files: [
                    'source/js/mgr/**/*.js'
                ],
                tasks: ['uglify', 'usebanner:js', 'sftp:js']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/ressavesort/elements/**/*.php',
                    dest: 'core/components/ressavesort/elements/'
                }, {
                    src: 'source/js/mgr/**/*.js',
                    dest: 'source/js/mgr/'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright 2013(-\d{4})? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2013 ? '2013-' : '') + new Date().getFullYear() + ' by'
                    }, {
                        pattern: /(@copyright .*?) 2013(-\d{4})?/g,
                        replacement: '$1 ' + (new Date().getFullYear() > 2013 ? '2013-' : '') + new Date().getFullYear()
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
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'usebanner', 'sftp']);
};
