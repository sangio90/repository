<?php

class Repository
{
    protected $stmt;
    /** @var  \Doctrine\DBAL\Connection */
    protected $connection;

    public function read()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select("*")->from("(" . $this->stmt . ") as result")->execute()->fetchAll();
    }

}