<?php
session_start();

// 1. CEK KEAMANAN: Pastikan User sudah Login
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

// 2. CEK PAKET (Opsional: Jika Chatbot hanya untuk Premium/Basic)
// if (!isset($_SESSION['plan_type']) || $_SESSION['plan_type'] === 'none') {
//     header("Location: plan.php");
//     exit();
// }

// 3. AMBIL DATA USER DARI SESI
$userName = $_SESSION['user_name'] ?? 'Pengguna'; // Default 'Pengguna' jika error session
$userPlan = $_SESSION['plan_type'] ?? 'basic';
$userInitial = strtoupper(substr($userName, 0, 1)); // Ambil huruf depan nama
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../user/style/chatbot.css">
</head>
<body>

    <?php include 'partials/header.php'; ?>

    <div class="chat-container">
        
        <div class="chat-sidebar">
            <div class="new-chat-btn" onclick="startNewChat()">
                <i class="fas fa-plus"></i> Chat Baru
            </div>
            
            <div class="chat-history">
                <p style="color:#6B7280; font-size:0.9rem; margin-top:10px;">Riwayat Percakapan:</p>
                </div>

            <div style="margin-top: auto; padding-top: 15px; border-top: 1px solid #E5E7EB; display: flex; align-items: center; gap: 10px;">
                <div style="width: 35px; height: 35px; background: #014C63; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    <?php echo $userInitial; ?>
                </div>
                <div>
                    <div style="font-size: 0.9rem; font-weight: 600; color: #1F2937;">
                        <?php echo htmlspecialchars($userName); ?>
                    </div>
                    <div style="font-size: 0.75rem; color: #6B7280;">
                        <?php echo ucfirst($userPlan); ?> Plan
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-main">
            <div class="chat-header">
                <div class="bot-info">
                    <div class="bot-avatar"><i class="fas fa-robot"></i></div>
                    <div>
                        <h3 style="margin:0; font-size:1rem;">SagaHealth AI</h3>
                        <span style="font-size:0.8rem; color:#059669;">● Online</span>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    <div class="message-sender">SagaBot</div>
                    Halo <strong><?php echo htmlspecialchars($userName); ?></strong>! 👋<br>
                    Saya asisten kesehatan AI Anda. Ada yang bisa saya bantu hari ini?
                </div>
            </div>

            <div class="chat-input-area">
                <textarea class="chat-input" id="messageInput" placeholder="Ketik pertanyaan kesehatan Anda..." onkeypress="handleEnter(event)"></textarea>
                <button class="send-btn" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

   <script>
        const currentUserName = "<?php echo htmlspecialchars($userName); ?>";
        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.querySelector('.send-btn');

        function handleEnter(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        }

        async function sendMessage() {
            const text = messageInput.value.trim();
            if (!text) return;

            // 1. Tampilkan Pesan User
            addMessage(text, 'user', currentUserName);
            messageInput.value = '';
            messageInput.disabled = true; // Kunci input saat loading
            sendBtn.disabled = true;

            // 2. Tampilkan Loading Bubble
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'message bot';
            loadingDiv.id = 'loadingBubble';
            loadingDiv.innerHTML = '<div class="message-sender">SagaBot</div><i class="fas fa-circle-notch fa-spin"></i> <em>Sedang berpikir...</em>';
            chatMessages.appendChild(loadingDiv);
            scrollToBottom();

            try {
                // 3. KIRIM KE BACKEND (Gemini API)
                const response = await fetch('../user/partials/chat_gemini.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();

                // Hapus Loading
                chatMessages.removeChild(loadingDiv);

                if (data.status === 'success') {
                    addMessage(data.reply, 'bot', 'SagaBot');
                } else {
                    addMessage("Maaf, terjadi kesalahan koneksi. Silakan coba lagi.", 'bot', 'System');
                    console.error(data);
                }

            } catch (error) {
                chatMessages.removeChild(loadingDiv);
                addMessage("Gagal terhubung ke server.", 'bot', 'System');
                console.error(error);
            } finally {
                // Buka kunci input
                messageInput.disabled = false;
                sendBtn.disabled = false;
                messageInput.focus();
            }
        }

        function addMessage(text, sender, senderName) {
            const div = document.createElement('div');
            div.className = `message ${sender}`;
            
            // Render HTML (karena kita sudah memformat di PHP)
            div.innerHTML = `
                <div class="message-sender">${senderName}</div>
                ${text}
            `;
            
            chatMessages.appendChild(div);
            scrollToBottom();
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function startNewChat() {
            if(confirm("Mulai sesi chat baru?")) {
                chatMessages.innerHTML = `
                    <div class="message bot">
                        <div class="message-sender">SagaBot</div>
                        Halo <strong>${currentUserName}</strong>! Saya siap membantu menjawab pertanyaan kesehatanmu.
                    </div>
                `;
            }
        }
    </script>
</body>
</html>