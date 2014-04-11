<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\ProxyQuery\Doctrine;

use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * Class ProxyQuery
 *
 * This is the Doctrine proxy query class
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
            $sortBy = sprintf('%s.%s', $query->getRootAlias(), $sortBy);
            $query->orderBy($sortBy, $sortOrder);
        }

        // Limit & offset
        $query->setFirstResult($this->getFirstResult());
        $query->setMaxResults($this->getMaxResults());

        $this->results = array(
            'results' => $query->getQuery()->execute(),
            'facets'  => $this->getFacets()
        );

        return $this->results['results'];
    }

    /**
     * Returns correctly formatted facets result array
     *
     * @return array
     */
    protected function getFacets()
    {
        $facetsQuery = $this->getQueryBuilder()->getFacetsQuery();
        $facets = $facetsQuery->getQuery()->execute();

        return array(
            'categories' => array(
                'terms' => array_map(function($facet) {
                    return array(
                        'term'  => $facet['term'],
                        'count' => (int) $facet['products_nb']
                    );
                }, $facets)
            )
        );
    }
}
