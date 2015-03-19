<?php

namespace USync\Parsing;

use USync\AST\Node;

use Symfony\Component\Yaml\Yaml;

class YamlReader implements ReaderInterface
{
    public function getFileExtensions()
    {
        return array('yml', 'yaml');
    }

    public function read($filename)
    {
        if (!class_exists('\Symfony\Yaml\Yaml')) {
            if (!@include_once __DIR__ . '/../../../vendor/autoload.php') {
                throw new \LogicException("Unable to find the \Symfony\Yaml class");
            }
        }

        $ret = Yaml::parse(file_get_contents($filename));

        if (!$ret) {
            throw new \InvalidArgumentException("Given data is not valid Yaml");
        }

        return $ret;
    }
}