<?php
namespace inopx\db;

/**
 * PDO Paginator
 *
 * @author INOVUM Tomasz Zadora
 */
class PDOPaginator extends \Phalcon\Paginator\Adapter\AbstractAdapter {
  
  /**
   * PDO Object
   * 
   * @var \PDO
   */
  protected $PDO;
  
  /**
   * SQL SELECT Statement without LIMIT / OFFSET directive plus bind params
   * 'sql' - SELECT SQL
   * 'bind' - array of binds
   * 'bindTypes' - array of bind types
   * 
   * @var array 
   */
  protected $sql;
  
  /**
   * SQL SELECT Statement for counting rows, it must have 1st result column containing total number of rows
   * 
   * @var type 
   */
  protected $sqlCount;
  
  /**
   * Dialect - used for limiting
   * 
   * @var \Phalcon\Db\DialectInterface 
   */
  protected $dialect;
  
  /**
   * Callback function to create row based on associative array from PDOStatemetn result row
   * 
   * @var callable 
   */
  protected $rowCreator;


  /**
   * 
   * Creates Paginator based on PDO Select Statement
   * 
   * Required $config keys:
   * 
   * 'limit' - page size / limit
   * 'page' - current number of page
   * 'sql' - SQL array, with keys 'sql', 'bind', 'bindTypes'
   * 'dialect' - dialect
   * 'pdo' - \PDO object for creating \PDOStatement
   * 'row_creator' - callback function for creating result row
   * 
   * Optional $config keys:
   * 'sql_count' - SQL Select statement
   * 
   * @param array $config
   */
  public function __construct(array $config) {
    
    parent::__construct($config);
    
    if (!isset($config['sql']) || !is_array($config['sql'])) {
      throw new Exception("Parameter 'sql' is required and it must be array");
    }
    
    if (!isset($config['dialect']) || !($config['dialect'] instanceof \Phalcon\Db\DialectInterface)) {
      throw new Exception("Parameter 'dialect' is required and it must be instance of \Phalcon\Db\DialectInterface");
    }
    
    if (!isset($config['pdo']) || !($config['pdo'] instanceof \PDO)) {
      throw new Exception("Parameter 'pdo' is required and it must be instance of \PDO");
    }
    
    if (!isset($config['row_creator']) || !is_callable($config['row_creator'])) {
      throw new Exception("Parameter 'row_creator' is required and it must be a callback");
    }
    
    $this->sql = $config['sql'];
    $this->dialect = $config['dialect'];
    $this->PDO = $config['pdo'];
    $this->rowCreator = $config['row_creator'];
    
    if (isset($config['sql_count'])) {
      $this->sqlCount = $config['sql_count'];
    }
    
  }
  

  public function paginate(): \Phalcon\Paginator\RepositoryInterface {
    
    !$this->PDO->inTransaction() ? $needTransaction = true : $needTransaction = false;
    
    if ($needTransaction) {
      $this->PDO->beginTransaction();
    }
    
    try {
      
      $limit = $this->limitRows;
      $last = 1;
      $next = 1;
      $previous = 1;
      $current = 1;
      $items = [];
      
      //////////////////
      // Execution without limit to get total number of pages and items
      if (!empty($this->sqlCount)) {
        $stmtALL = $this->PDO->prepare($this->sqlCount);
        $stmtALL->execute($this->sql['bind']);
        $tmp = $stmtALL->fetch(\PDO::FETCH_NUM);
        $totalItems = $tmp[0];
      }
      else {
        $stmtALL = $this->PDO->prepare($this->sql['sql']);
        $stmtALL->execute($this->sql['bind']);
        $totalItems = $stmtALL->rowCount();
      }
      
      
      if ($totalItems > 0) {
        
        // Last page number
        $last = floor( $totalItems / $this->limitRows );
        $last < 1 ? $last = 1 : null;
        
        // Current page number
        $current = $this->page;
        
        //////////////////
        // Let limit SQL
        $offset = 0;
        if (is_numeric($current) && $current > 1) {
          $offset += ($current-1)*$limit;
        }

        $sql = $this->dialect->limit($this->sql['sql'], [$limit, $offset]);
        
        
        
        $stmt = $this->PDO->prepare($sql);
        $stmt->execute($this->sql['bind']);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $items = [];
        
        foreach ($rows as &$row) {
          $items []= call_user_func($this->rowCreator, $row);
        }
        
      }
      
      
    }
    catch (\Exception $e) {
      
      if ($needTransaction) {
        $this->PDO->rollBack();
      }
      
      echo $e->getMessage(); exit;
      
    }
    
    if ($needTransaction) {
      $this->PDO->commit();
    }
    
    
    return PDOPaginatorRepository::factoryDefault($items, $current, $last, $next, $previous, $limit, $totalItems);
    
  }

  
}
