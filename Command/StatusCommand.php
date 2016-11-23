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

class StatusCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('avtonom:limit-calls:status')
            ->setDescription('View a list of blocked values and statistics list')
            ->addOption('collection', 'c', InputOption::VALUE_REQUIRED, 'collection print')
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
        $collection = $input->getOption('collection');
        $limitCallsRepository = $this->getContainer()->get('avtonom_limit_number_calls.repository');
        if($collection){
            $output->writeln('for collection '.$collection);
            $output->writeln(print_r($limitCallsRepository->getAllByCollection($collection), true));
        } else {
            $output->writeln('for block ');
            $output->writeln(print_r($limitCallsRepository->getAllBlock(), true));
        }
    }
}
