<?php declare(strict_types=1);

namespace Dotdigital\Tests\Controller;

use DG\BypassFinals;
use Dotdigital\Flow\Api\Controller\AddressBooksController;
use Dotdigital\Flow\Service\Client\DotdigitalClient;
use Dotdigital\Flow\Service\Client\DotdigitalClientFactory;
use Dotdigital\Tests\Traits\InteractWithAddressBooksTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddressBooksControllerTest extends TestCase
{
    use InteractWithAddressBooksTrait;

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

        $this->addressBooksController = new AddressBooksController(
            $this->dotdigitalClientFactoryMock
        );
    }

    public function testControllerForLessItemsThanTheApiLimit(): void
    {
        $this->dotdigitalClientMock->expects(static::any())
            ->method('getAddressBooks')
            ->willReturn($this->generateAddressBookCollection(self::LIMIT_NOT_EXCEEDED));

        $response = $this->addressBooksController->showAddressBooks();

        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertEquals(\count(json_decode($response->getContent())), self::LIMIT_NOT_EXCEEDED);
    }

    public function testControllerForMoreItemsThanTheApiLimit(): void
    {
        $this->dotdigitalClientMock->expects(static::any())
            ->method('getAddressBooks')
            ->willReturnOnConsecutiveCalls(
                $this->generateAddressBookCollection(1000),
                $this->generateAddressBookCollection(1000),
                $this->generateAddressBookCollection(500)
            );

        $response = $this->addressBooksController->showAddressBooks();

        static::assertInstanceOf(JsonResponse::class, $response);
        static::assertEquals(\count(json_decode($response->getContent())), self::LIMIT_EXCEEDED);
    }
}
