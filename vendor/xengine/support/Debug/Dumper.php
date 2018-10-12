<?php

namespace XEngine\Support\Debug;

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class Dumper
{
    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function dump($value)
    {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;
            $cloner = new VarCloner();
            $cloner->setMaxItems(999999);
            $dumper->dump($cloner->cloneVar($value));
        } else {
            var_dump($value);
        }
    }
}
