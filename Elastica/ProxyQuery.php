<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\DatagridBundle\Elastica;

use Application\Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;
use Application\Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

use Elastica\Search;

/**
 * This class try to unify the query usage with Doctrine
 */
class ProxyQuery extends BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        $query = $this->queryBuilder->getQuery();

        // Sorted field and sort order
        $sortBy = $this->getSortBy();
        $sortOrder = $this->getSortOrder();

        if ($sortBy && $sortOrder) {
            $query->setSort(array($sortBy => array('order' => $sortOrder)));
        }

        // Limit & offset
        $this->results = $this->queryBuilder->getRepository()->find($query, null, array(
            Search::OPTION_SIZE => $this->getMaxResults(),
            Search::OPTION_FROM => $this->getFirstResult()
        ));

        return $this->results;
    }
}
