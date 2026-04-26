<?php
include 'header.php'; 

$referral_link = $siteurl . "/auth/register.php?ref=" . $username;

// Count Total Referrals
$total_query = mysqli_query($link, "SELECT COUNT(*) as total FROM users WHERE referred_by = '$username'");
$total_data = mysqli_fetch_assoc($total_query);
$total_referrals = $total_data['total'];

// Count Active Referrals (Verified Email)
$active_query = mysqli_query($link, "SELECT COUNT(*) as total FROM users WHERE referred_by = '$username' AND email_verified_at IS NOT NULL");
$active_data = mysqli_fetch_assoc($active_query);
$active_referrals = $active_data['total'];

?>

<div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 space-y-6">

    <div class="text-center max-w-2xl mx-auto mb-10">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight uppercase mb-2">Referral Program</h1>
        <p class="text-slate-500 dark:text-slate-400">Share the <?php echo $sitename; ?> experience and earn <span class="text-indigo-500 font-bold"><?php echo isset($referral_bonus_percentage) ? $referral_bonus_percentage : '15'; ?>% Lifetime Commissions</span>.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        
        <div class="lg:col-span-2 glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 flex flex-col justify-center">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Your Unique Invite Link</p>
            
            <div class="flex flex-col md:flex-row gap-3 mb-6">
                <div class="flex-1 relative">
                    <input type="text" id="refLink" value="<?php echo $referral_link; ?>" readonly class="w-full bg-slate-100 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3.5 px-4 focus:outline-none text-sm font-mono">
                </div>
                <button onclick="copyToClipboard('refLink')" class="px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold shadow-lg shadow-indigo-500/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fa-regular fa-copy"></i> Copy Link
                </button>
            </div>

            <div class="flex gap-4">
                <a href="https://twitter.com/intent/tweet?text=Join%20me%20on%20<?php echo urlencode($sitename); ?>!%20Start%20earning%20today:%20<?php echo urlencode($referral_link); ?>" target="_blank" class="flex-1 py-3 rounded-xl bg-[#1DA1F2]/10 text-[#1DA1F2] border border-[#1DA1F2]/20 font-bold text-sm hover:bg-[#1DA1F2] hover:text-white transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-twitter"></i> Share on Twitter
                </a>
                <a href="https://t.me/share/url?url=<?php echo urlencode($referral_link); ?>&text=Join%20me%20on%20<?php echo urlencode($sitename); ?>%20and%20grow%20your%20wealth!" target="_blank" class="flex-1 py-3 rounded-xl bg-[#0088cc]/10 text-[#0088cc] border border-[#0088cc]/20 font-bold text-sm hover:bg-[#0088cc] hover:text-white transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-telegram"></i> Share on Telegram
                </a>
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 bg-gradient-to-br from-indigo-900 to-[#0B0F19] text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl -mr-10 -mt-10"></div>
            
            <!--<p class="text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Total Earnings</p>-->
            <!--<h2 class="text-4xl font-extrabold mb-6">$<?php echo number_format($referral_earnings, 2); ?></h2>-->

            <div class="flex justify-between items-center mb-8">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Referrals</p>
                    <p class="text-xl font-bold"><?php echo $total_referrals; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 uppercase font-bold">Active</p>
                    <p class="text-xl font-bold text-green-400"><?php echo $active_referrals; ?></p>
                </div>
            </div>

            <!--<button class="w-full py-3.5 rounded-xl bg-white text-indigo-900 font-bold hover:bg-slate-100 transition-all shadow-lg">-->
            <!--    Withdraw Commissions-->
            <!--</button>-->
        </div>

    </div>

    <div class="max-w-7xl mx-auto mt-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Promotional Kit</h3>
            <button onclick="downloadAsset('assets/promo/all-assets.zip', 'promo-kit.zip')" class="text-xs text-indigo-500 font-bold hover:underline">Download All (.ZIP)</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="group relative rounded-2xl overflow-hidden aspect-square bg-indigo-600 flex items-center justify-center text-center p-6 cursor-pointer">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10 backdrop-blur-sm">
                    <button onclick="downloadAsset('https://placehold.co/1080x1080/4f46e5/ffffff?text=Grow+Wealth', 'instagram-post.jpg')" class="w-12 h-12 rounded-full bg-white text-black flex items-center justify-center shadow-xl transform scale-75 group-hover:scale-100 transition-transform hover:bg-gray-100">
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-white/50 tracking-widest block mb-4"><?php echo strtoupper($sitename); ?></span>
                    <h4 class="font-extrabold text-white text-2xl mb-2">GROW YOUR WEALTH</h4>
                    <p class="text-xs text-white/80 uppercase font-bold tracking-wider">12.5% APY Staking</p>
                </div>
                <div class="absolute bottom-4 left-0 right-0 text-center">
                    <p class="text-[10px] text-white/60">Instagram Post (1:1)</p>
                </div>
            </div>

            <div class="group relative rounded-2xl overflow-hidden aspect-[9/16] md:aspect-square bg-[#0f172a] flex items-center justify-center text-center p-6 cursor-pointer border border-white/10">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10 backdrop-blur-sm">
                    <button onclick="downloadAsset('https://placehold.co/1080x1920/0f172a/ffffff?text=Crypto+Banking', 'facebook-story.jpg')" class="w-12 h-12 rounded-full bg-white text-black flex items-center justify-center shadow-xl transform scale-75 group-hover:scale-100 transition-transform hover:bg-gray-100">
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>
                <div>
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-bolt text-white"></i>
                    </div>
                    <h4 class="font-bold text-white text-2xl mb-2 leading-tight">FUTURE OF BANKING</h4>
                    <p class="text-xs text-white/60 uppercase tracking-widest">Get Your Virtual Card</p>
                </div>
                <div class="absolute bottom-4 left-0 right-0 text-center">
                    <p class="text-[10px] text-white/40">Facebook Story (9:16)</p>
                </div>
            </div>

            <div class="group relative rounded-2xl overflow-hidden aspect-video md:aspect-square bg-gradient-to-r from-slate-900 to-black flex items-center justify-center text-center p-6 cursor-pointer border border-white/10">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-10 backdrop-blur-sm">
                    <button onclick="downloadAsset('https://placehold.co/1500x500/000000/ffffff?text=Institutional+Tools', 'twitter-banner.jpg')" class="w-12 h-12 rounded-full bg-white text-black flex items-center justify-center shadow-xl transform scale-75 group-hover:scale-100 transition-transform hover:bg-gray-100">
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4 opacity-50">
                        <span class="text-[8px] uppercase tracking-widest text-white"><?php echo strtoupper($sitename); ?></span>
                    </div>
                    <h4 class="font-extrabold text-white text-xl mb-2">INSTITUTIONAL TOOLS</h4>
                    <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Join Elite Traders</p>
                </div>
                <div class="absolute bottom-4 left-0 right-0 text-center">
                    <p class="text-[10px] text-white/40">Twitter Banner (16:9)</p>
                </div>
            </div>

        </div>
    </div>

    <div class="glass-panel rounded-3xl p-6 md:p-8 border border-slate-200 dark:border-white/5 max-w-7xl mx-auto mt-8">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Suggested Ad Copy</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-50 dark:bg-black/20 p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative group">
                <p id="caption1" class="text-sm text-slate-600 dark:text-slate-300 italic mb-4">"Stop leaving money on the table. <?php echo $sitename; ?>'s auto-trading nodes are a game changer. Join me! 🚀 #PassiveIncome #Crypto"</p>
                <button onclick="copyText('caption1')" class="text-xs font-bold text-indigo-500 uppercase tracking-wider hover:text-indigo-400 flex items-center gap-1">
                    <i class="fa-regular fa-copy"></i> Copy Caption
                </button>
            </div>

            <div class="bg-slate-50 dark:bg-black/20 p-5 rounded-2xl border border-slate-200 dark:border-white/5 relative group">
                <p id="caption2" class="text-sm text-slate-600 dark:text-slate-300 italic mb-4">"The only dashboard you'll ever need. Crypto, banking, and institutional-grade trading in one place. Verify your KYC today. 💳 ✨ #CryptoLife"</p>
                <button onclick="copyText('caption2')" class="text-xs font-bold text-indigo-500 uppercase tracking-wider hover:text-indigo-400 flex items-center gap-1">
                    <i class="fa-regular fa-copy"></i> Copy Caption
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    // 1. DOWNLOAD ASSET FUNCTION
    function downloadAsset(url, filename) {
        // Create an invisible link and click it
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename); // This forces download
        link.setAttribute('target', '_blank');   // Opens in new tab if download fails
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Optional: Show feedback
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
        Toast.fire({
            icon: 'success',
            title: 'Downloading...'
        });
    }

    // 2. COPY LINK FUNCTION
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(copyText.value);
        } else {
            document.execCommand('copy');
        }
        
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Link copied to clipboard',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    // 3. COPY CAPTION FUNCTION
    function copyText(elementId) {
        var text = document.getElementById(elementId).innerText;
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            var textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
        }

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Caption copied to clipboard',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
</script>

<?php include 'footer.php'; ?>