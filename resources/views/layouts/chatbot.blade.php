
<div x-data="chatbotWidget()" x-init="init()" class="fixed bottom-5 right-5 z-[80] font-sans">

    {{-- CHAT WINDOW --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-5 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-5 scale-95"
        class="absolute bottom-[76px] right-0
               w-[calc(100vw-2.5rem)] sm:w-[410px]
               h-[min(650px,calc(100dvh-110px))]
               bg-[#fffaf5]
               rounded-[26px]
               shadow-2xl shadow-black/25
               border border-orange-100
               flex flex-col overflow-hidden">

        {{-- HEADER --}}
        <div class="relative bg-gray-950 px-5 py-4 flex items-center gap-3 flex-shrink-0 overflow-hidden">

            <div class="absolute inset-0 bg-gradient-to-r from-orange-950/50 to-transparent pointer-events-none"></div>

            <div class="relative w-12 h-12 rounded-2xl bg-orange-500 flex items-center justify-center text-2xl shadow-lg shadow-orange-900/30">
                👩‍🍳
            </div>

            <div class="relative flex-1 min-w-0">
                <p class="text-white font-extrabold text-sm tracking-wide">
                    Hotel Assistant
                </p>

                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full"
                          :class="voiceMode
                            ? (speaking ? 'bg-orange-400 animate-pulse' : listening ? 'bg-rose-400 animate-pulse' : typing ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400')
                            : 'bg-emerald-400'">
                    </span>

                    <span class="text-xs text-gray-300" x-text="headerStatus"></span>
                </div>
            </div>

            <template x-if="voiceMode">
                <span class="relative px-3 py-1 rounded-full bg-orange-500 text-white text-[10px] font-black tracking-widest">
                    LIVE
                </span>
            </template>

            <button type="button"
                    @click="toggleTts()"
                    :title="ttsEnabled ? 'Voice replies enabled' : 'Voice replies disabled'"
                    class="relative p-2.5 rounded-xl transition"
                    :class="ttsEnabled ? 'bg-white/10 text-orange-400' : 'text-gray-400 hover:bg-white/10 hover:text-white'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                </svg>
            </button>

            <button type="button"
                    @click="clearChat()"
                    title="Clear conversation"
                    class="relative p-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1-1h-4a1 1 0 01-1 1v3M4 7h16"/>
                </svg>
            </button>

            <button type="button"
                    @click="closeChat()"
                    class="relative p-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>


        {{-- WELCOME BANNER --}}
        <div class="bg-white border-b border-orange-100 px-4 py-3 flex items-center gap-3 flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-lg">
                🍽️
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-xs font-extrabold text-gray-900">A delicious experience starts here.</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Ask us about reservations, menu and orders.</p>
            </div>

            <span class="text-orange-500 text-lg">✦</span>
        </div>


        {{-- MESSAGES --}}
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4 bg-[#fffaf5] scroll-smooth"
             x-ref="messages">

            <template x-for="(msg, index) in messages" :key="'message-' + index">

                <div :class="msg.from === 'user' ? 'flex justify-end' : 'flex justify-start'"
                     class="animate-fadeIn">

                    <div class="flex gap-2 max-w-[88%]"
                         :class="msg.from === 'user' ? 'flex-row-reverse' : 'flex-row'">

                        <template x-if="msg.from !== 'user'">
                            <div class="w-8 h-8 rounded-xl bg-gray-950 flex items-center justify-center text-sm shrink-0 mt-1">
                                👩‍🍳
                            </div>
                        </template>

                        <div class="px-4 py-3 shadow-sm text-sm leading-relaxed break-words min-w-0"
                             :class="msg.from === 'user'
                                ? 'bg-orange-500 text-white rounded-2xl rounded-br-sm'
                                : 'bg-white text-gray-800 border border-orange-100 rounded-2xl rounded-bl-sm'">

                            <template x-if="msg.isLiveVoice">
                                <div class="flex items-center gap-2 py-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce" style="animation-delay:150ms"></span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-bounce" style="animation-delay:300ms"></span>
                                    <span class="text-xs text-gray-500" x-text="msg.text || 'Listening…'"></span>
                                </div>
                            </template>

                            <template x-if="!msg.isLiveVoice">
                                <p class="whitespace-pre-wrap" x-text="msg.text"></p>
                            </template>
                        </div>
                    </div>
                </div>
            </template>


            {{-- TYPING INDICATOR --}}
            <template x-if="typing">
                <div class="flex justify-start animate-fadeIn">
                    <div class="flex gap-2 items-end">
                        <div class="w-8 h-8 rounded-xl bg-gray-950 flex items-center justify-center text-sm">
                            👩‍🍳
                        </div>

                        <div class="bg-white border border-orange-100 rounded-2xl rounded-bl-sm px-5 py-4 shadow-sm">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-orange-400 rounded-full animate-bounce"></span>
                                <span class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>


        {{-- QUICK REPLIES --}}
        <template x-if="messages.length <= 1 && !typing && !voiceMode">
            <div class="px-4 pb-3 bg-[#fffaf5] flex flex-wrap gap-2 flex-shrink-0">

                <template x-for="quick in quickReplies" :key="quick">
                    <button type="button"
                            @click="sendMessage(quick)"
                            class="px-3 py-2 rounded-full bg-white border border-orange-200 text-gray-700 text-xs font-bold hover:bg-orange-50 hover:border-orange-400 hover:text-orange-600 active:scale-95 transition">
                        <span x-text="quick"></span>
                    </button>
                </template>

            </div>
        </template>


        {{-- VOICE STATUS --}}
        <div x-show="voiceMode"
             x-cloak
             class="px-4 py-3 bg-orange-50 border-t border-orange-100 flex items-center gap-3 flex-shrink-0">

            <div class="flex items-center gap-1 h-6">
                <template x-for="n in 5" :key="n">
                    <span class="w-1 rounded-full bg-orange-500"
                          :style="`height:${speaking ? 8 + (n % 3) * 5 : listening ? 8 + (n % 2) * 5 : 5}px; animation:voiceBar 0.5s ease-in-out infinite alternate; animation-delay:${n * 100}ms`">
                    </span>
                </template>
            </div>

            <p class="text-xs font-bold text-orange-700 flex-1" x-text="headerStatus"></p>

            <button type="button"
                    @click="stopVoiceMode()"
                    class="text-xs font-extrabold text-rose-600 hover:text-rose-700">
                End voice
            </button>
        </div>


        {{-- INPUT --}}
        <form @submit.prevent="sendMessage()"
              class="p-3 bg-white border-t border-orange-100 flex-shrink-0">

            <div class="flex items-end gap-2">

                {{-- MICROPHONE --}}
                <button type="button"
                        @click="toggleVoiceMode()"
                        :disabled="!voiceReady"
                        :title="voiceMode ? 'Stop voice conversation' : 'Start voice conversation'"
                        class="relative w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition active:scale-95 disabled:opacity-40"
                        :class="voiceMode
                            ? 'bg-rose-500 text-white shadow-lg shadow-rose-200'
                            : 'bg-orange-50 text-orange-600 hover:bg-orange-100'">

                    <span x-show="voiceMode && listening"
                          class="absolute inset-0 rounded-xl bg-rose-400 opacity-30 animate-ping"></span>

                    <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="9" y="3" width="6" height="12" rx="3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 10a7 7 0 01-14 0m7 7v4m-4 0h8"/>
                    </svg>
                </button>


                {{-- TEXT INPUT --}}
                <textarea
                    x-model="input"
                    @keydown.enter.exact.prevent="sendMessage()"
                    @keydown.enter.shift.exact.stop
                    rows="1"
                    placeholder="Ask anything..."
                    class="flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition max-h-24 overflow-y-auto">
                </textarea>


                {{-- SEND --}}
                <button type="submit"
                        :disabled="!input.trim() || typing"
                        class="w-11 h-11 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-orange-100 hover:bg-orange-600 active:scale-95 transition disabled:opacity-40 disabled:cursor-not-allowed">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>

            <p class="text-[10px] text-center text-gray-400 mt-2">
                Powered by AI · Here to make your dining experience easier
            </p>
        </form>

    </div>


    {{-- FLOATING BUTTON --}}
    <button type="button"
            @click="toggle()"
            aria-label="Open hotel assistant"
            class="relative w-[62px] h-[62px] rounded-2xl bg-orange-500 text-white shadow-xl shadow-orange-500/30 hover:bg-orange-600 hover:-translate-y-1 active:scale-95 transition-all duration-200 flex items-center justify-center">

        <span x-show="!open"
              class="absolute inset-0 rounded-2xl bg-orange-400 opacity-30 animate-ping pointer-events-none"></span>

        <svg x-show="!open" class="w-7 h-7 relative z-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        <svg x-show="open" x-cloak class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

</div>


{{-- ANIMATIONS --}}
<style>
    @keyframes voiceBar {
        from { transform: scaleY(.5); }
        to { transform: scaleY(1.5); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fadeIn {
        animation: fadeIn .2s ease-out;
    }

    .scroll-smooth {
        scroll-behavior: smooth;
    }

    [x-cloak] {
        display: none !important;
    }
</style>


{{-- CHATBOT LOGIC --}}
<script>
if (typeof window.chatbotWidget === 'undefined') {
    window.chatbotWidget = function () {
        return {
            /* CHAT */
            open: false,
            input: '',
            typing: false,
            messages: [{
                from: 'bot',
                text: "Hi! 👋 I'm your Hotel Assistant. How can I help you today?"
            }],
            quickReplies: [
                'Book a table for 3 at 7pm today',
                'Show me my reservations',
                'I want to order food'
            ],

            /* VOICE */
            voiceReady: false,
            voiceMode: false,
            listening: false,
            speaking: false,
            processingVoiceTurn: false,
            recognition: null,
            liveTranscript: '',
            finalTranscript: '',
            liveVoiceMessageIndex: null,
            voiceStatus: 'Voice ready',
            autoStopTimer: null,
            silenceTimer: null,
            restartTimer: null,
            restartingRecognition: false,

            /* LANGUAGE */
            currentLanguage: 'en',
            recognitionLanguage: 'en-US',

            /* TTS */
            ttsEnabled: true,
            availableVoices: [],
            selectedVoice: null,

            async init() {
                try {
                    const savedTts = localStorage.getItem('chatbot_tts');
                    if (savedTts !== null) {
                        this.ttsEnabled = savedTts === 'true';
                    }

                    const savedMessages = localStorage.getItem('chatbot_messages');
                    if (savedMessages) {
                        const parsed = JSON.parse(savedMessages);
                        if (Array.isArray(parsed) && parsed.length) {
                            this.messages = parsed.filter(m => !m.isLiveVoice);
                        }
                    }
                } catch (e) {
                    console.warn('[Chatbot] Local storage:', e);
                }

                this.$watch('ttsEnabled', value => {
                    localStorage.setItem('chatbot_tts', String(value));
                });

                this.$watch('messages', value => {
                    const clean = value.filter(m => !m.isLiveVoice);
                    localStorage.setItem(
                        'chatbot_messages',
                        JSON.stringify(clean.slice(-30))
                    );
                });

                this.initSpeechSynthesis();
                this.initSpeechRecognition();
            },

            detectLanguage(text) {
                const value = String(text || '').trim();

                if (/[\u0600-\u06FF]/.test(value)) return 'ur';

                const romanUrduWords = [
                    'hai', 'hain', 'hoon', 'hun', 'han',
                    'mujhe', 'mujhy', 'mera', 'meri', 'mere',
                    'aap', 'ap', 'kaise', 'kesa', 'kesay',
                    'kya', 'kia', 'kyun', 'q', 'kab',
                    'kahan', 'kidhar', 'chahiye', 'chahta',
                    'chahti', 'karna', 'karni', 'karo',
                    'karen', 'batao', 'bataiye', 'suno',
                    'sun', 'acha', 'achha', 'theek',
                    'shukriya', 'meherbani', 'sahib',
                    'bhai', 'yaar', 'nahi', 'nahin',
                    'haan', 'aur', 'lekin',
                    'book karna', 'kar do', 'kar dein'
                ];

                const normalized = value.toLowerCase()
                    .replace(/[^\p{L}\p{N}\s]/gu, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();

                const words = normalized.split(' ');
                const matches = romanUrduWords.filter(word => {
                    if (word.includes(' ')) return normalized.includes(word);
                    return words.includes(word);
                }).length;

                return matches >= 1 ? 'ur' : 'en';
            },

            getRecognitionLocale(language) {
                return language === 'ur' ? 'ur-PK' : 'en-US';
            },

            initSpeechSynthesis() {
                if (!('speechSynthesis' in window)) return;

                const load = () => {
                    this.availableVoices = window.speechSynthesis.getVoices();
                };

                load();
                window.speechSynthesis.addEventListener('voiceschanged', load);
            },

            findVoice(language = 'en') {
                const voices = window.speechSynthesis.getVoices();

                if (!voices.length) return null;

                if (language === 'ur') {
                    return voices.find(v =>
                        (v.lang || '').toLowerCase().startsWith('ur')
                    ) || null;
                }

                const english = voices.filter(v =>
                    (v.lang || '').toLowerCase().startsWith('en')
                );

                const preferred = [
                    'microsoft aria',
                    'microsoft jenny',
                    'samantha',
                    'zira',
                    'google us english'
                ];

                for (const name of preferred) {
                    const match = english.find(v =>
                        (v.name || '').toLowerCase().includes(name)
                    );
                    if (match) return match;
                }

                return english[0] || null;
            },

            initSpeechRecognition() {
                const SpeechRecognition =
                    window.SpeechRecognition ||
                    window.webkitSpeechRecognition;

                if (!SpeechRecognition) {
                    this.voiceReady = false;
                    this.voiceStatus = 'Voice not supported';
                    return;
                }

                this.recognition = new SpeechRecognition();
                this.recognition.continuous = true;
                this.recognition.interimResults = true;
                this.recognition.maxAlternatives = 1;
                this.recognition.lang = 'en-US';

                this.recognition.onstart = () => {
                    this.listening = true;
                    this.finalTranscript = '';
                    this.liveTranscript = '';
                    this.voiceStatus = 'Listening…';
                    this.ensureLiveVoiceBubble();
                };

                this.recognition.onresult = event => {
                    let interim = '';
                    let finalText = '';

                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        const transcript = event.results[i][0].transcript.trim();

                        if (event.results[i].isFinal) {
                            if (transcript) finalText += transcript + ' ';
                        } else {
                            interim += transcript + ' ';
                        }
                    }

                    if (finalText) {
                        this.finalTranscript =
                            (this.finalTranscript + ' ' + finalText).trim();
                    }

                    this.liveTranscript =
                        (this.finalTranscript + ' ' + interim).trim();

                    this.updateLiveVoiceBubble(this.liveTranscript);

                    if (finalText) this.resetSilenceTimer();
                };

                this.recognition.onerror = event => {
                    console.warn('[Chatbot] Recognition error:', event.error);

                    if (event.error === 'not-allowed' ||
                        event.error === 'service-not-allowed') {
                        this.stopVoiceMode();
                        alert('Please allow microphone access in your browser.');
                    }
                };

                this.recognition.onend = () => {
                    this.listening = false;

                    if (this.processingVoiceTurn || this.restartingRecognition) return;

                    if (this.voiceMode && !this.typing && !this.speaking) {
                        clearTimeout(this.restartTimer);

                        this.restartTimer = setTimeout(() => {
                            if (this.voiceMode && !this.typing &&
                                !this.speaking && !this.listening) {
                                this.startListening();
                            }
                        }, 250);
                    }
                };

                this.voiceReady = true;
                this.voiceStatus = 'Voice ready';
            },

            ensureLiveVoiceBubble() {
                this.messages = this.messages.filter(m => !m.isLiveVoice);

                this.messages.push({
                    from: 'user',
                    text: '',
                    isLiveVoice: true
                });

                this.liveVoiceMessageIndex = this.messages.length - 1;
                this.$nextTick(() => this.scrollToBottom());
            },

            updateLiveVoiceBubble(text) {
                if (this.liveVoiceMessageIndex === null) return;

                const msg = this.messages[this.liveVoiceMessageIndex];
                if (!msg || !msg.isLiveVoice) return;

                msg.text = text;
                this.$nextTick(() => this.scrollToBottom());
            },

            finalizeLiveVoiceBubble(text) {
                if (this.liveVoiceMessageIndex === null) return;

                const index = this.liveVoiceMessageIndex;
                const msg = this.messages[index];

                if (msg && msg.isLiveVoice) {
                    if (text.trim()) {
                        this.messages[index] = {
                            from: 'user',
                            text: text.trim()
                        };
                    } else {
                        this.messages.splice(index, 1);
                    }
                }

                this.liveVoiceMessageIndex = null;
                this.liveTranscript = '';
                this.finalTranscript = '';

                this.$nextTick(() => this.scrollToBottom());
            },

            startVoiceMode() {
                if (!this.voiceReady) {
                    alert('Voice is not supported. Please use Chrome or Edge.');
                    return;
                }

                this.voiceMode = true;
                this.ttsEnabled = true;
                this.voiceStatus = 'Starting voice…';

                this.startListening();
            },

            stopVoiceMode() {
                this.voiceMode = false;
                this.listening = false;
                this.processingVoiceTurn = false;

                if (this.liveVoiceMessageIndex !== null) {
                    const msg = this.messages[this.liveVoiceMessageIndex];
                    if (msg && msg.isLiveVoice) {
                        this.messages.splice(this.liveVoiceMessageIndex, 1);
                    }
                    this.liveVoiceMessageIndex = null;
                }

                this.clearVoiceTimers();
                clearTimeout(this.restartTimer);

                if (this.recognition) {
                    try {
                        this.restartingRecognition = true;
                        this.recognition.abort();
                    } catch (e) {}
                }

                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                }

                this.speaking = false;
                this.voiceStatus = 'Voice ready';

                setTimeout(() => {
                    this.restartingRecognition = false;
                }, 300);
            },

            toggleVoiceMode() {
                if (this.voiceMode) {
                    this.stopVoiceMode();
                } else {
                    this.startVoiceMode();
                }
            },

            startListening() {
                if (!this.voiceMode || !this.recognition ||
                    this.typing || this.speaking || this.listening) return;

                this.clearVoiceTimers();
                this.finalTranscript = '';
                this.liveTranscript = '';

                this.recognition.lang =
                    this.getRecognitionLocale(this.currentLanguage);

                try {
                    this.restartingRecognition = false;
                    this.recognition.start();
                } catch (error) {
                    console.warn('[Chatbot] Could not start recognition:', error);
                }

                this.autoStopTimer = setTimeout(() => {
                    if (this.listening) this.stopListening();
                }, 20000);
            },

            stopListening() {
                this.clearVoiceTimers();

                const text = (
                    this.finalTranscript || this.liveTranscript || ''
                ).trim();

                this.processingVoiceTurn = true;
                this.listening = false;

                if (this.recognition) {
                    try {
                        this.restartingRecognition = true;
                        this.recognition.stop();
                    } catch (e) {}
                }

                if (text) {
                    this.finalizeLiveVoiceBubble(text);
                    this.sendMessage(text, true, true);
                    return;
                }

                this.finalizeLiveVoiceBubble('');
                this.processingVoiceTurn = false;
                this.restartingRecognition = false;

                if (this.voiceMode) {
                    setTimeout(() => this.startListening(), 250);
                }
            },

            resetSilenceTimer() {
                this.clearSilenceTimer();

                this.silenceTimer = setTimeout(() => {
                    if (this.listening && !this.typing && !this.speaking) {
                        this.stopListening();
                    }
                }, 1000);
            },

            clearSilenceTimer() {
                clearTimeout(this.silenceTimer);
                this.silenceTimer = null;
            },

            clearVoiceTimers() {
                this.clearSilenceTimer();
                clearTimeout(this.autoStopTimer);
                this.autoStopTimer = null;
            },

            async sendMessage(text = null, fromVoice = false, skipUserBubble = false) {
                const message = String(text ?? this.input).trim();

                if (!message || this.typing) return;

                const language = this.detectLanguage(message);
                this.currentLanguage = language;

                if (this.listening && this.recognition) {
                    try {
                        this.recognition.stop();
                    } catch (e) {}
                    this.listening = false;
                }

                this.clearVoiceTimers();

                const history = this.messages
                    .filter(m =>
                        (m.from === 'user' || m.from === 'bot') &&
                        !m.isLiveVoice
                    )
                    .slice(-8);

                if (!skipUserBubble) {
                    this.messages.push({
                        from: 'user',
                        text: message
                    });
                }

                this.input = '';
                this.typing = true;
                this.voiceStatus = fromVoice ? 'Thinking…' : this.voiceStatus;

                this.$nextTick(() => this.scrollToBottom());

                try {
                    const csrf = document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content || '';

                    const response = await fetch(
                        '{{ route('chatbot.message') }}',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message,
                                history,
                                language,
                                response_language: language
                            })
                        }
                    );

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    const reply = String(
                        data.reply || 'Sorry, I could not process that.'
                    ).trim();

                    const speechText = String(
                        data.tts_text || reply
                    ).trim();

                    const replyLanguage = data.reply_language ||
                        data.language || language;

                    this.messages.push({
                        from: 'bot',
                        text: reply
                    });

                    this.$nextTick(() => this.scrollToBottom());

                    if (this.ttsEnabled) {
                        await this.speak(speechText, replyLanguage);
                    }

                } catch (error) {
                    console.error('[Chatbot] Request failed:', error);

                    const errorReply = language === 'ur'
                        ? 'معذرت، کچھ مسئلہ پیش آیا ہے۔ دوبارہ کوشش کریں۔'
                        : 'Sorry, something went wrong. Please try again.';

                    this.messages.push({
                        from: 'bot',
                        text: errorReply
                    });

                    if (this.ttsEnabled) {
                        await this.speak(errorReply, language);
                    }

                } finally {
                    this.typing = false;
                    this.processingVoiceTurn = false;
                    this.restartingRecognition = false;

                    if (this.voiceMode) {
                        this.voiceStatus = 'Listening…';

                        setTimeout(() => {
                            if (this.voiceMode && !this.typing &&
                                !this.speaking && !this.listening) {
                                this.startListening();
                            }
                        }, 250);
                    }
                }
            },

            speak(text, language = 'en') {
                return new Promise(resolve => {
                    if (!('speechSynthesis' in window)) {
                        resolve();
                        return;
                    }

                    window.speechSynthesis.cancel();

                    const speechText = this.cleanTextForSpeech(text);
                    if (!speechText) {
                        resolve();
                        return;
                    }

                    const isUrdu = language === 'ur' ||
                        /[\u0600-\u06FF]/.test(speechText);

                    const voice = this.findVoice(isUrdu ? 'ur' : 'en');

                    if (isUrdu && !voice) {
                        console.warn(
                            '[Chatbot] No Urdu speech voice installed. ' +
                            'Install an Urdu voice or use a cloud Urdu TTS service.'
                        );
                    }

                    const utterance = new SpeechSynthesisUtterance(speechText);

                    if (voice) {
                        utterance.voice = voice;
                        utterance.lang = voice.lang;
                    } else {
                        utterance.lang = isUrdu ? 'ur-PK' : 'en-US';
                    }

                    utterance.rate = isUrdu ? 0.88 : 1.0;
                    utterance.pitch = 1.0;
                    utterance.volume = 1;

                    this.speaking = true;
                    this.voiceStatus = 'Speaking…';

                    utterance.onend = () => {
                        this.speaking = false;
                        resolve();
                    };

                    utterance.onerror = event => {
                        console.warn('[Chatbot] Speech error:', event.error);
                        this.speaking = false;
                        resolve();
                    };

                    window.speechSynthesis.speak(utterance);
                });
            },

            cleanTextForSpeech(text) {
                return String(text || '')
                    .replace(/\*\*(.*?)\*\*/g, '$1')
                    .replace(/\*(.*?)\*/g, '$1')
                    .replace(/`([^`]+)`/g, '$1')
                    .replace(/~~(.*?)~~/g, '$1')
                    .replace(/^#{1,6}\s*/gm, '')
                    .replace(/^\s*[-*+]\s+/gm, '')
                    .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
                    .replace(/[\u{1F300}-\u{1FAFF}]/gu, '')
                    .replace(/[\u{2600}-\u{27BF}]/gu, '')
                    .replace(/\|/g, ', ')
                    .replace(/\n+/g, '. ')
                    .replace(/\s+/g, ' ')
                    .trim();
            },

            toggleTts() {
                this.ttsEnabled = !this.ttsEnabled;

                if (!this.ttsEnabled && 'speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                    this.speaking = false;
                }
            },

            toggle() {
                this.open = !this.open;

                if (this.open) {
                    this.$nextTick(() => this.scrollToBottom());
                } else if (this.voiceMode) {
                    this.stopVoiceMode();
                }
            },

            closeChat() {
                this.open = false;
                if (this.voiceMode) this.stopVoiceMode();
            },

            clearChat() {
                this.messages = [{
                    from: 'bot',
                    text: 'Chat cleared. How can I help you?'
                }];

                localStorage.removeItem('chatbot_messages');
                this.$nextTick(() => this.scrollToBottom());
            },

            scrollToBottom() {
                const element = this.$refs.messages;
                if (element) {
                    element.scrollTop = element.scrollHeight;
                }
            },

            get headerStatus() {
                if (this.voiceMode) {
                    if (this.speaking) return 'Speaking…';
                    if (this.typing) return 'Thinking…';
                    if (this.listening) return 'Listening…';
                    return 'Voice active';
                }

                return this.voiceReady ? 'Ready to help' : 'Text chat';
            }
        };
    };
}
</script>
