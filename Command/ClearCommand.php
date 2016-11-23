<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpFoundation\Request;

class ClearCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('avtonom:limit-calls:clear')
            ->setDescription('Remove statistics for the values for')
            ->addArgument('collection', InputOption::VALUE_REQUIRED, 'collection')
            ->addArgument('value', InputOption::VALUE_REQUIRED, 'value')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input  input
     * @param \Symfony\Component\Console\Output\OutputInterface $output output
     *
     * @return string
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $input->getArgument('collection');
        $value = $input->getArgument('value');

        $limitCallsRepository = $this->getContainer()->get('avtonom_limit_number_calls.repository');

        $result = $limitCallsRepository->clear($collection, $value);
        $output->writeln("Clearing counts for " .$collection. $value. print_r($result, true));
    }
}
