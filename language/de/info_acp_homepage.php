<?php
/**
*
* @package Homepage for phpBB3.1
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'HOMEPAGE'			=> 'Einstellung für Homepage',
	'HP_SHOW'			=> 'Forum auf Homepage anzeigen',
	'HP_SHOW_EXPLAIN'   => 'Bei ja, wird das Forum auf der Homepage angezeigt. <br /><b>Unabhängig von gesetzten Rechten!</b>',
	'HP_SPECIAL'		=> 'Spezialkategorie',
	'HP_SPECIAL_EXPLAIN'=> 'Soll es in der Spezialkategorie angezeigt werden?',
	'HP_URL'			=> 'Linkname',
    'HP_URL_EXPLAIN'    => 'Mit diesem Link, ist das Forum auf der Homepage erreichbar. <br /><b>Es muss einmalig sein!</b>',
    'HP_POST'           => 'Beitrag anzeigen',
    'HP_POST_EXPLAIN'   => 'Füge die ID-Nummer des Beitrages das angezeigt werden soll, wenn das Forum auf der Homepage aufgerufen wird. <br /><b>Es wird nicht auf die gesetzten Rechte geachtet!</b>',
	'HP_META'			=> 'Meta-Informationen für Forum',
    'HP_META_EXPLAIN'   => 'Stichpunkte, die das Forum beschreibt. Nicht mehr wirklich wichtig.',
	'HP_DESC'			=> 'Beschreibung fürs Forum',
    'HP_DESC_EXPLAIN'   => 'Meta-Beschreibung fürs Forum',
	'HP_GALLERY'		=> 'Bilder aus der Galerie',
	'HP_GALLERY_EXPLAIN'=> 'Füge ID des Album ein.',
	'HP_GAME'			=> 'Infos zum Game',
	'HP_GAME_EXPLAIN'	=> 'Füge ID des Games ein',
));
