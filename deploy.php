<?php

namespace Deployer;

require 'recipe/laravel.php';

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('repository', 'git@gitlab.com:trackitsuite/api2.trackitsuite.com.git');

add('shared_files', []);

add('writable_dirs', []);

// Servers

host('fr1.db.expomark.es')
    ->user('rsenses')
    ->identityFile('~/.ssh/id_digitalocean')
    // ->forwardAgent() // You can use identity key, ssh config, or username/password to auth on the server.
    ->stage('production')
    ->set('deploy_path', '/var/www/api2.trackitsuite.com');

// Tasks

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo service php7.2-fpm restart');
});
after('deploy:symlink', 'php-fpm:restart');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Generate Storage symlink
desc('Execute artisan storage:link');
task('artisan:storage:link', function () {
    run('{{bin/php}} {{release_path}}/artisan storage:link');
});
after('deploy:symlink', 'artisan:storage:link');

// Migrate database before symlink new release.
// before('deploy:symlink', 'artisan:migrate');
