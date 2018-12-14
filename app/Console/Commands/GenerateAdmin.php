<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;

class GenerateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thc:generate-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate the single admin for platform';

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
        //
        $user = Admin::updateOrCreate([
            'name' => 'admin',
            'email' => 'admin@thcreate.com.cn',
            'password' => bcrypt('THCpassword'),
        ]);

    }
}
