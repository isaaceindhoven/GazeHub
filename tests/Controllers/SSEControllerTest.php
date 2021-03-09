<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use GazeHub\Controllers\SSEController;
use GazeHub\Models\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class SSEControllerTest extends TestCase
{
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
        $controller = $this->container->get(SSEController::class);

        // Assert
        $response = $controller->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn200IfTokenIsCorrect()
    {
        // Arrange
        // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
        $validToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJhZG1pbiJdLCJqdGkiOiJyYW5kb21JZCJ9.JVsy8YrxrRSNHW1AnjpFpRE3G_iWBsJ4MIYeCcRJUUyQh_mb0Pg_vwvf7P8ehpC2YqWX0QcGxqmNYJa4d_de8r4oV5HKwOgc6b-oIpTpF7AO26yYfw2tguFVK37JLDW_5S49K-UK49a8G-5xRyF9cF6qUIVd-C5HxXmDUWPKbHsNM7DMLxpLASKKQw7vvo2MWVOmuOJp-whIEuc4ZuLng89iSW_pby0FlvlwC9s6HDqoP8z47ckz2Pd9a_iZkE_mHs2fQNupdTYlTB9_OAN1hvlktj3dXo4K98K-NCooQsLRtH5tohTTKqkI4ufaxItP5eb_7vz8NOgnIasV1GWk0CsRq9uv1_jrKSHY-DqZojTsRue52OdHPuvyPpFVqHrik2zH1uu62A8JJDC283XYBtO-VviRiTeD9FG20v7W4JgUcsDE7An6_CNtYX5Bj6RmWIXnNqU03av_R08hVA3hQBSROUNublCu-9uMXq3F1Z6C2Nb5uVAfF37ddfkV6E1rUn6jHYHIRy7EyKFGdM_d68TeIRJAf9oo88xFzrE_rPwXCTGKxNuc3kUXl4ecUt6FBjvjgRYPMsKxwnOZ28FP_oZFClhO_ZcJCqFjIcPGrdHpxf_xYQpE5-KsMnty9mQBXJMu2qfeLd22kr6wpkjDuQsEnyMiHHMD8PPykLkZXaw';
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->once())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->once())->method('getHeaderLine')->willReturn($validToken);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SSEController::class);

        // Assert
        $response = $controller->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
