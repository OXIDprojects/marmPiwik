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
 
class marm_piwik {

    const VERSION = '0.6';

    const CONFIG_ENTRY_NAME = 'marm_piwik_config';

    protected $_aConfig = array(
        'piwik_site_id' => array(
            'value'=> '99999',
            'input_type' => 'text'
        ),
        'piwik_url' => array(
            'value'=> 'http://yourshopurl.com/piwik/',
            'input_type' => 'text'
        ),
        'tracking_method' => array(
            'value' => 'javascript',
            'input_type' => 'select',
            'options' => array( 'javascript', 'php' )
        ),
        'newsletter_goal_id' => array(
            'value'=> '',
            'input_type' =>'text'
        ),
        'newsletter_var_name' => array(
            'value'=> 'Newsletter',
            'input_type' =>'text'
        ),
        'newsletter_var_subscribed' => array(
            'value'=> 'bestellt',
            'input_type' =>'text'
        ),
        'newsletter_var_activated' => array(
            'value'=> 'aktiviert',
            'input_type' =>'text'
        ),
        'newsletter_var_unsubscribed' => array(
            'value'=> 'abbestellt',
            'input_type' =>'text'
        ),
        'newsletter_var_form_showed' => array(
            'value'=> 'Bestellseite angeschaut',
            'input_type' =>'text'
        ),
        'searched_for_var_name' => array(
            'value'=> 'Suchparameter',
            'input_type' =>'text'
        ),
        'no_search_results_var_name' => array(
            'value'=> 'fehlgeschlagene Suche nach',
            'input_type' =>'text'
        )
    );
    protected $_aPushParams = array();

    public function __construct()
    {
        $this->_loadConfig();
    }

    /**
     * returns current version of marm_piwik class
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    protected function _loadConfig()
    {
        $aSavedConfig = oxConfig::getInstance()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        if ($aSavedConfig && count($aSavedConfig) == count($this->_aConfig)) {
            $this->_aConfig = $aSavedConfig;
        }
        else {
            $this->_saveConfig();
        }

    }

    protected function _saveConfig()
    {
        oxConfig::getInstance()->saveShopConfVar( 'arr', self::CONFIG_ENTRY_NAME, $this->_aConfig );
    }

    public function getConfig()
    {
        return $this->_aConfig;
    }

    public function changeConfig($aNewValues)
    {
        $blChanged = false;
        foreach ($aNewValues as $sKey => $sNewValue) {
            if (isset($this->_aConfig[$sKey])) {
                $blChanged = true;
                $this->_aConfig[$sKey]['value'] = $sNewValue;
            }
        }
        if ($blChanged) {
            $this->_saveConfig();
        }
        return $blChanged;
    }

    public function getConfigValue($sValue) {
        if (isset($this->_aConfig[$sValue]) && isset($this->_aConfig[$sValue]['value'])) {
            return $this->_aConfig[$sValue]['value'];
        }
    }

    /**
     * returns config parameter for siteId
     * @return int
     */
    public function getPiwikSiteId()
    {
        return (int) $this->getConfigValue('piwik_site_id');
    }

    /**
     * returns config parameter for PIWIK URL
     * @return string
     */
    public function getPiwikUrl($blFull = true)
    {
        $sUrl = $this->getConfigValue('piwik_url');
        if (!$blFull) {
            $sUrl = str_replace(
                array (
                    'http://',
                    'https://'
                ),
                '',
                $sUrl
            );
            $sUrl = str_replace(
                array (
                    '/proxy.php',
                    '/proxy-piwik.php',
                    '/piwik.php'
                ),
                '/',
                $sUrl
            );
         }
        return $sUrl;
    }

    /**
     * returns config parameter for Newsletter Goal ID
     * @return string
     */
    public function getPiwikNewsletterGoalId()
    {
        return $this->getConfigValue('newsletter_goal_id');
    }

    /**
     * returns current/active page controller (aka view)
     * @return oxUBase
     */
    protected function _getViewOrder()
    {
        return oxConfig::getInstance()->getActiveView();
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
					$pattern=array();
					$pattern[0]='#(?<!\\\\)"#';
					$pattern[1]='#&quot;#';
					$replacement=array();
					$replacement[0]='\"';
					$replacement[1]='\"';
                    $aFormed[] = '"'.preg_replace($pattern, $replacement, $mPushParam).'"';
				
                }
                elseif(is_bool($mPushParam)) {
                    $aFormed[] = $mPushParam?'true':'false';
                }
                elseif(is_double($mPushParam) || is_float($mPushParam)) {
                    $aFormed[] = sprintf("%.2f", $mPushParam);
                }
                elseif(is_array($mPushParam) && isset($mPushParam['type']) && $mPushParam['type'] == 'raw') {
                    $aFormed[] = $mPushParam['value'];
                }
                else {
                    $aFormed[] = $mPushParam;
                }
            }
            $sReturn .="\n_paq.push([".implode(', ', $aFormed)."]);";
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
        if($oViewObject->getPageNavigation()->NrOfPages > 0) {
            $this->addPushParams(
                'setCustomVariable',
                1,
                $this->_aConfig['searched_for_var_name']['value'],
                $oViewObject->getSearchParamForHtml(),
                'page'
            );
        }
        else{
            $this->addPushParams(
                'setCustomVariable',
                2,
                $this->_aConfig['no_search_results_var_name']['value'],
                $oViewObject->getSearchParamForHtml(),
                'page'
            );
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
                $this->addPushParams(
                    'setCustomVariable',
                    3,
                    $this->_aConfig['newsletter_var_name']['value'],
                    $this->_aConfig['newsletter_var_subscribed']['value'],
                    'page'
                );
                break;
            case 2:
                $this->addPushParams(
                    'setCustomVariable',
                    3,
                    $this->_aConfig['newsletter_var_name']['value'],
                    $this->_aConfig['newsletter_var_activated']['value'],
                    'page'
                );
                $iNewsLetterGoalId = $this->getPiwikNewsletterGoalId();
                if ( $iNewsLetterGoalId ) {
                    $this->addPushParams('trackGoal',$iNewsLetterGoalId);
                }
                break;
            case 3:
                $this->addPushParams(
                    'setCustomVariable',
                    3,
                    $this->_aConfig['newsletter_var_name']['value'],
                    $this->_aConfig['newsletter_var_unsubscribed']['value'],
                    'page'
                );

                break;
            case 4:
            default:
                $this->addPushParams(
                    'setCustomVariable',
                    3,
                    $this->_aConfig['newsletter_var_name']['value'],
                    $this->_aConfig['newsletter_var_form_showed']['value'],
                    'page'
                );
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
        $oBasket = oxSession::getInstance()->getBasket();
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
                number_format($oBasketItem->getUnitPrice()->getBruttoPrice(),2),
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

    /**
     * runs function setPiwikParamsFor.. by active controller (_getViewOrder())
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
var u=(("https:" == document.location.protocol) ? "https://'.$this->getPiwikUrl(false).'" : "http://'.$this->getPiwikUrl(false).'");
';

//            $this->_aPushParams = array();
        $this->addPushParams('setSiteId',     $this->getPiwikSiteId());
        $this->addPushParams('setTrackerUrl', array('type'=>'raw', 'value' => "u+'piwik.php'"));

        $this->_setPiwikParamsByViewObject();

        $this->addPushParams('trackPageView');
        $this->addPushParams('enableLinkTracking');

        $sMarmPiwikCode .= $this->generateParams();
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
        $sMarmPiwikCode .= '<noscript><img src="'.$this->getPiwikUrl().'?idsite='.$this->getPiwikSiteId().'&amp;rec=1" style="border:0" alt="" /></noscript>
<!-- End Piwik -->';
        return $sMarmPiwikCode;
    }
}
