<?php return array (
  'providers' => 
  array (
    0 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    1 => 'Illuminate\\Database\\DatabaseServiceProvider',
    2 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    3 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
    4 => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    5 => 'Tolerance\\Bridge\\Laravel\\Provider\\ToleranceProvider',
  ),
  'eager' => 
  array (
    0 => 'Illuminate\\Database\\DatabaseServiceProvider',
    1 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    2 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
    3 => 'Tolerance\\Bridge\\Laravel\\Provider\\ToleranceProvider',
  ),
  'deferred' => 
  array (
    'command.clear-compiled' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.auth.resets.clear' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.config.cache' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.config.clear' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.down' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.environment' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.key.generate' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.optimize' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.route.cache' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.route.clear' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.route.list' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.tinker' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.up' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'command.view.clear' => 'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider',
    'Illuminate\\Console\\Scheduling\\ScheduleRunCommand' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'migrator' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'migration.repository' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.rollback' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.reset' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.refresh' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.install' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.status' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'migration.creator' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.migrate.make' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'seeder' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.seed' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'composer' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.queue.failed' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.queue.retry' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.queue.forget' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
    'command.queue.flush' => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
  ),
  'when' => 
  array (
    'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider' => 
    array (
    ),
    'Illuminate\\Foundation\\Providers\\ArtisanServiceProvider' => 
    array (
    ),
  ),
);