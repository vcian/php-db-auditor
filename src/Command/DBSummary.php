<?php

namespace Vcian\PhpDbAuditor\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vcian\PhpDbAuditor\Traits\DBConnection;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Termwind\{render};

class DBSummary extends Command
{
    use DBConnection;
    protected static $defaultName = 'db:summary';

    protected function configure()
    {
        $this->setName('db:summary')
            ->setDescription('Execute the DB summary command');
    }
    /**
     * Execute command to check the standard of tables.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $data = [
            'databaseName' => $this->getDatabaseName(),
            'databaseSize' => $this->getDatabaseSize(),
            'tablistCount' => count($this->getTableList()),
            'databaseEngine' => $this->getDatabaseEngin(),
        ];
        $io = new SymfonyStyle($input, $output);

        $viewFilePath = __DIR__ . '/../views/summary.php';

        if (file_exists($viewFilePath)) {
            ob_start();
            include $viewFilePath;
            $viewContent = ob_get_clean();
        } else {
            $io->error('View file not found: '.$viewFilePath);
        }

        render($viewContent);

        return Command::SUCCESS;
    }
}
