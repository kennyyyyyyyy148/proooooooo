<?php
include 'header.php';


$coin_slug = isset($_GET['coin']) ? htmlspecialchars($_GET['coin']) : 'btc';

$coins = [
    'usdt_erc20' => ['name'=>'Tether USDT','symbol'=>'USDT','network'=>'ERC20','db'=>'usdt_erc20_balance','img'=>'https://cryptologos.cc/logos/tether-usdt-logo.png'],
    'eth'        => ['name'=>'Ethereum',   'symbol'=>'ETH', 'network'=>'ERC20','db'=>'eth_balance',       'img'=>'https://cryptologos.cc/logos/ethereum-eth-logo.png'],
    'btc'        => ['name'=>'Bitcoin',    'symbol'=>'BTC', 'network'=>'BTC',  'db'=>'btc_balance',       'img'=>'https://cryptologos.cc/logos/bitcoin-btc-logo.png'],
    'bnb'        => ['name'=>'Binance Coin','symbol'=>'BNB', 'network'=>'BEP20','db'=>'bnb_balance',       'img'=>'https://cryptologos.cc/logos/bnb-bnb-logo.png'],
    'trx'        => ['name'=>'Tron',       'symbol'=>'TRX', 'network'=>'TRC20','db'=>'trx_balance',       'img'=>'https://cryptologos.cc/logos/tron-trx-logo.png'],
    'usdt_trc20' => ['name'=>'Tether USDT','symbol'=>'USDT','network'=>'TRC20','db'=>'usdt_trc20_balance','img'=>'https://cryptologos.cc/logos/tether-usdt-logo.png'],
    'ltc'        => ['name'=>'Litecoin',   'symbol'=>'LTC', 'network'=>'LTC',  'db'=>'ltc_balance',       'img'=>'https://cryptologos.cc/logos/litecoin-ltc-logo.png'],
    'doge'       => ['name'=>'Dogecoin',   'symbol'=>'DOGE','network'=>'DOGE', 'db'=>'doge_balance',      'img'=>'https://cryptologos.cc/logos/dogecoin-doge-logo.png'],
    'sol'        => ['name'=>'Solana',     'symbol'=>'SOL', 'network'=>'SOL',  'db'=>'sol_balance',       'img'=>'https://cryptologos.cc/logos/solana-sol-logo.png'],
    'matic'      => ['name'=>'Polygon',    'symbol'=>'MATIC','network'=>'MATIC','db'=>'matic_balance',     'img'=>'https://cryptologos.cc/logos/polygon-matic-logo.png'],
];

if (!array_key_exists($coin_slug, $coins)) { echo "<script>window.location.href='assets.php';</script>"; exit(); }
$current = $coins[$coin_slug];
$current_sym = $current['symbol'];


$user_id = $_SESSION['user_id'];
$col = $current['db'];
$q = mysqli_query($link, "SELECT $col FROM users WHERE id='$user_id'");
$r = mysqli_fetch_assoc($q);
$usd_balance = floatval($r[$col]);

$tx_sql = "SELECT * FROM transactions 
           WHERE user_id='$user_id' 
           AND (
               coin_symbol = '$current_sym' 
               OR coin_symbol LIKE '$current_sym->%' 
               OR coin_symbol LIKE '%->$current_sym'
           ) 
           ORDER BY created_at DESC LIMIT 10";
$tx_query = mysqli_query($link, $tx_sql);
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="w-full max-w-6xl mx-auto">
        <a href="assets.php" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white mb-6 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Back to Assets
        </a>

        <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 relative overflow-hidden mb-8">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="flex flex-col md:flex-row justify-between items-start gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center border border-slate-200 dark:border-white/10 shadow-lg">
                            <img src="<?php echo $current['img']; ?>" class="w-10 h-10 object-contain">
                        </div>
                        <div class="absolute -bottom-1 -right-1 px-2 py-0.5 rounded-full bg-slate-900 border-2 border-[#0B0F19] flex items-center justify-center">
                            <span class="text-[10px] font-bold text-white"><?php echo $current['network']; ?></span>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $current['name']; ?></h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span id="live-price" class="text-lg text-slate-500 dark:text-slate-400 font-medium">Loading...</span>
                            <span id="price-change" class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-white/10 text-slate-500">0.00%</span>
                        </div>
                    </div>
                </div>

                <div class="text-left md:text-right">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Your Balance</p>
                    <h2 id="crypto-bal" class="text-4xl font-extrabold text-slate-900 dark:text-white">...</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">≈ $<?php echo number_format($usd_balance, 2); ?> USD</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8 max-w-2xl">
                <a href="send.php?coin=<?php echo $coin_slug; ?>" class="flex flex-col items-center justify-center gap-2 py-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 hover:border-indigo-500/50 hover:bg-indigo-500/5 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-arrow-up transform rotate-45"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Send</span>
                </a>
                <a href="receive.php?coin=<?php echo $coin_slug; ?>" class="flex flex-col items-center justify-center gap-2 py-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 hover:border-indigo-500/50 hover:bg-indigo-500/5 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-arrow-down transform rotate-45"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Receive</span>
                </a>
                <a href="buy.php" class="flex flex-col items-center justify-center gap-2 py-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 hover:border-indigo-500/50 hover:bg-indigo-500/5 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        <i class="fa-regular fa-credit-card"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Buy</span>
                </a>
                <a href="swap.php?from=<?php echo $coin_slug; ?>" class="flex flex-col items-center justify-center gap-2 py-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 hover:border-indigo-500/50 hover:bg-indigo-500/5 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-white/10 flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-repeat"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Swap</span>
                </a>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 mb-8">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Market Chart (7 Days)</h3>
            <div class="relative h-64 w-full">
                <canvas id="coinChart"></canvas>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Recent Activity</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                        
                        <?php if(mysqli_num_rows($tx_query) > 0): ?>
                            <?php while($tx = mysqli_fetch_assoc($tx_query)): 
                                // LOGIC: Determine if Incoming or Outgoing
                                $type = $tx['type'];
                                $symbol_db = $tx['coin_symbol'];
                                $is_incoming = false;
                                $display_title = ucfirst($type);

                                if ($type == 'deposit') {
                                    $is_incoming = true;
                                } elseif ($type == 'withdrawal') {
                                    $is_incoming = false;
                                } elseif ($type == 'swap') {
                                    // Check swap direction string (e.g. BTC->ETH)
                                    if (strpos($symbol_db, "->".$current_sym) !== false) {
                                        // Ends with ->BTC, so we received BTC
                                        $is_incoming = true;
                                        $display_title = "Swap Receive";
                                    } else {
                                        // Starts with BTC->, so we sent BTC
                                        $is_incoming = false;
                                        $display_title = "Swap Sent";
                                    }
                                }

                                $color = $is_incoming ? 'text-green-500' : 'text-slate-900 dark:text-white';
                                $icon = $is_incoming ? 'fa-arrow-down' : 'fa-arrow-up';
                                $sign = $is_incoming ? '+' : '-';
                                $icon_bg = $is_incoming ? 'bg-green-500/10 text-green-500' : 'bg-slate-100 dark:bg-white/5 text-slate-500';
                            ?>
                            <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-4 pl-2 pr-4">
                                    <div class="w-10 h-10 rounded-full <?php echo $icon_bg; ?> flex items-center justify-center">
                                        <i class="fa-solid <?php echo $icon; ?> transform <?php echo $is_incoming ? '' : 'rotate-45'; ?>"></i>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-900 dark:text-white text-sm"><?php echo $display_title; ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo substr($tx['tx_hash'], 0, 12) . '...'; ?></p>
                                </td>
                                <td class="py-4 px-4 hidden md:table-cell">
                                    <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo date("M d, Y", strtotime($tx['created_at'])); ?></p>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md <?php echo ($tx['status']=='completed')?'bg-green-500/10 text-green-500':'bg-yellow-500/10 text-yellow-500'; ?> text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full <?php echo ($tx['status']=='completed')?'bg-green-500':'bg-yellow-500 animate-pulse'; ?>"></span> <?php echo ucfirst($tx['status']); ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <p class="font-bold <?php echo $color; ?> text-sm"><?php echo $sign; ?> $<?php echo number_format($tx['amount_usd'], 2); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo number_format($tx['amount_crypto'], 6); ?> <?php echo $current_sym; ?></p>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-ghost text-2xl mb-2 opacity-50"></i>
                                    <p>No transactions found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const symbol = "<?php echo $current['symbol']; ?>";
    const balanceUSD = <?php echo $usd_balance; ?>;

    // 1. Live Price
    fetch(`https://min-api.cryptocompare.com/data/pricemultifull?fsyms=${symbol}&tsyms=USD`)
    .then(res => res.json())
    .then(data => {
        const coin = data.RAW[symbol].USD;
        const price = coin.PRICE;
        const change = coin.CHANGEPCT24HOUR;

        document.getElementById('live-price').innerText = `$${price.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        const changeEl = document.getElementById('price-change');
        if (change >= 0) {
            changeEl.classList.add('text-green-500', 'bg-green-500/10');
            changeEl.innerHTML = `<i class="fa-solid fa-arrow-trend-up"></i> +${change.toFixed(2)}%`;
        } else {
            changeEl.classList.add('text-red-500', 'bg-red-500/10');
            changeEl.innerHTML = `<i class="fa-solid fa-arrow-trend-down"></i> ${change.toFixed(2)}%`;
        }

        const cryptoAmount = (balanceUSD > 0) ? (balanceUSD / price).toFixed(6) : "0.000000";
        document.getElementById('crypto-bal').innerText = `${cryptoAmount} ${symbol}`;
    });

    // 2. Chart
    fetch(`https://min-api.cryptocompare.com/data/v2/histoday?fsym=${symbol}&tsym=USD&limit=7`)
    .then(res => res.json())
    .then(data => {
        const history = data.Data.Data;
        const labels = history.map(h => new Date(h.time * 1000).toLocaleDateString('en-US', {weekday: 'short'}));
        const prices = history.map(h => h.close);

        const ctx = document.getElementById('coinChart').getContext('2d');
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); 
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Price (USD)',
                    data: prices,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                    y: { display: false }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    });
</script>

<?php include 'footer.php'; ?>