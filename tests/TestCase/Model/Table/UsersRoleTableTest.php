<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersRoleTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersRoleTable Test Case
 */
class UsersRoleTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersRoleTable
     */
    protected $UsersRole;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.UsersRole',
        'app.Users',
        'app.Role',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UsersRole') ? [] : ['className' => UsersRoleTable::class];
        $this->UsersRole = $this->getTableLocator()->get('UsersRole', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->UsersRole);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\UsersRoleTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\UsersRoleTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
