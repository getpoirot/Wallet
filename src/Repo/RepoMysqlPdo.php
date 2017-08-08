<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Entity\EntityWallet;
use Poirot\Wallet\helper\MysqlHelper;


class RepoMysqlPdo
    implements iRepoWallet
{
    /** @var \PDO */
    private $conn;


    /**
     * RepoWallet constructor.
     *
     * @param \PDO $connection
     */
    function __construct(\PDO $connection)
    {
        $this->conn = $connection;
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed $uid
     * @param string $type
     *
     * @return float|int
     */
    function getCountTotalAmount($uid, $type)
    {
        $query="SELECT `last_total` FROM `tarnsactions` WHERE uid=\"{$uid}\" AND wallet_type=\"{$type}\" ORDER BY transactions_id DESC LIMIT 1 ";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        return $result["last_total"];

    }

    /**
     * Persist Entity Object
     *
     * @param EntityWallet $entityWallet
     *
     * @return mixed UID
     */
    function insert(EntityWallet $entityWallet)
    {
       // var_dump($entityWallet->getWalletType());



        $query = "SELECT last_total FROM `tarnsactions` WHERE uid={$entityWallet->getUid()} AND wallet_type=\"{$entityWallet->getWalletType()}\" ORDER BY transactions_id DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        var_dump($result["last_total"]);



        if (!$result){
            $result=0;
        }
        $result=$result["last_total"]+$entityWallet->getAmount();
        var_dump($result);

        $sql="INSERT INTO `tarnsactions`( `uid`, `wallet_type`, `amount`, `source`, `data_created`, `last_total`)
                                  VALUES (\"{$entityWallet->getUid()}\",
                                          \"{$entityWallet->getWalletType()}\",
                                          {$entityWallet->getAmount()},
                                          \"{$entityWallet->getSource()}\",
                                          \"{$entityWallet->getDateCreated()->format('Y/m/d H:i:s')}\",
                                          {$result})";
       // var_dump(2);
        $this->conn->exec($sql);


        return $this->conn;





    }

    /**
     * Find All Entities Match With Given Expression
     *
     * @param array $expr
     * @param string $offset
     * @param int $limit
     * @param string $sort
     *
     * @return \Traversable
     */
    function find(array $expr, $offset = null, $limit = null, $sort = self::SORT_ASC)
    {
        $where = '';

        if (! empty($expr) ) {


            $qFilter = MysqlHelper::buildMySqlQueryFromExpression($expr);
            if (isset($qFilter['WHERE']))
                $where = 'WHERE '. implode(' and ', $qFilter['WHERE']);
        }

        $query = "
          SELECT * FROM transactions
          $where 
          ORDER BY adsr_id \"{$sort}\"
          LIMIT ".( ($offset) ? "{$offset}, " : null )."{$limit}
        ";

        $stmt = $this->q->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        return $result;
    }
}