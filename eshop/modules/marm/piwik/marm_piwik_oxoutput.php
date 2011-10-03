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

class marm_piwik_oxoutput extends marm_piwik_oxoutput_parent {

    protected $_aPushParams = array();

    /**
     * returns config parameter for siteId
     * @return string
     */
    public function getPiwikSiteId()
    {
        return $this->getConfig()->getConfigParam('marm_piwik_site_id');
    }

    /**
     * returns config parameter for PIWIK URL
     * @return string
     */
    public function getPiwikUrl()
    {
        return $this->getConfig()->getConfigParam('marm_piwik_url');
    }

    /**
     * returns config parameter for Newsletter Goal ID
     * @return string
     */
    public function getPiwikNewsletterGoalId()
    {
        return $this->getConfig()->getConfigParam('marm_piwik_newsletter_goal_id');
    }

    /**
     * returns current/active page controller (aka view)
     * @return oxUBase
     */
    protected function _getViewOrder()
    {
        return $this->getConfig()->getActiveView();
    }

    /**
     * Saves given values for pushing to javascript array object  (_paq.push)
     * @return void
     */
    public function addPushParams()
    {
        if (func_num_args() < 1) {
            return;
        }
        $this->_aPushParams[] = func_get_args();
    }

    /**
     * returns javascript array pushes, that are saved
     * before with addPushParams("param1", "param2").
     * example:
     * _paq.push(["func1", false, "param2", 1, 0.2]);
     * _paq.push(["func2"]);
     * @return string
     */
    public function generateParams()
    {
        $sReturn = '';
        foreach ($this->_aPushParams as $aPushArray) {
            $aFormed = array();
            foreach($aPushArray as $mPushParam) {
                if (is_string($mPushParam)) {
                    $aFormed[] = '"'.$mPushParam.'"';
                }
                elseif(is_bool($mPushParam)) {
                    $aFormed[] = $mPushParam?'true':'false';
                }
                elseif(is_double($mPushParam) || is_float($mPushParam)) {
                    $aFormed[] = sprintf("%.2f", $mPushParam);
                }
                else {
                    $aFormed[] = $mPushParam;
                }
            }
            $sReturn .="\n _paq.push([".implode(', ', $aFormed)."]);";
        }
        return $sReturn;
    }

    /**
     * @param Search $oViewObject
     * @return void
     */
    public function setPiwikParamsForSearch($oViewObject)
    {
        // seems like deprecated but needed for downwards compatibility
        // better use $oViewObject->getArticleCount() at a later time
        if($oViewObject->getPageNavigation()->iArtCnt > 1) {
            $this->addPushParams('setCustomVariable', 1, 'Suchparameter', $oViewObject->getSearchParamForHtml(), 'page');
        }else{
            $this->addPushParams('setCustomVariable', 2, 'fehlgeschlagene Suche nach', $oViewObject->getSearchParamForHtml(), 'page');
        }
    }

    /**
     * @param Newsletter $oViewObject
     * @return void
     */
    public function setPiwikParamsForNewsletter($oViewObject)
    {
        switch ($oViewObject->getNewsletterStatus()) {
            case 1:
                $this->addPushParams('setCustomVariable',3,'Newsletter', 'bestellt', 'page');
                break;
            case 2:
                $this->addPushParams('setCustomVariable',3,'Newsletter', 'aktiviert', 'page');
                $iNewsLetterGoalId = $this->getPiwikNewsletterGoalId();
                if ( $iNewsLetterGoalId ) {
                    $this->addPushParams('trackGoal',$iNewsLetterGoalId);
                }
                break;
            case 3:
                $this->addPushParams('setCustomVariable',3,'Newsletter', 'abbestellt', 'page');
                break;
            case 4:
            default:
                $this->addPushParams('setCustomVariable',3,'Newsletter', 'Bestellseite angeschaut', 'page');
                break;
        }

    }


    /**
     * @param Basket $oViewObject
     * @return void
     */
    public function setPiwikParamsForBasket($oViewObject)
    {
        /**
         * using session basket as frontend controller ($oViewObject)
         * may NOT have getBasket method (for example Details)
         * @var oxBasket $oBasket
         */
        $oBasket = $this->getSession()->getBasket();
        $this->_setEcommerceItemsByBasket($oBasket);
        $this->addPushParams('trackEcommerceCartUpdate', $oBasket->getPrice()->getBruttoPrice());
    }

    /**
     * @param oxBasket $oBasket
     * @return void
     */
    protected function _setEcommerceItemsByBasket($oBasket)
    {
        foreach ($oBasket->getContents() as $oBasketItem)
        {
            $oArticle = $oBasketItem->getArticle();
            $this->addPushParams(
                'addEcommerceItem',
                $oArticle->oxarticles__oxartnum->value,
                $oArticle->oxarticles__oxtitle->value,
                $oArticle->getCategory()->oxcategories__oxtitle->value,
                $oBasketItem->getUnitPrice()->getBruttoPrice(),
                $oBasketItem->getAmount()
            );
        }
    }

    /**
     * @param Thankyou $oViewObject
     * @return void
     */
    public function setPiwikParamsForThankyou($oViewObject)
    {
        $oBasket = $oViewObject->getBasket();
        $oOrder = $oViewObject->getOrder();
        $this->_setEcommerceItemsByBasket($oBasket);
        $dTaxAmount = $oOrder->oxorder__oxartvatprice1->value + $oOrder->oxorder__oxartvatprice2->value;
        $dShippingTotal =
                  $oOrder->getOrderDeliveryPrice()->getBruttoPrice()
                + $oOrder->getOrderPaymentPrice() ->getBruttoPrice()
                + $oOrder->getOrderWrappingPrice()->getBruttoPrice()
        ;
        $this->addPushParams(
            'trackEcommerceOrder',
            $oOrder->oxorder__oxordernr->value,
            $oOrder->getTotalOrderSum(),
            $oBasket->getDiscountedProductsBruttoPrice(),
            $dTaxAmount,
            $dShippingTotal,
            $oOrder->oxorder__oxdiscount->value
        );
    }

    /**
     * @param aList $oViewObject
     * @return void
     */
    public function setPiwikParamsForAlist($oViewObject)
    {
        // i (Wanis) think it should pass category name, not alist tilte, as it could be renamed by module (like seo)
        //$this->addPushParams('setEcommerceView', false, false, $oViewObject->getTitle());

        $oCategory = $oViewObject->getActCategory();
        $this->addPushParams('setEcommerceView', false, false, $oCategory->oxcategories__oxtitle->value);
    }

    /**
     * @param Details $oViewObject
     * @return void
     */
    public function setPiwikParamsForDetails($oViewObject)
    {
        $oProduct =  $oViewObject->getProduct();
        $oCategory = $oViewObject->getCategory();
        $this->addPushParams(
            'setEcommerceView',
            $oProduct->oxarticles__oxartnum->value,
            $oProduct->oxarticles__oxtitle->value,
            $oCategory->oxcategories__oxtitle->value
        );
    }

    public function getPiwik404Code()
    {
        // if empty Class
        //_paq.push(["setDocumentTitle", '404/URL = '+String(document.location.pathname+document.location.search).replace(/\//g,"%2f") + '/From = ' + String(document.referrer).replace(/\//g,"%2f")]);

    }
    /**
     * runs functiion setPiwikParamsFor.. by active controller (_getViewOrder())
     * if function "tobasket" called, setPiwikParamsForBasket() executes
     * @return void
     */
    protected function _setPiwikParamsByViewObject()
    {
        $oViewObject = $this->_getViewOrder();
        $oRefClass = new ReflectionClass($this);
        $sFuncName = 'setPiwikParamsFor'.ucfirst($oViewObject->getClassName());
        if($oRefClass->hasMethod($sFuncName)) {
            $this->$sFuncName($oViewObject);
        }
        if ($oViewObject->getFncName() == 'tobasket') {
            $this->setPiwikParamsForBasket($oViewObject);
        }
    }

    /**
     * returns HTML string with PIWIK javascript source and params
     * @return string
     */
    public function getMarmPiwikCode()
    {
        $sMarmPiwikCode = "<!-- Piwik Plugin by marmalade.de, sponsored by WTC Media Productions and Hebsacker Stahlwaren GmbH -->\n";
        $sMarmPiwikCode .= '<script type="text/javascript">';
        $sMarmPiwikCode .= '
                            var _paq = _paq || [];
                            (function(){
                                var u=(("https:" == document.location.protocol) ? "https://'.$this->getPiwikUrl().'" : "http://'.$this->getPiwikUrl().'");
                                ';

//            $this->_aPushParams = array();
        $this->addPushParams('setSiteId',     $this->getPiwikSiteId());
        $this->addPushParams('setTrackerUrl', "u+'piwik.php'");

        $this->_setPiwikParamsByViewObject();

        $this->addPushParams('trackPageView');
        $this->addPushParams('enableLinkTracking');

        $sMarmPiwikCode .= $this->generateParams();
        $sMarmPiwikCode .= $this->getPiwik404Code();
        $sMarmPiwikCode .= '
                                var d=document,
                                    g=d.createElement(\'script\'),
                                    s=d.getElementsByTagName(\'script\')[0];
                                    g.type=\'text/javascript\';
                                    g.defer=true;
                                    g.async=true;
                                    g.src=u+\'piwik.js\';
                                    s.parentNode.insertBefore(g,s);
                            })();';
        $sMarmPiwikCode .= '</script>';
        $sMarmPiwikCode .= '
                            <noscript>
                                <img src="'.$this->getPiwikUrl().'piwik.php?idsite='.$this->getPiwikSiteId().'&rec=1" style="border:0" alt="" />
                            </noscript>
                            <!-- End Piwik -->';
        return $sMarmPiwikCode;
    }

    /**
     * appends PIWIK javascript source before body tag
     * @param $sOutput
     * @return mixed
     */
    public function marmReplaceBody( $sOutput )
    {
        //if(!isAdmin() && User is no Backenduser!)
        if(!isAdmin())
        {
            $sPiwikCode = $this->getMarmPiwikCode();
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