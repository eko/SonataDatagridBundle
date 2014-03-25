<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\DatagridBundle\Tests\Elastica;

use Application\Sonata\DatagridBundle\Elastica\ProxyQuery;

/**
 * Class ProxyQueryTest
 *
 * Tests Elastica specific ProxyQuery
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ProxyQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        // Given
        $repository = $this->getMockBuilder('FOS\ElasticaBundle\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())->method('find')->will($this->returnValue(array(1, 2)));

        $elasticaQuery = $this->getMockBuilder('Application\Sonata\DatagridBundle\Elastica\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $elasticaQuery->expects($this->once())->method('getRepository')->will($this->returnValue($repository));

        $pager = $this->getMock('Application\Sonata\DatagridBundle\Pager\PagerInterface');

        // When
        $proxyQuery = new ProxyQuery($elasticaQuery, $pager);

        $results = $proxyQuery->execute();

        // Then
        $this->assertEquals(array(1, 2), $results);
    }
}
