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

 
</body>
</html>