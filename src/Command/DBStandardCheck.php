<?php

namespace Vcian\PhpDbAuditor\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vcian\PhpDbAuditor\Traits\Rules;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vcian\PhpDbAuditor\Constants\Constant;
use function Termwind\{render};

class DBStandardCheck extends Command
{
    use Rules;
    protected static $defaultName = 'db:standard';

    protected function configure()
    {
        $this->setName('db:standard')
            ->setDescription('This command provides a list of available commands for database selection, such as checking database standards or verifying constraints');
    }
    /**
     * Execute command to check the standard of tables.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        $tableStatus = $this->tablesRule(); // Check multiple table rules

        if (!$tableStatus) {
            $output->writeln('<fg=bright-red>No Table Found</>');
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('PHP DB Auditor');
        self::checkStandard($tableStatus, $io);

        $continue = Constant::STATUS_TRUE;

        do {
            $tableName = $io->ask('Please enter table name if you want to see the table report');

            if (empty($tableName)) {
                self::errorMessage($io);
                return Constant::STATUS_FALSE;
            }

            $tableStatus = $this->tableRules($tableName);
            if (!$tableStatus) {
                self::errorMessage($io);
            } else {
                self::failStandardTable($tableStatus, $io);
            }

            $report = $io->confirm("Do you want see other table report?");

            if (!$report) {
                $continue = Constant::STATUS_FALSE;
            }
        } while ($continue === Constant::STATUS_TRUE);

        return Command::SUCCESS;
    }

    /**
     * Check multiple table standardization
     */
    public function checkStandard($tableStatus, $io) : void {

        $viewFilePath = __DIR__ . '/../views/standard.php';

        if (file_exists($viewFilePath)) {
            ob_start();
            include $viewFilePath;
            $viewContent = ob_get_clean();
        } else {
            $io->error('View file not found: '.$viewFilePath);
        }
        render($viewContent);
    }
    /**
     * Check table rules, datatypes and suggestions
     */
    public function failStandardTable($tableStatus, $io) : void {

        $viewFilePath = __DIR__ . '/../views/fail_standard_table.php';

        if (file_exists($viewFilePath)) {
            ob_start();
            include $viewFilePath;
            $viewContent = ob_get_clean();
        } else {
            $io->error('View file not found: '.$viewFilePath);
        }
        render($viewContent);
    }

    public function errorMessage($io) : void {
        $viewFilePath = __DIR__ . '/../views/error_message.php';

        if (file_exists($viewFilePath)) {
            $message = "No Table Found";
            ob_start();
            include $viewFilePath;
            $viewContent = ob_get_clean();
        } else {
            $io->error('View file not found: '.$viewFilePath);
        }
        render($viewContent);
    }
}
