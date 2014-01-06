<?php

namespace Jiabin\HolterBundle\Command;

use Jiabin\HolterBundle\Events;
use Jiabin\HolterBundle\Event\CheckEvent;
use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CheckCommand extends ContainerAwareCommand
{
    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('holter:check')
            ->setDescription('Runs health checks')
            ->addArgument('check', InputArgument::OPTIONAL, 'Check id to execute')
        ;
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dispatcher = $container->get('event_dispatcher'); 
        $cf = $container->get('holter.check_factory');

        if ($id = $input->getArgument('check')) {
            $check = $cf->getCheck($id);
            if (!$check) {
                return $output->writeln('<error>Check '.$id.' not found!</error>');
            }
            $checks = array($check);
        } else {
            $checks = $cf->getChecks();
        }

        foreach ($checks as $check) {
            $output->write(sprintf('Checking %s ', $check->getName()));
            $result = $cf->check($check);
            $output->writeln($this->resultString($result));

            $event = new CheckEvent($result);
            $dispatcher->dispatch(Events::HOLTER_CHECK, $event);
        }
    }

    /**
     * Outputs result 
     */
    private function resultString(Result $result)
    {
        switch ($result->getStatus()) {
            case Result::GOOD:
                $string = '<fg=green;options=bold>✔</fg=green;options=bold>';
                break;

            case Result::MINOR:
                $string = '<fg=yellow;options=bold>!</fg=yellow;options=bold>';
                break;

            case Result::MAJOR:
                $string = '<fg=red;options=bold>X</fg=red;options=bold>';
                break;

            case Result::UNKNOWN:
            default:
                $string = '<fg=blue;options=bold>?</fg=blue;options=bold>';
                break;
        }

        return $string;
    }
}
