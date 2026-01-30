<template>
  <div class="flex flex-col h-[600px] bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
    <!-- Header -->
    <div class="bg-emerald-600 p-4 text-white flex justify-between items-center shadow-md z-10">
      <h2 class="font-bold text-lg flex items-center">
        <span class="mr-2">💬</span> Centro de Mensajes
      </h2>
      <div class="text-xs bg-emerald-700 px-2 py-1 rounded">
        En vivo
      </div>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar: Conversations List -->
        <div class="w-1/3 border-r border-gray-200 flex flex-col bg-white">
        <!-- Search (Optional placeholder) -->
        <div class="p-3 border-b border-gray-100 bg-gray-50">
            <input type="text" placeholder="Buscar conversación..." class="w-full text-sm border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        
        <!-- List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div v-if="loading" class="p-8 text-center text-gray-400 flex flex-col items-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500 mb-2"></div>
                <span class="text-xs">Cargando chats...</span>
            </div>
            <div v-else-if="conversations.length === 0" class="p-8 text-center text-gray-500">
                <p>No hay conversaciones recientes (23h)</p>
            </div>
            
            <div 
                v-for="conv in conversations" 
                :key="conv.id"
                @click="selectConversation(conv)"
                class="p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-150 group relative"
                :class="{'bg-emerald-50 border-l-4 border-l-emerald-500': selectedConversation?.id === conv.id, 'border-l-4 border-l-transparent': selectedConversation?.id !== conv.id}"
            >
                <div class="flex justify-between items-start mb-1">
                    <div class="font-semibold text-gray-800 truncate pr-2 flex-1">
                        {{ conv.display_name || conv.wa_id }}
                    </div>
                    <div class="text-[10px] text-gray-400 whitespace-nowrap pt-1">
                        {{ formatTime(conv.last_message_at) }}
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500 truncate w-3/4 flex items-center">
                        <span v-if="conv.messages && conv.messages[0]" class="flex items-center">
                            <span v-if="conv.messages[0].direction === 'outbound'" class="mr-1 text-emerald-500">✓</span>
                            <span :class="{'font-medium text-gray-700': conv.messages[0].direction === 'inbound'}">
                                {{ parseBody(conv.messages[0]) }}
                            </span>
                        </span>
                        <span v-else class="italic text-gray-400">Sin mensajes</span>
                    </p>
                    <div v-if="conv.pinned" class="text-yellow-400 text-xs">⭐</div>
                </div>
            </div>
        </div>
        </div>

        <!-- Main: Chat Window -->
        <div class="w-2/3 flex flex-col bg-[#efeae2] relative">
            <!-- Chat Background Pattern -->
            <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');"></div>

            <div v-if="selectedConversation" class="flex-1 flex flex-col z-0">
                <!-- Chat Header -->
                <div class="p-3 bg-white border-b border-gray-200 flex justify-between items-center shadow-sm z-10">
                    <div class="flex items-center">
                        <div class="bg-gray-200 rounded-full h-10 w-10 flex items-center justify-center mr-3 text-gray-600 font-bold text-lg">
                            {{ (selectedConversation.display_name || selectedConversation.wa_id).charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ selectedConversation.display_name || selectedConversation.wa_id }}</div>
                            <div class="text-xs text-gray-500">{{ selectedConversation.wa_id }}</div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button 
                            @click="togglePin(selectedConversation)"
                            class="p-2 rounded-full hover:bg-gray-100 transition focus:outline-none"
                            :title="selectedConversation.pinned ? 'Desanclar' : 'Anclar'"
                        >
                            <span v-if="selectedConversation.pinned" class="text-yellow-400 text-xl">⭐</span>
                            <span v-else class="text-gray-400 text-xl">☆</span>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar" ref="messagesContainer">
                    <div v-if="loadingMessages" class="text-center text-gray-500 mt-4">
                        <span class="inline-block animate-pulse">Cargando historial...</span>
                    </div>
                    
                    <div 
                        v-for="msg in messages" 
                        :key="msg.id" 
                        class="flex flex-col"
                        :class="msg.direction === 'outbound' ? 'items-end' : 'items-start'"
                    >
                        <div 
                            class="max-w-[70%] rounded-lg px-3 py-2 shadow-sm text-sm relative group"
                            :class="msg.direction === 'outbound' ? 'bg-[#d9fdd3] text-gray-800 rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none'"
                        >
                            <div class="whitespace-pre-wrap break-words leading-relaxed">{{ parseBody(msg) }}</div>
                            <div class="text-[10px] text-gray-500 text-right mt-1 opacity-70 flex justify-end items-center gap-1">
                                {{ formatTime(msg.created_at) }}
                                <span v-if="msg.direction === 'outbound'">
                                    <span v-if="msg.status === 'read'" class="text-blue-500">✓✓</span>
                                    <span v-else class="text-gray-500">✓</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-3 bg-gray-50 border-t border-gray-200 z-10">
                    <div class="flex space-x-2 items-end">
                        <div class="flex-1 bg-white rounded-lg border border-gray-300 shadow-sm focus-within:ring-1 focus-within:ring-emerald-500 focus-within:border-emerald-500 overflow-hidden">
                            <textarea 
                                v-model="newMessage" 
                                @keydown.enter.exact.prevent="sendMessage"
                                placeholder="Escribe un mensaje..." 
                                class="w-full border-none focus:ring-0 resize-none py-3 px-4 text-sm max-h-32"
                                rows="1"
                                :disabled="sending"
                            ></textarea>
                        </div>
                        <button 
                            @click="sendMessage"
                            class="bg-emerald-600 text-white p-3 rounded-full hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-sm flex-shrink-0"
                            :disabled="!newMessage.trim() || sending"
                        >
                            <span v-if="sending" class="block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <p class="text-[10px] text-gray-400">Enter para enviar</p>
                        <button 
                            @click="sendFarewell"
                            class="text-xs text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 px-2 py-1 rounded transition border border-transparent hover:border-emerald-200"
                        >
                            👋 Enviar Despedida rápida
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400 p-8 text-center">
                <div class="bg-white p-6 rounded-full shadow-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-600 mb-1">WhatsApp Dashboard</h3>
                <p class="text-sm max-w-xs">Selecciona una conversación de la lista para ver el historial y responder mensajes.</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch, onUnmounted } from 'vue';
import axios from 'axios';

const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(true);
const loadingMessages = ref(false);
const sending = ref(false);
const messagesContainer = ref(null);
const userId = ref(window.Laravel?.user?.id || 1); // Assuming user ID is available in window.Laravel or similar

// Format time
const formatTime = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const isToday = date.toDateString() === now.toDateString();
    
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Parse message body
const parseBody = (msg) => {
    if (!msg.body) return '';
    try {
        // If it's JSON string, parse it
        const parsed = JSON.parse(msg.body);
        if (parsed && typeof parsed === 'object') {
            if (msg.type === 'text') return parsed.text?.body || parsed;
            if (msg.type === 'interactive') {
                return '[Interactivo] ' + (parsed.type || '');
            }
            return '[Mensaje]';
        }
        return msg.body;
    } catch (e) {
        return msg.body;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const fetchConversations = async () => {
    try {
        const response = await axios.get('/api/dashboard/conversations');
        conversations.value = response.data;
        loading.value = false;
    } catch (error) {
        console.error('Error fetching conversations:', error);
        loading.value = false;
    }
};

const selectConversation = async (conv) => {
    selectedConversation.value = conv;
    loadingMessages.value = true;
    messages.value = [];
    
    try {
        const response = await axios.get(`/api/conversations/${conv.id}/messages`);
        messages.value = response.data;
        scrollToBottom();
        
        // Remove badge or mark as read logic here if needed
    } catch (error) {
        console.error('Error fetching messages:', error);
    } finally {
        loadingMessages.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedConversation.value) return;
    
    sending.value = true;
    const text = newMessage.value;
    
    try {
        const response = await axios.post(`/api/conversations/${selectedConversation.value.id}/send-message`, {
            text: text
        });
        
        // Add to list immediately (optimistic) or wait for event?
        // Since we return the message, let's add it.
        messages.value.push(response.data);
        newMessage.value = '';
        scrollToBottom();
        
        // Update last message in conversation list
        const convIndex = conversations.value.findIndex(c => c.id === selectedConversation.value.id);
        if (convIndex !== -1) {
            conversations.value[convIndex].last_message_at = new Date().toISOString();
            conversations.value[convIndex].messages = [response.data]; // Update preview
            // Move to top if not pinned (or sort logic handles it)
            // Re-sort logic might be needed
            sortConversations();
        }
        
    } catch (error) {
        console.error('Error sending message:', error);
        alert(error.response?.data?.error || 'Error enviando mensaje');
    } finally {
        sending.value = false;
    }
};

const sendFarewell = () => {
    newMessage.value = "Gracias por comunicarte con nosotros 🙌\nSi necesitás algo más, escribinos cuando quieras.";
    // Optional: Auto send
    // sendMessage();
};

const togglePin = async (conv) => {
    try {
        const action = conv.pinned ? 'unpin' : 'pin';
        await axios.post(`/api/conversations/${conv.id}/${action}`);
        
        // Update local state
        conv.pinned = !conv.pinned;
        sortConversations();
    } catch (error) {
        console.error('Error toggling pin:', error);
    }
};

const sortConversations = () => {
    conversations.value.sort((a, b) => {
        if (a.pinned !== b.pinned) return b.pinned ? 1 : -1;
        return new Date(b.last_message_at) - new Date(a.last_message_at);
    });
};

// Pusher Integration
onMounted(() => {
    fetchConversations();
    
    // Listen to private channel
    // Assuming userId is available. If not, need to fetch user info first.
    // Here we use a placeholder or assume global user object.
    
    // Try to get user ID from meta tag or window
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const uid = userIdMeta ? userIdMeta.content : (window.Laravel?.user?.id);
    
    if (uid) {
        console.log(`Listening on private-dashboard.${uid}`);
        window.Echo.private(`dashboard.${uid}`)
            .listen('.conversation.created', (e) => {
                console.log('Conversation created', e);
                conversations.value.unshift(e.conversation);
                sortConversations();
            })
            .listen('.conversation.updated', (e) => {
                console.log('Conversation updated', e);
                const index = conversations.value.findIndex(c => c.id === e.conversation.id);
                if (index !== -1) {
                    conversations.value[index] = { ...conversations.value[index], ...e.conversation };
                } else {
                    conversations.value.unshift(e.conversation);
                }
                sortConversations();
            })
            .listen('.message.received', (e) => {
                console.log('Message received', e);
                handleNewMessage(e.message);
            })
            .listen('.message.sent', (e) => {
                console.log('Message sent', e);
                handleNewMessage(e.message);
            });
    }
});

const handleNewMessage = (message) => {
    // 1. Update active chat if open
    if (selectedConversation.value && selectedConversation.value.id === message.conversation_id) {
        // Check if message already exists (to avoid duplicate from manual send)
        if (!messages.value.find(m => m.id === message.id)) {
            messages.value.push(message);
            scrollToBottom();
        }
    }
    
    // 2. Update conversation list preview
    const convIndex = conversations.value.findIndex(c => c.id === message.conversation_id);
    if (convIndex !== -1) {
        const conv = conversations.value[convIndex];
        conv.last_message_at = message.created_at;
        conv.messages = [message]; // Update preview
        
        // Move to top (respecting pins)
        sortConversations();
    } else {
        // Fetch conversation if not in list (new conversation)
        // Ideally conversation.created event handles this, but just in case
        fetchConversations(); 
    }
};

onUnmounted(() => {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const uid = userIdMeta ? userIdMeta.content : (window.Laravel?.user?.id);
    if (uid) {
        window.Echo.leave(`dashboard.${uid}`);
    }
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d5db; 
  border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9ca3af; 
}
</style>