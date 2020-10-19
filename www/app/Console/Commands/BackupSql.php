<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:backup_db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Database Every Day By Cron Job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $command = "docker exec $(docker ps -q -f name=lemp_mariadb) /usr/bin/mysqldump -u lemp --password=123456 lemp_db > ./backup_".now()->timestamp.".sql";
        exec($command);
    }
}
