<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Controller;

use Sonata\Component\Currency\Currency;
use Sonata\DatagridBundle\Datagrid\Datagrid;

use Sonata\ElasticaBundle\Datagrid\Filter\RangeFilter;
use Sonata\ElasticaBundle\Datagrid\QueryBuilder;
use Sonata\ElasticaBundle\Datagrid\Pager;
use Sonata\ElasticaBundle\Datagrid\ProxyQuery;

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
        $queryBuilder->setCriteria(array('name' => '*'));

        $proxyQuery = new ProxyQuery($queryBuilder);

        // Create datagrid
        $pager = new Pager();

        $datagrid = new Datagrid($proxyQuery, $pager, $formBuilder, array(
            '_page'       => $this->getRequest()->query->get('page', 1),
            '_per_page'   => 6,
            '_sort_by'    => 'id',
            '_sort_order' => 'asc'
        ));

        // Create a range filter
        $rangeFilter = new RangeFilter();
        $rangeFilter->initialize('sonata.elastica.range.filter', array(
            'field_name' => 'id',
            'min'        => 5,
            'max'        => 50
        ));

        $datagrid->addFilter($rangeFilter);

        // Create fake currency for e-commerce rendering
        $currency = new Currency();
        $currency->setLabel('EUR');

        return $this->render('SonataDatagridBundle:Search:results.html.twig', array(
            'results'  => $datagrid->getResults(),
            'pager'    => $pager,
            'currency' => $currency,
            'route'    => 'sonata_datagrid_search'
        ));
    }
}