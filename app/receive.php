<?php
include 'header.php';

// 1. Get Coin from URL (Default to BTC)
$coin_slug = isset($_GET['coin']) ? htmlspecialchars($_GET['coin']) : 'btc';

// 2. Local Coin Configuration
$coins = [
    'usdt_erc20' => [
        'name' => 'Tether USDT', 'symbol' => 'USDT', 'network' => 'ERC20', 
        'db' => 'usdt_erc20_balance', 
        'address' => $USDT_ERC20,
        'img' => 'https://cryptologos.cc/logos/tether-usdt-logo.png'
    ],
    'eth' => [
        'name' => 'Ethereum', 'symbol' => 'ETH', 'network' => 'ERC20', 
        'db' => 'eth_balance', 
        'address' => $ETH,
        'img' => 'https://cryptologos.cc/logos/ethereum-eth-logo.png'
    ],
    'btc' => [
        'name' => 'Bitcoin', 'symbol' => 'BTC', 'network' => 'BTC', 
        'db' => 'btc_balance', 
        'address' => $BTC,
        'img' => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png'
    ],
    'bnb' => [
        'name' => 'Binance Coin', 'symbol' => 'BNB', 'network' => 'BEP20', 
        'db' => 'bnb_balance', 
        'address' => $BNB,
        'img' => 'https://cryptologos.cc/logos/bnb-bnb-logo.png'
    ],
    'trx' => [
        'name' => 'Tron', 'symbol' => 'TRX', 'network' => 'TRC20', 
        'db' => 'trx_balance', 
        'address' => $TRX,
        'img' => 'https://cryptologos.cc/logos/tron-trx-logo.png'
    ],
    'usdt_trc20' => [
        'name' => 'Tether USDT', 'symbol' => 'USDT', 'network' => 'TRC20', 
        'db' => 'usdt_trc20_balance', 
        'address' => $USDT_TRC20,
        'img' => 'https://cryptologos.cc/logos/tether-usdt-logo.png'
    ],
    'ltc' => [
        'name' => 'Litecoin', 'symbol' => 'LTC', 'network' => 'LTC', 
        'db' => 'ltc_balance', 
        'address' => $LTC,
        'img' => 'https://cryptologos.cc/logos/litecoin-ltc-logo.png'
    ],
    'doge' => [
        'name' => 'Dogecoin', 'symbol' => 'DOGE', 'network' => 'DOGE', 
        'db' => 'doge_balance', 
        'address' => $DOGE,
        'img' => 'https://cryptologos.cc/logos/dogecoin-doge-logo.png'
    ],
    'sol' => [
        'name' => 'Solana', 'symbol' => 'SOL', 'network' => 'SOL', 
        'db' => 'sol_balance', 
        'address' => $SOL,
        'img' => 'https://cryptologos.cc/logos/solana-sol-logo.png'
    ],
    'matic' => [
        'name' => 'Polygon', 'symbol' => 'MATIC', 'network' => 'MATIC', 
        'db' => 'matic_balance', 
        'address' => $MATIC,
        'img' => 'https://cryptologos.cc/logos/polygon-matic-logo.png'
    ],
];

// 3. Validate Coin
if (!array_key_exists($coin_slug, $coins)) { 
    echo "<script>window.location.href='assets.php';</script>"; 
    exit(); 
}

// 4. Set Variables
$current = $coins[$coin_slug];
$coin_symbol = $current['symbol'];
$coin_network = $current['network'];
$addr_val = $current['address']; // This now holds the variable value
$coin_img = $current['img'];

$alert = "";
$user_id = $_SESSION['user_id'];

// 5. Handle Deposit Submission
if(isset($_POST['submit_deposit'])){
    $tx_hash = clean($_POST['tx_hash']);
    $amount_usd = floatval($_POST['amount_usd']);
    $amount_crypto = floatval($_POST['amount_crypto']); 
    
    // Save to DB
    $sql = "INSERT INTO transactions (user_id, coin_symbol, network, type, amount_usd, amount_crypto, status, tx_hash) 
            VALUES ('$user_id', '$coin_symbol', '$coin_network', 'deposit', '$amount_usd', '$amount_crypto', 'pending', '$tx_hash')";
    
    if(mysqli_query($link, $sql)){
      $subject = "Deposit Submitted ($" . $amount_usd . ")";

        $body = '
        <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f9fafb; padding: 50px 0;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                
                <div style="padding: 40px 0 20px; text-align: center;">
                    <div style="background-color: #eef2ff; width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px;">
                        📥
                    </div>
                    <h1 style="color: #111827; margin: 0; font-size: 24px; font-weight: bold;">Deposit Submitted</h1>
                </div>
        
                <div style="padding: 20px 40px 40px;">
                    <p style="color: #4b5563; font-size: 16px; text-align: center; margin-bottom: 30px;">
                        We have received your deposit details. The transaction is currently being verified.
                    </p>
        
                    <div style="background-color: #f3f4f6; border-radius: 8px; padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-bottom: 10px; color: #6b7280; font-size: 14px;">Amount (USD)</td>
                                <td style="padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">$' . $amount_usd . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">Crypto Amount</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-weight: bold; font-size: 14px;">' . $amount_crypto . ' ' . $coin_symbol . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; color: #6b7280; font-size: 14px;">TXID</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; padding-bottom: 10px; text-align: right; color: #111827; font-size: 12px; font-family: monospace; word-break: break-all;">' . $tx_hash . '</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">Status</td>
                                <td style="padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: right;">
                                     <span style="background-color: #fffbeb; color: #d97706; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 20px; border: 1px solid #fcd34d;">Pending Verification</span>
                                </td>
                            </tr>
                        </table>
                    </div>
        
                    <div style="margin-top: 30px; text-align: center;">
                        <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                            We will notify you once the blockchain confirms the transaction.
                        </p>
                    </div>
                </div>
            </div>
        </div>';
        
        sendMail($email, $subject, $body);

        $alert = "Swal.fire({icon:'success', title:'Submitted', text:'Deposit submitted successfully.', confirmButtonText:'View Assets'}).then(()=>{ window.location.href='receive.php?coin=$coin_slug'; });";
    } else {
        $alert = "Swal.fire({icon:'error', title:'Database Error', text:'Could not save transaction.'});";
    }
}

// 6. Fetch Recent History
$tx_sql = "SELECT * FROM transactions WHERE user_id='$user_id' AND type='deposit' AND coin_symbol='$coin_symbol' ORDER BY created_at DESC LIMIT 5";
$tx_query = mysqli_query($link, $tx_sql);
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="w-full max-w-6xl mx-auto">
        <a href="coin-details.php?coin=<?php echo $coin_slug; ?>" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white mb-6 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Wallet
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <div class="lg:col-span-5 space-y-6 sticky top-6">
                
                <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 flex flex-col items-center justify-center text-center">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Scan to Pay</h2>
                    <div class="bg-white p-4 rounded-3xl shadow-xl shadow-green-500/10 mb-6 relative group">
                        <div class="absolute inset-0 bg-green-500/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $addr_val; ?>" 
                             alt="QR Code" 
                             class="w-52 h-52 object-contain rounded-lg relative z-10">
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Send only <span class="font-bold text-slate-900 dark:text-white"><?php echo $current['name']; ?></span> to this address.
                    </p>
                </div>

                <div class="glass-panel rounded-3xl p-6 border border-slate-200 dark:border-white/5">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Recent Deposits</h3>
                    <div class="space-y-4">
                        <?php if(mysqli_num_rows($tx_query) > 0): ?>
                            <?php while($tx = mysqli_fetch_assoc($tx_query)): ?>
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-500/10 flex items-center justify-center text-green-500">
                                        <i class="fa-solid fa-arrow-down"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-slate-900 dark:text-white">Deposit</p>
                                        <p class="text-xs text-slate-500"><?php echo date("M d, Y", strtotime($tx['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-sm text-green-500">+$<?php echo number_format($tx['amount_usd'], 2); ?></p>
                                    <p class="text-xs text-slate-500 capitalize"><?php echo $tx['status']; ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-500 text-center py-4">No recent deposits found.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-7">
                <div class="glass-panel rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-white/5">
                    
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Payment Details</h1>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-green-500/10 text-green-500 dark:text-green-400 border border-green-500/20">
                            <i class="fa-solid fa-shield-halved"></i> Secure
                        </span>
                    </div>

                    <form action="?coin=<?php echo $coin_slug; ?>" method="POST">
                        
                        <input type="hidden" name="amount_crypto" id="hiddenCryptoAmount" value="0">

                        <div class="space-y-6">
                            
                            <div class="flex items-center justify-between w-full bg-slate-50 dark:bg-[#02040a] border border-slate-200 dark:border-white/10 rounded-2xl p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center p-1 border border-slate-100">
                                        <img src="<?php echo $coin_img; ?>" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white"><?php echo $current['name']; ?></p>
                                        <p class="text-xs text-slate-500"><?php echo $coin_network; ?></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Deposit Address</label>
                                <div class="relative group">
                                    <input type="text" id="walletAddress" value="<?php echo $addr_val; ?>" readonly
                                           class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-2xl py-4 pl-4 pr-12 focus:outline-none focus:border-green-500 transition-all font-mono text-sm">
                                    
                                    <button type="button" onclick="copyAddress()" class="absolute right-2 top-1/2 transform -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-white dark:bg-white/10 text-green-600 dark:text-white rounded-lg hover:bg-green-50 dark:hover:bg-white/20 transition-colors shadow-sm" title="Copy Address">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" onclick="copyAddress()" class="flex-1 bg-green-600 hover:bg-green-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-green-500/30 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fa-regular fa-copy"></i> Copy Address
                                </button>
                                <button type="button" class="flex-1 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-white border border-slate-200 dark:border-white/10 hover:bg-slate-200 dark:hover:bg-white/10 font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-share-nodes"></i> Share
                                </button>
                            </div>

                            <div class="border-t border-slate-200 dark:border-white/10 pt-8 mt-8">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Submit Transaction</h3>
                                
                                <div class="space-y-4">
                                    
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Deposit Amount (USD)</label>
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500 font-bold">$</div>
                                            <input type="number" step="0.01" name="amount_usd" id="amountUsd" required placeholder="0.00" 
                                                   class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-2xl py-4 pl-8 pr-4 focus:outline-none focus:border-green-500 font-mono text-sm">
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 text-right">
                                            ≈ <span id="cryptoPreview">0.00000000</span> <?php echo $coin_symbol; ?>
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Transaction Hash (TXID)</label>
                                        <input type="text" name="tx_hash" required placeholder="Paste your transaction ID here..." 
                                               class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-2xl py-3 px-4 focus:outline-none focus:border-green-500 font-mono text-sm">
                                    </div>

                                    <button type="submit" name="submit_deposit" class="w-full bg-slate-900 dark:bg-white text-white dark:text-black font-bold py-4 rounded-xl hover:bg-slate-800 dark:hover:bg-slate-200 transition-all flex items-center justify-center gap-2 shadow-lg">
                                        <i class="fa-solid fa-check-circle"></i> Verify Deposit
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

<script>
    const symbol = "<?php echo $coin_symbol; ?>"; 
    let livePrice = 0;

    // 1. Fetch Price
    fetch(`https://min-api.cryptocompare.com/data/price?fsym=${symbol}&tsyms=USD`)
    .then(res => res.json())
    .then(data => {
        if(data.USD) {
            livePrice = data.USD;
        }
    });

    // 2. Real-time Calculation
    const amountInput = document.getElementById('amountUsd');
    const previewSpan = document.getElementById('cryptoPreview');
    const hiddenInput = document.getElementById('hiddenCryptoAmount');

    amountInput.addEventListener('input', function() {
        const usdVal = parseFloat(this.value);
        if(usdVal > 0 && livePrice > 0) {
            const cryptoAmt = (usdVal / livePrice).toFixed(8);
            previewSpan.innerText = cryptoAmt;
            hiddenInput.value = cryptoAmt;
        } else {
            previewSpan.innerText = "0.00000000";
            hiddenInput.value = "0";
        }
    });

    // 3. Copy Address
    function copyAddress() {
        const addr = document.getElementById('walletAddress');
        addr.select();
        addr.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(addr.value);
        
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Address copied to clipboard',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    <?php echo $alert; ?>
</script>

<?php include 'footer.php'; ?>