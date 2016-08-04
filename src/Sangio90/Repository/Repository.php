<?php
namespace Sangio90\Repository;


class Repository
{
    protected $stmt;
    /** @var  \Doctrine\DBAL\Connection */
    protected $connection;
    protected $fields = [];
    protected $tableName;

    public function read()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        return $queryBuilder->select("*")->from("(" . $this->stmt . ") as result")->execute()->fetchAll();
    }

    public function insert($row)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $data = [];
        $values = [];
        foreach ($this->fields as $field) {
            $fieldName = $field['name'];
            $data[] = [
                $fieldName['name'] => '?'
            ];
            $values[] = $row[$fieldName];
        }
        $queryBuilder->insert($this->tableName)->values($data)->setParameters($values)->execute();
    }

}