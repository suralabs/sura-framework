<?php
declare(strict_types=1);

namespace Sura\Libs;

use Sura\Contracts\ParseInterface;

class Parse implements ParseInterface
{
	
	/**
	 * @param string $source
	 * @param bool $preview
	 * @return string
	 */
	public function BBparse(string $source, $preview = false): string
	{
//		global $config;
		
		$source = preg_replace("#<iframe#i", "&lt;iframe", $source);
		$source = preg_replace("#<script#i", "&lt;script", $source);

        $source = str_ireplace(array("{", "`", "{theme}", "[/b]", "[b]", "[/i]", "[i]", "[/u]", "[u]"), array("&#123;", "&#96;", "&#123;theme}", "</b>", "<b>", "</i>", "<i>", "</u>", "<u>"), $source);
		
		$source = preg_replace("#\[(left|right|center)\](.+?)\[/\\1\]#is", "<div >\\2</div>", $source);
		
		$source = preg_replace("#\[quote\](.+?)\[/quote\]#is", "<blockquote>\\1</blockquote>", $source);
		
		if (stripos($source, "[video]") !== false || stripos($source, "[photo]") !== false || stripos($source, "[link]") !== false) {
			$source = preg_replace("#\\[video\\](.*?)\\[/video\\]#ies", "\$this->BBvideo('\\1', '{$preview}')", $source);
			$source = preg_replace("#\\[photo\\](.*?)\\[/photo\\]#ies", "\$this->BBphoto('\\1', '{$preview}')", $source);
			$source = preg_replace("#\\[link\\](.*?)\\[/link\\]#ies", "\$this->BBlink('\\1')", $source);
		}
		return $source;
	}
	
	/**
	 * @param $source
	 * @param bool $preview
	 * @return string
	 */
	public function BBvideo(string $source, bool $preview = false): string
	{
//		global $config;
		$config = Settings::load();
		
		$exp = explode('|', $source);
		$home_url = $config['home_url'];
		
		$border = $width = $height = '';
		
		if (stripos($source, "{$exp['0']}|{$exp['1']}|{$home_url}") !== false) {
			
			if ($exp['3']) {
				if ($exp['3'] > 175) $width = "width=\'175\'"; else
					$width = "width=\'{$exp[3]}\'";
			}
			
			
			if ($exp['4']) {
				if ($exp['4'] > 131) {
                    $height = "height=\'131\'";
                } else {
                    $height = "height=\'{$exp['4']}\'";
                }
			}
			
			if ($exp['5']) {
                $border = 'notes_videoborder';
            }
			
			if ($exp['6']) {
                $blank = 'target="_blank"';
            } else {
                $blank = "onClick=\"videos.show({$exp['1']}, this.href, \'/notes/view/{note-id}\'); return false\"";
            }
			
			if ($exp['7'] == 1) {
                $pos = "align=\"left\"";
            } elseif ($exp['7'] == 2) {
                $pos = "align=\"right\"";
            }
			else {
                $pos = "";
            }
			
			if (!$preview) {
				$link = "<a href=\"/video{$exp['0']}_{$exp['1']}_sec=notes/id={note-id}\" {$blank}>";
				$slink = "</a>";
			} else {
				$link = '';
				$slink = '';
			}
			
			$source = "<!--video:{$source}-->{$link}<img src=\"{$exp['2']}\" {$width} {$height} {$pos} class=\"notes_videopad {$border}\"  alt=\"img\"/>{$slink}<!--/video-->";
		}
		
		return $source;
	}
	
	/**
	 * @param $source
	 * @param bool $preview
	 * @return string
	 */
	public function BBphoto(string $source, $preview = false): string
	{
		$config = Settings::load();
		$exp = explode('|', $source);
		$home_url = $config['home_url'];
		
		$border = $width = $height = '';
		
		if (stripos($source, "{$exp['0']}|{$exp['1']}|{$home_url}") !== false) {
			
			if ($exp['3'] > 160) $exp['2'] = str_replace('/c_', '/', $exp['2']);
			
			if ($exp['4'] > 120) $exp['2'] = str_replace('/c_', '/', $exp['2']);
			
			if ($exp['3']) if ($exp['3'] > 740) $width = "width=\'740\'"; else
				$width = "width=\'{$exp['3']}\'";
			
			if ($exp['4']) if ($exp['4'] > 547) $height = "height=\'547\'"; else
				$height = "height=\'{$exp['4']}\'";
			
			if ($exp['5']) {
                $border = 'notes_videoborder';
            }
			
			if ($exp['6']) {
                $blank = 'target="_blank"';
            } else {
                $blank = "onClick=\"Photo.Show(this.href); return false\"";
            }
			
			if ($exp['7'] == 1) {
                $pos = "align=\"left\"";
            } elseif ($exp['7'] == 2) {
                $pos = "align=\"right\"";
            }
			else {
                $pos = "";
            }
			
			if ($exp['8'] and !$preview and $exp['0'] and $exp['1']) {
				$link = "<a href=\"/photo{$exp['0']}_{$exp['1']}_sec=notes/id={note-id}\" {$blank}>";
				$elink = "</a>";
			} elseif ($exp['8']) {
				$link = "<a href=\"{$exp['2']}\" target=\"_blank\">";
				$elink = "</a>";
			} else {
				$link = '';
				$elink = '';
			}
			
			if ($exp['0'] && $exp['1']) {
                $source = "<!--photo:{$source}-->{$link}<img class=\"notes_videopad {$border}\" src=\"{$exp['2']}\" {$width} {$height} {$pos} />{$elink}<!--/photo-->";
            } else {
                $source = "<!--photo:{$source}-->{$link}<img class=\"notes_videopad {$border}\" src=\"{$exp['2']}\" {$width} {$height} {$pos} />{$elink}<!--/photo-->";
            }
		}
		
		return $source;
	}
	
	/**
	 * @param $source
	 * @return string
	 */
	public function BBlink(string $source): string
	{
		$exp = explode('|', $source);
		if ($exp['0']) {
			if (!$exp['1']) {
                $exp['1'] = $exp['0'];
            }
			$exp['0'] = str_replace(':', '', $exp[0]);
			$source = "<!--link:{$source}--><a href=\"{$exp['0']}\" target=\"_blank\">{$exp['1']}</a><!--/link-->";
		}
		return $source;
	}
	
	/**
	 * @param string $source
	 * @return string
	 */
	public function BBdecode(string $source): string
	{
        $source = str_ireplace(array("&#123;", "&#96;", "&#123;theme}", "</b>", "<b>", "</i>", "<i>", "</u>", "<u>"), array("{", "`", "{theme}", "[/b]", "[b]", "[/i]", "[i]", "[/u]", "[u]"), $source);
		$source = preg_replace("#<div align=\"(left|right|center)\">(.+?)</div>#is", "[\\1]\\2[/\\1]", $source);
		$source = preg_replace("#\[quote\](.+?)\[/quote\]#is", "<blockquote>\\1</blockquote>", $source);
		$source = preg_replace("#<blockquote>(.+?)</blockquote>#is", "[quote]\\1[/quote]", $source);
		if (stripos($source, "<!--photo:") !== false || stripos($source, "<!--video:") !== false || stripos($source, "<!--link:") !== false) {
			$source = preg_replace("#\\<!--video:(.*?)\\<!--/video-->#ies", "\$this->BBdecodeVideo('\\1')", $source);
			$source = preg_replace("#\\<!--photo:(.*?)\\<!--/photo-->#ies", "\$this->BBdecodePhoto('\\1')", $source);
			$source = preg_replace("#\\<!--link:(.*?)\\<!--/link-->#ies", "\$this->BBdecodeLink('\\1')", $source);
		}
		return $source;
	}
	
	/**
	 * @param $source
	 * @return string
	 */
	public function BBdecodePhoto(string $source): string
	{
		$start = explode('-->', $source);
		return "[photo]{$start['0']}[/photo]";
	}
	
	/**
	 * @param $source
	 * @return string
	 */
	public function BBdecodeVideo(string $source): string
	{
		$start = explode('-->', $source);
		return "[video]{$start['0']}[/video]";
	}
	
	/**
	 * @param $source
	 * @return string
	 */
	public function BBdecodeLink(string $source): string
	{
		$start = explode('-->', $source);
		return "[link]{$start['0']}[/link]";
	}
}
