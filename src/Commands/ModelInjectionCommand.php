<?php declare(strict_types=1);

namespace Soyhuce\ModelInjection\Commands;

use Illuminate\Console\Command;

class ModelInjectionCommand extends Command
{
    public $signature = 'laravel-model-injection';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
