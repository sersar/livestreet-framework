<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

/**
 * Плагин для смарти
 * Подключает шаблон компонента
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_component($aParams, &$oSmarty)
{
    if (isset($aParams['_default_short'])) {
        $aParams['name'] = $aParams['_default_short'];
    }
    if (empty($aParams['name'])) {
        trigger_error("Config: missing 'name' parametr", E_USER_WARNING);
        return;
    }
    $sName = $aParams['name'];
    $sTemplate = null;
    if (isset($aParams['template'])) {
        $sTemplate = $aParams['template'];
    }
    /**
     * Получаем параметры компонента
     */
    $aComponentParams = array();
    if (isset($aParams['params']) and is_array($aParams['params'])) {
        $aComponentParams = $aParams['params'];
    } else {
        unset($aParams['name']);
        unset($aParams['_default_short']);
        unset($aParams['template']);
        $aComponentParams = $aParams;
    }
    /**
     * Получаем путь до шаблона
     */
    if ($sPathTemplate = Engine::getInstance()->Component_GetTemplatePath($sName,
            $sTemplate) and Engine::getInstance()->Viewer_TemplateExists($sPathTemplate)
    ) {
        $oViewerLocal = Engine::getInstance()->Viewer_GetLocalViewer();
        /**
         * Загружаем глобальные
         */
        $oViewerLocal->Assign($oSmarty->getTemplateVars());
        /**
         * Загружаем локальные
         */
        foreach ($aComponentParams as $sKey => $mValue) {
            $oViewerLocal->Assign($sKey, $mValue, true);
        }
        $sResult = $oViewerLocal->Fetch($sPathTemplate);
        unset($oViewerLocal);
    } else {
        $sResult = 'Component template not found: ' . $sName . '/' . ($sTemplate ? $sTemplate : $sName) . '.tpl';
    }

    if (!empty($aParams['assign'])) {
        $oSmarty->assign($aParams['assign'], $sResult);
    } else {
        return $sResult;
    }

    return '';
}