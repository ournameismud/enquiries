<?php
/**
 * Mud Filters plugin for Craft CMS 3.x
 *
 * Plugin for custom Mud filters
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\enquiries\twigextensions;

use Craft;

/**
 * @author    @cole007
 * @package   MudFilters
 * @since     1.0.0
 */
class EnquiriesTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Enquiries';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('json_decode', [$this, 'json_decode']),
            new \Twig_SimpleFilter('truncate', [$this, 'truncate']),
            new \Twig_SimpleFilter('implode', [$this, 'implode']),
            new \Twig_SimpleFilter('array_search', [$this, 'array_search'])
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('json_decode', [$this, 'json_decode']),
            new \Twig_SimpleFunction('truncate', [$this, 'truncate']),
            new \Twig_SimpleFunction('implode', [$this, 'implode']),
            new \Twig_SimpleFunction('array_search', [$this, 'array_search'])
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function json_decode($string, $array = false)
    {
        $output = json_decode($string, $array);
        // if ($array) $output = (array) $output;
        return $output;
    }

    public function array_search($array, $value)
    {
        $key = array_search($value,$array);
        return $key;
    }
    public function implode($array, $glue = ' ')
    {
        $output = implode($glue, $array);
        // if ($array) $output = (array) $output;
        return $output;
    }

    public function truncate($object, $words = 1)
    {
        $output = [];
        $array = (array) $object;
        $array = array_slice($array,0,$words);
        foreach ($array as $key => $value) {
            if (gettype($value) == 'string') $output[] = trim( $value );
            elseif (gettype($value) == 'array') $output[] = implode(' ', $value );
        }
        return implode(', ' , $output);
    }
}
