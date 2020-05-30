<?php
namespace inopx\db;

/**
 * Repository for PDO Paginator
 *
 * @author INOVUM Tomasz Zadora
 */
class PDOPaginatorRepository implements \Phalcon\Paginator\RepositoryInterface {
  
  protected $aliases;
  
  protected $properties;
  
  /**
   * Number of current page
   * @var int  
   */
  protected  $current;
  
  /**
   * Number of first page (always 1)
   * @var type 
   */
  protected  $first = 1;
  
  /**
   * Array of items
   * @var mixed[] 
   */
  protected  $items;
  
  /**
   * Number of last page and aloso total number of pages.
   * 
   * @var int 
   */
  protected  $last;
  
  /**
   * Number of next page, or null if current page is the Last Page
   * @var int 
   */
  protected  $next;
  
  /**
   * Number of previous page or null if current page is the First Page
   * @var int 
   */
  protected  $previous;
  
  /**
   * Current page size
   * @var int 
   */
  protected $limit;
  
  /**
   * Total number of rows
   * @var int 
   */
  protected $totalItems;
  
  /**
   * 
   * @param type $items
   * @param type $current
   * @param type $last
   * @param type $next
   * @param type $previous
   * @param type $limit
   * @param type $totalItems
   * @return \inopx\db\PDOPaginatorRepository
   */
  public static function factoryDefault($items, $current, $last, $next, $previous, $limit, $totalItems) {
    
    $rt = new self();
    
    $rt->items = $items;
    $rt->current = $current;
    $rt->last = $last;
    $rt->next = $next;
    $rt->previous = $previous;
    $rt->limit = $limit;
    $rt->totalItems = $totalItems;
    
    return $rt;
    
  }

  public function getAliases(): array {
    return $this->aliases;
  }

  public function getCurrent(): int {
    return $this->current;
  }

  public function getFirst(): int {
    return $this->first;
  }

  public function getItems() {
    return $this->items;
  }

  public function getLast(): int {
    return $this->last;
  }

  public function getLimit(): int {
    return $this->limit;
  }

  public function getNext(): int {
    return $this->next;
  }

  public function getPrevious(): int {
    return $this->previous;
  }

  public function getTotalItems(): int {
    return $this->totalItems;
  }

  public function setAliases(array $aliases): \Phalcon\Paginator\RepositoryInterface {
    $this->aliases = $aliases;
    return $this;
  }

  public function setProperties(array $properties): \Phalcon\Paginator\RepositoryInterface {
    $this->properties = $properties;
    return $this;
  }

  
}
