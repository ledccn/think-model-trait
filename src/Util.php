<?php

namespace Ledc\ThinkModelTrait;

class Util
{
    /**
     * @param string $name
     * @return string
     */
    public static function nameToNamespace(string $name): string
    {
        $namespace = ucfirst($name);
        $namespace = preg_replace_callback(['/-([a-zA-Z])/', '/(\/[a-zA-Z])/'], function ($matches) {
            return strtoupper($matches[1]);
        }, $namespace);

        return str_replace('/', '\\' ,ucfirst($namespace));
    }

    /**
     * @param string $class
     * @return string
     */
    public static function classToName(string $class): string
    {
        $class = lcfirst($class);
        return preg_replace_callback(['/([A-Z])/'], function ($matches) {
            return '_' . strtolower($matches[1]);
        }, $class);
    }

    /**
     * @param string $class
     * @return string
     */
    public static function nameToClass(string $class): string
    {
        $class = preg_replace_callback(['/-([a-zA-Z])/', '/_([a-zA-Z])/'], function ($matches) {
            return strtoupper($matches[1]);
        }, $class);

        if (!($pos = strrpos($class, '/'))) {
            $class = ucfirst($class);
        } else {
            $path = substr($class, 0, $pos);
            $class = ucfirst(substr($class, $pos + 1));
            $class = "$path/$class";
        }
        return $class;
    }
}