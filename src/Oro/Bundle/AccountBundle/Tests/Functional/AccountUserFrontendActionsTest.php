<?php

namespace Oro\Bundle\AccountBundle\Tests\Functional;

use Oro\Bundle\FrontendTestFrameworkBundle\Migrations\Data\ORM\LoadAccountUserData;
use Oro\Bundle\AccountBundle\Entity\AccountUser;

/**
 * @dbIsolation
 */
class AccountUserFrontendActionsTest extends AbstractAccountUserActionsTestCase
{
    const EMAIL = 'account.user2@test.com';

    protected function setUp()
    {
        $this->initClient(
            [],
            $this->generateBasicAuthHeader(LoadAccountUserData::AUTH_USER, LoadAccountUserData::AUTH_PW)
        );

        $this->loadFixtures(
            [
                'Oro\Bundle\AccountBundle\Tests\Functional\DataFixtures\LoadAccountUserRoleData'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccountUserEnableOperationName()
    {
        return 'orob2b_account_frontend_accountuser_enable';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccountUserDisableOperationName()
    {
        return 'orob2b_account_frontend_accountuser_disable';
    }

    /**
     * {@inheritdoc}
     */
    protected function executeOperation(AccountUser $accountUser, $operationName)
    {
        $this->client->request(
            'GET',
            $this->getUrl(
                'orob2b_frontend_action_operation_execute',
                [
                    'operationName' => $operationName,
                    'route' => 'orob2b_account_frontend_account_user_view',
                    'entityId' => $accountUser->getId(),
                    'entityClass' => 'Oro\Bundle\AccountBundle\Entity\AccountUser'
                ]
            ),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
    }
}