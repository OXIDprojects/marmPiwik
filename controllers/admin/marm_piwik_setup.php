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

class marm_piwik_setup extends oxAdminDetails
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = "marm_piwik_setup.tpl";

    /**
     * saves instance of marm_piwik
     * @var marm_piwik
     */
    protected $_oMarmPiwik = null;

    /**
     * returns marm_piwik object
     * @param bool $blReset forde create new object
     * @return marm_piwik
     */
    public function getMarmPiwik($blReset = false)
    {
        if ($this->_oMarmPiwik !== null && !$blReset) {
            return $this->_oMarmPiwik;
        }
        $this->_oMarmPiwik = oxNew('marm_piwik');

        return $this->_oMarmPiwik;
    }

    /**
     * returns marm_piwik full config array
     * @return array
     */
    public function getConfigValues()
    {
        $oMarmPiwik = $this->getMarmPiwik();
        return $oMarmPiwik->getConfig();
    }

    /**
     * passes given parameters from 'editval' to marm_piwik change config
     * @return void
     */
    public function save()
    {
        $aParams = oxConfig::getParameter( "editval" );
        $oMarmPiwik = $this->getMarmPiwik();
        $oMarmPiwik->changeConfig($aParams);
    }
}