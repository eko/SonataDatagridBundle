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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * Search action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $formBuilder = $this->get('form.factory')->createNamedBuilder('', 'form', array(), array(
            'csrf_protection' => false
        ));
        $formBuilder->add('search', 'text');
        $formBuilder->add('priceStart', 'integer', array('required' => false));
        $formBuilder->add('priceEnd', 'integer', array('required' => false));
        $formBuilder->add('sort', 'choice', array(
            'multiple' => false,
            'choices' => array(
                'score'      => 'Sort by pertinence',
                'price_asc'  => 'Sort by ascending price',
                'price_desc' => 'Sort by descending price',
            )
        ));

        $form = $formBuilder->getForm();
        $form->bind($request);

        $sort = array('by' => null, 'order' => null);

        switch ($request->get('sort')) {
            case 'price_asc':
                $sort = array('by' => 'price', 'order' => 'asc');
                break;

            case 'price_desc':
                $sort = array('by' => 'price', 'order' => 'desc');
                break;
        }

        // Create proxy query
        $manager = $this->get('fos_elastica.manager.orm');

        $queryBuilder = new QueryBuilder('ApplicationSonataProductBundle:Product', $manager);
        $queryBuilder->setCriteria(array(
            'name' => $request->get('search', '*', true),
            'description' => $request->get('search', '*', true),
        ));

        $proxyQuery = new ProxyQuery($queryBuilder);

        // Create datagrid
        $pager = new Pager();

        $datagrid = new Datagrid($proxyQuery, $pager, $formBuilder, array(
            '_page'       => $this->getRequest()->query->get('page', 1),
            '_per_page'   => 6,
            '_sort_by'    => $sort['by'],
            '_sort_order' => $sort['order']
        ));

        // Create a price (range) filter
        $priceFilter = new RangeFilter();
        $priceFilter->initialize('sonata.elastica.range.filter', array(
            'field_name' => 'price',
            'min'        => $request->get('priceStart'),
            'max'        => $request->get('priceEnd')
        ));

        $datagrid->addFilter($priceFilter);

        // Create fake currency for e-commerce rendering
        $currency = new Currency();
        $currency->setLabel('EUR');

        return $this->render('SonataDatagridBundle:Search:results.html.twig', array(
            'form'     => $form->createView(),
            'results'  => $datagrid->getResults(),
            'pager'    => $pager,
            'currency' => $currency,
            'route'    => 'sonata_datagrid_search'
        ));
    }
}