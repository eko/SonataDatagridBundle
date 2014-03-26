<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\DatagridBundle\Controller;

use Application\Sonata\DatagridBundle\Datagrid\Datagrid;

use Application\Sonata\ElasticaBundle\Datagrid\Filter\RangeFilter;
use Application\Sonata\ElasticaBundle\Datagrid\QueryBuilder;
use Application\Sonata\ElasticaBundle\Datagrid\Pager;
use Application\Sonata\ElasticaBundle\Datagrid\ProxyQuery;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchController extends Controller
{
    /**
     * Search action
     *
     * @return JsonResponse
     */
    public function searchAction()
    {
        $formBuilder = $this->get('form.factory')->createNamedBuilder('filter', 'form', array(), array(
            'csrf_protection' => false
        ));

        // Create proxy query
        $manager = $this->get('fos_elastica.manager.orm');

        $queryBuilder = new QueryBuilder('ApplicationSonataProductBundle:Product', $manager);
        $queryBuilder->setCriteria(array('name' => 'Dummy'));

        $proxyQuery = new ProxyQuery($queryBuilder);

        // Create a range filter
        $rangeFilter = new RangeFilter();
        $rangeFilter->setValue(10);
        $rangeFilter->initialize('sonata.elastica.range.filter', array(
            'field_name' => 'id',
            'operator'   => '>'
        ));

        // Create datagrid
        $datagrid = new Datagrid($proxyQuery, new Pager(), $formBuilder, array(
            '_page'       => 1,
            '_per_page'   => 5,
            '_sort_by'    => 'id',
            '_sort_order' => 'asc'
        ));

        $datagrid->addFilter($rangeFilter);

        $names = array();

        foreach ($datagrid->getResults() as $product) {
            $names[] = $product->getName();
        }

        return new JsonResponse($names);
    }
}