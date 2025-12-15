<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/chatbot.css">
  <link rel="stylesheet" href="../assets/style/auth.css" />
  <link rel="stylesheet" href="../user/style/chatbot.css" />
  <link rel="stylesheet" href="../assets/style/profile.css">

  <!-- CSS kecil untuk avatar di sidebar -->
  <style>
    #sidebarUserAvatarContainer i {
      font-size: 32px;
      color: #014C63;
    }
    #sidebarUserAvatarContainer img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>
<body>

  <?php include '../user/partials/header.php'; ?>
  <div class="overlay" id="overlay"></div>

  <div class="app-container">

    <!-- Sidebar -->
    <div class="sidebar">
      <div>
        <div class="sidebar-header">
          <h1>SAGABOT</h1>
        </div>

        <button class="new-chat-btn" id="newChatBtn"><i class="fas fa-plus"></i> New chat</button>

        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search..." />
        </div>

        <div class="section-header">
          <span>Your conversations</span>
          <a href="#" id="clearAllBtn">Clear All</a>
        </div>

        <div class="conversations" id="conversationList">
          <!-- Riwayat otomatis dari localStorage -->
        </div>
      </div>

      <!-- Sidebar Footer - Avatar & Nama sinkron otomatis dari header -->
      <div class="sidebar-footer">
        <div class="user-profile" onclick="window.location.href='profile.php'">
          <div id="sidebarUserAvatarContainer">
            <i class="fas fa-user-circle"></i>
          </div>
          <span id="sidebarUsername">Guest</span>
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
            <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar" onerror="this.src='https://placehold.co/40x40/FFD700/000000?text=S'">
            <div class="message-bubble">
              <p>Hai! Aku SagaBot 🐥 versi lucu seperti SimSimi! Apa kabar hari ini?</p>
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

  <!-- Script Utama Chat -->
  <script>
    const sendBtn = document.getElementById("sendBtn");
    const input = document.getElementById("messageInput");
    const messagesContainer = document.getElementById("messagesContainer");

    function createMessage(text, sender = "user") {
      const messageDiv = document.createElement("div");
      messageDiv.classList.add("message", `${sender}-message`);

      if (sender === "bot") {
        const avatar = document.createElement("img");
        avatar.src = "img/MASKOT.png";
        avatar.onerror = function() { this.src='https://placehold.co/40x40/FFD700/000000?text=S' };
        avatar.alt = "Bot Avatar";
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
    
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = (input.scrollHeight) + 'px';
    });

    function sendMessage() {
      const text = input.value.trim();
      if (!text) return;

      createMessage(text, "user");
      input.value = "";
      input.style.height = 'auto';

      showTypingIndicator();

      fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyBJFckWFfRbA0GKAZ1bo8ZvDHUMd2MiDrM`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          contents: [{ role: "user", parts: [{ text: text }] }]
        })
      })
      .then(res => res.json())
      .then(data => {
        hideTypingIndicator();
        
        let reply = "Maaf, aku bingung nih...";
        if (data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts[0]) {
          reply = data.candidates[0].content.parts[0].text;
        }
        
        reply = reply
          .replace(/\*\*(.*?)\*\*/g, '$1')
          .replace(/\*(.*?)\*/g, '$1')
          .trim();

        createMessage(reply, "bot");
        saveCurrentChat();
      })
      .catch(err => {
        hideTypingIndicator();
        createMessage("Ups, ada masalah koneksi. Coba lagi ya!", "bot");
        console.error(err);
      });
    }

    sendBtn.addEventListener("click", sendMessage);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
  </script>

  <!-- Script Fitur Tambahan + Sinkronisasi Avatar & Nama -->
  <script>
    const typingId = "typing-indicator-unique";
    
    function showTypingIndicator() {
      if (document.getElementById(typingId)) return;
      const typingDiv = document.createElement("div");
      typingDiv.id = typingId;
      typingDiv.className = "message bot-message";
      const avatar = document.createElement("img");
      avatar.src = "img/MASKOT.png";
      avatar.onerror = () => avatar.src = 'https://placehold.co/40x40/FFD700/000000?text=S';
      avatar.className = "avatar";
      const bubble = document.createElement("div");
      bubble.className = "message-bubble typing-indicator";
      bubble.innerHTML = "<span></span><span></span><span></span> <small>SagaBot sedang berpikir...</small>";
      typingDiv.appendChild(avatar);
      typingDiv.appendChild(bubble);
      messagesContainer.appendChild(typingDiv);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function hideTypingIndicator() {
      const el = document.getElementById(typingId);
      if (el) el.remove();
    }

    const conversationList = document.getElementById("conversationList");
    const newChatBtn = document.getElementById("newChatBtn");
    const clearAllBtn = document.getElementById("clearAllBtn");

    function saveCurrentChat() {
      const messages = Array.from(messagesContainer.querySelectorAll(".message:not(#typing-indicator-unique)"))
        .map(m => ({
          text: m.querySelector(".message-bubble").innerText,
          sender: m.classList.contains("user-message") ? "user" : "bot"
        }));

      if (messages.length <= 1) return;

      const title = messages.find(m => m.sender === "user")?.text.slice(0, 35) + "..." || "New Chat";

      let history = JSON.parse(localStorage.getItem("sagabot_history") || "[]");
      history = history.filter(c => c.title !== title);
      history.unshift({ title, messages, timestamp: Date.now() });

      localStorage.setItem("sagabot_history", JSON.stringify(history));
      loadHistoryList();
    }

    function loadHistoryList() {
      const history = JSON.parse(localStorage.getItem("sagabot_history") || "[]");
      conversationList.innerHTML = "";
      history.forEach((chat, i) => {
        const item = document.createElement("div");
        item.className = "conversation-item";
        item.innerHTML = `<i class="fas fa-comment-dots"></i> ${chat.title}`;
        
        const deleteBtn = document.createElement("i");
        deleteBtn.className = "fas fa-trash delete-btn";
        deleteBtn.onclick = (e) => {
          e.stopPropagation();
          deleteChat(i, item);
        };
        item.appendChild(deleteBtn);

        item.onclick = () => {
          messagesContainer.innerHTML = `<div class="message bot-message">
            <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar" onerror="this.src='https://placehold.co/40x40/FFD700/000000?text=S'">
            <div class="message-bubble">
              <p>Hai! Aku SagaBot 🐥 versi lucu seperti SimSimi! Apa kabar hari ini?</p>
            </div>
          </div>`;
          chat.messages.forEach(m => createMessage(m.text, m.sender));
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        };
        conversationList.appendChild(item);
      });
    }

    function deleteChat(index, item) {
      if (confirm("Hapus chat ini?")) {
        item.style.opacity = '0';
        setTimeout(() => {
          let history = JSON.parse(localStorage.getItem("sagabot_history") || "[]");
          history.splice(index, 1);
          localStorage.setItem("sagabot_history", JSON.stringify(history));
          loadHistoryList();
        }, 500);
      }
    }

    newChatBtn.onclick = () => {
      if (confirm("Mulai chat baru? Riwayat saat ini akan disimpan.")) {
        saveCurrentChat();
        messagesContainer.innerHTML = `<div class="message bot-message">
          <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar" onerror="this.src='https://placehold.co/40x40/FFD700/000000?text=S'">
          <div class="message-bubble">
            <p>Hai! Aku SagaBot 🐥 versi lucu seperti SimSimi! Apa kabar hari ini?</p>
          </div>
        </div>`;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
    };

    clearAllBtn.onclick = (e) => {
      e.preventDefault();
      if (confirm("Hapus semua riwayat?")) {
        localStorage.removeItem("sagabot_history");
        loadHistoryList();
      }
    };

    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const overlay = document.getElementById("overlay");
    if (mobileMenuBtn) {
      mobileMenuBtn.onclick = () => {
        document.querySelector(".sidebar").classList.toggle("active");
        overlay.classList.toggle("active");
      };
    }
    if (overlay) {
      overlay.onclick = () => {
        document.querySelector(".sidebar").classList.remove("active");
        overlay.classList.remove("active");
      };
    }

    // Sinkronisasi Nama & Avatar dari header ke sidebar
    window.addEventListener('load', function() {
      const headerUsername = document.getElementById('user-name-display');
      const sidebarUsername = document.getElementById('sidebarUsername');
      const sidebarAvatarContainer = document.getElementById('sidebarUserAvatarContainer');

      // Sinkron nama
      if (headerUsername && sidebarUsername) {
        const nameText = headerUsername.textContent.trim();
        sidebarUsername.textContent = nameText || 'Guest';
      }

      // Jika ada <img> foto profil di dalam .btn-profile (nanti saat upload foto)
      const headerProfileLink = document.querySelector('.btn-profile');
      const headerImg = headerProfileLink ? headerProfileLink.querySelector('img') : null;

      if (headerImg && sidebarAvatarContainer) {
        const img = document.createElement('img');
        img.src = headerImg.src;
        img.alt = "Profil User";
        img.onerror = () => {
          sidebarAvatarContainer.innerHTML = '<i class="fas fa-user-circle"></i>';
        };
        sidebarAvatarContainer.innerHTML = '';
        sidebarAvatarContainer.appendChild(img);
      }
    });

    window.onload = () => loadHistoryList();
  </script>
</body>
</html>
