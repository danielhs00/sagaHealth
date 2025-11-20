<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/chatbot.css">
</head>
<body>

 <!-- LOGIN WALL (MODAL) -->
<div id="login-wall">
  <div class="login-box">
    <!-- Ikon Gembok dari Font Awesome -->
    <i class="fas fa-lock login-icon"></i>
    
    <!-- Judul Pesan -->
    <h2>Fitur Ini Memerlukan Login</h2>
    
    <!-- Deskripsi Pesan -->
    <p>Silakan login atau register untuk menikmati fitur mood tracker ini.</p>
    
    <!-- Tombol Aksi (Link ke halaman login) -->
    <a href="../auth/login.php" class="login-button-link">Login atau Register</a>
    <br>
    <a href="../dashboard/index.php" class="login-button">Kembali</a>
  </div>
</div>


  <!-- Ini adalah konten Chatbot Anda yang sudah ada -->
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
            <!-- 
              Saya asumsikan Anda punya gambar ini.
              Jika tidak, ganti dengan placeholder: https://placehold.co/40x40/FFD700/000000?text=S
            -->
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

  <script>
    // SKRIP PENJAGA (GUARD SCRIPT) BARU
    // Dijalankan segera untuk memeriksa status login dari alur Anda sebelumnya.
    (function() {
      // Cek sessionStorage. Ini adalah KUNCI dari alur Anda.
      if (sessionStorage.getItem('isLoggedIn') === 'true') {
        // Jika user sudah login (di sesi ini),
        // sembunyikan login wall dan izinkan scroll.
        document.getElementById('login-wall').classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
      // Jika tidak (isLoggedIn null), biarkan login wall tampil
      // dan biarkan overflow: hidden pada body (dari CSS).
    })();


    // Skrip Chatbot Anda (SUDAH ADA)
    const sendBtn = document.getElementById("sendBtn");
    const input = document.getElementById("messageInput");
    const messagesContainer = document.getElementById("messagesContainer");

    function createMessage(text, sender = "user") {
      const messageDiv = document.createElement("div");
      messageDiv.classList.add("message", `${sender}-message`);

      if (sender === "bot") {
        const avatar = document.createElement("img");
        // Ganti juga di sini jika placeholder diperlukan
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
    
    // Fungsi untuk auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto'; // Reset tinggi
        input.style.height = (input.scrollHeight) + 'px';
    });

    // Fungsi untuk mengirim pesan
    function sendMessage() {
      const text = input.value.trim();
      if (!text) return;

      createMessage(text, "user");
      input.value = "";
      input.style.height = 'auto'; // Reset tinggi textarea

      // Respon bot (simulasi)
      setTimeout(() => {
        const responses = [
          "Haha iya, aku paham 😄",
          "Wah menarik banget!",
          "Ceritain lebih lanjut dong~",
          "Aku suka cara kamu ngomong 😆",
          "Hmm... menarik, menurutmu gimana?"
        ];
        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        createMessage(randomResponse, "bot");
      }, 700);
    }

    sendBtn.addEventListener("click", sendMessage);
    
    // Kirim dengan tombol Enter (Ctrl+Enter untuk baris baru)
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault(); // Mencegah baris baru
            sendMessage();
        }
    });
    
  </script>
</body>
</html>