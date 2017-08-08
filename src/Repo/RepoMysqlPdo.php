<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Entity\EntityWallet;


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
        $query="SELECT `last_total` 
                FROM `transactions` 
                WHERE uid = :uid 
                AND wallet_type= :type 
                ORDER BY transactions_id 
                DESC LIMIT 1 ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, \PDO::PARAM_STR);
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
        $query = "SELECT last_total FROM `transactions`
                  WHERE uid={$entityWallet->getUid()} 
                  AND wallet_type=\"{$entityWallet->getWalletType()}\" 
                  ORDER BY transactions_id DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if (! $result )
            $result=0;

        $result=$result["last_total"]+$entityWallet->getAmount();

        $sql="INSERT INTO `transactions`( `uid`, `wallet_type`, `amount`, `target`, `data_created`, `last_total`)
                                  VALUES (\"{$entityWallet->getUid()}\",
                                          \"{$entityWallet->getWalletType()}\",
                                          {$entityWallet->getAmount()},
                                          \"{$entityWallet->getTarget()}\",
                                          \"{$entityWallet->getDateCreated()->format('Y/m/d H:i:s')}\",
                                          {$result})";
        $this->conn->exec($sql);


        return $this->conn->lastInsertId();
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
     * @throws \Exception
     */
    function find(array $expr, $offset = null, $limit = null, $sort = self::SORT_ASC)
    {
        $where = null;

        if (! empty($expr) )
        {
            $q=[];
            foreach ($expr as $k => $v) {
                switch ( strtolower($k) ) {
                    case "uid":
                        $q['uid'] = "uid = :uid";
                        break ;
                    case "wallet_type":
                        $q['wallet_type'] = "wallet_type = :wallet_type";
                       break;
                    case "target":
                        $q ['target'] = "target = :target";
                        break;
                    default:
                        throw new \Exception(sprintf(
                            'The Expression (%s) is unknown.'
                            , $k
                        ));
                }
            }

            $where = 'WHERE '.implode(' & ', $q);
        }




        $query = "
          SELECT * FROM transactions
          $where
          ORDER BY  transactions_id $sort
          LIMIT ".( ($offset) ? "{$offset}, " : null )."{$limit}
        ";

        $stmt = $this->conn->prepare($query);
        $q = [];
        foreach ($expr as $k => $v) {
            $stmt->bindParam(':'.$k, $v,\PDO::PARAM_STR);
           $q[':'.$k] = $v;
        }



        $stmt->execute($q);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        return $result;
    }
}