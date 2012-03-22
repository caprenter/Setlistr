<?php
/*
 *      exportXML.php
 *      Exports a set list in 'playlist' format
 *      
 *      Copyright 2012 caprenter <caprenter@gmail.com>
 *      
 *      This file is part of Setlistr.
 *      
 *      Setlistr is free software: you can redistribute it and/or modify
 *      it under the terms of the GNU Affero General Public License as published by
 *      the Free Software Foundation, either version 3 of the License, or
 *      (at your option) any later version.
 *      
 *      Setlistr is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU Affero General Public License for more details.
 *      
 *      You should have received a copy of the GNU Affero General Public License
 *      along with Setlistr.  If not, see <http://www.gnu.org/licenses/>.
 *      
 *      Setlistr relies on other free software products. See the README.txt file 
 *      for more details.
 */
 
 /**
 * Tests to see if a list is public. If it is we return the Title and last updated time 
 * name: is_list_public
 * @param integer $list_id The unique id of a set lists
 * @return string Tilte of the set list
 */
function exportXML($data, $username, $list_title, $return_string = false) {
    
    $stream = ($return_string) ? fopen ('php://temp/maxmemory', 'w+') : fopen ('php://output', 'w');

    $doc = new DomDocument('1.0','UTF-8');
    
    //<playlist version="1" xmlns="http://xspf.org/ns/0/">
    $root = $doc->createElement('playlist');
    $root = $doc->appendChild($root);
      $root->setAttribute('version', '1');
      $root->setAttribute('xmlns', 'http://xspf.org/ns/0/');
      
    //<title>80s Music</title>
    $title = $doc->createElement('title');
    $title = $root->appendChild($title);
    $value = $doc->createTextNode($list_title);
    $value = $title->appendChild($value);
    
    //<creator>Jane Doe</creator>
    $creator = $doc->createElement('creator');
    $creator = $root->appendChild($creator);
    $value = $doc->createTextNode($username);
    $value = $creator->appendChild($value);

    //<trackList>
    $trackList = $doc->createElement('trackList');
    $trackList = $root->appendChild($trackList);

    foreach ($data as $record) {
      //<track>
      $track = $doc->createElement('track');
      $track = $trackList->appendChild($track);
      
      //<title>
      $title = $doc->createElement('title');
      $title = $track->appendChild($title);
      $value = $doc->createTextNode($record[0]);
      $value = $title->appendChild($value);
      
      if ($record[1] == "not in set") {
          $annotation_msg = "not in set";
      }
      
      if ($record[2] == 'break') {
        if (isset($annotation_msg)) {
          $annotation_msg = "break;not in set";
        } else {
          $annotation_msg = "break";
        }
      }
      if (isset($annotation_msg)) {
        //<annotation>I love this song</annotation>
        $annotation = $doc->createElement('annotation');
        $annotation = $track->appendChild($annotation);
        $value = $doc->createTextNode($annotation_msg);
        $value = $annotation->appendChild($value);
        unset($annotation_msg);
      }
      
      
    }

  $doc->formatOutput = true;
  echo $doc->saveXML(); 
}
?>
