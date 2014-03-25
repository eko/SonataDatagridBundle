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

use Application\Sonata\DatagridBundle\Pager\BasePager;

/**
 * Elastica pager class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Pager extends BasePager
{
    /**
     * {@inheritdoc}
     */
    public function computeNbResult()
    {
        $countQuery = clone $this->getQuery();
        $countQuery->execute();

        return count($countQuery->getResults());
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->resetIterator();

        $this->setNbResults($this->computeNbResult());

        $this->getQuery()->setFirstResult(null);
        $this->getQuery()->setMaxResults(null);

        if (count($this->getParameters()) > 0) {
            $this->getQuery()->setParameters($this->getParameters());
        }

        if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults()) {
            $this->setLastPage(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

            $this->getQuery()->setFirstResult($offset);
            $this->getQuery()->setMaxResults($this->getMaxPerPage());
        }
    }
}