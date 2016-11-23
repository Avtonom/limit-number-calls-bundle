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

class AddCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('avtonom:limit-calls:add')
            ->setDescription('Add the execution of the request in the statistics (does not establish a lock. But check for blocking)')
            ->addArgument('collection', InputOption::VALUE_REQUIRED, 'collection')
            ->addArgument('value', InputOption::VALUE_REQUIRED, 'value')
            ->addArgument('time_period', InputOption::VALUE_REQUIRED, 'time period microsecond')
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
        $timePeriod = $input->getArgument('time_period');

        $limitCallsRepository = $this->getContainer()->get('avtonom_limit_number_calls.repository');

        $result = $limitCallsRepository->add($collection, $value, $timePeriod);
        if(false == $result){
            $output->writeln('<error>not add. value in block</error>');
        } else {
            $output->writeln(sprintf('<info>count after insert: %s</info>', $result));
        }
    }
}
