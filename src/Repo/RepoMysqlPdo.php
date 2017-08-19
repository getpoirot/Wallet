<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Entity\EntityWallet;


class RepoMysqlPdo
    implements iRepoWallet
{
    /** @var \PDO */
    private $conn;
    private $dbname;


    /**
     * RepoWallet constructor.
     *
     * @param \PDO   $connection
     * @param string $dbname
     */
    function __construct(\PDO $connection, $dbname = 'Transactions')
    {
        $this->conn   = $connection;
        $this->dbname = (string) $dbname;
    }


    /**
     * Persist Entity Object
     *
     * @param EntityWallet $entityWallet
     *
     * @return mixed UID
     * @throws \Exception
     */
    function insert(EntityWallet $entityWallet)
    {
        $uid          = $entityWallet->getOwnerId();
        $wallet_type  = $entityWallet->getWalletType();
        $amount       = $entityWallet->getAmount();
        $target       = $entityWallet->getTarget();
        $data_created = $entityWallet->getDateCreated()->format('YYYY-MM-DD HH:MM:SS');
        $meta         = $entityWallet->getMeta();

        # Get Last Total Amount Of User
        #
        $query = 'SELECT last_total FROM `'.$this->dbname.'`
                  WHERE uid = :uid
                  AND wallet_type = :wallet_type 
                  ORDER BY transaction_id DESC 
                  LIMIT 1'
        ;
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam('uid', $uid);
        $stmt->bindParam('wallet_type', $wallet_type);
        $stmt->execute();
        $lastTotal = $stmt->fetch(\PDO::FETCH_ASSOC);

        # Insert New Entity Include Current Amount + Total Last Amount
        #
        if ($lastTotal)
            $lastTotal = $lastTotal["last_total"] + $entityWallet->getAmount();
        else $lastTotal = $entityWallet->getAmount();

        $sql    = 'INSERT INTO `'.$this->dbname.'` 
            ( `uid`, `wallet_type`, `amount`, `target`, `date_created`, `last_total`,`meta`)
            VALUES (?,?,?,?,?,?,?)'
        ;

        $stm = $this->conn->prepare($sql);
        $stm->bindParam(1,$uid);
        $stm->bindParam(2,$wallet_type);
        $stm->bindParam(3,$amount);
        $stm->bindParam(4,$target);
        $stm->bindParam(5,$data_created);
        $stm->bindParam(6,$lastTotal);
        $stm->bindParam(7,$meta);

        if ( false === $stm->execute() )
            throw new \Exception(sprintf(
                'Error While Insert Into (%s).'
                , $this->dbname
            ));

        return $this->conn->lastInsertId();
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed   $uid       Owner unique id of wallet
     * @param string $walletType Wallet type
     *
     * @return float|int
     */
    function getSumTotalAmount($uid, $walletType)
    {
        $query = 'SELECT `last_total` 
            FROM `'.$this->dbname.'` 
            WHERE uid = :uid 
            AND wallet_type = :wtype
            ORDER BY transaction_id 
            DESC LIMIT 1 '
        ;

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':wtype', $walletType, \PDO::PARAM_STR);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        if ( false === $result = $stmt->fetch() )
            return 0;

        return $result['last_total'];
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
                    case "meta":
                        $q ['meta'] = "meta = :meta";
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
          SELECT * FROM `{$this->dbname}`
          $where
          ORDER BY  transaction_id $sort
          ";

        (isset($offset) || isset($limit)) ? $query.= "LIMIT ".( ($offset) ? "{$offset}, " : null )."{$limit}" : null;


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
