<?php
// eWallet - PHP Script
// Author: DeluxeScript
if(!defined('PWV1_INSTALLED')){
    header("HTTP/1.0 404 Not Found");
	exit;
}
?>

<?php include("menu_notlogged.php");?>
<style type="text/css">
header{
    background: #<?=$bck_color?>;
}	
</style>
<section class="bottomSlid">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <br><br>
                    <div class="card">
                        <div class="card-body">
                            <h3><?php echo filter_var($lang['mp_text_1']); ?></h3>
                            <p><?php echo filter_var($lang['mp_text_2']); ?></p>    
                            <hr/>
                            <code class="code-view">
						    &lt;form action="<?php echo filter_var($settings['url']); ?>payment" method="POST"><br/>
                                &lt;input type="hidden" name="merchant_account" value="merchant@xyz.com"><br/>
                                &lt;input type="hidden" name="item_number" value="2"><br/>
                                &lt;input type="hidden" name="item_name" value="Chocolates"><br/>
                                &lt;input type="hidden" name="item_price" value="15"><br/>
                                &lt;input type="hidden" name="item_currency" value="USD"><br/>
                                &lt;input type="hidden" name="return_success" value="http://domain.com/success.php"><br/>
                                &lt;input type="hidden" name="return_fail" value="http://domain.com/fail.php"><br/>
                                &lt;input type="hidden" name="return_cancel" value="http://domain.com/cancel.php"><br/>
                                &lt;button type="submit">Pay via <?php echo filter_var($settings['name']); ?>&lt;/button><br/>
                            &lt;/form>
                            </code>
                            <br><br>
                            <h3>HTML Form For Receive Donations</h3>
                            <p>Config form to get paid from your donaters.</p>    
                            <hr/>
                            <code class="code-view">
						    &lt;form action="<?php echo filter_var($settings['url']); ?>payment" method="POST"><br/>
                                &lt;input type="hidden" name="merchant_account" value="merchant@xyz.com"><br/>
                                &lt;input type="hidden" name="item_number" value="2"><br/>
                                &lt;input type="hidden" name="item_name" value="Donation For Children"><br/>
                                &lt;input type="hidden" name="item_price" value="15"><br/>
                                &lt;input type="hidden" name="item_currency" value="USD"><br/>
                                &lt;input type="hidden" name="return_success" value="http://domain.com/success.php"><br/>
                                &lt;input type="hidden" name="return_fail" value="http://domain.com/fail.php"><br/>
                                &lt;input type="hidden" name="return_cancel" value="http://domain.com/cancel.php"><br/>
                                &lt;button type="submit">Donate via <?php echo filter_var($settings['name']); ?>&lt;/button><br/>
                            &lt;/form>
                            </code>
                            <br><br>
                            <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%"><?php echo filter_var($lang['mp_text_3']); ?></th>
                                    <th width="25%"><?php echo filter_var($lang['mp_text_4']); ?></th>
                                    <th width="65%"><?php echo filter_var($lang['mp_text_5']); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>merchant_account</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: merchant@xyz.com</td>
                                    <td><?php echo filter_var($lang['mp_text_7']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>item_number</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: 2</td>
                                    <td><?php echo filter_var($lang['mp_text_8']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>item_name</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: Chocolates</td>
                                    <td><?php echo filter_var($lang['mp_text_9']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>item_price</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: 15</td>
                                    <td><?php echo filter_var($lang['mp_text_10']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>item_currency</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: USD/EUR/RUB</td>
                                    <td><?php echo filter_var($lang['mp_text_11']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>return_success</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: http://domain.com/success.php</td>
                                    <td><?php echo filter_var($lang['mp_text_12']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>return_fail</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: http://domain.com/fail.php</td>
                                    <td><?php echo filter_var($lang['mp_text_13']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>return_cancel</b></td>
                                    <td><?php echo filter_var($lang['mp_text_6']); ?>: http://domain.com/cancel.php</td>
                                    <td><?php echo filter_var($lang['mp_text_14']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <br><br>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3><?php echo filter_var($lang['mp_text_15']); ?></h3>
                            <p><?php echo filter_var($lang['mp_text_16']); ?></p>
                            <hr/>
                            <code class="code-view">
                            &lt;?php<br/>
                            $merchant_key = '...'; // Enter here your merchant API Key<br/>
                            <br/>
                            $merchant_account = $_POST['merchant_account'];<br/>
                            $item_number = $_POST['item_number'];<br/>
                            $item_name = $_POST['item_name'];<br/>
                            $item_price = $_POST['item_price'];<br/>
                            $item_currency = $_POST['item_currency'];<br/>
                            $txid = $_POST['txid']; // Transaction ID<br/>
                            $payment_time = $_POST['payment_time']; // Current time of payment<br/>
                            $payee_account = $_POST['payee_account']; // The account of payee<br/>
                            $verification_link = "<?php echo filter_var($settings['url']); ?>payment_status.php?merchant_key=$merchant_key&merchant_account=$merchant_account&txid=$txid";<br/>
                            $ch = curl_init();<br/>
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);<br/>
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br/>
                            curl_setopt($ch, CURLOPT_URL,$verification_link);<br/>
                            $results=curl_exec($ch);<br/>
                            curl_close($ch);<br/>
                            $results = json_decode($results);<br/>
                            if($results->status == "success") {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Payment is successful<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Run your php code here<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo 'Payment is successful.';<br/>
                            } else {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo 'Payment was failed.';<br/>
                            }<br/>
                            ?>
                        </code>
                        </div>
                    </div>
                    <br><br>
                </div>
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Get Wallet Balances</h3>
                            <p>Run this codes to get wallet balances via API.</p>
                            <hr/>
                            <code class="code-view">
                            &lt;?php<br/>
                            $merchant_key = '...'; // Enter here your merchant API Key<br/>
                            <br/>
                            $merchant_account = "..."; // Enter Account Email address<br/>
                            
                            $verification_link = "<?php echo filter_var($settings['url']); ?>requests/GetWalletCurrency.php?merchant_key=$merchant_key&merchant_account=$merchant_account";<br/>
                            $ch = curl_init();<br/>
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);<br/>
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br/>
                            curl_setopt($ch, CURLOPT_URL,$verification_link);<br/>
                            $results=curl_exec($ch);<br/>
                            curl_close($ch);<br/>
                            $results = json_decode($results);<br/>
                            if($results->status == "success") {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Fetching is successful<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Run your php code here<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Given Below code showing all currencies balance<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;foreach ($results as $item) {<br/>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo "$item &lt;br/>";<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;}<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Given Below code showing one currency balance<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo $results->USD; //Write currency code to see specific currency balance<br/>
                            } else {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo $results->status;<br/>
                            }<br/>
                            ?>
                        </code>
                        </div>
                    </div>
                    <br><br>
                </div>
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Get Current Currency Rates</h3>
                            <p>Run this codes to get current currency rates via API.</p>
                            <hr/>
                            <code class="code-view">
                            &lt;?php<br/>
                            $from_currency = '...'; // Enter here From Currency Ex: USD<br/>
                            $to_currency = "..."; // Enter here To Currency Ex: EUR<br/>
                            
                            $verification_link = "<?php echo filter_var($settings['url']); ?>requests/Convert.php?amount=1&from=$from_currency&to=$to_currency";<br/>
                            $ch = curl_init();<br/>
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);<br/>
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br/>
                            curl_setopt($ch, CURLOPT_URL,$verification_link);<br/>
                            $results=curl_exec($ch);<br/>
                            curl_close($ch);<br/>
                            $results = json_decode($results);<br/>
                            if($results->status == "success") {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Fetching is successful<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Run your php code here<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Given Below code showing a currency rates<br/>
                            
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "Status : $results->status &lt;br>";<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "Rate From : $results->rate_from &lt;br>";<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "Rate To : $results->rate_to &lt;br>";<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "Currency From : $results->currency_from &lt;br>";<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "Currency To : $results->currency_to &lt;br>";<br/>
                            } else {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo $results->status;<br/>
                            }<br/>
                            ?>
                        </code>
                        </div>
                    </div>
                    <br><br>
                </div>
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Currency Conversion</h3>
                            <p>Convert Currency with our rates.</p>
                            <hr/>
                            <code class="code-view">
                            &lt;?php<br/>
                            $amount = '...'; // Enter Amount here Ex: 100<br/>
                            $from_currency = '...'; // Enter here From Currency Ex: USD<br/>
                            $to_currency = "..."; // Enter here To Currency Ex: EUR<br/>
                            $prefix = $from_currency.'_'.$to_currency;<br/>
                            
                            $verification_link = "<?php echo filter_var($settings['url']); ?>requests/CurrencyConverter.php?amount=$amount&from=$from_currency&to=$to_currency";<br/>
                            $ch = curl_init();<br/>
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);<br/>
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br/>
                            curl_setopt($ch, CURLOPT_URL,$verification_link);<br/>
                            $results=curl_exec($ch);<br/>
                            curl_close($ch);<br/>
                            $results = json_decode($results);<br/>
                            if($results->status == "success") {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Fetching is successful<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;//Run your php code here<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo "$prefix : $results->convert &lt;br>";<br/>
                            } else {<br/>
                            &nbsp;&nbsp;&nbsp;&nbsp;echo $results->status;<br/>
                            }<br/>
                            ?>
                        </code>
                        </div>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </section>

    <?php include("footer.php"); ?>