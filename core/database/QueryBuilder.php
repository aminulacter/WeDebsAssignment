<?php

namespace Core\database;

class QueryBuilder
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function login($table, $model, $field)
    {
        $model = "App\\models\\{$model}";
        $statement = $this->pdo->prepare("select * from {$table} where email= ?");

        $statement->execute(array($field));
        return $statement->fetchAll(\PDO::FETCH_CLASS, $model);
    }

    public function selectAll($table, $model)
    {
        $model = "App\\models\\{$model}";
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, $model);
    }

    public function selectWhere($table, $model, $attribute, $value)
    {
        $model = "App\\models\\{$model}";
        $statement = $this->pdo->prepare("select * from {$table} where {$attribute} = ?");

        $statement->execute(array($value));
        return $statement->fetchAll(\PDO::FETCH_CLASS, $model);
    }
    public function insert($table, $parameters)
    {
        $sql = sprintf(
            'insert into %s (%s) values (%s)',
            $table,
            implode(', ', array_keys($parameters)),
            ':' . implode(', :', array_keys($parameters))

        );
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
        } catch (\Exception $e) {
            die('Whoops, Something went wrong.');
        }
    }

    public function update($table, $parameters)
    {
        $sql = "Update {$table}  SET ";
        foreach ($parameters as $parameterKey => $value) {
            $sql = $sql . "{$parameterKey}=:{$parameterKey}, ";
        }
        $length = strrpos($sql, ',');
        $sql = substr($sql, 0, $length);
        $sql = $sql . " where id=:id";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
        } catch (\Exception $e) {
            die('Whoops, Something went wrong.');
        }
    }

    public function delete($table,  $id)
    {
        //$model = "App\\models\\{$model}";
        $statement = $this->pdo->prepare("delete  from {$table} where id= ?");

        $statement->execute(array($id));
        //return $statement->fetchAll(\PDO::FETCH_CLASS, $model);
    }

    public function orderAll()
    {
        $statement = $this->pdo->prepare("select orders.id, users.name as user, products.Name as product, products.Image, products.Price, orders.status

        from users inner join orders on users.id = orders.user_id inner join products on orders.product_id = products.id");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS);
    }
    public function orderCustomer($id)
    {
        $statement = $this->pdo->prepare("select orders.id, users.name as user, products.Name as product, products.Image, products.Price, orders.status

        from users inner join orders on users.id = orders.user_id inner join products on orders.product_id = products.id where users.id= ?");
        $statement->execute(array($id));
        return $statement->fetchAll(\PDO::FETCH_CLASS);
    }
}
