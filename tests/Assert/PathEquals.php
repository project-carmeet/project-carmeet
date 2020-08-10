<?php

declare(strict_types=1);

namespace App\Tests\Assert;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\BrowserKit\AbstractBrowser;

final class PathEquals extends Constraint
{
    /**
     * @var string
     */
    protected $expectedPath;

    public function __construct(string $expectedPath)
    {

        $this->expectedPath = $expectedPath;
    }

    protected function matches($subject): bool
    {
        if (!$subject instanceof AbstractBrowser) {
            return false;
        }

        $currentRequest = $subject->getInternalRequest();
        $parsed = parse_url($currentRequest->getUri());

        return ($parsed['path'] ?? '') === $this->expectedPath;
    }

    public function toString(): string
    {
        return 'path equals';
    }

    protected function failureDescription($subject): string
    {
        if ($subject instanceof AbstractBrowser) {
            return sprintf('Failed asserting that "%s" equals expected "%s"', $this->getPath($subject), $this->expectedPath);
        }

        return sprintf('Subject is not an instance of "%s".', AbstractBrowser::class);
    }

    private function getPath(AbstractBrowser $browser): string
    {
        $currentRequest = $browser->getInternalRequest();
        $parsed = parse_url($currentRequest->getUri());

        return $parsed['path'] ?? '';
    }
}
