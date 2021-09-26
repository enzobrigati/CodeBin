<?php

namespace App\Twig\Report;

use App\Repository\Paste\ReportRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReportExtension extends AbstractExtension
{

    public function __construct(private ReportRepository $reportRepository)
    {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('countReport', [$this, 'displayReportCount']),
        ];
    }

    public function displayReportCount(): int
    {
        return $this->reportRepository->count([]);
    }

}