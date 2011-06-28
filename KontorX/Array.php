<?php
class KontorX_Array
{
    protected static $_multisortResult;
    
    public static function multisort(array $data, $column, $order = SORT_ASC)
    {
        $args = func_get_args();
        $args = array_slice($args, 3);

        $columnsData = array(
            $column
        );
        
        $multisortArgs = array(
            $column .'_column' => array(),
            $column .'_order'  => $order,
        );

        if (is_array($args))
        {
            while (!empty($args)) 
            {
                $column = array_shift($args);
                $order  = array_shift($args);
                
                $columnsData[] = $column;
                
                $multisortArgs[$column .'_column'] = array();
                $multisortArgs[$column .'_order']  = $order;
            }
        }
        
        foreach ($data as $key => $values)
        {
            foreach ($columnsData as $column)
            {
                $multisortArgs[$column .'_column'][$key] = $values[$column];
            }
        }

        self::$_multisortResult = $data;
        $multisortArgs[] = & self::$_multisortResult;
        
        call_user_func_array('array_multisort', $multisortArgs);
        return self::$_multisortResult;
        
        /*
         * Rozwiązanie B.
         * To działa ale... jest wświckłe jak pies! 
         */

        /*
        $varNames = array_keys($multisortArgs);
        $varNames[] = 'data';
        $varNames = '$' . implode(',$', $varNames);

        $evilString = 'array_multisort(%s);';
        $evilString = sprintf($evilString, $varNames);
        
        extract($multisortArgs);
        eval($evilString);

        return $data;
        */
    }
}