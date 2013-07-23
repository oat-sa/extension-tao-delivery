<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

 $url = $_SERVER['REQUEST_URI'];
 $configPath = dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'configGetFile.php';
 $ttl = 60;
 
 $rel = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/getFile.php/') + strlen('/getFile.php/'));
 $parts = explode('/', $rel, 3);
 if (count($parts) < 3 || !file_exists($configPath)) {
 	header('HTTP/1.0 403 Forbidden');
    die();
 }
  $config = include $configPath;
 $compiledPath = $config['folder'];
 $secretPassphrase = $config['secret'];

 list($timestamp, $token, $subPath) = $parts;
 $parts = explode('*/', $subPath, 2);
 //TODO add security check on url
 if (count($parts) < 2) {
 	header('HTTP/1.0 403 Forbidden');
    die();
 }
 list($subPath, $file) = $parts;
 $correctToken = md5($timestamp.$subPath.$secretPassphrase);

 if (time() - $timestamp > $ttl || $token != $correctToken) {
 	header('HTTP/1.0 403 Forbidden');
    die();
 }
 
 $filename = $compiledPath.$subPath.$file;
  
 require_once '../generis/helpers/class.File.php';
 require_once '../tao/helpers/class.File.php';
 $mimeType = tao_helpers_File::getMimeType($filename);
 
 header('Content-Type: '.$mimeType);
 $fp = fopen($filename, 'rb');
 fpassthru($fp);
 exit;