<?php
namespace Lemon;

class Cache {

    public function compressStart()
    {
        if(!ob_start("ob_gzhandler")) ob_start();
    }

    public function compressEnd()
    {
        $gzip_contents = ob_get_contents();
        $gzip_contents = ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</","//","'/\*[^*]*\*/'","/\r\n/","/\n/","/\t/",'/>[ ]+</'),array(">\\1<",'','','','','','><'),$gzip_contents)));
        ob_end_clean();
        return $gzip_contents;
    }
}