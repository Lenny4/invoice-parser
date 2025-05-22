<?php

declare(strict_types=1);


namespace App\Command;

use App\Service\Parser\InvoiceParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Throwable;

#[AsCommand(name: self::NAME)]
class ParseInvoicesCommand extends Command
{
    public final const NAME = 'app:parse';

    public function __construct(
        private readonly InvoiceParser         $parser,
        private readonly ParameterBagInterface $param,
        private readonly LoggerInterface       $logger,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $start = microtime(true);

        $message = ' start ' . self::NAME . ' ...';
        $this->logger->info($message);
        $io->info($message);

        $finder = new Finder();
        $files = $finder->files()->in($this->param->get('FILES_DIR'));
        $nbInvoicesCreatedUpdated = 0;
        foreach ($files as $file) {
            try {
                $nbInvoicesCreatedUpdated += $this->parser->parse($file->getPathname());
            } catch (Throwable $e) {
                $this->logger->error($e->getMessage());
                $io->error($e->getMessage());
            }
        }

        $executionTime = (int)(microtime(true) - $start);
        $message = self::NAME . ' : ' . $nbInvoicesCreatedUpdated . ' invoices created/updated, finish in (' . $executionTime . 's -> ' . ($executionTime / 60) . 'm)';
        $this->logger->info($message);
        $io->info($message);

        return Command::SUCCESS;
    }
}
