<?php

namespace Spy\TimelineBundle\Command;

use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Spread\DeployerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will deploy each actions (see limit option) which
 * has PUBLISHED on status_wanted.
 */
class DeployActionCommand extends Command
{
    protected static $defaultName = 'spy_timeline:deploy';

    private ActionManagerInterface $actionManager;
    private DeployerInterface $deployer;
    private LoggerInterface $logger;

    public function __construct(
        ActionManagerInterface $actionManager,
        DeployerInterface $deployer,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->actionManager = $actionManager;
        $this->deployer = $deployer;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Deploy on spreads for waiting action')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'How many actions will be deployed', 200);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int) $input->getOption('limit');

        if ($limit < 1) {
            throw new InvalidArgumentException('Limit defined should be biggest than 0 ...');
        }

        $results = $this->actionManager->findActionsWithStatusWantedPublished($limit);

        $output->writeln(sprintf('<info>There is %s action(s) to deploy</info>', count($results)));

        foreach ($results as $action) {
            try {
                $this->deployer->deploy($action, $this->actionManager);
                $output->writeln(sprintf('<comment>Deploy action %s</comment>', $action->getId()));
            } catch (Exception $e) {
                $message = sprintf('[TIMELINE] Error during deploy action %s', $action->getId());
                if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                    $message .= sprintf(': %s', $e->getMessage());
                }

                $this->logger->critical($message);
                $output->writeln(sprintf('<error>%s</error>', $message));
            }
        }

        $output->writeln('<info>Done</info>');

        return Command::SUCCESS;
    }
}
