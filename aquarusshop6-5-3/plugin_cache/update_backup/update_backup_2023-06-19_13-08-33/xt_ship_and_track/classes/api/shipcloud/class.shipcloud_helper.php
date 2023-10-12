<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


class shipcloud_helper
{
    /**
     * @param $clz
     * @param $input
     * @return array|null|shipcloud_carrier
     * @throws Exception
     */
    public static function clzFactory($clz, $input)
    {
        $ret = null;
        if(is_array($input))
        {
            $ret = array();
            foreach($input as $i)
            {
                $ret[] = self::clz($clz, $i);
            }
        }
        else if(is_string($input))
        {
            $std = json_decode($input, true);
            $ret = self::clz($clz, $std);
        }
        else {
            $ret = self::clz($clz, $input);
        }

        return $ret;
    }

    /**
     * @param $clz
     * @param stdClass $stdClass
     * @return null|shipcloud_carrier
     * @throws Exception
     */
    private static function clz($clz, array $stdClass)
    {
        $ret = null;
        switch($clz)
        {
            case 'carrier':
                $ret = new shipcloud_carrier();
                foreach ($stdClass as $k => $v) $ret->{$k} = $v;
                break;
            default:
                throw new Exception('unknown class name');
        }

        return $ret;
    }

    static function cast($destination, $sourceObject)
    {
        if(is_array($destination) && is_object($sourceObject))
        {
            $arr = array();
            $vals = array_values(get_object_vars($sourceObject));
            foreach($vals[0] as $k => $v)
            {
                $arr[] = self::cast($destination[0], $v);
            }
            return $arr;
        }
        else if(is_array($destination) && is_array($sourceObject))
        {
            $arr = array();
            foreach($sourceObject as $k => $v)
            {
                $arr[] = self::cast($destination[0], $v);
            }
            return $arr;
        }
        if (is_string($destination)) {
            $destination = new $destination();
        }
        if(empty($sourceObject))
        {
            return $destination;
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination,$value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }

    public static function buildAddressString(Shipcloud\Address $addr, $glue = '|', $omitEmty = true)
    {
        $arr = array(

        );

        foreach($arr as $k => $v)
        {
            $a = 0;
        }
        //$objVars =  $vals = array_values(get_object_vars($obj));
        //$vals = array_values(get_object_vars($objVars));
        $a = 0;
    }


    public static function dismount($object) {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }

}