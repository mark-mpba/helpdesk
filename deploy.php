<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'Laravel Helpdesk Application');          // The Application Title
set('repository', 'git@github.com:mark-mpba/helpdesk.git');   // SCM Target
set('keep_releases', 3);                                     // Number of releases to keep on hosts
set('default_timeout', 1200);                                // default ssh timeout

add('shared_files', ['.env']);                                                    // shared files
add('shared_dirs', ['storage', 'vendor', 'node_modules', 'bootstrap/cache']);      // Shared dirs between deploys
add('writable_dirs', ['storage', 'vendor', 'node_modules', 'bootstrap/cache']);    // Writable dirs by web server

set('bin/php', function () {
    return '/usr/bin/php8.2';
});


// Hosts

host('main')
    ->set('hostname', 'www.absdev.net')
    ->set('remote_user', 'mag')
    ->set('identityFile', '~/.ssh/id_rsa')
    ->set('deploy_path', '/var/www/helpdesk_old/prod')
    ->set('writable_use_sudo', false)
    ->set('use_relative_symlink', true)
    ->set('http_user', 'mag')
    ->set('branch', 'main')
    ->set('ssh_multiplexing', true)
    ->set('git_tty', false)
    ->set('ssh_type', 'native');


// Hooks
after('deploy:success', 'artisan:config:clear');
after('deploy:success', 'artisan:route:clear');
after('deploy:success', 'artisan:cache:clear');
after('deploy:failed', 'deploy:unlock');
