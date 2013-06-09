<?php

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <i.miroslavov@gmail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Iliya Miroslavov Iliev
 * ----------------------------------------------------------------------------
 */

namespace Iliev\SymfonySchemaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Finder\Finder;
use Iliev\SymfonySchemaBundle\ParameterBag\ParameterBag;

/**
 * @author Iliya Miroslavov Iliev <i.miroslavov@gmail.com>
 */
class SymfonySchemaUpdateCommand extends ContainerAwareCommand
{
    /**
     * @var InputInterface 
     */
    private $input = null;

    /**
     * @var OutputInterface
     */
    private $output = null;
    
    /**
     * @var mixed
     */
    private $connection = null;

    /**
     * @var string
     */
    private $connectionAdapter = null;
    
    /**
     * @var ParameterBag
     */
    private $parameterBag = null;
    
    /**
     * @var integer
     */
    private $updateCounter = 0;
    
    /**
     * @var string
     */
    private $shellCommand = null;
    
    protected function configure()
    {
        $this
            ->setName('schema:update')
            ->setDescription('Applies database schema update')
            ->addArgument('update-file', InputArgument::OPTIONAL, 'Update file to be applied', null)

            ->addOption('connection', 'c', InputOption::VALUE_REQUIRED, 'Doctrine DBAL connection')
            ->addOption('dummy', null, InputOption::VALUE_NONE, 'Just insert data in the tracking table')
            ->addOption('skip-history', 'skip', InputOption::VALUE_NONE, 'Don\'t insert data in the tracking table')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update')
            ->addOption('mysql', 'm', InputOption::VALUE_OPTIONAL, 'MySQL client location')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'MySQL Username')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'MySQL Password')
            ->addOption('database', 'd', InputOption::VALUE_OPTIONAL, 'MySQL Database')
            ->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'MySQL Hostname')
            ->addOption('port', 'P', InputOption::VALUE_OPTIONAL, 'MySQL Port')

            ->setHelp(<<<EOT
The <info>%command.name%</info> command applies database update file passed as parameter or all not aplied updates yet:

  <info>php %command.full_name%</info>

  <info>php %command.full_name% [path/to/update-file.sql]</info>
EOT
            )
        ;
    }
    
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->embedIOInterfaces($input, $output);
        $this->validateWorkingPath();
        $this->initializeDatabase();
        $this->initializeParameters();
        $this->initializeProgressBar();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appliedFiles = $this->getAppliedFiles();
        $allFiles     = $this->getAllFiles();
        
        $progress = $this->getProgressBar(count($allFiles));

        foreach ($allFiles as $filename) {
            if (!in_array($this->getBasename($filename), $appliedFiles)) {
                $this->processUpdate($filename);
            }
            
            $progress->advance();
        }
        
        $progress->finish();
        
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function embedIOInterfaces(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * @throws \ErrorException
     */
    protected function validateWorkingPath()
    {
        if (!is_dir($this->getContainer()->getParameter('iliev_symfony_schema.working_path'))) {
            throw new \ErrorException(
                sprintf(
                    'Directory "%s" was not found', 
                    $this->getContainer()->getParameter('iliev_symfony_schema.working_path')
                )
            );
        }
    }
    
    protected function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->getConnectionAdapter()->createConnection();
        }
        
        return $this->connection;
    }

    /**
     * @return \Iliev\SymfonySchemaBundle\Connection\Adapter\ConnectionAdapter
     */
    protected function getConnectionAdapter()
    {
        if (null === $this->connectionAdapter) {
            $adapters = $this->getContainer()->getParameter('connection_adapters');
            
            $this->connectionAdapter = $this->getContainer()->get(
                $adapters[$this->getContainer()->getParameter('iliev_symfony_schema.database.orm')]
            );
            
            $this->connectionAdapter->initialize(
                ($this->input->getOption('connection') ?: $this->getContainer()->getParameter('iliev_symfony_schema.database.default_connection'))
            );
        }
        
        return $this->connectionAdapter;
    }

    protected function initializeDatabase()
    {
        $statement = $this->getConnection()->prepare(sprintf('SHOW TABLES LIKE "%s"', $this->getContainer()->getParameter('iliev_symfony_schema.database.table_name')));
        $statement->execute();
        
        if (false === (boolean)$statement->fetch()) {
            $filename = $this->getSchemaTableFilename();
            
            $this->validateFile($filename);
            
            $this->output->write(
                sprintf(
                    '<info>Table "%s" was not found. Creating...</info> ', 
                    $this->getContainer()->getParameter('iliev_symfony_schema.database.table_name')
                )
            );

            try {
                $this->getConnection()->exec(
                    sprintf(
                        file_get_contents($filename), 
                        $this->getContainer()->getParameter('iliev_symfony_schema.database.table_name')
                    )
                );
            } catch (\Exception $exception) {
                throw new \ErrorException(sprintf('OOPS: %s', $exception->getMessage()));
            }
            
            $this->output->writeln('<comment>Done.</comment>');
        }
    }
    
    /**
     * @return ParameterBag
     */
    protected function getParameterBag()
    {
      if (null === $this->parameterBag) {
          $this->parameterBag = new ParameterBag();
      }
      
      return $this->parameterBag;
    }

    /**
     * Initialize the command parameters
     * 
     * @param InputInterface $input
     */
    private function initializeParameters()
    {
        $connection = $this->getConnectionAdapter()->getParameterBag();
        
        $this->getParameterBag()->set('username', $this->input->getOption('username') ?: $connection->get('username'));
        $this->getParameterBag()->set('password', $this->input->getOption('password') ?: $connection->get('password'));
        $this->getParameterBag()->set('database', $this->input->getOption('database') ?: $connection->get('database'));

        $this->getParameterBag()->set('host', $this->input->getOption('host') ?: $connection->get('host'));
        $this->getParameterBag()->set('port', $this->input->getOption('port') ?: $connection->get('port'));
        
        $this->getParameterBag()->set('mysql_client', $this->input->getOption('mysql') ?: trim(`which mysql`));
    }
    
    /**
     * @return string
     */
    protected function getSchemaTableFilename()
    {
        return sprintf('%s/../Resources/database/sql/table_structure.sql', __DIR__);
    }
    
    /**
     * @param  string $filename
     * @throws \ErrorException
     */
    protected function validateFile($filename)
    {
        if (!file_exists($filename)) {
            throw new \ErrorException(sprintf('File "%s" was not found.', $filename));
        }
        
        if (!is_readable($filename)) {
            throw new \ErrorException(sprintf('File "%s" is not readable.', $filename));
        }
    }

    /**
     * Get a list of the applied updates
     * 
     * @return array
     */
    protected function getAppliedFiles()
    {
        $result = array();
        
        if (!(($this->input->getOption('force')) && ($this->input->getArgument('update-file')))) {
            $statement = $this->getConnection()->prepare(
                sprintf(
                    'SELECT `name` FROM `%s`', 
                    $this->getContainer()->getParameter('iliev_symfony_schema.database.table_name')
                )
            );
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $result[] = $row['name'];
            }
        }
        
        return $result;
    }
    
    /**
     * @return mixed
     */
    protected function getAllFiles()
    {
        if ($this->input->getArgument('update-file')) {
            $result = (array)escapeshellcmd($this->input->getArgument('update-file'));
        } else {
            $result = new Finder();
            $result
                ->files()
                ->name('*.sql')
                ->sortByName()
                ->in($this->getContainer()->getParameter('iliev_symfony_schema.working_path'))
            ;
        }
        
        return $result;
    }

    /**
     * @param  integer $maximum
     * @return ProgressHelper
     */
    protected function getProgressBar($maximum)
    {
        $result = $this->getHelperSet()->get('progress');
        $result->setFormat(ProgressHelper::FORMAT_VERBOSE);
        $result->setBarWidth(100);

        $result->start($this->output, $maximum);
        
        return $result;
    }

    /**
     * Inject a fake progress bar if the command is in verbose mode
     */
    protected function initializeProgressBar()
    {
        if ($this->inVerboseOutput()) {
            $this->getHelperSet()->set($this->getContainer()->get('iliev_symfony_schema.helper.progress'));
        }
    }
    
    /**
     * @return boolean
     */
    protected function inVerboseOutput()
    {
        return (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity());
    }
   
    /**
     * @param  string $filename
     * @return string
     */
    protected function getBasename($filename)
    {
        return pathinfo($filename, PATHINFO_BASENAME);
    }
    
    /**
     * @param string $filename
     */
    protected function processUpdate($filename)
    {
        $this->validateFile($filename);
        
        if ($this->inVerboseOutput()) {
            $this->output->writeln(sprintf('<info>Processing updates in file:</info> <comment>%s</comment>.', $filename));
        }
        
        if (!$this->input->getOption('dummy')) {
            $this->executeUpdate($filename);
        }
        
        if (!$this->input->getOption('skip-history')) {
            $this->storeUpdateInformation($filename);
        }
        
        $this->updateCounter++;
    }

    /**
     * 
     * @param  string $filename
     * @throws \ErrorException
     */
    protected function executeUpdate($filename)
    {
        $output = array();
        $return = 0;

        exec(sprintf($this->getShellCommandFormat(), $filename), $output, $return);
        
        if (0 !== $return) {
            throw new \ErrorException(sprintf("In file \"%s\"\n%s", $filename, implode(PHP_EOL, $output)));
        }
    }

    /**
     * @return string
     */
    protected function getShellCommandFormat()
    {
        if (null === $this->shellCommand) {
            $this->shellCommand = sprintf(
                '%s -h%s -P%d -D%s -u%s %s < %%s 2>&1',
                $this->getParameterBag()->get('mysql_client'),
                $this->getParameterBag()->get('host'),
                $this->getParameterBag()->get('port'),
                $this->getParameterBag()->get('database'),
                $this->getParameterBag()->get('username'),
                ((!$this->getParameterBag()->isEmpty('password')) ? sprintf('-p%s', $this->getParameterBag()->get('password')) : '')
            );
        }
        
        return $this->shellCommand;
    }

    /**
     * @param string $filename
     */
    protected function storeUpdateInformation($filename)
    {
        $statement = $this->getConnection()->prepare(
            sprintf(
                'INSERT INTO `%s` (name, description) VALUES (:name, :description)', 
                $this->getContainer()->getParameter('iliev_symfony_schema.database.table_name')
            )
        );
        
        $statement->bindValue('name',        $this->getBasename($filename));
        $statement->bindValue('description', $this->getDescriptionFromFile($filename));
        $statement->execute();
        $statement->closeCursor();
    }
    
    /**
     * Get the description of the update
     * 
     * @param  string $filename
     * @return string
     */
    protected function getDescriptionFromFile($filename)
    {
        $result = '';
        $matches = array();
        $multilineDescription = false;

        $handle = fopen($filename, 'r');
        
        while (!feof($handle)) {
            $line = fgets($handle);
            
            if ($multilineDescription) {
                if (1 === preg_match('/.*(?P<description>.*)<\/description>.*\n/', $line, $matches)) {
                    $result .= $matches['description'];
                    break;
                } else {
                    $result .= $line;
                }
            } else {
                if (1 === preg_match('/.*<description>(?P<description>.*).*\n/', $line, $matches)) {
                    $result .= $matches['description'];
                    $multilineDescription = true;
                    continue;
                }

                if (1 === preg_match('/.*#\s+-->\s+(?P<description>.*).*\n/', $line, $matches)) {
                    $result = $matches['description'];
                    break;
                }
            }
        }
        
        fclose($handle);
        
        if ((empty($result)) && ($this->inVerboseOutput())) {
            $this->output->writeln(sprintf('<info>No description found in file: </info> <comment>%s</comment>.', $filename));
        }
        
        return $result;
    }
    
}
