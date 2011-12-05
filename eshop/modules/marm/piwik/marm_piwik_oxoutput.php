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

class marm_piwik_oxoutput extends marm_piwik_oxoutput_parent
{
    /**
     * appends PIWIK javascript source before body tag
     * @param $sOutput
     * @return mixed
     */
    public function marmReplaceBody( $sOutput )
    {
        $blAdminUser=false;
        $oUser=$this->getUser();
        if ($oUser) $blAdminUser=$oUser->inGroup("oxidadmin");
        if(!isAdmin()&&!$blAdminUser) {
            $oMarmPiwik = oxNew('marm_piwik');
            $sPiwikCode = $oMarmPiwik->getMarmPiwikCode();
            $sOutput = str_ireplace("</body>", "{$sPiwikCode}\n</body>", ltrim($sOutput));
        }
        return $sOutput;
    }

    /**
     * returns $sValue filtered by parent and marm_piwik_oxoutput::marmReplaceBody
     * @param $sValue
     * @param $sClassName
     * @return mixed
     */
    public function process($sValue, $sClassName)
    {
        $sValue = parent::process($sValue, $sClassName);
        $sValue = $this->marmReplaceBody( $sValue);
        return $sValue;
    }
}