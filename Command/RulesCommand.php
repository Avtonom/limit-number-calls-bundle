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

class RulesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('avtonom:limit-calls:rules')
            ->setDescription('Open the list current words for checking locks')
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
        $limitCallsManager = $this->getContainer()->get('avtonom_limit_number_calls.manager');
        $output->writeln(print_r($limitCallsManager->getRules(), true));
    }
}
