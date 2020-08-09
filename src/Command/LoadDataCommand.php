<?php

declare(strict_types=1);

namespace App\Command;

use Nusje2000\ProcessRunner\Executor\SequentialExecutor;
use Nusje2000\ProcessRunner\Factory\TaskListFactory;
use Nusje2000\ProcessRunner\Listener\ConsoleListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

final class LoadDataCommand extends Command
{
    protected static $defaultName = 'dev:load_data';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutput) {
            throw new UnexpectedValueException(sprintf('Expected output to be an instance of "%s".', ConsoleOutput::class));
        }

        $taskList = TaskListFactory::createFromArray([
            'php bin/console doctrine:database:drop --force --if-exists',
            'php bin/console doctrine:database:create',
            'php bin/console doctrine:migrations:migrate --no-interaction',
            'php bin/console doctrine:fixtures:load --no-interaction',
        ]);

        $executor = new SequentialExecutor(1);
        $executor->addListener(new ConsoleListener($output));
        $executor->execute($taskList);

        if ($taskList->getFailedTasks()->count() > 0) {
            return 1;
        }

        return 0;
    }
}
