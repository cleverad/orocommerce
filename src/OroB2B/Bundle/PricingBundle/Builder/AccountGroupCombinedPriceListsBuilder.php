<?php

namespace OroB2B\Bundle\PricingBundle\Builder;

use OroB2B\Bundle\AccountBundle\Entity\AccountGroup;
use OroB2B\Bundle\PricingBundle\Entity\PriceListAccountGroupFallback;
use OroB2B\Bundle\PricingBundle\Entity\Repository\PriceListToAccountGroupRepository;
use OroB2B\Bundle\WebsiteBundle\Entity\Website;

/**
 * @method PriceListToAccountGroupRepository getPriceListToEntityRepository()
 */
class AccountGroupCombinedPriceListsBuilder extends AbstractCombinedPriceListBuilder
{
    /**
     * @var AccountCombinedPriceListsBuilder
     */
    protected $accountCombinedPriceListsBuilder;

    /**
     * @param AccountCombinedPriceListsBuilder $builder
     */
    public function setAccountCombinedPriceListsBuilder(AccountCombinedPriceListsBuilder $builder)
    {
        $this->accountCombinedPriceListsBuilder = $builder;
    }

    /**
     * @param Website $website
     * @param AccountGroup|null $currentAccountGroup
     * @param boolean|false $force
     */
    public function build(Website $website, AccountGroup $currentAccountGroup = null, $force = false)
    {
        $accountGroups = [$currentAccountGroup];
        if (!$currentAccountGroup) {
            $accountGroups = $this->getPriceListToEntityRepository()
                ->getAccountGroupIteratorByFallback($website, PriceListAccountGroupFallback::WEBSITE);
        }

        foreach ($accountGroups as $accountGroup) {
            $this->updatePriceListsOnCurrentLevel($website, $accountGroup, $force);
            $this->accountCombinedPriceListsBuilder->buildByAccountGroup($website, $accountGroup, $force);
        }

        if ($currentAccountGroup) {
            $this->garbageCollector->cleanCombinedPriceLists();
        }
    }

    /**
     * @param Website $website
     * @param AccountGroup $accountGroup
     * @param boolean $force
     */
    protected function updatePriceListsOnCurrentLevel(Website $website, AccountGroup $accountGroup, $force)
    {
        $collection = $this->priceListCollectionProvider->getPriceListsByAccountGroup($accountGroup, $website);
        $combinedPriceList = $this->combinedPriceListProvider->getCombinedPriceList($collection, $force);

        $this->getCombinedPriceListRepository()
            ->updateCombinedPriceListConnection($combinedPriceList, $website, $accountGroup);
    }
}
