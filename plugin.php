<?php 

/*
Plugin Name: ct-cal
Plugin URI: http://rhein-main-kreis.feg.de/ct-cal-beispiele/
Description: Diese Plugin zeigt die Einträge eines ChurchTool-Kalenders an.
Version: 1.3.3
Date: 07.03.2023
Author: Stefan Hermes, Mühlheim am Main (auf Basis des Codes von Kevin Hermann, Bielefeld)
Author URI: http://rhein-main-kreis.feg.de/
*/

// Der Shortcode hat folgende Attribute:
//
// id=1           Kennung der hinterlegten ChurchTools-Anbindung
// count=42       Anzahl der anzuzeigenden Kalendereinträge
// filter=...     Die Bereiche aus denen die Kalendereinträge stammen sollen.
//                Z.B. Kinder, Pfadfinder, Jugend, Single, Frauen, Senioren, Seelsorge, Alle
//                Es können mehrere Bereich durch ein Komma getrennt angegebwen werden.
// title=no       Mit dem Wert no kann die Anzeige des Titels ausgeblendet werden.
// titlelink=yes  Mit dem Wert yes kann der Titel als Link eingeschaltet werden.
// comment=no     Mit dem Wert no kann die Anzeige der Bemerkung ausgeblendet werden.
// date=no        Mit dem Wert no kann die Terminanzeige ausgeschaltet werden.
// time=no        Mit dem Wert no kann die Uhrzeitanzeige ausgeschaltet werden.
// end=yes        Mit dem Wert yes kann die Endzeit eingeschaltet werden.
// place=no       Mit dem Wert no kann die Anzeige des Veranstaltungsortes ausgeblendet werden.
// contact=no     Mit dem Wert no kann die Anzeige des Veranstalters ausgeblendet werden.
// phone=no       Mit dem Wert no kann die Anzeige der Kontakttelefonnummer ausgeblendet werden.
// mail=no        Mit dem Wert no kann die Anzeige der Kontakt-E-Mail-Adresse ausgeblendet werden.
// link=no        Mit dem Wert no kann die Anzeige eines zweiten Links ausgeblendet werden.
// note=no        Mit dem Wert no kann die Beschreibung des Kalendereintrags ausgeschaltet werden.
// more=no        Mit dem Wert no kann der More-Link am Ende ausgeschaltet werden.
// category=yes   Mit dem Wert yes kann die Anzeige der Filterkategorien eingeschaltet werden.
// calendar=yes   Mit dem Wert yes kann die Kalenderblattdarstellung links neben den Daten eingeschaltet werden.
// symbols=no     Mit dem Wert no kann die Anzeige der Symbole bei Termin, Ort, Kontakt, Telefon, E-Mail-Adresse und Link ausgeschaltet werden.
//
// Wird der Shortcode in der Form [...]blabla[/...] verwendet, wird der Text "blabla" nur dann angezeigt, wenn es wenigstens einen Kalendereintrag gibt.

////////////////////////////////////////////////////////////////////////////////

include(dirname(__FILE__).'/settingspage.php');
include(dirname(__FILE__).'/shortcode.php');

////////////////////////////////////////////////////////////////////////////////

// Name des Shortcodes
const CURCHTOOLS_CALENDAR_SHORTCODE = 'ct-cal';

// Name der Datenablage
const sOPTION_NAME = 'ct-cal';

// Adresse des ChurchTool-Webservers 
const sADDRESS_NAME = 'address';
const sADDRESS_DEFAULT = 'ct-rhein-main-kreis.feg.de';

// Kennungen der Kalender, die angezeigt werden sollen.
const sCATEGORY_NAME = 'category';
const sCATEGORY_DEFAULT = '2,5';

// Abfragezeitraum (in Tagen)
const sPERIOD_NAME = 'period';
const sPERIOD_DEFAULT = '366';

// Aktualisierungsintervall (in Sekunden)
const sINTERVAL_NAME = 'interval';
const sINTERVAL_DEFAULT = '3600';

// Direkte Abfrage der Daten:
// => https://ct-rhein-main-kreis.feg.de/index.php?q=churchcal/ajax&func=getCalendarEvents&category_ids[0]=2&category_ids[1]=5&from=0&to=366
// => https://ct-rhein-main-kreis.feg.de/index.php?q=churchcal/infoscreen_ids[1]=5&from=0&to=366

////////////////////////////////////////////////////////////////////////////////

if (is_admin())
{
    $oSettingsPage = new SettingsPage();
}

if (TRUE)
{
    $oCalendarShortcode = new CalendarShortcode();
}

////////////////////////////////////////////////////////////////////////////////

wp_register_style('ct-style',plugins_url('/style.css',__FILE__));

////////////////////////////////////////////////////////////////////////////////

// Version 1.3.0
//
// - Den Wert place aus der Ajax-Anfrage als Untertitel interpretiert.
// - Stattdessen das place-Tag eingeführt.
//
// Version 1.3.1
//
// - Notizen mit dem Text "Erstellt aus ChurchService" (oder ähnliches) unterbunden.
//
// Version 1.3.2 
//
// - Bugfix $sIntervall
// - Negativer Filter
//
// Version 1.3.3 
//
// - Bugfix $array_key_exists -> property_exists

////////////////////////////////////////////////////////////////////////////////

?>