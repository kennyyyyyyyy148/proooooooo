<?php
include 'header.php'; 
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">Your Assets</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Manage your crypto portfolio.</p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Balance</p>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">$<?php echo number_format($total_balance, 2); ?></h2>
        </div>
    </div>

    <div class="space-y-4">
        
        <a href="coin-details.php?coin=usdt_erc20" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/usdt.png" alt="USDT" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/tether-usdt-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tether USDT</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">ERC20</span>
                            <span id="change-USDT_ERC20" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_usdt_erc20, 2); ?></h3>
                    <span id="calc-USDT_ERC20" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_usdt_erc20; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=eth" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/eth.png" alt="ETH" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/ethereum-eth-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Ethereum</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">ERC20</span>
                            <span id="change-ETH" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_eth, 2); ?></h3>
                    <span id="calc-ETH" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_eth; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=btc" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/btc.png" alt="BTC" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/bitcoin-btc-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Bitcoin</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">BTC</span>
                            <span id="change-BTC" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_btc, 2); ?></h3>
                    <span id="calc-BTC" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_btc; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=bnb" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/bnb.png" alt="BNB" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/bnb-bnb-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Binance Coin</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">BEP20</span>
                            <span id="change-BNB" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_bnb, 2); ?></h3>
                    <span id="calc-BNB" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_bnb; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=trx" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/trx.png" alt="TRX" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/tron-trx-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tron</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">TRC20</span>
                            <span id="change-TRX" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_trx, 2); ?></h3>
                    <span id="calc-TRX" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_trx; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=usdt_trc20" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/usdt.png" alt="USDT" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/tether-usdt-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Tether USDT</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">TRC20</span>
                            <span id="change-USDT_TRC20" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_usdt_trc20, 2); ?></h3>
                    <span id="calc-USDT_TRC20" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_usdt_trc20; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=ltc" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/ltc.png" alt="LTC" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/litecoin-ltc-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Litecoin</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">LTC</span>
                            <span id="change-LTC" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_ltc, 2); ?></h3>
                    <span id="calc-LTC" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_ltc; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=doge" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/doge.png" alt="DOGE" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/dogecoin-doge-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Dogecoin</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">DOGE</span>
                            <span id="change-DOGE" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_doge, 2); ?></h3>
                    <span id="calc-DOGE" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_doge; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=sol" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/sol.png" alt="SOL" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/solana-sol-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Solana</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">SOL</span>
                            <span id="change-SOL" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_sol, 2); ?></h3>
                    <span id="calc-SOL" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_sol; ?>">Calculating...</span>
                </div>
            </div>
        </a>

        <a href="coin-details.php?coin=matic" class="block glass-panel rounded-3xl p-5 hover:border-indigo-500/30 transition-all duration-300 group">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center p-2">
                        <img src="assets/img/matic.png" alt="MATIC" class="w-full h-full object-contain" onerror="this.src='https://cryptologos.cc/logos/polygon-matic-logo.png'">
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">Polygon</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-white/10 px-2 py-0.5 rounded-md">MATIC</span>
                            <span id="change-MATIC" class="text-xs font-bold flex items-center gap-1 text-slate-400">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <h3 class="font-bold text-lg text-slate-900 dark:text-white">$<?php echo number_format($usd_matic, 2); ?></h3>
                    <span id="calc-MATIC" class="text-sm text-slate-500 dark:text-slate-400 font-mono" data-usd="<?php echo $usd_matic; ?>">Calculating...</span>
                </div>
            </div>
        </a>

    </div>

</div>

<script>
    const coins = "BTC,ETH,USDT,BNB,TRX,LTC,DOGE,SOL,MATIC";
    
    // Fetch FULL market data including 24h Change
    fetch(`https://min-api.cryptocompare.com/data/pricemultifull?fsyms=${coins}&tsyms=USD`)
    .then(response => response.json())
    .then(data => {
        const rawData = data.RAW;

        // Helper function to update Coin UI
        const updateCoinUI = (symbol, elementIdSuffix = symbol) => {
            const coinData = rawData[symbol].USD;
            const price = coinData.PRICE;
            const change24h = coinData.CHANGEPCT24HOUR;

            // 1. Calculate Balance Amount
            const balEl = document.getElementById(`calc-${elementIdSuffix}`);
            if(balEl) {
                const usdBalance = parseFloat(balEl.getAttribute('data-usd'));
                const cryptoAmount = (usdBalance > 0) ? (usdBalance / price).toFixed(6) : "0.000000";
                balEl.innerText = `${cryptoAmount} ${symbol}`;
            }

            // 2. Update 24h Change Indicator
            const changeEl = document.getElementById(`change-${elementIdSuffix}`);
            if(changeEl) {
                const isPositive = change24h >= 0;
                const colorClass = isPositive ? 'text-green-500' : 'text-red-500';
                const iconClass = isPositive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                const sign = isPositive ? '+' : '';

                changeEl.className = `text-xs font-bold flex items-center gap-1 ${colorClass}`;
                changeEl.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${sign}${change24h.toFixed(2)}%`;
            }
        };

        // Loop through standard coins
        updateCoinUI('BTC');
        updateCoinUI('ETH');
        updateCoinUI('BNB');
        updateCoinUI('TRX');
        updateCoinUI('LTC');
        updateCoinUI('DOGE');
        updateCoinUI('SOL');
        updateCoinUI('MATIC');

        // Handle USDT specifically for both ERC20 and TRC20 using the same USDT API data
        updateCoinUI('USDT', 'USDT_ERC20');
        updateCoinUI('USDT', 'USDT_TRC20');

    })
    .catch(error => console.error('Error fetching prices:', error));
</script>

<?php include 'footer.php'; ?>