<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class UpdateDelayedProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update projects status to delayed if due date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Project::updateDelayedProjects();
        $this->info("Delayed project's status is updated successfully.");
        return Command::SUCCESS;
    }
}
