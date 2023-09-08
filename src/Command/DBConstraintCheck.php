<?php

namespace Vcian\PhpDbAuditor\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vcian\PhpDbAuditor\Traits\Rules;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vcian\PhpDbAuditor\Constants\Constant;
use Vcian\PhpDbAuditor\Traits\Audit;
use Vcian\PhpDbAuditor\Traits\AuditService;
use Vcian\PhpDbAuditor\Traits\DBConnection;

class DBConstraintCheck extends Command
{
    use AuditService;

    protected bool $skip = Constant::STATUS_FALSE;

    protected function configure()
    {
        $this->setName('constraint')
            ->setDescription('My custom command');
    }
    /**
     * Execute command to check the standard of tables.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : ?int
    {
        try {
            $tableList = $this->getTableList();
            if (!$tableList) {
                $output->writeln('<fg=bright-red>No Table Found</>');
            }

            $io = new SymfonyStyle($input, $output);
            $io->title('PHP DB Auditor');

            foreach ($tableList as $key => $tableName) {
                $tableLists[] = [$tableName.'................................................................................................................................', $key];
            }

            $io->table(
                ['Table Name','Choice Number'],
                $tableLists
            );

            do {
                $tableName = $io->ask('Which table would you like to audit?');
                $this->displayTable($tableName,$input,$output);
                if (empty($tableName)) {
                    $output->writeln('<fg=bright-red>No Table Found</>');
                    return Constant::STATUS_FALSE;
                } else {
                    $continue = Constant::STATUS_TRUE;

                    $noConstraintFields = $this->getNoConstraintFields($tableName);

                    if (empty($noConstraintFields)) {
                        $continue = Constant::STATUS_FALSE;
                    } else {
                        $ask = $io->ask('Do you want add more constraint? (yes/no) [no]');
                        if (strtolower($ask) == 'yes') {
                            $this->skip = Constant::STATUS_FALSE;
                            $constraintList = $this->getConstraintList($tableName, $noConstraintFields);
                            $selectConstrain = $io->choice('Please select a constraint which you want to add.',
                                $constraintList
                            );

                            $this->selectedConstraint($selectConstrain, $noConstraintFields, $tableName);
                        } else {
                            $continue = Constant::STATUS_FALSE;
                        }
                    }
                }

            } while ($continue === Constant::STATUS_TRUE);

            return Command::SUCCESS;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $exception->getMessage();
        }

        return Command::SUCCESS;
    }

    /**
     * Display selected table
     * @param string $tableName
     * @return void
     */
    public function displayTable(string $tableName,$input,$output): string
    {
        $data = [
            "table" => $tableName,
            "size" => $this->getTablesSize($tableName),
            "fields" => $this->getTableFields($tableName),
            'field_count' => count($this->getTableFields($tableName)),
            'constrain' => [
                'primary' => $this->getConstraintField($tableName, Constant::CONSTRAINT_PRIMARY_KEY),
                'unique' => $this->getConstraintField($tableName, Constant::CONSTRAINT_UNIQUE_KEY),
                'foreign' => $this->getConstraintField($tableName, Constant::CONSTRAINT_FOREIGN_KEY),
                'index' => $this->getConstraintField($tableName, Constant::CONSTRAINT_INDEX_KEY)
            ]
        ];

        $output->writeln('TABLE NAME: <fg=blue>'.$data['table'].'</>');
        $output->writeln('');
        $output->writeln('<fg=bright-green>Columns</><fg=bright-white> => '.$data['field_count'].'</>');
        $output->writeln('<fg=bright-green>Table Size</><fg=bright-white> => '.$data['size'].'</>');
        $output->writeln('');
        foreach ($data['fields'] as $field) {

            $tableLists[] = ['<fg=bright-white>' .$field['COLUMN_NAME'].'</>'.
                            ' <fg=bright-blue>' .$field['COLUMN_TYPE'].'</>...............................................................................................................................',
                            '<fg=bright-green>'.$field['DATA_TYPE'].'</>'];
        }
        $io = new SymfonyStyle($input, $output);
        $io->table(
            ['<fg=bright-green>Fields</>',
            '<fg=bright-white>DataType</>'],
            $tableLists
        );

        foreach ($data['constrain'] as $key => $value) {
            if($value) {
                $output->writeln('<fg=bright-green>'.strtoupper($key).'</>');
            }
            foreach ($value as $constrainField) {
                    if($key === 'foreign') {
                        $output->writeln('<fg=bright-white>'.$constrainField['column_name'].'</>.........................................................................................................................................'.
                        '<fg=blue>'.$constrainField['foreign_table_name'].'</>'.' <fg=bright-green>'.$constrainField['foreign_column_name'].'</>');
                    } else {
                        $output->writeln('<fg=bright-white>'.$constrainField.'</>.............................................................................................................................................');
                        $output->writeln('');
                }
            }
        }
        // echo "<pre>"; print_r($data);die;
        return true;
    }

    /**
     * @param string $selectConstrain
     * @param array $noConstraintFields
     * @param string $tableName
     * @return void
     */
    public function selectedConstraint(string $selectConstrain, array $noConstraintFields, string $tableName): void
    {

        $auditService = app(AuditService::class);

        if ($selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
            $tableHasValue = $auditService->tableHasValue($tableName);

            if ($tableHasValue) {
                $this->errorMessage(__('Lang::messages.constraint.error_message.constraint_not_apply', ['constraint' => strtolower($selectConstrain)]));
            }
        }

        if (!$this->skip) {
            if ($selectConstrain === Constant::CONSTRAINT_PRIMARY_KEY || $selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
                $fields = $noConstraintFields['integer'];
            } else {
                $fields = $noConstraintFields['mix'];
            }

            if ($selectConstrain === Constant::CONSTRAINT_UNIQUE_KEY) {
                $fields = $auditService->getUniqueFields($tableName, $noConstraintFields['mix']);
                if (empty($fields)) {
                    $this->errorMessage(__('Lang::messages.constraint.error_message.unique_constraint_not_apply'));
                }
            }

            if (!$this->skip) {
                $selectField = $this->choice(
                    __('Lang::messages.constraint.question.field_selection') . ' ' . strtolower($selectConstrain) . ' key',
                    $fields
                );

                if ($selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
                    $this->foreignKeyConstraint($tableName, $selectField);
                } else {
                    $auditService->addConstraint($tableName, $selectField, $selectConstrain);
                }
            }
        }

        if (!$this->skip) {
            renderUsing($this->output);

            $this->successMessage(__('Lang::messages.constraint.success_message.constraint_added'));

            $this->displayTable($tableName);
        }

    }
}
