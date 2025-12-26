<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/chatbot.css">
  <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body>

  <!-- LOGIN WALL (MODAL) -->
  <div id="login-wall">
    <div class="login-box">
      <i class="fas fa-lock login-icon"></i>
      <h2>Fitur Ini Memerlukan Login</h2>
      <p>Silakan login atau register untuk menikmati fitur SagaBot ini.</p>
      <a href="../auth/login.php" class="login-button-link">Login atau Register</a>
      <br>
      <a href="../dashboard/index.php" class="login-button">Kembali</a>
    </div>
  </div>

  <!-- Konten Chatbot -->
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
          <!-- Riwayat akan diisi otomatis oleh JS -->
        </div>
      </div>

      <!-- Sidebar Footer -->
      <div class="sidebar-footer">
        <div class="user-profile" onclick="window.location.href='../user/profile.php'">
          <div id="sidebarUserAvatarContainer">
            <i class="fas fa-user-circle"></i>
          </div>
          <span id="sidebarUsername">Guest</span>
        </div>
      </div>
    </div>

    <!-- Area Chat -->
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

  <!-- Script: Guard + Force Redirect Jika Tidak Login -->
  <script>
    (function() {
      const isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true' || localStorage.getItem('isLoggedIn') === 'true';
      const userId = sessionStorage.getItem('userId') || localStorage.getItem('userId');

      if (!isLoggedIn || !userId) {
        sessionStorage.clear();
        localStorage.clear();
        window.location.replace('../auth/login.php');
      } else {
        // Jika sudah login → sembunyikan login wall
        const loginWall = document.getElementById('login-wall');
        if (loginWall) loginWall.classList.add('hidden');
      }
    })();
  </script>

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
        avatar.onerror = () => { this.src = 'https://placehold.co/40x40/FFD700/000000?text=S'; };
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
        if (data.candidates?.[0]?.content?.parts?.[0]?.text) {
          reply = data.candidates[0].content.parts[0].text;
        }
        reply = reply.replace(/\*\*(.*?)\*\*/g, '$1').replace(/\*(.*?)\*/g, '$1').trim();
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

  <!-- Script Riwayat & Sinkronisasi Profil -->
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

    // Sinkronisasi Avatar & Nama User
    window.addEventListener('load', () => {
      const sidebarUsername = document.getElementById('sidebarUsername');
      const sidebarAvatarContainer = document.getElementById('sidebarUserAvatarContainer');

      // Nama dari header (jika header di-load dari halaman lain)
      const headerUsername = document.getElementById('user-name-display');
      if (headerUsername && sidebarUsername) {
        const name = headerUsername.textContent.trim();
        if (name) sidebarUsername.textContent = name;
      }

      // Foto dari localStorage (prioritas utama, disimpan oleh profile.php)
      const savedProfilePic = localStorage.getItem('userProfilePicture');
      if (savedProfilePic && sidebarAvatarContainer) {
        const img = document.createElement('img');
        img.src = savedProfilePic;
        img.alt = "Foto Profil";
        img.onerror = () => {
          sidebarAvatarContainer.innerHTML = '<i class="fas fa-user-circle"></i>';
        };
        sidebarAvatarContainer.innerHTML = '';
        sidebarAvatarContainer.appendChild(img);
        return;
      }

      // Fallback ke ikon default
    });

    window.onload = () => loadHistoryList();
  </script>
</body>
</html>