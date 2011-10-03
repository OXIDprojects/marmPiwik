<?php
/**
 * Piwik integration in OXID
 *
 * Copyright (c) 2011 Joscha Krug | marmalade.de
 * E-mail: mail@marmalade.de
 * http://www.marmalade.de
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

class marm_piwik_setup  extends oxAdminDetails
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = "marm_piwik_setup.tpl";

    protected $_aConfigKeys = array(
        'marm_piwik_site_id',
        'marm_piwik_url',
        'marm_piwik_newsletter_goal_id'
    );

    public function getConfigValues()
    {
        $oConfig = $this->getConfig();
        $aValues = array();
        foreach ($this->_aConfigKeys as $sConfigKey) {
            $aValues[$sConfigKey] = $oConfig->getConfigParam($sConfigKey);
        }
        return $aValues;
    }

    public function save()
    {
        $aParams = oxConfig::getParameter( "editval" );
        $oConfig = $this->getConfig();
        foreach ($this->_aConfigKeys as $sConfigKey) {
            if (isset($aParams[$sConfigKey])) {
                $oConfig->saveShopConfVar( 'str', $sConfigKey, $aParams[$sConfigKey] );
            }
        }
    }
}