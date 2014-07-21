<?php

namespace Jiabin\HolterBundle\Command;

use Jiabin\HolterBundle\Model\CheckInterface;
use Jiabin\HolterBundle\Model\Result;
use Jiabin\HolterBundle\Model\Status;
use React\EventLoop\Factory;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class WorkerCommand extends ContainerAwareCommand
{
    protected $loop;
    protected $timers = array();
    protected $checks = 0;

    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('holter:worker')
            ->setDescription('Starts a new holter worker')
            ->addOption('die-after', null, InputOption::VALUE_OPTIONAL, 'Worker will die after reaching this limit. (0 for unlimited)', 100)
        ;
    }

    /**
     * @see Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->loop = Factory::create();

        $output->writeln($this->getWelcome());
        $output->writeln('<info>Holter worker running...</info>');
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('holter.manager');
        $checks  = $manager->getChecks();

        foreach ($checks as $check) {
            $timer = $this->addCheckTimer($output, $check);
        }

        // Logic timer
        $this->loop->addPeriodicTimer(5, function ($timer) use ($input, $output, $manager) {
            // Wait for new checks
            $checks = $manager->getChecks();
            foreach ($checks as $check) {
                if (array_key_exists($check->getId(), $this->timers) === false) {
                    $this->addCheckTimer($output, $check);

                    $output->writeln(sprintf('<comment>A new check "%s" has been added.</comment>', $check->getName()));
                }
            }

            // Die after limit
            $dieAfter = $input->getOption('die-after');
            if ($dieAfter !== 0 && $this->checks > $dieAfter) {
                $output->writeln(sprintf('<comment>Reached maximum number (%s) of checks performed, preparing to stop...</comment>', $dieAfter));
                $timer->getLoop()->stop();
            }
        });

        $this->loop->run();
    }

    /**
     * Add check timer
     *
     * @param OutputInterface $output
     * @param CheckInterface  $check
     */
    private function addCheckTimer(OutputInterface $output, CheckInterface $check)
    {
        $id      = $check->getId();
        $name    = $check->getName();
        $manager = $this->getContainer()->get('holter.manager');

        $timer = $this->loop->addPeriodicTimer($check->getInterval() ?: 30, function ($timer) use ($manager, $output, $id, $name) {
            // Get check
            $check = $manager->getCheck($id);
            if (is_null($check)) {
                $output->writeln(sprintf('<comment>Check "%s" has been deleted.</comment>', $name));

                return $timer->cancel();
            }

            // Check
            $engine = $manager->getEngine($check->getEngine());
            $output->write(sprintf('[%s] Checking %s:%s ', date('Y-m-d\TH:i:sP'), $engine->getLabel(), $check->getName()));
            $result = $manager->check($check);
            $output->writeln($this->resultString($result));

            // Save result
            $manager->getObjectManager()->persist($result);
            $manager->getObjectManager()->flush();
            $manager->getObjectManager()->detach($check);

            $this->checks++;
        });

        $this->timers[$check->getId()] = $timer;
    }

    /**
     * Outputs result
     */
    private function resultString(Result $result)
    {
        switch ($result->getStatus()) {
            case Status::GOOD:
                $string = '<fg=green;options=bold>✔</fg=green;options=bold>';
                break;

            case Status::MINOR:
                $string = '<fg=yellow;options=bold>!</fg=yellow;options=bold>';
                break;

            case Status::MAJOR:
                $string = '<fg=red;options=bold>X</fg=red;options=bold>';
                break;

            case Status::UNKNOWN:
            default:
                $string = '<fg=blue;options=bold>?</fg=blue;options=bold>';
                break;
        }

        return $string;
    }

    /**
     * Get welcome message
     *
     * @return string
     */
    protected function getWelcome()
    {
        return strtr("  _           _ _
 | |__   ___ | | |_ ___ _ __    |
 | '_ \ / _ \| | __/ _ \ '__|   | Host: %host%
 | | | | (_) | | ||  __/ |      | PID : %pid%
 |_| |_|\___/|_|\__\___|_|      | Date: %date%
", array(
    '%host%' => gethostname(),
    '%pid%'  => getmypid(),
    '%date%' => date('c')
));
    }
}
