<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\DatagridBundle\Elastica;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use FOS\ElasticaBundle\Repository;

/**
 * Class QueryBuilder
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class QueryBuilder
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var \Elastica\Query\Bool
     */
    protected $query;

    /**
     * Constructor
     *
     * @param string                     $class
     * @param RepositoryManagerInterface $repositoryManager
     */
    public function __construct($class, RepositoryManagerInterface $repositoryManager)
    {
        $this->class      = $class;
        $this->repository = $repositoryManager->getRepository($class);

        $this->query      = new \Elastica\Query();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets query criteria
     *
     * @param array $criteria
     */
    public function setCriteria(array $criteria)
    {
        if (empty($criteria)) {
            return;
        }

        $boolQuery = new \Elastica\Query\Bool();

        foreach ($criteria as $key => $value) {
            $queryString = new \Elastica\Query\QueryString();
            $queryString->setFields(array($key));
            $queryString->setQuery($value);

            $boolQuery->addShould($queryString);
        }

        $this->query->setQuery($boolQuery);
    }
}