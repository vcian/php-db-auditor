<?php

namespace Vcian\PhpDbAuditor\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Vcian\PhpDbAuditor\Traits\Rules;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vcian\PhpDbAuditor\Constants\Constant;

class DBStandardCheck extends Command
{
    use Rules;
    protected static $defaultName = 'db:standard';

    protected function configure()
    {
        $this->setName('db:standard')
            ->setDescription('Execute the DB standard command')
            ->setHelp('This command give you result with list of table with standard follow indication.');
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
        self::checkStandard($tableStatus, $io, $output);

        $continue = Constant::STATUS_TRUE;

        do {
            $tableName = $io->ask('Please enter table name if you want to see the table report');

            if (empty($tableName)) {
                $output->writeln('<fg=bright-red>No Table Found</>');
                return Constant::STATUS_FALSE;
            }

            $tableStatus = $this->tableRules($tableName);
            if (!$tableStatus) {
                $output->writeln('<fg=bright-red>No Table Found</>');
                return Constant::STATUS_FALSE;
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
    public function checkStandard($tableStatus, $io, $output) : void {
        $success = 0;
        $error = 0;

        foreach ($tableStatus as $table) {

            $dotsCount = max(0, 110 - strlen($table['name']));
            $dots = str_repeat('.', $dotsCount);

            if($table['status']) {
                $status = '<fg=bright-green>✓</>';
                $success++;
            } else {
                $status = '<fg=bright-red>✗</>';
                $error++;
            }
            $tableLists[] = [$table['name']. '<fg=bright-blue> ('.$table['size'].' MB)</><fg=gray>'.$dots.'</>', $status];
        }

        $io->table(
            ['Table Name', '<fg=bright-white>Standardization</>'],
            $tableLists
        );

        $output->writeln('  <fg=bright-green><bg=green>'.$success.'</> TABLE PASSED ✓</>');
        $output->writeln('  <fg=bright-red><bg=red>'.$error.'</> TABLE FAILED ✗</>');
        $io->newLine();

    }
    /**
     * Check table rules, datatypes and suggestions
     */
    public function failStandardTable($tableStatus, $io) : void {
        $io->text('TABLE NAME : <fg=bright-blue>'. str_replace('_', ' ', $tableStatus['table'])."</>");
        $io->newLine();

        $io->text('suggestion(s)');
        $io->newLine();

        foreach ($tableStatus['table_comment'] as $comment) {
            $io->text('1. <fg=bright-yellow>'.$comment.'</>');
        }
        $handEmoji = "\u{1F449}";
        // echo "<pre>"; print_r($tableStatus);die;
        foreach ($tableStatus['fields'] as $key => $field) {

            if ((isset($field['suggestion']) && isset($field['datatype']) && count($field) === 2) || count($field) === 1) {
                $stanradCheck = '<fg=bright-green>✓</>';
                $suggestion = isset($field['suggestion'])?$handEmoji.'  '.$field['suggestion']:"";
                $fieldName = $key;
            } else {
                $stanradCheck = '<fg=bright-red>✗</>';
                $suggestion = isset($field[0])?$handEmoji.'  '.$field[0]:"";
                $fieldName = '<fg=bright-red>'.$key.'</>';
            }

            $reportLists[] = [ $fieldName, $stanradCheck , $field['datatype']['data_type'] ?? "-", $field['datatype']['size'] ?? "-", '<fg=bright-yellow>'. $suggestion.'</>'?? "-" ];

            if(isset($field['datatype'])) {
                unset($field['datatype']);
            }
        }

        $io->table(
            ['field name', 'standard check', 'datatype', 'size', 'suggestion(s)'],
            $reportLists
        );

    }
}
