<?php

namespace Jsgettext\Dumper;

use Jsgettext\File\File,
    Jsgettext\Poedit\PoeditFile,
    Jsgettext\Poedit\PoeditString;

class PoeditDumper implements DumperInterface
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
    *   Dump PoeditFile into .po file
    *
    *   @param PoeditFile   $file
    *   @param string       $filename
    *   @param boolean      $sort       if enabled, sort strings and their comments. implemented to avoid too many git conflicts
    *
    *   @return boolean
    */
    public function dump(PoeditFile $file, $filename = null, $sort = false)
    {
        $filename = null !== $filename ? $filename : $this->file;
        $content = $file->getHeaders() . PHP_EOL . PHP_EOL;

        $strings = true === $sort ? $file->sortStrings()->getStrings() : $file->getStrings();

        foreach ($strings as $string) {
            $content .= true === $sort ? $string->sortReferences()->sortComments()->sortExtracteds()->sortFlags() : $string;
        }

        // ensure that path and file exists
        File::mkdirr(substr($filename, 0, strrpos($filename, '/')));

        return false !== file_put_contents($filename, $content);
    }
}
