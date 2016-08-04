<?php
namespace Sangio90\Repository;

use Doctrine\DBAL\Query\QueryBuilder;

class Repository
{
    protected $stmt = '';

    /** @var  \Doctrine\DBAL\Connection */
    protected $connection;

    protected $fields = [];

    protected $tableName = '';

    public function read($parameters = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query = $queryBuilder->select("*")->from("(" . $this->stmt . ") as result");
        $query = $this->parseParameters($query, $parameters);
        return $query->execute()->fetchAll();
    }

    /**
     * @param QueryBuilder $query
     * @param array $parameters
     * @return QueryBuilder
     */
    protected function parseParameters(QueryBuilder $query, $parameters = [])
    {
        if (array_key_exists('order', $parameters)) {
            $orderRule = $parameters['order'];
            $orderField = $orderRule['field'];
            $orderDirection = $orderRule['direction'] ? $orderRule['direction'] : 'ASC';
            $query = $query->orderBy($orderField, $orderDirection);
        }
        if (array_key_exists('start', $parameters)) {
            $query = $query->setFirstResult($parameters['start']);
        }
        if (array_key_exists('limit', $parameters)) {
            $query = $query->setMaxResults($parameters['limit']);
        }
        return $query;
    }

    public function insert($row)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $data = [];
        $values = [];
        foreach ($this->fields as $field) {

            /**
             * If the field is the identifier (supposed to be auto_increment) it should not be written
             */
            if ($field['id']) continue;

            $fieldName = $field['name'];
            $data[$fieldName] = '?';
            $values[] = $row[$fieldName];
        }
        $queryBuilder->insert($this->tableName)->values($data)->setParameters($values)->execute();
    }
}