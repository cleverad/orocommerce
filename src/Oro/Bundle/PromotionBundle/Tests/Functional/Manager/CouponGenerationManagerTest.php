<?php

namespace Oro\Bundle\PromotionBundle\Tests\Functional\Manager;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData;
use Oro\Bundle\PromotionBundle\CouponGeneration\Options\CodeGenerationOptions;
use Oro\Bundle\PromotionBundle\CouponGeneration\Options\CouponGenerationOptions;
use Oro\Bundle\PromotionBundle\Entity\Coupon;
use Oro\Bundle\PromotionBundle\Entity\Promotion;
use Oro\Bundle\PromotionBundle\Manager\CouponGenerationManager;
use Oro\Bundle\PromotionBundle\Tests\Functional\DataFixtures\LoadPromotionData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class CouponGenerationManagerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures([
            LoadPromotionData::class,
        ]);
    }

    public function testGenerateCoupons()
    {
        /** @var Promotion $promotion */
        $promotion = $this->getReference(LoadPromotionData::ORDER_AMOUNT_PROMOTION);
        /** @var BusinessUnit $businessUnit */
        $businessUnit = $this->getDoctrineHelper()
            ->getEntityRepository(BusinessUnit::class)
            ->findOneBy(['name' => LoadOrganizationAndBusinessUnitData::MAIN_BUSINESS_UNIT]);

        $options = new CouponGenerationOptions();
        $options->setOwner($businessUnit);
        $options->setExpirationDate(new \DateTime('01-01-2020 12:00:00'));
        $options->setPromotion($promotion);

        $options->setCouponQuantity(10000);
        $options->setCodeLength(5);
        $options->setCodeType(CodeGenerationOptions::NUMERIC_CODE_TYPE);

        /** @var CouponGenerationManager $manager */
        $manager = $this->getContainer()->get('oro_promotion.coupon_generation.manager');

        $start = microtime(true);
        $manager->generateCoupons($options);
        $elapsed = round(microtime(true) - $start, 4);

        $stmnt = $this->getDoctrineHelper()
            ->getEntityManager(Coupon::class)
            ->getConnection()
            ->prepare('SELECT COUNT(*) FROM oro_promotion_coupon');
        $stmnt->execute();
        fwrite(STDERR, print_r('Elapsed: '. $elapsed .PHP_EOL, true));
        fwrite(STDERR, print_r('Inserted: ' . ($inserted = $stmnt->fetchColumn(0)) . PHP_EOL, true));
        $this->assertEquals(10000, $inserted);
    }
    /**
     * @return DoctrineHelper
     */
    protected function getDoctrineHelper()
    {
        return $this->getContainer()->get('oro_entity.doctrine_helper');
    }
}
