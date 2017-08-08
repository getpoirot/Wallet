<?php
namespace Poirot\Wallet\helper;


class MysqlHelper
{
    /**
     * Build Mysql Query From Expression
     *
     * @param $parsedExpression
     *
     * @return array
     * @throws \Exception
     */
     static function buildMySqlQueryFromExpression($parsedExpression, array $aliases = [])
    {
        if (!is_array($parsedExpression))
            throw new \InvalidArgumentException(sprintf(
                'Expression must be parsed to Array; given: (%s).'
                , \Poirot\Std\flatten($parsedExpression)
            ));


        $condition = [];
        foreach ($parsedExpression as $field => $conditioner) {
            foreach ($conditioner as $o => $vl) {
                if ($o === '$eq')
                {
                    $v = current($vl);
                    if (!isset($condition['WHERE']))
                        $condition['WHERE'] = [];

                    if (array_key_exists($field, $aliases))
                        $field = $aliases[$field];

                    if (strpos($field, '.')) {
                        $clause = "$field = " . ((is_int($v) ? $v : '"'.$v.'"'));
                    } else {
                        $clause = "`$field` = " . ((is_int($v) ? $v : '"'.$v.'"'));
                    }
                    $condition['WHERE'][] = $clause;
                } elseif ($o === '$like')
                {
                    if (!isset($condition['WHERE']))
                        $condition['WHERE'] = [];

                    $v = trim(current($vl));
                    $condition['WHERE'][] = "`$field` LIKE " . ((is_int($v) ? $v : '"%'.$v.'%"'));
                } else {
                    throw new \Exception('Unknown Condition.');
                }
            }
        }

        return $condition;
    }

}