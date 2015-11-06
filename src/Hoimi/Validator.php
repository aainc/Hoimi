<?php
namespace Hoimi;

/**
 * Class Validator
 * @package Hoimi
 */
class Validator
{
    /**
     * validate array(hash) by definitions
     *
     * definition format:
     *   [
     *     {keyName} => [
     *        'required' => bool(true = if value is not required then error |false),
     *        'dataType' => string(string|integer|date),
     *        'min'   => minimumValue(minimumInteger, minimumDate, minimumStringLength)
     *        'max'   => maximumValue(maximumInteger, maximumDate, maximumStringLength)
     *        'regex' => preg_match's pattern
     *     ],
     *     {keyName} => ...
     * ]
     * error format:
     *   NOT_REQUIRED = value is not required
     *   INVALID_TYPE@[DATE_TYPE] = value is not valid data type
     *   INVALID_RANGE@[DATE_TYPE]@[MIN]@[MAX] = value is not valid range
     *
     * @param $variables
     * @param $definitions
     * @return array
     * @throws \RuntimeException
     */
    public static function validate (Gettable $variables, $definitions)
    {
        $result = array();
        foreach ($definitions as $name => $definition) {
            $values = $variables->get($name);
            if (isset($definitions['allowArray']) && $definitions['allowArray'] !== true && is_array($values)) {
                $result[$name] = 'INVALID_TYPE@ARRAY';
                continue;
            }
            if (!is_array($values)) $values = array($values);
            if (!isset($definition['required'])) $definition['required']  = false;
            if (!isset($definition['dataType'])) $definition['dataType']  = 'string';
            foreach ($values as $target) {
                if (!self::required($target)) {
                    if ($definition['required']) $result[$name] = 'NOT_REQUIRED';
                    break;
                } elseif ($definition['dataType'] === 'integer') {
                    if (!isset($definition['max'])) $definition['max'] = PHP_INT_MAX;
                    if (!isset($definition['min'])) $definition['min'] = ~PHP_INT_MAX;
                    if (!self::isInteger($definition['max']) || !self::isInteger($definition['min'])) {
                        throw new \RuntimeException('invalid definition');
                    }
                    if (!self::isInteger($target)) {
                        $result[$name] = 'INVALID_TYPE@INT';
                        break;
                    } else if (!self::isValidRange($target, $definition['min'], $definition['max'])) {
                        $result[$name] = 'INVALID_RANGE@INT@' . $definition['min'] . '@' . $definition['max'];
                        break;
                    }
                } elseif ($definition['dataType'] === 'double') {
                    if (!isset($definition['max'])) $definition['max'] = self::getMaxDouble();
                    if (!isset($definition['min'])) $definition['min'] = 0;
                    if (!self::isDouble($definition['max']) || !self::isDouble($definition['min'])) {
                        throw new \RuntimeException('invalid definition');
                    }
                    if (!self::isDouble($target)) {
                        $result[$name] = 'INVALID_TYPE@DOUBLE';
                        break;
                    } elseif (!self::isValidRange($target, $definition['min'], $definition['max'])) {
                        $result[$name] = 'INVALID_RANGE@DOUBLE@' . $definition['min'] . '@' . $definition['max'];
                        break;
                    }
                } elseif ($definition['dataType'] === 'date') {
                    if (!isset($definition['max'])) $definition['max'] = (PHP_INT_SIZE === 4 ? \DateTime::createFromFormat('Y-m-d H:i:s', PHP_INT_MAX) : '9999-12-31 23:59:59');
                    if (!isset($definition['min'])) $definition['min'] = (PHP_INT_SIZE === 4 ? \DateTime::createFromFormat('Y-m-d H:i:s', 0)           : '0000-01-01 00:00:00');
                    if (!self::isDate($definition['max']) || !self::isDate($definition['min'])) {
                        throw new \RuntimeException('invalid definition');
                    }
                    if (!self::isDate($target)) {
                        $result[$name] = 'INVALID_TYPE@DATE';
                        break;
                    } elseif (!self::isValidRange(
                        (new \DateTime($target))->getTimestamp(),
                        (new \DateTime($definition['min']))->getTimestamp(),
                        (new \DateTime($definition['max']))->getTimestamp()
                    )) {
                        $result[$name] = 'INVALID_RANGE@DATE@' . $definition['min'] . '@' . $definition['max'];
                        break;
                    }
                } elseif ($definition['dataType'] === 'string') {
                    if (!isset($definition['max'])) $definition['max'] = PHP_INT_MAX;
                    if (!isset($definition['min'])) $definition['min'] = ~PHP_INT_MAX;
                    if (!self::isInteger($definition['max']) || !self::isInteger($definition['min'])) {
                        throw new \RuntimeException('invalid definition');
                    }
                    if (!self::isValidRange(mb_strlen($target, 'UTF-8'), $definition['min'], $definition['max'])) {
                        $result[$name] = 'INVALID_RANGE@STRING@' . $definition['min'] . '@' . $definition['max'];
                        break;
                    }
                    if (isset($definition['regex']) && !preg_match($definition['regex'], $target)) {
                        $result[$name] = 'INVALID_FORMAT@STRING@' . $definition['regex'];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $val
     * @return bool
     */
    public static function required ($val)
    {
        return isset($val) && $val !== '';
    }

    /**
     * @param $val
     * @return bool
     */
    public static function isInteger ($val)
    {
        return isset($val) && strval(intval($val)) === strval($val);
    }

    /**
     * @param $val
     * @return bool
     */
    public static function isDouble ($val)
    {
        return isset($val) && strval(doubleval($val)) === strval($val);
    }

    /**
     * @param $val
     * @return bool
     */
    public static function isDate ($val)
    {
        if (!isset($val)) return false;
        $date = date_parse($val);
        return !$date['errors'] &&
            checkdate($date['month'], $date['day'], $date['year'] ?: '1900')               &&
            ($date['hour']   === false || ($date['hour']   <= 23 && $date['hour']   >= 0)) &&
            ($date['minute'] === false || ($date['minute'] <= 59 && $date['minute'] >= 0)) &&
            ($date['second'] === false || ($date['second'] <= 59 && $date['second'] >= 0));
    }

    /**
     * @param $val
     * @param $min
     * @param $max
     * @return bool
     */
    public static function isValidRange ($val, $min, $max)
    {
        return $val >= $min && $val <= $max;
    }

    public static function getMaxDouble ()
    {
        static $value = null;
        if  ($value === null) {
            $binary = pack('H*', '7fefffffffffffff');
            $reverse = strrev($binary);
            $data = unpack("d", $reverse);
            $value = $data[1];
        }
        return $value;
    }
}