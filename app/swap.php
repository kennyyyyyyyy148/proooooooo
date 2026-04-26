<?php
include 'header.php';

// 1. Get Coins from URL
$from_slug = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : 'btc';
$to_slug   = isset($_GET['to'])   ? htmlspecialchars($_GET['to'])   : 'eth';

// =========================================================
// 2. [FIX] PREVENT SAME COIN SWAP
// =========================================================
if ($from_slug === $to_slug) {
    // If coins are the same, switch 'To' coin to something else
    $fallback = ($from_slug == 'eth') ? 'btc' : 'eth';
    echo "<script>
        alert('You cannot swap the same asset!');
        window.location.href = 'swap.php?from=$from_slug&to=$fallback';
    </script>";
    exit();
}

// 3. Local Coin Configuration
$coins = [
    'usdt_erc20' => ['name'=>'Tether USDT','symbol'=>'USDT','db'=>'usdt_erc20_balance','img'=>'https://cryptologos.cc/logos/tether-usdt-logo.png'],
    'eth'        => ['name'=>'Ethereum',   'symbol'=>'ETH', 'db'=>'eth_balance',       'img'=>'https://cryptologos.cc/logos/ethereum-eth-logo.png'],
    'btc'        => ['name'=>'Bitcoin',    'symbol'=>'BTC', 'db'=>'btc_balance',       'img'=>'https://cryptologos.cc/logos/bitcoin-btc-logo.png'],
    'bnb'        => ['name'=>'Binance Coin','symbol'=>'BNB', 'db'=>'bnb_balance',       'img'=>'https://cryptologos.cc/logos/bnb-bnb-logo.png'],
    'trx'        => ['name'=>'Tron',       'symbol'=>'TRX', 'db'=>'trx_balance',       'img'=>'https://cryptologos.cc/logos/tron-trx-logo.png'],
    'usdt_trc20' => ['name'=>'Tether USDT','symbol'=>'USDT','db'=>'usdt_trc20_balance','img'=>'https://cryptologos.cc/logos/tether-usdt-logo.png'],
    'ltc'        => ['name'=>'Litecoin',   'symbol'=>'LTC', 'db'=>'ltc_balance',       'img'=>'https://cryptologos.cc/logos/litecoin-ltc-logo.png'],
    'doge'       => ['name'=>'Dogecoin',   'symbol'=>'DOGE','db'=>'doge_balance',      'img'=>'https://cryptologos.cc/logos/dogecoin-doge-logo.png'],
    'sol'        => ['name'=>'Solana',     'symbol'=>'SOL', 'db'=>'sol_balance',       'img'=>'https://cryptologos.cc/logos/solana-sol-logo.png'],
    'matic'      => ['name'=>'Polygon',    'symbol'=>'MATIC','db'=>'matic_balance',     'img'=>'https://cryptologos.cc/logos/polygon-matic-logo.png'],
];

if (!array_key_exists($from_slug, $coins) || !array_key_exists($to_slug, $coins)) { echo "<script>window.location.href='swap.php';</script>"; exit(); }

$from = $coins[$from_slug];
$to   = $coins[$to_slug];

// 4. Get User Balances
$user_id = $_SESSION['user_id'];
$col_from = $from['db'];
$col_to   = $to['db'];

$q = mysqli_query($link, "SELECT $col_from, $col_to FROM users WHERE id='$user_id'");
$r = mysqli_fetch_assoc($q);
$bal_from_usd = floatval($r[$col_from]);
$bal_to_usd   = floatval($r[$col_to]);

$alert = "";

// 5. Handle Swap
if(isset($_POST['confirm_swap'])){
    $swap_amount_usd = floatval($_POST['amount_usd']);
    // Capture the calculated crypto amount from hidden input
    $swap_amount_crypto = floatval($_POST['hidden_crypto_val']);
    
    if($swap_amount_usd > 0 && $swap_amount_usd <= $bal_from_usd){
        
        // A. Update Balances
        $new_from = $bal_from_usd - $swap_amount_usd;
        $new_to   = $bal_to_usd + $swap_amount_usd;

        $update = mysqli_query($link, "UPDATE users SET $col_from = '$new_from', $col_to = '$new_to' WHERE id='$user_id'");

        if($update){
            // B. Record Transaction
            $tx_symbol = "{$from['symbol']}->{$to['symbol']}";
            
            // Insert with calculated crypto amount
            $sql = "INSERT INTO transactions (user_id, coin_symbol, type, amount_usd, amount_crypto, status, tx_hash) 
                    VALUES ('$user_id', '$tx_symbol', 'swap', '$swap_amount_usd', '$swap_amount_crypto', 'completed', 'SWAP-" . time() . "')";
            mysqli_query($link, $sql);

           $subject = "Swap Successful";

            $body = '
            <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    
                    <div style="padding: 40px 0 20px; text-align: center;">
                        <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                            🔁
                        </div>
                        <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Swap Complete</h1>
                    </div>
            
                    <div style="padding: 20px 40px 40px;">
                        <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                            Your exchange was successful. The funds have been added to your balance.
                        </p>
            
                        <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Amount Swapped</td>
                                    <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $swap_amount_usd . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">From</td>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $from['symbol'] . '</td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">To</td>
                                    <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $to['symbol'] . '</td>
                                </tr>
                            </table>
                        </div>
            
                        <div style="margin-top: 30px; text-align: center;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                Transaction ID: #' . uniqid() . '
                            </p>
                        </div>
                    </div>
                </div>
            </div>';
            
            sendMail($email, $subject, $body);

            $alert = "Swal.fire({icon:'success', title:'Swap Successful', text:'Swapped $$swap_amount_usd.', confirmButtonText:'Done'}).then(()=>{ window.location.href='assets.php'; });";
        }
    } else {
        $alert = "Swal.fire({icon:'error', title:'Insufficient Balance', text:'You have $$bal_from_usd available.'});";
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6 flex flex-col items-center">
    <div class="w-full max-w-xl">
        <form method="POST">
            <input type="hidden" name="hidden_crypto_val" id="hiddenCryptoVal" value="0">

            <div class="glass-panel rounded-3xl p-1 border border-slate-200 dark:border-white/5 shadow-2xl relative">
                
                <div class="p-5 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Swap Assets</h1>
                    <a href="assets.php" class="text-slate-400 hover:text-indigo-500"><i class="fa-solid fa-xmark text-lg"></i></a>
                </div>

                <div class="p-4 space-y-1">
                    
                    <div class="bg-slate-50 dark:bg-[#0B0F19] rounded-2xl p-4 border border-slate-200 dark:border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase">You Pay (USD)</span>
                            <span class="text-xs text-slate-500">Bal: <span class="text-indigo-600 font-bold">$<?php echo number_format($bal_from_usd, 2); ?></span></span>
                        </div>
                        <div class="flex justify-between items-center gap-4">
                            <input type="number" step="any" name="amount_usd" id="swapAmount" placeholder="0.00" required
                                   class="w-full bg-transparent text-4xl font-bold text-slate-900 dark:text-white focus:outline-none">
                            
                            <div class="relative">
                                <select onchange="if(this.value == '<?php echo $to_slug; ?>'){ alert('Cannot swap same coin!'); this.value='<?php echo $from_slug; ?>'; } else { window.location.href='?from='+this.value+'&to=<?php echo $to_slug; ?>'; }" class="absolute inset-0 w-full opacity-0 cursor-pointer">
                                    <?php foreach($coins as $slug => $coin): ?>
                                        <option value="<?php echo $slug; ?>" <?php echo ($slug == $from_slug) ? 'selected' : ''; ?>><?php echo $coin['symbol']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="flex items-center gap-2 bg-white dark:bg-[#1E293B] px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 min-w-[110px] justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="<?php echo $from['img']; ?>" class="w-6 h-6 rounded-full">
                                        <span class="font-bold text-slate-900 dark:text-white"><?php echo $from['symbol']; ?></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-sm text-slate-400">≈ <span id="cryptoFromPreview">0.000000</span> <?php echo $from['symbol']; ?></span>
                            <button type="button" onclick="setMax()" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded">MAX</button>
                        </div>
                    </div>

                    <div class="relative h-2 z-10">
                        <a href="?from=<?php echo $to_slug; ?>&to=<?php echo $from_slug; ?>" class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-10 h-10 bg-white dark:bg-[#1E293B] border-4 border-white dark:border-[#121826] rounded-xl flex items-center justify-center text-indigo-600 dark:text-white shadow-lg">
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>

                    <div class="bg-slate-50 dark:bg-[#0B0F19] rounded-2xl p-4 border border-slate-200 dark:border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase">You Receive (USD)</span>
                            <span class="text-xs text-slate-500">Bal: <span class="text-indigo-600 font-bold">$<?php echo number_format($bal_to_usd, 2); ?></span></span>
                        </div>
                        <div class="flex justify-between items-center gap-4">
                            <input type="text" id="receiveAmount" placeholder="0.00" readonly
                                   class="w-full bg-transparent text-4xl font-bold text-slate-900 dark:text-white focus:outline-none">
                            
                            <div class="relative">
                                <select onchange="if(this.value == '<?php echo $from_slug; ?>'){ alert('Cannot swap same coin!'); this.value='<?php echo $to_slug; ?>'; } else { window.location.href='?from=<?php echo $from_slug; ?>&to='+this.value; }" class="absolute inset-0 w-full opacity-0 cursor-pointer">
                                    <?php foreach($coins as $slug => $coin): ?>
                                        <option value="<?php echo $slug; ?>" <?php echo ($slug == $to_slug) ? 'selected' : ''; ?>><?php echo $coin['symbol']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="flex items-center gap-2 bg-white dark:bg-[#1E293B] px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 min-w-[110px] justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="<?php echo $to['img']; ?>" class="w-6 h-6 rounded-full">
                                        <span class="font-bold text-slate-900 dark:text-white"><?php echo $to['symbol']; ?></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-sm text-slate-400">≈ <span id="cryptoToPreview">0.000000</span> <?php echo $to['symbol']; ?></span>
                        </div>
                    </div>

                </div>
                
                <!--<div class="p-5 pt-2">-->
                <!--    <div class="bg-indigo-50/50 dark:bg-indigo-500/5 rounded-xl p-3 space-y-2 border border-indigo-100 dark:border-indigo-500/10">-->
                <!--        <div class="flex justify-between items-center text-xs">-->
                <!--            <span class="text-slate-500 dark:text-slate-400">Exchange Rate</span>-->
                <!--            <span class="font-bold text-slate-700 dark:text-slate-200">1 <?php echo $from['symbol']; ?> = <span id="exchangeRate">...</span> <?php echo $to['symbol']; ?></span>-->
                <!--        </div>-->
                <!--        <div class="flex justify-between items-center text-xs">-->
                <!--            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">Network Fee <i class="fa-solid fa-circle-info text-[10px]"></i></span>-->
                <!--            <span class="font-bold text-slate-700 dark:text-slate-200">0.05%</span>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="p-5 pt-0 pb-6">
                    <button type="submit" name="confirm_swap" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold py-4 rounded-xl shadow-lg transform active:scale-95">
                        Confirm Swap
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    const symbolFrom = "<?php echo $from['symbol']; ?>";
    const symbolTo   = "<?php echo $to['symbol']; ?>";
    const maxBal     = <?php echo $bal_from_usd; ?>;
    
    let priceFrom = 0;
    let priceTo   = 0;

    // Fetch Prices
    fetch(`https://min-api.cryptocompare.com/data/pricemulti?fsyms=${symbolFrom},${symbolTo}&tsyms=USD`)
    .then(res => res.json())
    .then(data => {
        if(data[symbolFrom]) priceFrom = data[symbolFrom].USD;
        if(data[symbolTo])   priceTo   = data[symbolTo].USD;
    });

    const input = document.getElementById('swapAmount');
    input.addEventListener('input', function() {
        const usdVal = parseFloat(this.value);
        document.getElementById('receiveAmount').value = usdVal ? usdVal : ''; 

        if(usdVal > 0 && priceFrom > 0 && priceTo > 0) {
            const cryptoVal = (usdVal / priceFrom).toFixed(6);
            document.getElementById('cryptoFromPreview').innerText = cryptoVal;
            
            // Pass crypto value to hidden input
            document.getElementById('hiddenCryptoVal').value = cryptoVal;

            document.getElementById('cryptoToPreview').innerText = (usdVal / priceTo).toFixed(6);
        } else {
            document.getElementById('cryptoFromPreview').innerText = "0.000000";
            document.getElementById('hiddenCryptoVal').value = "0";
            document.getElementById('cryptoToPreview').innerText = "0.000000";
        }
    });

    function setMax() {
        input.value = maxBal;
        input.dispatchEvent(new Event('input'));
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>