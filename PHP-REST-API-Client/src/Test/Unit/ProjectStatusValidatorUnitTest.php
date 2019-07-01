<?php
declare(strict_types=1);

use Eurotext\RestApiClient\Api\ProjectV1ApiInterface;
use Eurotext\RestApiClient\Enum\ProjectStatusEnum;
use Eurotext\RestApiClient\Request\Data\Project\ItemData;
use Eurotext\RestApiClient\Response\ProjectGetResponse;
use Eurotext\RestApiClient\Validator\ProjectStatusValidator;
use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use PHPUnit\Framework\TestCase;

/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */
class ProjectStatusValidatorUnitTest extends TestCase
{
    /** @var ProjectStatusValidator */
    private $sut;

    /** @var ProjectV1ApiInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $projectApi;

    protected function setUp()
    {
        parent::setUp();

        $this->projectApi = $this->createMock(ProjectV1ApiInterface::class);

        $this->sut = new ProjectStatusValidator($this->projectApi);
    }

    public function testItShouldValidateProjectStatus()
    {
        $projectId = 1;

        $project = $this->createMock(ProjectInterface::class);
        $project->expects($this->once())->method('getExtId')->willReturn($projectId);
        /** @var ProjectInterface $project */

        $items    = [
            new ItemData(['status' => 'finished'], []),
        ];
        $response = new ProjectGetResponse('', $items, []);

        $this->projectApi->expects($this->once())->method('get')->willReturn($response);

        $result = $this->sut->validate($project, ProjectStatusEnum::FINISHED());

        $this->assertTrue($result);
    }

    public function testItShouldReturnFalseIfOneItemHasADiverentStatus()
    {
        $projectId = 1;

        $project = $this->createMock(ProjectInterface::class);
        $project->expects($this->once())->method('getExtId')->willReturn($projectId);
        /** @var ProjectInterface $project */

        $items    = [
            new ItemData(['status' => 'finished'], []),
            new ItemData(['status' => 'in_progress'], []),
        ];
        $response = new ProjectGetResponse('', $items, []);

        $this->projectApi->expects($this->once())->method('get')->willReturn($response);

        $result = $this->sut->validate($project, ProjectStatusEnum::FINISHED());

        $this->assertFalse($result);
    }
}