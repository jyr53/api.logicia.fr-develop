<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersRoleFixture
 */
class UsersRoleFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'users_role';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'user_id' => 1,
                'role_id' => 1,
            ],
        ];
        parent::init();
    }
}
