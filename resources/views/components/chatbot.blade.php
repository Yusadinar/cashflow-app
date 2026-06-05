<div x-data="{ 
        isOpen: false, 
        message: '', 
        messages: [{ type: 'bot', text: 'Halo! Saya asisten keuangan Anda. Ada yang bisa saya bantu terkait kondisi keuangan Anda?' }],
        isLoading: false,
        sendMessage() {
            if (this.message.trim() === '') return;
            
            const userMsg = this.message;
            this.messages.push({ type: 'user', text: userMsg });
            this.message = '';
            this.isLoading = true;
            
            // Scroll to bottom after user message
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
            
            fetch('/fin-assist/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ message: userMsg })
            })
            .then(async response => {
                if (!response.ok) {
                    const errData = await response.json().catch(() => null);
                    throw new Error(errData?.reply || errData?.message || `HTTP Error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                this.messages.push({ type: 'bot', text: data.reply });
                this.isLoading = false;
                
                // Scroll to bottom after bot message
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    container.scrollTop = container.scrollHeight;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                this.messages.push({ type: 'bot', text: error.message !== 'Failed to fetch' ? error.message : 'Terjadi kesalahan jaringan atau server memblokir akses ke AI.' });
                this.isLoading = false;
            });
        }
    }" 
    @keydown.escape.window="isOpen = false"
>
    <!-- Blurred Backdrop (Mobile Only) -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 sm:hidden"
         @click="isOpen = false">
    </div>

    <!-- Widget Container -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    
    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-[350px] max-w-[calc(100vw-3rem)] flex flex-col overflow-hidden mb-4"
         style="height: 500px; max-height: calc(100vh - 8rem);">
         
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-600 to-brand-500 p-4 text-white flex justify-between items-center shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">FinAssist</h3>
                    <p class="text-xs text-brand-100">Asisten Keuangan Pintar</p>
                </div>
            </div>
            <button @click="isOpen = false" class="text-white/80 hover:text-white transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 bg-slate-50 space-y-4">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.type === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.type === 'user' 
                        ? 'bg-brand-600 text-white rounded-2xl rounded-tr-sm py-2 px-4 max-w-[85%] text-sm shadow-sm' 
                        : 'bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-sm py-2 px-4 max-w-[85%] text-sm shadow-sm leading-relaxed'"
                         x-html="msg.text">
                    </div>
                </div>
            </template>
            
            <!-- Loading indicator -->
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white border border-slate-200 text-slate-500 rounded-2xl rounded-tl-sm py-3 px-4 max-w-[85%] text-sm shadow-sm flex items-center gap-1">
                    <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
            <form @submit.prevent="sendMessage" class="flex items-center gap-2 relative">
                <input type="text" 
                       x-model="message" 
                       placeholder="Tanyakan keuangan Anda..." 
                       class="w-full pl-4 pr-12 py-2.5 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-shadow"
                       :disabled="isLoading"
                >
                <button type="submit" 
                        class="absolute right-1 w-8 h-8 flex items-center justify-center bg-brand-600 text-white rounded-full hover:bg-brand-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isLoading || message.trim() === ''">
                    <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="isOpen = !isOpen" 
            class="w-14 h-14 bg-brand-600 rounded-full text-white shadow-lg shadow-brand-500/30 flex items-center justify-center hover:bg-brand-700 hover:scale-105 transition-all duration-200"
            :class="isOpen ? 'scale-0 opacity-0 absolute' : 'scale-100 opacity-100'">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
    </button>
    </div>
</div>
