<?php

namespace Spy\TimelineBundle\Command;

use Spy\Timeline\Spread\DeployerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will show all services which are defined as spread.
 */
class SpreadListCommand extends Command
{
    protected static $defaultName = 'spy_timeline:spreads';

    private DeployerInterface $deployer;

    public function __construct(DeployerInterface $deployer)
    {
        parent::__construct();
        $this->deployer = $deployer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Show list of spreads');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spreads = $this->deployer->getSpreads();

        $output->writeln(sprintf('<info>There is %s timeline spread(s) defined</info>', count($spreads)));

        foreach ($spreads as $spread) {
            $output->writeln(sprintf('<comment>- %s</comment>', get_class($spread)));
        }

        return Command::SUCCESS;
    }
}
