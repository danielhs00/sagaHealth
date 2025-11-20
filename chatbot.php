<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DeepSeek Chat - AI with Avatar</title>
  <link rel="icon" href="🤖">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: #F5F7FB;
      display: flex;
      height: 100vh;
    }

    .app-container {
      display: flex;
      width: 100%;
    }

    /* ===== Sidebar Modern ===== */
    .sidebar {
      width: 300px;
      background: #FFFFFF;
      border-right: 1px solid #E0E3EB;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 15px;
    }

    .sidebar-header h1 {
      font-size: 1.3em;
      font-weight: 700;
      color: #000;
      letter-spacing: 1px;
      margin: 0 0 20px 5px;
    }

    .new-chat-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      background: #26667F;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 30px;
      padding: 12px 0;
      width: 100%;
      cursor: pointer;
      font-size: 0.95em;
      transition: 0.2s;
    }

    .new-chat-btn i {
      margin-right: 8px;
    }

    .new-chat-btn:hover {
      background: #2c45d4;
    }

    .search-bar {
      margin: 15px 0;
      display: flex;
      align-items: center;
      background: #F0F2F8;
      border-radius: 20px;
      padding: 8px 12px;
    }

    .search-bar input {
      flex: 1;
      border: none;
      background: transparent;
      outline: none;
      font-size: 0.9em;
      color: #333;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 15px 0 8px;
      font-size: 0.9em;
      color: #666;
    }

    .section-header a {
      font-size: 0.8em;
      color: #26667F;
      text-decoration: none;
    }

    .conversations {
      flex: 1;
      overflow-y: auto;
      padding-right: 5px;
    }

    .conversation-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: transparent;
      border-radius: 10px;
      padding: 10px;
      font-size: 0.9em;
      cursor: pointer;
      transition: background 0.2s;
    }

    .conversation-item:hover {
      background: #F0F2F8;
    }

    .conversation-item.active {
      background: #E9ECFE;
      color: #26667F;
      font-weight: 600;
    }

    .conversation-item i {
      color: #26667F;
      margin-right: 8px;
    }

    .sidebar-footer {
      border-top: 1px solid #E0E3EB;
      padding-top: 15px;
    }

    .settings {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 15px;
      border-radius: 12px;
      background: #F0F2F8;
      cursor: pointer;
      margin-bottom: 15px;
    }

    .settings:hover {
      background: #E9ECFE;
    }

    .settings i {
      color: #26667F;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
    }

    .user-profile img {
      width: 38px;
      height: 38px;
      border-radius: 50%;
    }

    .user-profile span {
      font-weight: 500;
      color: #333;
    }

    /* ===== Main Chat Area (biarkan seperti sebelumnya) ===== */
    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #EAF2ED;
    }

    .chat-header {
      background: #26667F;
      color: white;
      padding: 15px;
      display: flex;
      align-items: center;
    }

    .chat-info {
      margin-left: 10px;
    }

    .chat-info h2 {
      margin: 0;
      font-size: 1.2em;
    }

    .chat-info p {
      margin: 0;
      font-size: 0.9em;
      color: #E9ECFE;
    }

    .chat-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .messages-container {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .message {
      display: flex;
      align-items: flex-end;
      margin-bottom: 15px;
    }

    .bot-message { justify-content: flex-start; }
    .user-message { justify-content: flex-end; }

    .avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .message-bubble {
      max-width: 70%;
      padding: 12px 16px;
      border-radius: 15px;
      font-size: 15px;
      line-height: 1.4;
    }

    .bot-message .message-bubble {
      background: #fff;
      color: #26667F;
      border: 1px solid #d0d0d0;
      border-top-left-radius: 0;
    }

    .user-message .message-bubble {
      background: #26667F;
      color: white;
      border-top-right-radius: 0;
    }

    .input-container {
      padding: 15px;
      border-top: 1px solid #ddd;
      background: #fff;
    }

    .input-wrapper {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    textarea {
      flex: 1;
      resize: none;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-family: inherit;
      background: #ffffff;
      color: #333;
    }

    #sendBtn {
      background: #26667F;
      color: white;
      border: none;
      padding: 10px 14px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 16px;
    }

    #sendBtn:hover {
      background: #2c45d4;
    }
  </style>
</head>
<body>
  <div class="app-container">

    <!-- Sidebar baru -->
    <div class="sidebar">
      <div>
        <div class="sidebar-header">
          <h1>SAGABOT</h1>
        </div>

        <button class="new-chat-btn"><i class="fas fa-plus"></i> New chat</button>

        <div class="search-bar">
          <i class="fas fa-search" style="color:#999;"></i>
          <input type="text" placeholder="Search..." />
        </div>

        <div class="section-header">
          <span>Your conversations</span>
          <a href="#">Clear All</a>
        </div>

        <div class="conversations">
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> Create Html Game Environment</div>
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> Apply To Leave For Emergency</div>
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> What Is UI UX Design?</div>
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> Create POS System</div>
          <div class="conversation-item active"><i class="fas fa-comment-dots"></i> Create Chatbot GPT</div>
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> How Chat GPT Work?</div>
        </div>

        <div class="section-header" style="margin-top: 20px;">
          <span>Last 7 Days</span>
        </div>

        <div class="conversations">
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> Crypto Lending App Name</div>
          <div class="conversation-item"><i class="fas fa-comment-dots"></i> Operator Grammar Types</div>
          <div class="conversation-item" style="opacity:0.5;"><i class="fas fa-comment-dots"></i> Min States For Binary DFA</div>
        </div>
      </div>

      <div class="sidebar-footer">
        <div class="settings">
          <span>Settings</span>
          <i class="fas fa-cog"></i>
        </div>
        <div class="user-profile">
          <img src="https://i.pravatar.cc/50" alt="user">
          <span>Andrew Neilson</span>
        </div>
      </div>
    </div>

    <!-- Area chat -->
    <div class="main-content">
      <div class="chat-header">
        <i class="fas fa-robot"></i>
        <div class="chat-info">
          <h2>Welcome to SagaBot Chat</h2>
          <p>Chat with your AI companion</p>
        </div>
      </div>

      <div class="chat-container">
        <div class="messages-container" id="messagesContainer">
          <div class="message bot-message">
            <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar">
            <div class="message-bubble">
              <p>Hai! Aku SagaBot, Apa kabar hari ini?</p>
            </div>
          </div>
        </div>

        <div class="input-container">
          <div class="input-wrapper">
            <textarea id="messageInput" placeholder="Tulis pesanmu di sini..." rows="1"></textarea>
            <button id="sendBtn"><i class="fas fa-paper-plane"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
<script>
let chatHistory = JSON.parse(localStorage.getItem("chatHistory")) || [];
let currentChatIndex = null;

const API_KEY = "AIzaSyBvVLriN6P5JaWwe4B4bvXwoWfjl20CheA";
const MODEL = "gemini-2.5-flash";

const sendBtn = document.getElementById("sendBtn");
const input = document.getElementById("messageInput");
const messagesContainer = document.getElementById("messagesContainer");

// Fungsi untuk membuat pesan tampil di chat
function createMessage(text, sender = "user") {
  const messageDiv = document.createElement("div");
  messageDiv.classList.add("message", `${sender}-message`);

  if (sender === "bot") {
    const avatar = document.createElement("img");
    avatar.src = "img/MASKOT.png";
    avatar.classList.add("avatar");
    messageDiv.appendChild(avatar);
  }

  const bubble = document.createElement("div");
  bubble.classList.add("message-bubble");
  bubble.innerText = text;
  messageDiv.appendChild(bubble);

  messagesContainer.appendChild(messageDiv);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Fungsi call API Gemini
async function chatWithGemini(message) {
  const response = await fetch(
    `https://generativelanguage.googleapis.com/v1beta/models/${MODEL}:generateContent?key=${API_KEY}`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        contents: [{ role: "user", parts: [{ text: message }] }]
      })
    }
  );

  const data = await response.json();
  console.log(data); // <--- PENTING, LIAT DI CONSOLE

  try {
    return data.candidates[0].content.parts[0].text;
  } catch (err) {
    return "⚠️ *API Error:* " + (data.error?.message ?? "Tidak diketahui");
  }
}

function updateSidebar() {
  const list = document.querySelector(".conversations");
  list.innerHTML = "";

  chatHistory.forEach((chat, index) => {
    const item = document.createElement("div");
    item.classList.add("conversation-item");
    item.innerHTML = `<i class="fas fa-comment-dots"></i> ${chat.title}`;

    item.addEventListener("click", () => loadChat(index));
    list.appendChild(item);
  });
}



// Event Send Pesan
sendBtn.addEventListener("click", async () => {
  const text = input.value.trim();
  if (!text) return;

  createMessage(text, "user");
  input.value = "";

  // --- Simpan pesan user ---
  if (currentChatIndex === null) {
    currentChatIndex = chatHistory.length;
    chatHistory.push({
      title: text.slice(0, 20) + "...",
      messages: []
    });
  }
  chatHistory[currentChatIndex].messages.push({ role: "user", text });

  localStorage.setItem("chatHistory", JSON.stringify(chatHistory));
  updateSidebar();

  const botReply = await chatWithGemini(text);
  createMessage(botReply, "bot");

  // --- Simpan pesan bot ---
  chatHistory[currentChatIndex].messages.push({ role: "bot", text: botReply });
  localStorage.setItem("chatHistory", JSON.stringify(chatHistory));
});

function loadChat(index) {
  currentChatIndex = index;
  messagesContainer.innerHTML = "";

  chatHistory[index].messages.forEach(msg => {
    createMessage(msg.text, msg.role === "bot" ? "bot" : "user");
  });
}

document.querySelector(".new-chat-btn").addEventListener("click", () => {
  currentChatIndex = null;
  messagesContainer.innerHTML = `
    <div class="message bot-message">
      <img src="img/MASKOT.png" class="avatar">
      <div class="message-bubble">
        Hai! Aku SagaBot, Apa kabar hari ini?
      </div>
    </div>`;
});


</script>

</html>
