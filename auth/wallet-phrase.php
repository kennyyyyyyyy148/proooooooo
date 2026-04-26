<?php
include '../app/session.php';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Backup | <?php echo $sitename ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        dark: { bg: '#02040a', panel: '#0B0F19', border: '#1E293B' },
                        light: { bg: '#F8FAFC', panel: '#FFFFFF', border: '#E2E8F0' }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            @apply bg-white/70 dark:bg-[#121826]/70;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            @apply border border-slate-200 dark:border-white/5;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-lg rounded-3xl p-8 relative z-10">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Secret Recovery Phrase</h1>
            <p class="text-sm text-red-400 font-medium bg-red-500/10 border border-red-500/20 py-2 px-4 rounded-lg inline-block">
                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Save these words securely.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6" id="phraseContainer">
            </div>

        <button onclick="copyPhrase()" class="w-full py-3 mb-4 rounded-xl border border-slate-200 dark:border-white/10 text-indigo-500 font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition-all flex items-center justify-center gap-2 group">
            <i class="fa-regular fa-copy group-hover:scale-110 transition-transform"></i> Copy to Clipboard
        </button>

        <a href="set-pin.php" class="block w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold text-center shadow-lg hover:scale-[1.02] transition-transform">
            I Have Saved My Phrase
        </a>
    </div>

    <script>
        // Inject PHP variable safely
        const phrase = "<?php echo $secret_phrase; ?>";
        const words = phrase.split(" ");
        const container = document.getElementById('phraseContainer');

        // Render words
        if(phrase && words.length > 1) {
            words.forEach((word, index) => {
                const div = document.createElement('div');
                div.className = "bg-slate-50 dark:bg-[#0B0F19] border border-slate-200 dark:border-white/10 rounded-lg p-2 flex items-center gap-2";
                div.innerHTML = `<span class="text-xs text-slate-400 select-none">${index + 1}.</span> <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">${word}</span>`;
                container.appendChild(div);
            });
        } else {
            container.innerHTML = '<p class="col-span-3 text-center text-red-500">Error loading phrase.</p>';
        }

        // --- ROBUST COPY FUNCTION (Works on HTTP & HTTPS) ---
        function copyPhrase() {
            // Method 1: Modern API (Best for HTTPS)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(phrase).then(() => {
                    showSuccess();
                }).catch(() => {
                    fallbackCopy();
                });
            } else {
                // Method 2: Fallback (Best for HTTP/Localhost)
                fallbackCopy();
            }
        }

        function fallbackCopy() {
            const textArea = document.createElement("textarea");
            textArea.value = phrase;
            
            // Ensure textarea is not visible but part of DOM
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            document.body.appendChild(textArea);
            
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                showSuccess();
            } catch (err) {
                Swal.fire({icon: 'error', title: 'Oops', text: 'Failed to copy automatically. Please copy manually.'});
            }
            
            document.body.removeChild(textArea);
        }

        function showSuccess() {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Phrase copied to clipboard.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    </script>

</body>
</html>