<?php
namespace Poirot\Wallet\Repo;

use Poirot\Wallet\Interfaces\iRepoWallet;
use Poirot\Wallet\Entity\EntityWallet;


class RepoMysqli
    implements iRepoWallet
{
    /** @var \mysqli */
    private $conn;
    private $dbname;


    /**
     * RepoWallet constructor.
     *
     * @param \mysqli $connection
     * @param string $dbname
     */
    function __construct(\mysqli $connection, $dbname = 'Transactions')
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
        $data_created = $entityWallet->getDateCreated()->format('Y-m-d H:i:s');
        $meta         = $entityWallet->getMeta();


        # Get Last Total Amount Of User
        #
        $query = 'SELECT last_total FROM `'.$this->dbname.'`
                  WHERE uid = "'.$uid.'"
                  AND wallet_type = "'.$wallet_type.'" 
                  ORDER BY transaction_id DESC 
                  LIMIT 1'
        ;

        $r = $this->conn->query($query);

        $lastTotal = $entityWallet->getAmount();
        if ($r->num_rows > 0) {
            // output data of each row
            while( $row = $r->fetch_assoc() )
                $lastTotal = $row['last_total'] + $entityWallet->getAmount();

        }

        # Insert New Entity Include Current Amount + Total Last Amount
        #
        $sql    = "INSERT INTO `{$this->dbname}` 
            ( `uid`, `wallet_type`, `amount`, `target`, `date_created`, `last_total`,`meta`)
            VALUES ('$uid', '$wallet_type', $amount, '$target', '$data_created', $lastTotal, '$meta')"
        ;

        if ( false === $this->conn->query($sql) )
            throw new \Exception(sprintf(
                'Error While Insert Into (%s).'
                , $this->dbname
            ));

        return $this->conn->insert_id;
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed  $uid        Owner unique id of wallet
     * @param string $walletType Wallet type
     *
     * @return float|int
     * @throws \Exception
     */
    function getSumTotalAmount($uid, $walletType)
    {
        $query = "SELECT `last_total` 
            FROM `{$this->dbname}` 
            WHERE uid = '$uid'
            AND wallet_type = '$walletType'
            ORDER BY transaction_id 
            DESC LIMIT 1"
        ;

        if ( false === $r = $this->conn->query($query) )
            throw new \Exception(sprintf(
                'Error While Fetch Data (%s).'
                , $this->dbname
            ));


        $lastTotal = 0;
        if ($r->num_rows > 0) {
            // output data of each row
            while( $row = $r->fetch_assoc() )
                $lastTotal = $row['last_total'];

        }

        return $lastTotal;
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
                        $q['uid'] = "uid = '$v'";
                        break ;
                    case "wallet_type":
                        $q['wallet_type'] = "wallet_type = '$v'";
                       break;
                    case "target":
                        $q ['target'] = "target = '$v'";
                        break;
                    default:
                        throw new \Exception(sprintf(
                            'The Expression (%s) is unknown.'
                            , $k
                        ));
                }
            }

            $where = 'WHERE '.implode(' and ', $q);
        }

        $query = "
          SELECT * FROM `{$this->dbname}`
          $where
          ORDER BY transaction_id $sort
          ";

        (isset($offset) || isset($limit)) ? $query.= "LIMIT ".( ($offset) ? "{$offset}, " : null )."{$limit}" : null;

        if ( false === $r = $this->conn->query($query) )
            throw new \Exception(sprintf(
                'Error While Fetch Data (%s).'
                , $this->dbname
            ));


        $result = [];
        if ($r->num_rows > 0) {
            // output data of each row
            while( $row = $r->fetch_assoc() )
                $result[] = $row;

        }

        return $result;
    }
}
