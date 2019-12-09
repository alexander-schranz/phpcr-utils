<?php

namespace PHPCR\Tests\Util\Console\Command;

use PHPCR\Query\QueryInterface;
use PHPCR\Util\Console\Command\WorkspaceQueryCommand;
use PHPUnit\Framework\MockObject\MockObject;

class WorkspaceQueryCommandTest extends BaseCommandTest
{
    /**
     * @var QueryInterface|MockObject
     */
    protected $query;

    public function setUp()
    {
        parent::setUp();

        $this->application->add(new WorkspaceQueryCommand());
        $this->query = $this->createMock(QueryInterface::class);
    }

    public function testQuery()
    {
        $this->queryManager->expects($this->any())
            ->method('getSupportedQueryLanguages')
            ->will($this->returnValue(['JCR-SQL2']));

        $this->session->expects($this->any())
            ->method('getWorkspace')
            ->will($this->returnValue($this->workspace));

        $this->workspace->expects($this->any())
            ->method('getQueryManager')
            ->will($this->returnValue($this->queryManager));

        $this->queryManager->expects($this->once())
            ->method('createQuery')
            ->with('SELECT foo FROM foo', 'JCR-SQL2')
            ->will($this->returnValue($this->query));

        $this->query->expects($this->once())
            ->method('getLanguage')
            ->will($this->returnValue('FOOBAR'));

        $this->query->expects($this->once())
            ->method('execute')
            ->will($this->returnValue([]));

        $this->executeCommand('phpcr:workspace:query', [
            'query' => 'SELECT foo FROM foo',
        ]);
    }
}
