<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use GazeHub\Controllers\EventController;
use GazeHub\Models\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class EventControllerTest extends TestCase
{
    /**
     * @var string
     */
    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlIjoic2VydmVyIn0.ux1bA7AhD8VBb0f2nXwfi_vRKNxol6jNkINi7Cw18aiK1-0gcoJ5Tdvqg4B6IADcX1SbvizUzKoNPVegQjJ0NIiWMOPoFY-cXF9C4IVw682heIFLX1lyJkRS6ONT-YBAyCb3vuj1LdDuVQf3B94chKu-QdMdVfmjPm2mhYpgjtDmG29cg58zofBoek1iCSHVOvt5DJFe4w0Kse1wicb-q8xlZBmjY_UN9_VgqYLj6YX15TEYzh47RMAdHoghrk53TTASaAg3f3gpvCMiXCzC4mGVvOJUuf-xa43hYISOrtQche4PZzMf_MDpkF7PLI2Nb3Ykgc__PGGXDVXfZyLYIGZwI-GqhxjwES6IqiF-N4VIsMrpkwpkFlXGz8EExLvBZjUX5IxiLky_XtG_zqKLFJmWCtXlsiDWI2AZUoRl3krrSFQAZ8XyyMZOyXlrFu5qo1P5mBOnGIqDemOQgvYUvihfjnzRXveiQrmBM2n7FbJzg1bezJR_3g0ZEINaUOXORSfz6pHLdlIxqaOUCB-7nXEmBVL1ANL4eAIDCgp8eDWmw-_G_hmeTN-nFg_5NBUgItr_ngp2iS3R5GAqrxJ6uqqPv5zgOpt4aP6rNP_n6fdhnhpKSBThqAGPudPGxHYimq8c8CrZkag_-ABXLsgZzrs-NlfEzE_PO0_u5NXbeQk';

    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
    }

    public function testShouldReturn401IfNoTokenPresent()
    {
        // Arrange
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn401IfTokenIsEmptyString()
    {
        // Arrange
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn('');

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn401IfTokenIsInvalid()
    {
        // Arrange
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn('INVALID_TOKEN');

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn400IfTopicIsMissingFromBody()
    {
        // Arrange
        // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn($this->token);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['payload' => ['id' => 1, 'name' => 'Shirt']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn400IfPayloadIsMissingFromBody()
    {
        // Arrange
        // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn($this->token);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn200IfTokenIsCorrect()
    {
        // Arrange
        // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn($this->token);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated', 'payload' => ['id' => 1, 'name' => 'Shirt']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
