<?php declare(strict_types=1);

namespace Dotdigital\Tests\Controller;

use DG\BypassFinals;
use Dotdigital\Flow\Api\Controller\ProgramsController;
use Dotdigital\Flow\Service\Client\DotdigitalClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Tests\Traits\InteractWithProgramsTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProgramControllerTest extends TestCase
{
    use InteractWithProgramsTrait;

    private const LIMIT_NOT_EXCEEDED = 900;
    private const LIMIT_EXCEEDED = 2500;

    private $dotdigitalClientMock;

    private $dotdigitalClientFactoryMock;

    protected function setUp(): void
    {
        BypassFinals::enable();
        $this->dotdigitalClientFactoryMock = $this->createMock(DotdigitalClientFactory::class);
        $this->dotdigitalClientMock = $this->createMock(DotdigitalClient::class);

        $this->dotdigitalClientFactoryMock->expects(static::any())
            ->method('createClient')
            ->willReturn($this->dotdigitalClientMock);

        $this->addressProgramsController = new ProgramsController(
            $this->dotdigitalClientFactoryMock
        );
    }

    public function testControllerForLessItemsThanTheApiLimit(): void
    {
        $this->dotdigitalClientMock->expects(static::any())
            ->method('getPrograms')
            ->willReturn($this->generateProgramCollection(self::LIMIT_NOT_EXCEEDED));

        $response = $this->addressProgramsController->showPrograms();

        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertEquals(\count(json_decode($response->getContent())), self::LIMIT_NOT_EXCEEDED);
    }

    public function testControllerForMoreItemsThanTheApiLimit(): void
    {
        $this->dotdigitalClientMock->expects(static::any())
            ->method('getPrograms')
            ->willReturnOnConsecutiveCalls(
                $this->generateProgramCollection(1000),
                $this->generateProgramCollection(1000),
                $this->generateProgramCollection(500)
            );

        $response = $this->addressProgramsController->showPrograms();

        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertEquals(\count(json_decode($response->getContent())), self::LIMIT_EXCEEDED);
    }
}
