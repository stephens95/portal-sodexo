<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MigrateFresh extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'migrate:fresh';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Drop all tables, re-run migrations and seeders';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'migrate:fresh [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        '-all' => 'Refresh all migrations (default behavior)',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--seed' => 'Run seeders after migration (default: true)',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // Check if -all flag is present (optional, karena default behavior)
        $refreshAll = in_array('-all', $params) || in_array('--all', $params);
        $runSeed = !isset($params['seed']) || $params['seed'] !== 'false';

        CLI::write('🔄 Dropping all tables and re-running migrations...', 'yellow');

        // Refresh migrations (always use -all for consistency)
        command('migrate:refresh -all');

        if ($runSeed) {
            CLI::newLine();
            CLI::write('🌱 Running seeders...', 'yellow');
            command('db:seed DatabaseSeeder');
        }

        CLI::newLine();
        CLI::write('✅ Database refreshed successfully!', 'green');
    }
}
