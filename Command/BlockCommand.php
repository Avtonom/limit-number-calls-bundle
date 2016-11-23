<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class BlockCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('avtonom:limit-calls:block')
            ->setDescription('Add value to the list of locks on value')
            ->addArgument('collection', InputOption::VALUE_REQUIRED, 'collection to block')
            ->addArgument('value', InputOption::VALUE_REQUIRED, 'value to block')
            ->addArgument('blocking_duration', InputOption::VALUE_OPTIONAL, 'blocking duration or all (second)', null)
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
        $blockingDuration = $input->getArgument('blocking_duration');
        // @todo fix my
        if(empty($blockingDuration)){
            $blockingDuration = null;
        }elseif($blockingDuration && is_array($blockingDuration)){
            $blockingDuration = current($blockingDuration);
        }
        $limitCallsRepository = $this->getContainer()->get('avtonom_limit_number_calls.repository');
        $output->writeln($limitCallsRepository->block($collection, $value, $blockingDuration));
    }
}
