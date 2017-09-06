<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 9/6/17
 * Time: 11:20 AM
 */

namespace Poirot\Wallet\Repo;


use Poirot\Wallet\Entity\EntityWallet;
use Poirot\Wallet\Interfaces\iRepoWallet;
use MongoDB\BSON\ObjectID;
use Module\MongoDriver\Model\Repository\aRepository;

class RepoMongo
    extends aRepository
    implements iRepoWallet
{

    /**
     * Initialize Object
     *
     */
    protected function __init()
    {
        if (! $this->persist )
            $this->setModelPersist(new Mongo\WalletEntity());
    }

    /**
     * Generate next unique identifier to persist
     * data with
     *
     * @param null|string $id
     *
     * @return mixed
     * @throws \Exception
     */
    function attainNextIdentifier($id = null)
    {
        try {
            $objectId = ($id !== null) ? new ObjectID( (string)$id ) : new ObjectID;
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Invalid Persist (%s) Id is Given.', $id));
        }

        return $objectId;
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



        $r = $this->_query()->findOne(
            [
                'owner_id'=>$entityWallet->getOwnerId(),
                'wallet_type' =>$entityWallet->getWalletType()

            ]
            , [

                'sort'  => ['_id' => -1,],
            ]
        );
        $lastTotal=0;

        if ($r){
            $lastTotal= $r->getLastTotal()+$entityWallet->getAmount();
        }else{
            $lastTotal=$entityWallet->getAmount();
        }


        var_dump($lastTotal);
      //  die();
        $pEntity = new Mongo\WalletEntity();
        $pEntity
            ->setOwnerId( $entityWallet->getOwnerId() )
            ->setWalletType( $entityWallet->getWalletType() )
            ->setAmount( $entityWallet->getAmount() )
            ->setTarget( $entityWallet->getTarget() )
            ->setLastTotal($lastTotal )
            ->setMeta($entityWallet->getMeta())
            ->setDateCreated($entityWallet->getDateCreated())
        ;

        $r = $this->_query()->insertOne($pEntity);

        $rEntity = new EntityWallet();
        $rEntity
            ->setOwnerId( $entityWallet->getOwnerId() )
            ->setWalletType( $entityWallet->getWalletType() )
            ->setAmount( $entityWallet->getAmount() )
            ->setTarget( $entityWallet->getTarget() )
            ->setLastTotal( $lastTotal )
            ->setMeta($entityWallet->getMeta())
            ->setDateCreated($entityWallet->getDateCreated())
        ;

        return $rEntity;
    }

    /**
     * Get last amount wallet of any user
     *
     * @param mixed $uid Owner unique id of wallet
     * @param string $walletType Wallet type
     *
     * @return float|int|null
     */
    function getSumTotalAmount($uid, $walletType)
    {
        $r = $this->_query()->findOne(
            [
                'owner_id'=>$uid,
                'wallet_type' =>$walletType

            ]
            , [

                'sort'  => ['_id' => -1,],
            ]
        );

        if (!$r)
            return null;



        return $r->getLastTotal();
    }

    /**
     * Find All Entities Match With Given Expression
     *
     * $exp: [
     *   'uid'         => ..,
     *   'wallet_type' => ..,
     *   'target'      => ...
     * ]
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

        $r = $this->_query()->find(
            $expr,
            [
                'limit' => $limit,
                'skip'  => $offset,
            ]
        );


        die();

        return $r;
    }
}