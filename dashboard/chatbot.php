<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/chatbot.css">
  <style>
    .copy-btn{position:absolute;top:8px;right:10px;background:rgba(0,0,0,0.1);border:none;border-radius:6px;padding:4px 8px;cursor:pointer;opacity:0;transition:.2s;font-size:11px}
    .message-bubble:hover .copy-btn{opacity:1}
    .message-bubble{position:relative;padding-right:45px !important}
    .typing-dots{display:flex;gap:6px;align-items:center}
    .dot{width:9px;height:9px;background:#FFD700;border-radius:50%;animation:bounce 1.4s infinite}
    .dot:nth-child(1){animation-delay:0s}.dot:nth-child(2){animation-delay:0.2s}.dot:nth-child(3){animation-delay:0.4s}
    @keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
    
    /* Styling untuk daftar riwayat di sidebar */
    .history-item {
      padding: 10px 15px; cursor: pointer; border-radius: 8px; margin: 4px 10px;
      background: #f0f0f0; font-size: 14px; position: relative; transition: .2s;
    }
    .history-item:hover { background: #e0e0e0; }
    .history-item.active { background: #007bff; color: white; }
    .delete-history {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      font-size: 12px; color: #999; cursor: pointer;
    }
    .delete-history:hover { color: #f44; }
  </style>
</head>
<body>

<!-- LOGIN WALL -->
<div id="login-wall">
  <div class="login-box">
    <i class="fas fa-lock login-icon"></i>
    <h2>Fitur Ini Memerlukan Login</h2>
    <p>Silakan login atau register untuk menikmati fitur mood tracker ini.</p>
    <a href="../auth/login.php" class="login-button-link">Login atau Register</a><br>
    <a href="../dashboard/index.php" class="login-button">Kembali</a>
  </div>
</div>

<div class="app-container">
  <!-- SIDEBAR -->
  <div class="sidebar">
    <div>
      <div class="sidebar-header"><h1>SAGABOT</h1></div>
      <button class="new-chat-btn" id="newChatBtn"><i class="fas fa-plus"></i> New chat</button>
      <div class="search-bar"><i class="fas fa-search" style="color:#999;"></i><input type="text" placeholder="Search..." /></div>
      <div class="section-header"><span>Riwayat Chat (maks 3)</span></div>
      <div id="historyList"></div>
    </div>
    <div class="sidebar-footer">
      <div class="settings"><span>Settings</span><i class="fas fa-cog"></i></div>
      <div class="user-profile">
        <img src="https://i.pravatar.cc/50" alt="user"><span>Andrew Neilson</span>
      </div>
    </div>
  </div>

  <!-- CHAT AREA -->
  <div class="main-content">
    <div class="chat-header">
      <i class="fas fa-robot"></i>
      <div class="chat-info">
        <h2>Welcome to SagaBot Chat</h2>
        <p>Chat with your AI companion</p>
      </div>
    </div>
    <div class="chat-container">
      <div class="messages-container" id="messagesContainer"></div>
      <div class="input-container">
        <div class="input-wrapper">
          <textarea id="messageInput" placeholder="Tulis pesanmu di sini..." rows="1"></textarea>
          <button id="sendBtn"><i class="fas fa-paper-plane"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// === GANTI DENGAN API KEY GEMINI KAMU ===
const GEMINI_API_KEY = "AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

// Login wall
(function(){
  if(sessionStorage.getItem('isLoggedIn')==='true'){
    document.getElementById('login-wall').style.display='none';
    document.body.style.overflow='auto';
  }
})();

const input = document.getElementById("messageInput");
const sendBtn = document.getElementById("sendBtn");
const container = document.getElementById("messagesContainer");
const historyList = document.getElementById("historyList");
const newChatBtn = document.getElementById("newChatBtn");

let chatHistory = JSON.parse(localStorage.getItem('sagabot_history') || '[]');
let currentChatIndex = chatHistory.length; // chat baru = di luar index

// Render riwayat di sidebar
function renderHistory() {
  historyList.innerHTML = '';
  chatHistory.forEach((chat, i) => {
    const firstMsg = chat.find(m => m.sender === 'user')?.text || 'Chat baru';
    const preview = firstMsg.substring(0, 30) + (firstMsg.length > 30 ? '...' : '');
    
    const div = document.createElement('div');
    div.className = 'history-item';
    if (i === currentChatIndex) div.classList.add('active');
    div.innerHTML = `
      ${preview}
      <span class="delete-history" data-index="${i}"><i class="fas fa-trash"></i></span>
    `;
    div.addEventListener('click', (e) => {
      if (!e.target.closest('.delete-history')) {
        loadChat(i);
      }
    });
    historyList.appendChild(div);
  });
  
  // Event hapus manual
  document.querySelectorAll('.delete-history').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const idx = parseInt(btn.dataset.index);
      if (confirm('Hapus chat ini?')) {
        chatHistory.splice(idx, 1);
        localStorage.setItem('sagabot_history', JSON.stringify(chatHistory));
        if (currentChatIndex >= chatHistory.length) currentChatIndex = chatHistory.length;
        renderHistory();
        if (currentChatIndex < chatHistory.length) loadChat(currentChatIndex);
        else startNewChat();
      }
    });
  });
}

// Load chat dari riwayat
function loadChat(index) {
  currentChatIndex = index;
  container.innerHTML = '';
  chatHistory[index].forEach(msg => createMessage(msg.text, msg.sender));
  renderHistory();
}

// Mulai chat baru
function startNewChat() {
  container.innerHTML = '';
  createBotMessage("Hai! Aku SagaBot versi lucu seperti SimSimi! Apa kabar hari ini?");
  currentChatIndex = chatHistory.length;
  renderHistory();
}

// Simpan chat saat new chat atau tutup halaman
function saveCurrentChat() {
  const messages = Array.from(container.querySelectorAll('.message')).map(m => ({
    text: m.querySelector('.message-bubble').innerText.replace(/Copy|Copied!/g,'').trim(),
    sender: m.classList.contains('user-message') ? 'user' : 'bot'
  }));
  if (messages.length > 1) {
    if (currentChatIndex < chatHistory.length) {
      chatHistory[currentChatIndex] = messages;
    } else {
      chatHistory.push(messages);
      if (chatHistory.length > 3) {
        alert("Riwayat sudah penuh (maks 3). Silakan hapus salah satu secara manual.");
      }
    }
    localStorage.setItem('sagabot_history', JSON.stringify(chatHistory));
  }
}

// New Chat Button
newChatBtn.addEventListener('click', () => {
  saveCurrentChat();
  startNewChat();
});

// Loading & Pesan
function showLoading(){ /* sama seperti sebelumnya */ }
function hideLoading(){ document.getElementById("loading")?.remove(); }

function createMessage(text, sender="user"){
  const d=document.createElement("div"); d.className=`message ${sender}-message`;
  if(sender==="bot"){
    const img=document.createElement("img");
    img.src="img/MASKOT.png"; img.className="avatar"; img.alt="Bot";
    img.onerror=()=>img.src='https://placehold.co/40x40/FFD700/000000?text=S';
    d.appendChild(img);
  }
  const b=document.createElement("div"); b.className="message-bubble"; b.textContent=text;
  if(sender==="bot"){
    const c=document.createElement("button"); c.className="copy-btn"; c.textContent="Copy";
    c.onclick=()=>{navigator.clipboard.writeText(text); c.textContent="Copied!"; setTimeout(()=>c.textContent="Copy",1500);}
    b.appendChild(c);
  }
  d.appendChild(b); container.appendChild(d);
  container.scrollTop=container.scrollHeight;
}
function createBotMessage(text){ createMessage(text, "bot"); }

// Gemini Call (sama seperti sebelumnya)
async function callGemini(message){
  showLoading();
  try {
    const res = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
      method: "POST", headers: {"Content-Type":"application/json"},
      body: JSON.stringify({
        contents: [{ role: "user", parts: [{ text: message }] }],
        systemInstruction: { parts: [{ text: "Kamu adalah SagaBot, AI kesehatan yang ramah, suportif, lucu seperti SimSimi tapi fokus pada kesehatan fisik, mental, tidur, makan, olahraga, dan gaya hidup sehat. Jawab dalam bahasa Indonesia yang santai, hangat, dan penuh empati. Gunakan emoji secukupnya." }] },
        generationConfig: { temperature: 0.9, maxOutputTokens: 600 }
      })
    });
    const data = await res.json();
    hideLoading();
    const reply = data.candidates?.[0]?.content?.parts?.[0]?.text?.trim() || "Aduh bingung nih... coba lagi ya!";
    createMessage(reply, "bot");
  } catch (err) {
    hideLoading();
    createMessage("Koneksi bermasalah nih... coba lagi ya", "bot");
  }
}

function sendMessage(){
  const text = input.value.trim(); if(!text) return;
  createMessage(text,"user");
  input.value=""; input.style.height='auto';
  callGemini(text);
}

// Event
input.addEventListener('input',()=>{input.style.height='auto';input.style.height=input.scrollHeight+'px';});
input.addEventListener("keydown",e=>{if(e.key==="Enter"&&!e.shiftKey){e.preventDefault();sendMessage();}});
sendBtn.addEventListener("click",sendMessage);

// Simpan saat tutup/tab close
window.addEventListener('beforeunload', saveCurrentChat);

// Load saat buka
window.onload = () => {
  renderHistory();
  if (chatHistory.length === 0) startNewChat();
  else loadChat(chatHistory.length - 1);
  input.focus();
};
</script>
</body>
</html>
