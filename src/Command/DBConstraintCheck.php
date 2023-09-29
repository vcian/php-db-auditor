<?php

namespace Vcian\PhpDbAuditor\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Vcian\PhpDbAuditor\Constants\Constant;
use Vcian\PhpDbAuditor\Traits\AuditService;
use function Termwind\{render};
class DBConstraintCheck extends Command
{
    use AuditService;

    protected bool $skip = Constant::STATUS_FALSE;

    protected function configure()
    {
        $this->setName('db:constraint')
            ->setDescription('This command gives you result with list of tables with primary,foreign,unique,index constraint');
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

            $tableName = $io->choice('Which table would you like to audit?',$tableList);
            self::displaySpinner($output);
            self::displayTable($tableName,$io);
            if (empty($tableName)) {
                $output->writeln('<fg=bright-red>No Table Found</>');
                return Constant::STATUS_FALSE;
            }

            if ($tableName) {

                $continue = Constant::STATUS_TRUE;

                do {
                    $noConstraintFields = $this->getNoConstraintFields($tableName);
                    if (empty($noConstraintFields)) {
                        $continue = Constant::STATUS_FALSE;
                    } else {
                        $io->newLine();
                        $ask = $io->ask('Do you want add more constraint? (yes/no) [no]');
                        if (strtolower($ask) == 'yes') {
                            $this->skip = Constant::STATUS_FALSE;
                            $constraintList = $this->getConstraintList($tableName, $noConstraintFields);
                            $selectConstrain = $io->choice('Please select a constraint which you want to add.',
                                $constraintList
                            );

                            $this->selectedConstraint($selectConstrain, $noConstraintFields, $tableName, $input, $output);
                        } else {
                            $continue = Constant::STATUS_FALSE;
                        }
                    }

                } while ($continue === Constant::STATUS_TRUE);
            }

        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }

        return Command::SUCCESS;
    }

    /**
     * Display selected table
     * @param string $tableName
     * @return void
     */
    public function displayTable(string $tableName, $io): void
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

        $viewFilePath = __DIR__ . '/../views/constraint.php';

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
     * @param string $selectConstrain
     * @param array $noConstraintFields
     * @param string $tableName
     * @return void
     */
    public function selectedConstraint(string $selectConstrain, array $noConstraintFields, string $tableName, $input, $output): void
    {

        if ($selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
            $tableHasValue = $this->tableHasValue($tableName);

            if ($tableHasValue) {
                $output->writeln(' <fg=bright-red>Can not apply '.strtolower($selectConstrain).' key | Please truncate table.</>');
            }
        }

        if (!$this->skip) {
            if ($selectConstrain === Constant::CONSTRAINT_PRIMARY_KEY || $selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
                $fields = $noConstraintFields['integer'];
            } else {
                $fields = $noConstraintFields['mix'];
            }

            if ($selectConstrain === Constant::CONSTRAINT_UNIQUE_KEY) {
                $fields = $this->getUniqueFields($tableName, $noConstraintFields['mix']);
                if (empty($fields)) {
                    $output->writeln(" <fg=bright-red>All field values are duplicate. You can't add unique constraint.</>");
                }
            }

            $io = new SymfonyStyle($input, $output);

            if (!$this->skip) {
                $selectField = $io->choice('Please select a field to add constraint' . ' ' . strtolower($selectConstrain) . ' key',
                    $fields
                );

                if ($selectConstrain === Constant::CONSTRAINT_FOREIGN_KEY) {
                    $this->foreignKeyConstraint($tableName, $selectField, $input, $output);
                } else {
                    $this->addConstraint($tableName, $selectField, $selectConstrain);
                }
            }
        }
        if (!$this->skip) {
            self::successMessage('Congratulations! Constraint Added Successfully.',$io);
            self::displayTable($tableName, $input);
        }
    }

    /**
     * Get Foreign Key Constrain
     * @param string $tableName
     * @param string $selectField
     * @return void
     */
    public function foreignKeyConstraint(string $tableName, string $selectField , $input, $output): void
    {
        $foreignContinue = Constant::STATUS_FALSE;
        $referenceField = Constant::NULL;
        $fields = Constant::ARRAY_DECLARATION;

        do {
            $io = new SymfonyStyle($input, $output);
            $referenceTable = $io->ask('Please add foreign table name.');

            if ($referenceTable && $this->checkTableExistOrNot($referenceTable)) {

                foreach ($this->getTableFields($referenceTable) as $field) {
                    $fields[] = $field['COLUMN_NAME'];
                }
                do {
                    $referenceField = $io->ask('Please add primary key name of foreign table.');

                    if (!$referenceField || !$this->checkFieldExistOrNot($referenceTable, $referenceField)) {
                        $io->error('Foreign field not found.');
                    } else {
                        $foreignContinue = Constant::STATUS_TRUE;
                    }
                } while ($foreignContinue === Constant::STATUS_FALSE);

            } else {
                $io->error('Foreign table not found.');
            }
        } while ($foreignContinue === Constant::STATUS_FALSE);

        $referenceFieldType = $this->getFieldDataType($referenceTable, $referenceField);
        $selectedFieldType = $this->getFieldDataType($tableName, $selectField);

        if ($referenceTable === $tableName) {
            $io->error("Can't add constraint because ".$tableName." table and foreign ".$referenceTable." table are same. Please use different table name.");
            $this->skip = Constant::STATUS_TRUE;
        }

        // Get the terminal width
        $terminal = new Terminal();
        $terminalWidth = $terminal->getWidth();

        $columnHeaders = ['Table Name', '<fg=bright-white>Data type</>'];
        // Calculate the size of the dotted line based on the terminal width
        $totalWidth = $terminalWidth - strlen($columnHeaders[0]) - strlen($columnHeaders[1]);
        $dotsCount = max(0, $totalWidth);
        $dots = str_repeat('.', 80);

        if ($referenceFieldType['data_type'] !== $selectedFieldType['data_type']) {
            $output->writeln(" <fg=bright-green>".$selectedFieldType['data_type']."</> <fg=bright-blue>".$selectField."</><fg=gray>".$dots."</><fg=bright-blue>".$referenceField."</> <fg=bright-green>".$referenceFieldType['data_type']."</>");
            $output->writeln("");
            $io->error('Columns must have the same datatype.');
            $this->skip = Constant::STATUS_TRUE;
        } else {
            $this->addConstraint($tableName, $selectField, Constant::CONSTRAINT_FOREIGN_KEY, $referenceTable, $referenceField);
        }
    }

    /**
     * Display success messages
     * @param string $message
     */
    public function successMessage(string $message,$io): void
    {
        $viewFilePath = __DIR__ . '/../views/success_message.php';

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
     * Display loading messages
     */
    public function displaySpinner($output): bool {
        // Display a spinner at the beginning
        $spinner = ['-', '\\', '|', '/'];
        $spinnerIndex = 0;
        $output->write('  Loading...');

        // Simulate some time-consuming task
        for ($i = 0; $i < 10; $i++) {
            usleep(100000); // Sleep for 100 milliseconds

            // Update the spinner
            $output->write("\x08" . $spinner[$spinnerIndex]);
            $spinnerIndex = ($spinnerIndex + 1) % 4;
        }

        return Constant::STATUS_TRUE;
    }
}
