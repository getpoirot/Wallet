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
        $query = 'SELECT `last_total` 
                FROM `transactions` 
                WHERE uid = :uid 
                AND wallet_type = :type 
                ORDER BY transactions_id 
                DESC LIMIT 1 ';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, \PDO::PARAM_STR);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        return $result['last_total'];

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
        $uid          = $entityWallet->getOwnerId();
        $wallet_type  = $entityWallet->getWalletType();
        $amount       = $entityWallet->getAmount();
        $target       = $entityWallet->getTarget();
        $data_created = $entityWallet->getDateCreated()->format('Y/m/d H:i:s');


        # Get Last Total Amount Of User
        #
        $query = 'SELECT last_total FROM `transactions`
                  WHERE uid = :uid 
                  AND wallet_type  =:wallet_type 
                  ORDER BY transactions_id DESC LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':wallet_type', $wallet_type);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        if (! $lastTotal = $stmt->fetch() )
            return false;


        # Insert New Entity Include Current Amount + Total Last Amount
        #
        $lastTotal = $lastTotal["last_total"] + $entityWallet->getAmount();
        $sql    = 'INSERT INTO `transactions` 
            ( `uid`, `wallet_type`, `amount`, `target`, `data_created`, `last_total`)
            VALUES (?,?,?,?,?,?)';

        $statment = $this->conn->prepare($sql);
        $statment->bindParam(1,$uid);
        $statment->bindParam(2,$wallet_type);
        $statment->bindParam(3,$amount);
        $statment->bindParam(4,$target);
        $statment->bindParam(5,$data_created);
        $statment->bindParam(6,$lastTotal);
        $statment->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Find All Entities Match With Given Expression
     *
     * $exp: [
     *   'uid'         => ..,
     *   'wallet_type' => ..,
     *   'target'      => ..,
     * ]
     *
     * @param array   $expr
     * @param string  $offset
     * @param int     $limit
     * @param string  $sort
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
          LIMIT ".( ($offset) ? "{$offset}, " : null )."{$limit}";

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
