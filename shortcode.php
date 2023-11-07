<?php

class CalendarShortcode
{
    // Konstruktor - Ruft die benötigten WordPress-Funktionen auf
    public function __construct()
    {
        add_shortcode(CURCHTOOLS_CALENDAR_SHORTCODE,array($this,'churchtool_shortcode'));
    }
    
    ////////////////////////////////////////////////////////////////////////////

    // Fügt den "shortcode" zu WordPress hinzu.
    function churchtool_shortcode($aAttributes,$sContent)
    {
        $nId = 1;
        if (array_key_exists('id',$aAttributes))
        {
            $nId = intval($aAttributes['id']);
        }

        return $this->getHtmlCalendarEvents($aAttributes,$sContent,$this->getCalendarEvents($nId));
    }

    ////////////////////////////////////////////////////////////////////////////

    function getHtmlCalendarEvents($aAttributes,$sTitle,&$aEvents)
    {
        wp_enqueue_style('ct-style');

        $nCount = 1; // Anzahl der anzuzeigenden Termine
        if (array_key_exists('count',$aAttributes))
        {
            $nCount = intval($aAttributes['count']);
        }
        
        $bIsPositiveFilter = TRUE; // Negativfilter ab Version 1.3.2 vom 13.02.2020
        $aFilters = array();
        if (array_key_exists('filter',$aAttributes))
        {
            $sFilters = $aAttributes["filter"];
            if (substr($sFilters,0,1) == "-")
            {
                $bIsPositiveFilter = FALSE; // Negativ-Filter
                $sFilters = substr($sFilters,1);
            }
            if (substr($sFilters,0,1) == "+")
            {
                $bIsPositiveFilter = TRUE; // Positiv-Filter
                $sFilters = substr($sFilters,1);
            }
            
            $aFilters = explode(',',$sFilters);
        }

        $bTitle = TRUE;
        if (array_key_exists('title',$aAttributes))
        {
            $bTitle = strtolower($aAttributes['title']) != 'no';
        }

        $bComment = TRUE;
        if (array_key_exists('comment',$aAttributes))
        {
            $bComment = strtolower($aAttributes['comment']) != 'no';
        }

        $bTitleLink = FALSE;
        if (array_key_exists('titlelink',$aAttributes))
        {
            $bTitleLink = strtolower($aAttributes['titlelink']) == 'yes';
        }
        
        $bDate = TRUE;
        if (array_key_exists('date',$aAttributes))
        {
            $bDate = strtolower($aAttributes['date']) != 'no';
        }

        $bTime = TRUE;
        if (array_key_exists('time',$aAttributes))
        {
            $bTime = strtolower($aAttributes['time']) != 'no';
        }

        $bEnd = FALSE;
        if (array_key_exists('end',$aAttributes))
        {
            $bEnd = strtolower($aAttributes['end']) == 'yes';
        }
        
        $bPlace = TRUE;
        if (array_key_exists('place',$aAttributes))
        {
            $bPlace = strtolower($aAttributes['place']) != 'no';
        }

        $bContact = TRUE;
        if (array_key_exists('contact',$aAttributes))
        {
            $bContact = strtolower($aAttributes['contact']) != 'no';
        }

        $bPhone = TRUE;
        if (array_key_exists('phone',$aAttributes))
        {
            $bPhone = strtolower($aAttributes['phone']) != 'no';
        }

        $bMail = TRUE;
        if (array_key_exists('mail',$aAttributes))
        {
            $bMail = strtolower($aAttributes['mail']) != 'no';
        }

        $bLink = TRUE;
        if (array_key_exists('link',$aAttributes))
        {
            $bLink = strtolower($aAttributes['link']) != 'no';
        }

        $bNote = TRUE;
        if (array_key_exists('note',$aAttributes))
        {
            $bNote = strtolower($aAttributes['note']) != 'no';
        }

        $bMore = TRUE;
        if (array_key_exists('more',$aAttributes))
        {
            $bMore = strtolower($aAttributes['more']) != 'no';
        }

        $bCategory = FALSE;
        if (array_key_exists('category',$aAttributes))
        {
            $bCategory = strtolower($aAttributes['category']) == 'yes';
        }
        
        $bCalendar = FALSE;
        if (array_key_exists('calendar',$aAttributes))
        {
            $bCalendar = strtolower($aAttributes['calendar']) == 'yes';
        }

        $bSymbols = TRUE;
        if (array_key_exists('symbols',$aAttributes))
        {
            $bSymbols = strtolower($aAttributes['symbols']) != 'no';
        }

        $sText = '';
        
        $i=0;
        if (is_array($aEvents))
        {
            foreach($aEvents as $aEvent)
            {
                if( $i >= $nCount)
                {
                    break;
                }

                $sName = $aEvent->bezeichnung;
                
                $sStartdate = substr($aEvent->startdate,0,10);
                $sEnddate = substr($aEvent->enddate,0,10);
                $sStarttime = substr($aEvent->startdate,11,8);
                $sEndtime = substr($aEvent->enddate,11,8);
                $dStart = strtotime($aEvent->startdate);
                $dEnd = strtotime($aEvent->enddate);
                
                $aMonth = array("Jan","Feb","Mär","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez");
                $aDays = array("Mo","Di","Mi","Do","Fr","Sa","So");
                
                $sMonth = $aMonth[date('n',$dStart) - 1];
                $sDay = date('j',$dStart);
                $sWeekday = $aDays[date('N',$dStart) - 1];
                
                $sDate = "";
                if ($sStartdate == $sEnddate)
                {
                    if (($bTime == TRUE) && ($bEnd == TRUE) && ($sStarttime != '00:00:00') && ($sStarttime < $sEndtime))
                    {
                        $sDate = date('d.m.Y \v\o\n H:i',$dStart).' bis '.date('H:i',$dEnd).' Uhr';
                    }
                    else if (($bTime == TRUE) && ($sStarttime != '00:00:00'))
                    {
                        $sDate = date('d.m.Y \u\m H:i',$dStart).' Uhr';
                    }
                    else
                    {
                        $sDate = date('d.m.Y',$dStart);
                    }
                }
                else
                {
                    if (substr($sStartdate,0,7) == substr($sEnddate,0,7))
                    {
                        $sDate = date('d.',$dStart)." - ".date('d.m.Y',$dEnd);
                    }
                    else if (substr($sStartdate,0,4) == substr($sEnddate,0,4))
                    {
                        $sDate = date('d.m',$dStart)." - ".date('d.m.Y',$dEnd);
                    }
                    else
                    {
                        $sDate = date('d.m.Y',$dStart)." - ".date('d.m.Y',$dEnd);
                    }
                }

                $sComment = '';
                if (property_exists($aEvent,'ort'))
                {
                    $sComment = $aEvent->ort;
                }

                $sPlace = '';
                $sNotes = '';
                $sContact = '';
                $sPhone = '';
                $sMail = '';
                $sLink = '';
                $aCategories = array();
                if (property_exists($aEvent,'notizen'))
                {
                    $aResult = $this->getEventCategories($aEvent->notizen);
                    $sNotes = $aResult[0];
                    // ab Version 1.3.1 vom 28.12.2019
                    // Notizen mit dem Text "Erstellt aus ChurchService" (oder ähnliches) unterbinden.
                    // Da ChurchTools auch in anderen Sprachen als Deutsch betrieben werden kann, wird die Suche auf den Text "ChurchService" beschränkt.
                    if (strpos($sNotes,"ChurchService") !== FALSE)
                    {
                        $sNotes = "";
                    }
                    $aCategories = $aResult[1]; 
                    
                    $sPlace = $this->getSpecialTags($sNotes,'place');
                    $sContact = $this->getSpecialTags($sNotes,'contact');
                    $sPhone = $this->getSpecialTags($sNotes,'phone');
                    $sMail = $this->getSpecialTags($sNotes,'mail');
                    $sLink = $this->getSpecialTags($sNotes,'link');

                    $sNotes = str_replace("\n",'<br>',$sNotes); // Hier " statt ' verwenden!
                    $sNotes = $this->replaceSpecialTags($sNotes,'caption','<span class="symbol"><strong>','</strong></span>');
                    $sNotes = $this->replaceSpecialTags($sNotes,'place','','');
                    $sNotes = $this->replaceSpecialTags($sNotes,'contact','','');
                    $sNotes = $this->replaceSpecialTags($sNotes,'phone','','');
                    $sNotes = $this->replaceSpecialTags($sNotes,'mail','','');
                    $sNotes = $this->replaceSpecialTags($sNotes,'link','','');
                    
                    // Leerzeilen am Ende entfernen
                    while (substr($sNotes,-4,4) == "<br>")
                    {
                        $sNotes = substr($sNotes,0,strlen($sNotes) - 4);
                    }
                }
                
                $sMore = '';
                if (property_exists($aEvent,'link'))
                {
                    $sMore = $aEvent->link;
                }
                
                $bShow = TRUE;
                if (count($aFilters) > 0)
                {
                    // Bei Positivfilterung werden nur die Termine verwendet, die zu den Filterkategorien passen.
                    // Bei Negativfilterung werden alle Termine verwendet, bis auf die die Filterkategorien passen.
                    $bShow = !$bIsPositiveFilter;
                    foreach($aFilters as $sFilter)
                    {
                        if (($bIsPositiveFilter == TRUE) && (strtolower($sFilter) == 'alle'))
                        {
                            $bShow = $bIsPositiveFilter;
                            break;
                        }
                        
                        foreach($aCategories as $sCategory)
                        {
                            if (strtolower($sFilter) == strtolower($sCategory))
                            {
                                $bShow = $bIsPositiveFilter;
                                break;
                            }
                        }
                    }
                }

                if ($bShow == TRUE)
                {
                    $sText .= '<div class="cc-event" >';

                    if ($bCalendar == TRUE)
                    {
                        $sText .= '<div class="cc-date">';
                        if (($bTitleLink == TRUE) && ($sMore != ""))
                        {
                            $sText .= '<a href="'.$sMore.'">';
                        }

                        $sText .= '<div class="cc-month">'.$sMonth.'</div>';
                        $sText .= '<div class="cc-day">'.$sDay.'</div>';
                        $sText .= '<div class="cc-weekday">'.$sWeekday.'</div>';
                        if (($bTitleLink == TRUE) && ($sMore != ""))
                        {
                            $sText .= '</a>';
                        }
                        $sText .= '</div>'; // cc-date

                        $sText .= '<div class="cc-eventbox">';
                    }
                    
                    if ($bTitle == TRUE)
                    {
                        if (($bTitleLink == TRUE) && ($sMore != ""))
                        {
                            $sText .= '<div class="cc-name"><a href="'.$sMore.'">'.$sName.'</a></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-name">'.$sName.'</div>';
                        }
                    }
                    if (($bComment == TRUE) && ($sComment != ""))
                    {
                        $sText .= '<div class="cc-note">'.$sComment.'</div>';
                    }
                    if (($bDate == TRUE) && ($sDate != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-date-symbol">'.$sDate.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sDate.'</span></div>';
                        }
                    }
                    if (($bPlace == TRUE) && ($sPlace != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-place-symbol">'.$sPlace.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sPlace.'</span></div>';
                        }
                    }
                    if (($bContact == TRUE) && ($sContact != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-contact-symbol">'.$sContact.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sContact.'</span></div>';
                        }
                    }
                    if (($bPhone == TRUE) && ($sPhone != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-phone-symbol">'.$sPhone.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sPhone.'</span></div>';
                        }
                    }
                    if (($bMail == TRUE) && ($sMail != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-mail-symbol">'.$sMail.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sMail.'</span></div>';
                        }
                    }
                    if (($bLink == TRUE) && ($sLink != ""))
                    {
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-link-symbol">'.$sLink.'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.$sLink.'</span></div>';
                        }
                    }
                    if (($bNote == TRUE) && ($sNotes != ""))
                    {
                        $sText .= '<div class="cc-note">'.$sNotes.'</div>';
                    }
                    if (($bMore == TRUE) && ($sMore != ""))
                    {
                        $sText .= '<div class="cc-note"><a href="'.$sMore.'">Mehr...</a></div>';
                    }
                    if (($bCategory == TRUE) && (count($aCategories) != 0))
                    {
                        $sCategories = "";
                        foreach($aCategories as $sCategory)
                        {
                            $sCategories .= ", ".$sCategory;
                        }
                        if ($bSymbols == TRUE)
                        {
                            $sText .= '<div class="cc-symbol"><span class="cc-category-symbol">'.substr($sCategories,2).'</span></div>';
                        }
                        else
                        {
                            $sText .= '<div class="cc-symbol"><span>'.substr($sCategories,2).'</span></div>';
                        }
                    }
                    
                    if ($bCalendar == TRUE)
                    {
                        $sText .= '</div>'; // eventbox
                    }
                    
                    $sText .= '</div>'; // event

                    $i++;
                }
            }
            
            if ($i > 0)
            {
                return '<div id="events">'.'<div class="cc-titte" style="clear:both">'.$sTitle.'</div>'.$sText.'</div><div style="clear:both"></div>';
            }
            return "";
        }
        else
        {
            return $aEvents;
        }    
    }

    ////////////////////////////////////////////////////////////////////////////

    // Liefert ein zweielementiges Array mit den Resttext und einem Array mit den Kategorien zurück.
    function getEventCategories($sText)
    {
        $aResult = array($sText,array());
        
        $nStart = strpos($sText,'[filter]');
        if ($nStart !== FALSE)
        {
            $nEnd = strpos($sText,'[/filter]');
            if ($nEnd != FALSE)
            {
                $sFilter = substr($sText,$nStart + 8,$nEnd - $nStart - 8);
                $aResult[1] = explode(",",$sFilter);
                
                $aResult[0] = substr($sText,0,$nStart).substr($sText,$nEnd + 9); 
            }
        }
        
        return $aResult;
    }

    ////////////////////////////////////////////////////////////////////////////

    // Sucht die angegebenen Spezial-Tags in dem Text und liefert diese zurück.
    function getSpecialTags($sText,$sTag)
    {
        $nLen = strlen($sTag);
        
        $nStart = strpos($sText,'['.$sTag.']');
        if ($nStart !== FALSE)
        {
            $nEnd = strpos($sText,'[/'.$sTag.']');
            if ($nEnd != FALSE)
            {
                $sContent = substr($sText,$nStart + $nLen + 2,$nEnd - $nStart - $nLen - 2);
                
                return $sContent;
            }
        }
        
        return "";
    }

    ////////////////////////////////////////////////////////////////////////////

    // Entfernt aus dem Text die angegebenen Spezial-Tags und liefert den angepassten Text zurück.
    function replaceSpecialTags($sText,$sTag,$sPre,$sPost)
    {
        $nLen = strlen($sTag);
        
        $n=0; // Endlosschleife vermeiden
        $nStart = strpos($sText,'['.$sTag.']');
        while (($nStart !== FALSE) && ($n < 10))
        {
            $nEnd = strpos($sText,'[/'.$sTag.']');
            if ($nEnd != FALSE)
            {
                if (($sPre != "") || ($sPost != ""))
                {
                    $sContent = substr($sText,$nStart + $nLen + 2,$nEnd - $nStart - $nLen - 2);
                }
                else
                {
                    $sContent = "";
                }
                $sText = substr($sText,0,$nStart).$sPre.$sContent.$sPost.substr($sText,$nEnd + $nLen + 3); 
            }
            
            $n++;
            $nStart = strpos($sText,'['.$sTag.']');
        }
        
        return $sText;
    }

    ////////////////////////////////////////////////////////////////////////////

    function hackText($sText)
    {
        $aChars = str_split($sText);
        foreach ($aChars as $nKey => $sChar)
        {
            $nOrd = ord($sChar);
            if ($nOrd < 128)
            {
                if (($nKey % 3 == 0) && ($sChar != '@'))
                {
                }
                else if ($nKey % 3 == 1)
                {
                    $aChars[$nKey] = '&#x'.dechex($nOrd).';';
                }
                else
                {
                    $aChars[$nKey] = '&#'.$nOrd.';';
                }
            }
        }
        
        return implode('',$aChars);
    }

    ////////////////////////////////////////////////////////////////////////////

    // Liefert die Kalendereinträge als Array.
    function getCalendarEvents($nId)
    {
        if (false === ($aEvents = get_transient('churchtools_calendar'.$nId)))
        {
            $aEvents = $this->getUpdatedCalendarEvents($nId);
        }
        
        $aEvents = $this->sortCalendarEvents($aEvents);
        
        return $aEvents;
    }

    ////////////////////////////////////////////////////////////////////////////

    // Sortiert die Kalendereintrage nach ihrem Starttermin
    function sortCalendarEvents($aEvents)
    {
        if (is_array($aEvents))
        {
            $aDates = array();
            foreach($aEvents as $nId => $aEvent)
            {
                $aDates[$nId] = strtotime($aEvent->startdate);
            }
            array_multisort($aDates,SORT_ASC,$aEvents);
        }
        
        return $aEvents;
    }

    ////////////////////////////////////////////////////////////////////////////

    // Liefert die Kalendereinträge zurück.
    // Dabei wird entweder auf die im Cockie gespeicherten Daten zurückgegriffen, 
    // oder falls das Aktualisierungsintervall überschritten wurde, ein neuer Ajax-Request abgesetzt.
    function getUpdatedCalendarEvents($nId)
    {
        $aOptions = get_option(sOPTION_NAME);
        
        $sAddress = ($nId == 1) ? sADDRESS_DEFAULT : '';
        if (isset($aOptions[sADDRESS_NAME.$nId]))
        {
            $sAddress = 'https://'.$aOptions[sADDRESS_NAME.$nId].'/index.php?q=churchcal/ajax';
        }

        $sCategory = ($nId == 1) ? sCATEGORY_DEFAULT : '';
        if (isset($aOptions[sCATEGORY_NAME.$nId]))
        {
            $sCategory = $aOptions[sCATEGORY_NAME.$nId];
        }
        
        $sPeriod = sPERIOD_DEFAULT;
        if (isset($aOptions[sPERIOD_NAME.$nId]))
        {
            $sPeriod = $aOptions[sPERIOD_NAME.$nId];
        }
        
        $sInterval = sINTERVAL_DEFAULT;
        if (isset($aOptions[sINTERVAL_NAME.$nId]))
        {
            $sInterval = $aOptions[sINTERVAL_NAME.$nId];
        }
        
        $aCategoryIds = explode(',',$sCategory);
        $aData = array('func' => 'getCalendarEvents', 
                    'category_ids' => $aCategoryIds,
                    'from' => 0,  
                    'to' => absint($sPeriod)); 
                    
        $aResult = $this->sendRequest($sAddress,$aData);
        
        if (is_string($aResult) == TRUE)
        {
            return $aResult; // Fehlertext
        }

        if(empty($aResult) == false)
        { 
            set_transient('churchtools_calendar'.$nId,$aResult,intval($sInterval)); 
        }

        return $aResult;
    }

    ////////////////////////////////////////////////////////////////////////////

    // Setzt einen Ajax-Request an einen ChurchTools-Webserver ab 
    // und liefert die erhaltenen Kalendereinträge (oder einen Fehlertext) zurück.
    function sendRequest($sUrl,$aData)
    {
        $aOptions = array
        (
            'http'=>array
            (
                'header' => 'Cookie: \r\nContent-type: application/x-www-form-urlencoded\r\n',
                'method' => 'POST',
                'content' => http_build_query($aData),
            )
        );
        $xContext = stream_context_create($aOptions);
        $sResult = file_get_contents($sUrl,false,$xContext);
        $aResult = json_decode($sResult);
        
        if ($aResult->status == 'success')
        {
            return $aResult->data;
        }
        else if ($aResult->status == 'error')
        {
            return $aResult->message;
        }
        else if ($aResult->status == 'fail')
        {
            return 'fail (wrong calendar id?)';
        }
        else
        {
            return 'unknown state';
        }
    }
}
?>