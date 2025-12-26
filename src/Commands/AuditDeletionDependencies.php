<?php

namespace WorkDoneRight\DeletionGuard\Commands;

use Illuminate\Console\Command;

class AuditDeletionDependencies extends Command
{
    protected $signature = 'deletion-guard:audit {model} {id}';

    protected $description = 'Audit deletion blockers';

    public function handle()
    {
        $modelClass = $this->argument('model');
        $id = $this->argument('id');

        if (! class_exists($modelClass)) {
            $this->error('Model not found');

            return;
        }

        $model = $modelClass::find($id);

        if (! $model) {
            $this->error("Record does not exist on the {$modelClass}");

            return;
        }

        if (! method_exists($model, 'deletionBlockers')) {
            $this->error('No deletion guard configured.');

            return;
        }

        $blockers = $model->deletionBlockers();

        if (empty($blockers)) {
            $this->info('✅ No blockers found.');

            return;
        }

        foreach ($blockers as $blocker) {
            $this->line('❌ '.$blocker['message']);
        }
    }
}
