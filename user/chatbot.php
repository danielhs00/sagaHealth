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

  <style>
    /* CSS Tambahan untuk Animasi dan Tombol Hapus (tanpa ganggu CSS asli) */
    .conversation-item {
      position: relative;
      transition: opacity 0.5s ease; /* Animasi fade-out */
    }
    .delete-btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #ff4d4d;
      font-size: 14px;
      opacity: 0.7;
    }
    .delete-btn:hover {
      opacity: 1;
    }

    

    <style>
 .top-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 100;
      height: 60px;
      background: #1e293b;;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      z-index: 1000;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
  .mobile-menu-btn:hover {
    background: #ececf1;
    transform: scale(1.05);
  }
  @media (max-width: 768px) {
    .mobile-menu-btn { display: flex; align-items: center; justify-content: center; }
  }

  /* Warna ikon hapus: default abu-abu → merah saat hover/klik */
  .delete-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #ff6b6b !important;     /* merah muda */
    font-size: 15px;
    opacity: 0.7;
    transition: all 0.2s ease;
  }
  .delete-btn:hover {
    opacity: 1;
    color: #ff4d4d !important;     /* merah terang saat hover */
    transform: translateY(-50%) scale(1.2);
  }

  /* Animasi fade-out tetap */
  .conversation-item {
    position: relative;
    padding-right: 40px;   /* beri ruang untuk tombol hapus */
    transition: opacity 0.5s ease;
  }
</style>
  </style>
</head>
<body>

  <?php include '../user/partials/header.php'; ?>
<div class="overlay" id="overlay"></div>

  <div class="app-container">

    <!-- Sidebar (Last 7 Days sudah dihapus + biru tua) -->
    <div class="sidebar" style="background: #1e293b; color: #1e293b;">
      <div>
        <div class="sidebar-header">
          <h1>SAGABOT</h1>
        </div>

        <button class="new-chat-btn" id="newChatBtn"><i class="fas fa-plus"></i> New chat</button>

        <div class="search-bar">
          <i class="fas fa-search" style="color:#999;"></i>
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

     <div style="border-top:1px solid #565869; padding:15px; margin-top:auto;">
      <div class="user-profile" onclick="window.location.href='profile.php'" style="display:flex; align-items:center; gap:12px; cursor:pointer; padding:10px; border-radius:8px; transition:0.2s;">
        <img src="https://i.pravatar.cc/50" alt="user" style="width:40px; height:40px; border-radius:50%;">
        <span>
    <?php echo htmlspecialchars(isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'); ?>
</span>
        
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
              <p>Hai! Aku SagaBot 🐥 versi lucu seperti SimSimi! Apa kabar hari ini? </p>
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

  <!-- KODE ASLI KAMU (TIDAK DIUBAH SAMA SEKALI) -->
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

      // === KONEKSI KE GOOGLE GEMINI ===
      fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=AIzaSyBJFckWFfRbA0GKAZ1bo8ZvDHUMd2MiDrM`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          contents: [{ role: "user", parts: [{ text: text }] }]
        })
      })
      .then(res => res.json())
      .then(data => {
        hideTypingIndicator();
        const reply = data.candidates[0].content.parts[0].text || "Maaf, aku bingung nih...";
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

  <!-- FITUR TAMBAHAN (dengan modifikasi hapus manual + animasi) -->
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
      bubble.innerHTML = "<span></span><span></span><span></span> <small style='color:#888;'>SagaBot sedang berpikir...</small>";
      typingDiv.appendChild(avatar);
      typingDiv.appendChild(bubble);
      messagesContainer.appendChild(typingDiv);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function hideTypingIndicator() {
      const el = document.getElementById(typingId);
      if (el) el.remove();
    }

    // === RIWAYAT CHAT (unlimited, hapus manual per item dengan animasi) ===
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
      // Hapus baris batas max (sekarang unlimited)

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
        
        // Tambah tombol hapus manual
        const deleteBtn = document.createElement("i");
        deleteBtn.className = "fas fa-trash delete-btn";
        deleteBtn.onclick = (e) => {
          e.stopPropagation(); // Agar tidak trigger load chat
          deleteChat(i, item); // Panggil fungsi hapus dengan animasi
        };
        item.appendChild(deleteBtn);

        // Klik item untuk load chat (selain tombol hapus)
        item.onclick = () => {
          messagesContainer.innerHTML = `<div class="message bot-message">
            <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar" onerror="this.src='https://placehold.co/40x40/FFD700/000000?text=S'">
            <div class="message-bubble"><p>Hai! Aku SagaBot sekarang pakai Google Gemini! Tanya apa saja, aku jawab pinter </p></div>
          </div>`;
          chat.messages.forEach(m => createMessage(m.text, m.sender));
        };
        conversationList.appendChild(item);
      });
    }

    // Fungsi baru: Hapus chat manual dengan animasi fade-out
    function deleteChat(index, item) {
      if (confirm("Hapus chat ini?")) {
        // Mulai animasi fade-out
        item.style.opacity = '0';
        
        // Tunggu animasi selesai (0.5s), lalu hapus
        setTimeout(() => {
          let history = JSON.parse(localStorage.getItem("sagabot_history") || "[]");
          history.splice(index, 1); // Hapus item spesifik
          localStorage.setItem("sagabot_history", JSON.stringify(history));
          loadHistoryList(); // Refresh list
        }, 500);
      }
    }

    newChatBtn.onclick = () => {
      if (confirm("Mulai chat baru? Riwayat saat ini akan disimpan.")) {
        saveCurrentChat();
        messagesContainer.innerHTML = `<div class="message bot-message">
          <img src="img/MASKOT.png" alt="Bot Avatar" class="avatar" onerror="this.src='https://placehold.co/40x40/FFD700/000000?text=S'">
          <div class="message-bubble"><p>Hai! Aku SagaBot sekarang pakai Google Gemini! Tanya apa saja, aku jawab pinter </p></div>
        </div>`;
      }
    };

    clearAllBtn.onclick = (e) => {
      e.preventDefault();
      if (confirm("Hapus semua riwayat?")) {
        localStorage.removeItem("sagabot_history");
        loadHistoryList();
      }
    };

    document.getElementById("mobileMenuBtn").onclick = () => {
      document.querySelector(".sidebar").classList.toggle("active");
      document.getElementById("overlay").classList.toggle("active");
    };
    document.getElementById("overlay").onclick = () => {
      document.querySelector(".sidebar").classList.remove("active");
      document.getElementById("overlay").classList.remove("active");
    };

    window.onload = () => loadHistoryList();
  </script>
</body>
</html>