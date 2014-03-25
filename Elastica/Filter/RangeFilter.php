<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\DatagridBundle\Elastica\Filter;

use Application\Sonata\DatagridBundle\Filter\BaseFilter;
use Application\Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

class RangeFilter extends BaseFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($queryBuilder, $value)
    {
        $this->filter($queryBuilder, null, null, null);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        $query = $queryBuilder->getQueryBuilder()->getQuery();

        $query->setFilter(new \Elastica\Filter\Range($this->getFieldName(),
            array($this->getOperator() => $this->getValue())
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'field_name' => null,
            'operator'   => null,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return array('sonata_type_filter_choice', array(
            'field_type'    => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label'         => $this->getLabel()
        ));
    }

    /**
     * Returns operator to use
     *
     * @return null|string
     */
    protected function getOperator()
    {
        $operator = $this->getOption('operator');

        switch ($operator) {
            case '<':
                return 'lt';
                break;

            case '>':
                return 'gt';
                break;

            case '<=':
                return 'lte';
                break;

            case '>=':
                return 'gte';
                break;
        }

        return $operator;
    }
}
