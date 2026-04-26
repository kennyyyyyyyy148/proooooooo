<?php
include 'header.php';
$alert = "";

if ($enable_kyc == 1 && ($kyc_status == 'unverified' || $kyc_status == 'pending')) {


    $alert = "Swal.fire({icon:'error', title:'Sent!', text:'please Complete Your KYC', confirmButtonText:'Go to KYC'}).then(()=>{ window.location.href='kyc.php'; });";
}

// 1. Get Coin from URL (Default to BTC)
$coin_slug = isset($_GET['coin']) ? clean($_GET['coin']) : 'btc';

// 2. Coin Configuration (Updated with CDN Links)
$coins = [
    'usdt_erc20' => [
        'name' => 'Tether USDT', 'symbol' => 'USDT', 'network' => 'ERC20', 
        'db' => 'usdt_erc20_balance', 
        'img' => 'https://cryptologos.cc/logos/tether-usdt-logo.png'
    ],
    'eth' => [
        'name' => 'Ethereum', 'symbol' => 'ETH', 'network' => 'ERC20', 
        'db' => 'eth_balance', 
        'img' => 'https://cryptologos.cc/logos/ethereum-eth-logo.png'
    ],
    'btc' => [
        'name' => 'Bitcoin', 'symbol' => 'BTC', 'network' => 'BTC', 
        'db' => 'btc_balance', 
        'img' => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png'
    ],
    'bnb' => [
        'name' => 'Binance Coin', 'symbol' => 'BNB', 'network' => 'BEP20', 
        'db' => 'bnb_balance', 
        'img' => 'https://cryptologos.cc/logos/bnb-bnb-logo.png'
    ],
    'trx' => [
        'name' => 'Tron', 'symbol' => 'TRX', 'network' => 'TRC20', 
        'db' => 'trx_balance', 
        'img' => 'https://cryptologos.cc/logos/tron-trx-logo.png'
    ],
    'usdt_trc20' => [
        'name' => 'Tether USDT', 'symbol' => 'USDT', 'network' => 'TRC20', 
        'db' => 'usdt_trc20_balance', 
        'img' => 'https://cryptologos.cc/logos/tether-usdt-logo.png'
    ],
    'ltc' => [
        'name' => 'Litecoin', 'symbol' => 'LTC', 'network' => 'LTC', 
        'db' => 'ltc_balance', 
        'img' => 'https://cryptologos.cc/logos/litecoin-ltc-logo.png'
    ],
    'doge' => [
        'name' => 'Dogecoin', 'symbol' => 'DOGE', 'network' => 'DOGE', 
        'db' => 'doge_balance', 
        'img' => 'https://cryptologos.cc/logos/dogecoin-doge-logo.png'
    ],
    'sol' => [
        'name' => 'Solana', 'symbol' => 'SOL', 'network' => 'SOL', 
        'db' => 'sol_balance', 
        'img' => 'https://cryptologos.cc/logos/solana-sol-logo.png'
    ],
    'matic' => [
        'name' => 'Polygon', 'symbol' => 'MATIC', 'network' => 'MATIC', 
        'db' => 'matic_balance', 
        'img' => 'https://cryptologos.cc/logos/polygon-matic-logo.png'
    ],
];

if (!array_key_exists($coin_slug, $coins)) { echo "<script>window.location.href='assets.php';</script>"; exit(); }
$current = $coins[$coin_slug];

$col = $current['db'];
$q = mysqli_query($link, "SELECT $col FROM users WHERE id='$user_id'");
$r = mysqli_fetch_assoc($q);
$usd_balance = floatval($r[$col]);



// 4. Handle Send Logic
if(isset($_POST['send_crypto'])){
    $address = clean($_POST['address']);
    $amount_usd = floatval($_POST['amount_usd']);
    $amount_crypto = floatval($_POST['amount_crypto']); // Calculated by JS

    if($amount_usd > 0 && $amount_usd <= $usd_balance){
        // Deduct Balance
        $new_balance = $usd_balance - $amount_usd;
        mysqli_query($link, "UPDATE users SET $col = '$new_balance' WHERE id='$user_id'");

        // Record Transaction
        $sql = "INSERT INTO transactions (user_id, coin_symbol, network, type, amount_usd, amount_crypto, status, tx_hash) 
                VALUES ('$user_id', '{$current['symbol']}', '{$current['network']}', 'withdrawal', '$amount_usd', '$amount_crypto', 'pending', 'Wait for Hash')";
        
        if(mysqli_query($link, $sql)){
            
           $subject = "Withdrawal Request Received";

                $body = '
                <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
                    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        
                        <div style="padding: 40px 0 20px; text-align: center;">
                            <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                                📤
                            </div>
                            <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Withdrawal Requested</h1>
                        </div>
                
                        <div style="padding: 20px 40px 40px;">
                            <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                                Hello <strong>' . $fullname . '</strong>, we received your request to withdraw funds. Here are the details:
                            </p>
                
                            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Amount</td>
                                        <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">
                                            ' . $amount_crypto . ' ' . $current['symbol'] . ' <span style="color: #9ca3af; font-weight: normal;">($' . $amount_usd . ')</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">To Address</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-size: 12px; font-family: monospace; word-break: break-all;">
                                            ' . $address . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                        <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                             <span style="background-color: #fffbeb; color: #d97706; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #fcd34d;">Pending Review</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                
                            <div style="margin-top: 30px; text-align: center;">
                                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                    We will process your request shortly.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
                
                sendMail($email, $subject, $body);

            $alert = "Swal.fire({icon:'success', title:'Sent!', text:'Your withdrawal is processing.', confirmButtonText:'Go to Dashboard'}).then(()=>{ window.location.href='dashboard.php'; });";
        }
    } else {
        $alert = "Swal.fire({icon:'error', title:'Insufficient Funds', text:'You have $$usd_balance available.'});";
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="w-full max-w-xl mx-auto">
        <a href="coin-details.php?coin=<?php echo $coin_slug; ?>" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white mb-4 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>

        <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5">
            
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Send <?php echo $current['symbol']; ?></h1>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 border border-indigo-500/20">
                    <i class="fa-solid fa-bolt"></i> <?php echo $current['network']; ?>
                </span>
            </div>

            <form method="POST">
                <input type="hidden" name="amount_crypto" id="hiddenCryptoAmount">
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Asset</label>
                    <div class="flex items-center justify-between w-full bg-slate-50 dark:bg-[#02040a] border border-slate-200 dark:border-white/10 rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center p-1 border border-slate-100">
                                <img src="<?php echo $current['img']; ?>" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white"><?php echo $current['name']; ?></p>
                                <p class="text-xs text-slate-500">Balance: $<?php echo number_format($usd_balance, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Recipient Address</label>
                    <div class="relative">
                        <input type="text" name="address" required placeholder="Paste address..." class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-2xl py-4 pl-4 pr-12 focus:outline-none focus:border-indigo-500 transition-all font-mono text-sm">
                        <button type="button" onclick="pasteAddress()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">PASTE</button>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Amount (USD)</label>
                        <button type="button" onclick="setMax()" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Max: $<?php echo $usd_balance; ?></button>
                    </div>
                    <div class="relative">
                        <input type="number" step="any" name="amount_usd" id="amountUsd" required placeholder="0.00" oninput="calculateCrypto()" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white text-2xl font-bold border border-slate-200 dark:border-white/10 rounded-2xl py-4 pl-4 pr-20 focus:outline-none focus:border-indigo-500">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 flex items-center gap-2">
                            <span class="font-bold text-slate-500">USD</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">≈ <span id="cryptoPreview">0.000000</span> <?php echo $current['symbol']; ?></p>
                </div>

                <button type="submit" name="send_crypto" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95">
                    Continue
                </button>
            </form>

        </div>
    </div>
</div>

<script>
    const symbol = "<?php echo $current['symbol']; ?>";
    let livePrice = 0;

    // 1. Fetch Live Price on Load
    fetch(`https://min-api.cryptocompare.com/data/price?fsym=${symbol}&tsyms=USD`)
    .then(res => res.json())
    .then(data => {
        livePrice = data.USD;
    });

    // 2. Calculate Crypto Amount based on USD Input
    function calculateCrypto() {
        const usdAmount = parseFloat(document.getElementById('amountUsd').value);
        if(usdAmount > 0 && livePrice > 0) {
            const cryptoAmount = (usdAmount / livePrice).toFixed(8);
            document.getElementById('cryptoPreview').innerText = cryptoAmount;
            document.getElementById('hiddenCryptoAmount').value = cryptoAmount;
        } else {
            document.getElementById('cryptoPreview').innerText = "0.000000";
            document.getElementById('hiddenCryptoAmount').value = 0;
        }
    }

    // 3. Set Max Balance
    function setMax() {
        document.getElementById('amountUsd').value = <?php echo $usd_balance; ?>;
        calculateCrypto();
    }

    // 4. Paste Function
    async function pasteAddress() {
        try {
            const text = await navigator.clipboard.readText();
            document.querySelector('input[name="address"]').value = text;
        } catch (err) {
            console.error('Failed to read clipboard', err);
        }
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>