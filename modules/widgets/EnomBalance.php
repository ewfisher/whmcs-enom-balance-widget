<?php

/**
 * Name: WHMCS eNom Balance Widget
 * Description: This widget provides you with your eNom balance on your WHMCS admin dashboard.
 * Version 1.0
 * Created by Host Media Ltd
 * Website: https://www.hostmedia.co.uk/
 */

add_hook('AdminHomeWidgets', 1, function() {
    return new eNomBalanceWidget();
});

class eNomBalanceWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'eNom Balance';
    protected $description = 'Widget provides you with your eNom balance on your admin dashboard. Created by Host Media.';
    protected $weight = 150;
    protected $columns = 1;
    protected $cache = false;
    protected $cacheExpiry = 120;
    protected $requiredPermission = '';

    public function getData()
    {
        // Config
        $enomusername = 'YOUR-ENOM-USERNAME-HERE';
        $enompassword = 'YOUR-ENOM-PASSWORD-HERE';
        
        // URL
        // Live
        $enomapiurl = 'https://reseller.enom.com/';
        
        // Test
        // $enomapiurl = 'https://resellertest.enom.com/';
        
        // Curl
        $enomapiurl .= 'interface.asp?command=GetBalance&uid='.$enomusername.'&pw='.$enompassword.'&responsetype=xml';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $enomapiurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        
        $dataArray = array(
            enom  => json_decode($json)
        );
        
        return $dataArray;
    }

    public function generateOutput($data)
    {

        if ($data['enom']->ErrCount > 0) {
            
return <<<EOF
    <div class="widget-content-padded">
        <strong>There was an error:</strong><br/>
        {$data['enom']->errors->Err1}
    </div>
EOF;
        }
        
        return <<<EOF
    <div class="widget-content-padded">
        <div class="row text-center">
            <div class="col-sm-6">
                <h4><strong>&#36;{$data['enom']->Balance}</strong></h4>
                Balance
            </div>
            <div class="col-sm-6">
                <h4><strong>&#36;{$data['enom']->AvailableBalance}</strong></h4>
                Available Balance
            </div>
        </div>
        <div class="row text-center" style="margin-top: 20px;">
            <a href="https://www.enom.com/myaccount/RefillAccount.aspx" class="btn btn-default btn-sm" target="_blank"><i class="fas fa-credit-card fa-fw"></i> Refill Account</a>
        </div>
    </div>
EOF;
    }
}