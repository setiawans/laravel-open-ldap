<?php
namespace Setiawans\OpenLDAP\Console;

use Illuminate\Console\Command;

/**
 * Publish the openLDAP config to the config directory
 *
 * @author Toriq Setiawan <toriqbagus@gmail.com>
 *
 */
class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'openldap:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the openLDAP config';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info(
            'Congratulation!.'
        );
    }
}
