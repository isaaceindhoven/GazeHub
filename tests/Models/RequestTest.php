<?php

/**
 *   Do not remove or alter the notices in this preamble.
 *   This software code regards ISAAC Standard Software.
 *   Copyright © 2021 ISAAC and/or its affiliates.
 *   www.isaac.nl All rights reserved. License grant and user rights and obligations
 *   according to applicable license agreement. Please contact sales@isaac.nl for
 *   questions regarding license and user rights.
 */

declare(strict_types=1);

namespace GazeHub\Tests\Models;

use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Request;
use GazeHub\Services\ConfigRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestTest extends TestCase
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
    private $clientToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJhZG1pbiJdLCJqdGkiOiJyYW5kb21JZCJ9.JVsy8YrxrRSNHW1AnjpFpRE3G_iWBsJ4MIYeCcRJUUyQh_mb0Pg_vwvf7P8ehpC2YqWX0QcGxqmNYJa4d_de8r4oV5HKwOgc6b-oIpTpF7AO26yYfw2tguFVK37JLDW_5S49K-UK49a8G-5xRyF9cF6qUIVd-C5HxXmDUWPKbHsNM7DMLxpLASKKQw7vvo2MWVOmuOJp-whIEuc4ZuLng89iSW_pby0FlvlwC9s6HDqoP8z47ckz2Pd9a_iZkE_mHs2fQNupdTYlTB9_OAN1hvlktj3dXo4K98K-NCooQsLRtH5tohTTKqkI4ufaxItP5eb_7vz8NOgnIasV1GWk0CsRq9uv1_jrKSHY-DqZojTsRue52OdHPuvyPpFVqHrik2zH1uu62A8JJDC283XYBtO-VviRiTeD9FG20v7W4JgUcsDE7An6_CNtYX5Bj6RmWIXnNqU03av_R08hVA3hQBSROUNublCu-9uMXq3F1Z6C2Nb5uVAfF37ddfkV6E1rUn6jHYHIRy7EyKFGdM_d68TeIRJAf9oo88xFzrE_rPwXCTGKxNuc3kUXl4ecUt6FBjvjgRYPMsKxwnOZ28FP_oZFClhO_ZcJCqFjIcPGrdHpxf_xYQpE5-KsMnty9mQBXJMu2qfeLd22kr6wpkjDuQsEnyMiHHMD8PPykLkZXaw';

    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
    protected $serverToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlIjoic2VydmVyIn0.ux1bA7AhD8VBb0f2nXwfi_vRKNxol6jNkINi7Cw18aiK1-0gcoJ5Tdvqg4B6IADcX1SbvizUzKoNPVegQjJ0NIiWMOPoFY-cXF9C4IVw682heIFLX1lyJkRS6ONT-YBAyCb3vuj1LdDuVQf3B94chKu-QdMdVfmjPm2mhYpgjtDmG29cg58zofBoek1iCSHVOvt5DJFe4w0Kse1wicb-q8xlZBmjY_UN9_VgqYLj6YX15TEYzh47RMAdHoghrk53TTASaAg3f3gpvCMiXCzC4mGVvOJUuf-xa43hYISOrtQche4PZzMf_MDpkF7PLI2Nb3Ykgc__PGGXDVXfZyLYIGZwI-GqhxjwES6IqiF-N4VIsMrpkwpkFlXGz8EExLvBZjUX5IxiLky_XtG_zqKLFJmWCtXlsiDWI2AZUoRl3krrSFQAZ8XyyMZOyXlrFu5qo1P5mBOnGIqDemOQgvYUvihfjnzRXveiQrmBM2n7FbJzg1bezJR_3g0ZEINaUOXORSfz6pHLdlIxqaOUCB-7nXEmBVL1ANL4eAIDCgp8eDWmw-_G_hmeTN-nFg_5NBUgItr_ngp2iS3R5GAqrxJ6uqqPv5zgOpt4aP6rNP_n6fdhnhpKSBThqAGPudPGxHYimq8c8CrZkag_-ABXLsgZzrs-NlfEzE_PO0_u5NXbeQk';

    public function testIfAuthThrowsErrorIfNoTokenPresent()
    {
        $this->expectException(UnAuthorizedException::class);
        $request = $this->createRequest('');
        $request->isAuthorized();
    }

    public function testIfAuthThrowsErrorIfHeaderTokenIsInvalid()
    {
        $this->expectException(UnAuthorizedException::class);
        $request = $this->createRequest('ABC');
        $request->isAuthorized();
    }

    public function testIfAuthThrowsErrorIfQueryTokenIsInvalid()
    {
        $this->expectException(UnAuthorizedException::class);
        $request = $this->createRequest('', ['token' => 'ABC']);
        $request->isAuthorized();
    }

    public function testIfAuthPassesIfHeaderTokenValid()
    {
        $this->expectNotToPerformAssertions();
        $request = $this->createRequest('Bearer ' . $this->clientToken);
        $request->isAuthorized();
    }

    public function testIfAuthPassesIfQueryTokenValid()
    {
        $this->expectNotToPerformAssertions();
        $request = $this->createRequest('', ['token' => $this->clientToken]);
        $request->isAuthorized();
    }

    public function testIfRoleMatches()
    {
        $this->expectNotToPerformAssertions();
        $request = $this->createRequest($this->serverToken);
        $request->isRole('server');
    }

    public function testThrowIfRoleWrong()
    {
        $this->expectException(UnAuthorizedException::class);
        $request = $this->createRequest($this->serverToken);
        $request->isRole('server1');
    }

    private function createRequest(string $headerToken = '', array $querys = []): Request
    {
        $config = new ConfigRepository();
        $config->loadConfig(__DIR__ . '/../assets/testConfig.php');
        $originalReq = $this->createMock(ServerRequestInterface::class);
        $originalReq->method('getHeaderLine')->willReturn($headerToken);
        $originalReq->method('getQueryParams')->willReturn($querys);
        $request = new Request($config);
        $request->setOriginalRequest($originalReq);
        return $request;
    }
}
