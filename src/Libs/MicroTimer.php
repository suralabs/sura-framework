<?php


namespace Sura\Libs;


 class MicroTimer
{
    private int $start_time;

     /**
      * @return int
      */
	public function start():int
    {
		$mtime = microtime();
		$mtime = explode( ' ', $mtime );
		$mtime = $mtime['1'] + $mtime['0'];
		$this->start_time = $mtime;
		return true;
	}

     /**
      * @return int
      */
	public function stop():int
    {
		$mtime = microtime();
		$mtime = explode( ' ', $mtime );
		$mtime = $mtime['1'] + $mtime['0'];
		$end_time = $mtime;
        return round( ($end_time - $this->start_time), 5 );
	}
}
