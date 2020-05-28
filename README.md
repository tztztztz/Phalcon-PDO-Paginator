# Phalcon PDO Paginator

Paginator is compatible with standard Phalcon paginators, as it extends \Phalcon\Paginator\Adapter\AbstractAdapter class.

Example usage:

```
//////////
// Page size
$pageSize = 10;

//////////
// Page number
$pageNumber = 1;

//////////
// Array with format/structure similar to restul value of Builder->getQuery()->getSQL() function
// it contains query to execute
// For example:
$sqlData = [];
$sqlData['sql'] = 'SELECT * FROM shop_orders WHERE creation_time > :ct:';
$sqlData['bind'] = '2020-03-31';

//////////
// Optional but strongly recommended array with format/structure similar to restul value of Builder->getQuery()->getSQL() function
// it should contain dedicated query to determine number of records for $sqlData - usually calculating it by COUNT() function
// Please note, that for $sqlCount binds from sqlData will be used inside PDOPaginator
// For example:
$sqlCount = 'SELECT COUNT(*) FROM shop_orders WHERE creation_time > :ct:';



//////////
// Dialect interface object, for example \Phalcon\Db\Adapter\Pdo\Postgresql
$dialect = new \Phalcon\Db\Adapter\Pdo\Postgresql( $descriptor ); 

//////////
// Initialized \PDO class object
$PDO;     

// 'row_creator' key contains callback class to create row in result set array based on $row fetched form PDO result,
// with PDO::FETCH_ALL option
// the most basic is just to return $row
// In this example we're returning Phalcon Entity created from raw row

// Name of entity class that is a subclass of \Phalcon\Mvc\Model
$entClass = '\my_namespace\MyEntity';

$config = [
    'limit' => $pageSize,
    'page' => pageNumber,
    'sql' => $sqlData,
    'sql_count' => sqlCount,
    'pdo' => $PDO,
    'dialect' => $dialect,
    'row_creator' => function($row) use($entClass) {

      /* @var $entity \Phalcon\Mvc\Model */
      $entity = new $entClass( $row );
      if (method_exists($entity, 'afterFetch')) {
        $entity->afterFetch();
      }
      return $entity;

    }
];

$paginator = new \inopx\db\PDOPaginator($config);
$paginator->paginate();
```
