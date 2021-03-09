<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ControllerTestCase extends TestCase
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
    protected $clientToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlcyI6WyJhZG1pbiJdLCJqdGkiOiJyYW5kb21JZCJ9.JVsy8YrxrRSNHW1AnjpFpRE3G_iWBsJ4MIYeCcRJUUyQh_mb0Pg_vwvf7P8ehpC2YqWX0QcGxqmNYJa4d_de8r4oV5HKwOgc6b-oIpTpF7AO26yYfw2tguFVK37JLDW_5S49K-UK49a8G-5xRyF9cF6qUIVd-C5HxXmDUWPKbHsNM7DMLxpLASKKQw7vvo2MWVOmuOJp-whIEuc4ZuLng89iSW_pby0FlvlwC9s6HDqoP8z47ckz2Pd9a_iZkE_mHs2fQNupdTYlTB9_OAN1hvlktj3dXo4K98K-NCooQsLRtH5tohTTKqkI4ufaxItP5eb_7vz8NOgnIasV1GWk0CsRq9uv1_jrKSHY-DqZojTsRue52OdHPuvyPpFVqHrik2zH1uu62A8JJDC283XYBtO-VviRiTeD9FG20v7W4JgUcsDE7An6_CNtYX5Bj6RmWIXnNqU03av_R08hVA3hQBSROUNublCu-9uMXq3F1Z6C2Nb5uVAfF37ddfkV6E1rUn6jHYHIRy7EyKFGdM_d68TeIRJAf9oo88xFzrE_rPwXCTGKxNuc3kUXl4ecUt6FBjvjgRYPMsKxwnOZ28FP_oZFClhO_ZcJCqFjIcPGrdHpxf_xYQpE5-KsMnty9mQBXJMu2qfeLd22kr6wpkjDuQsEnyMiHHMD8PPykLkZXaw';
    
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.Files.LineLength.LineTooLong
    protected $serverToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJyb2xlIjoic2VydmVyIn0.ux1bA7AhD8VBb0f2nXwfi_vRKNxol6jNkINi7Cw18aiK1-0gcoJ5Tdvqg4B6IADcX1SbvizUzKoNPVegQjJ0NIiWMOPoFY-cXF9C4IVw682heIFLX1lyJkRS6ONT-YBAyCb3vuj1LdDuVQf3B94chKu-QdMdVfmjPm2mhYpgjtDmG29cg58zofBoek1iCSHVOvt5DJFe4w0Kse1wicb-q8xlZBmjY_UN9_VgqYLj6YX15TEYzh47RMAdHoghrk53TTASaAg3f3gpvCMiXCzC4mGVvOJUuf-xa43hYISOrtQche4PZzMf_MDpkF7PLI2Nb3Ykgc__PGGXDVXfZyLYIGZwI-GqhxjwES6IqiF-N4VIsMrpkwpkFlXGz8EExLvBZjUX5IxiLky_XtG_zqKLFJmWCtXlsiDWI2AZUoRl3krrSFQAZ8XyyMZOyXlrFu5qo1P5mBOnGIqDemOQgvYUvihfjnzRXveiQrmBM2n7FbJzg1bezJR_3g0ZEINaUOXORSfz6pHLdlIxqaOUCB-7nXEmBVL1ANL4eAIDCgp8eDWmw-_G_hmeTN-nFg_5NBUgItr_ngp2iS3R5GAqrxJ6uqqPv5zgOpt4aP6rNP_n6fdhnhpKSBThqAGPudPGxHYimq8c8CrZkag_-ABXLsgZzrs-NlfEzE_PO0_u5NXbeQk';

    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
    }

    protected function getRequestMock(string $token = ''): MockObject
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->expects($this->any())->method('hasHeader')->willReturn(true);
        $requestMock->expects($this->any())->method('getHeaderLine')->willReturn($token);
        $requestMock->expects($this->any())->method('getQueryParams')->willReturn([]);
        return $requestMock;
    }
}
