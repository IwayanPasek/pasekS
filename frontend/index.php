<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasek Tech Hub | AI E-Commerce Prototype</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary: #0d6efd; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg); color: var(--dark); }
        
        /* Navbar Modern */
        .navbar { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .nav-logo { font-weight: 800; letter-spacing: -1px; }

        /* Hero Section */
        .hero { padding: 80px 0; background: linear-gradient(135deg, #0d6efd 0%, #00d4ff 100%); color: white; border-radius: 0 0 40px 40px; }

        /* Product Cards */
        .card-product { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; background: white; overflow: hidden; }
        .card-product:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .wf-img { width: 100%; height: 200px; object-fit: cover; background: #eee; }

        /* Floating Chat Widget */
        #chat-widget { position: fixed; bottom: 30px; right: 30px; z-index: 1050; }
        #chat-window { 
            width: 400px; height: 600px; background: white; border-radius: 24px; 
            display: none; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.2); 
            overflow: hidden; border: 1px solid rgba(255,255,255,0.3);
        }
        #chat-header { background: var(--dark); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        #chat-body { flex: 1; padding: 20px; overflow-y: auto; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px; }
        
        /* Message Bubbles */
        .msg { max-width: 85%; padding: 12px 16px; border-radius: 18px; font-size: 0.95rem; }
        .bot-msg { background: white; border-bottom-left-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .user-msg { background: var(--primary); color: white; align-self: flex-end; border-bottom-right-radius: 4px; }
        
        /* Product Result in Chat */
        .chat-wf-item { background: white; border-radius: 12px; padding: 10px; border: 1px solid #e2e8f0; margin-top: 10px; }
        .chat-wf-item img { width: 100%; border-radius: 8px; margin-bottom: 8px; border: 1px solid #ddd; }

        #chat-footer { padding: 20px; background: white; border-top: 1px solid #f1f5f9; }
        .chat-input-box { background: #f1f5f9; border-radius: 15px; padding: 5px 15px; display: flex; align-items: center; }
        #userInput { border: none; background: transparent; padding: 8px 0; width: 100%; outline: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand nav-logo text-primary" href="#"><i class="bi bi-cpu-fill"></i> PASEK.TECH</a>
        <div class="d-flex align-items:center">
            <div class="me-3 d-none d-md-block"><i class="bi bi-person-circle"></i> Admin Mode</div>
            <div class="position-relative">
                <i class="bi bi-cart-fill fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </div>
        </div>
    </div>
</nav>

<div class="hero text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Enterprise AI Integration</h1>
        <p class="lead opacity-75">Sistem E-Commerce berbasis Azure OpenAI dengan Deep Thinking & SQL Generator.</p>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4" id="main-catalog">
        <div class="col-md-3">
            <div class="card card-product">
                <img src="https://placehold.co/400x300/e0e0e0/969696/png?text=WIRE_FRAME_IPHONE" class="wf-img">
                <div class="card-body">
                    <span class="badge bg-light text-primary mb-2">Smartphone</span>
                    <h6 class="fw-bold mb-1">iPhone 15 Pro Max</h6>
                    <p class="text-primary fw-bold mb-3">Rp 24.999.000</p>
                    <button class="btn btn-primary w-100 rounded-pill btn-sm">Beli Sekarang</button>
                </div>
            </div>
        </div>
        </div>
</div>

<div id="chat-widget">
    <div id="chat-window">
        <div id="chat-header">
            <div>
                <i class="bi bi-robot me-2"></i>
                <span>AI SHOP CONSULTANT</span>
            </div>
            <button class="btn btn-sm text-white" onclick="toggleChat()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="chat-body">
            <div class="msg bot-msg shadow-sm">
                Halo! Saya asisten AI Pasek. Ada yang bisa saya bantu cari di database produk?
            </div>
        </div>
        <div id="chat-footer">
            <div class="chat-input-box">
                <input type="text" id="userInput" placeholder="Tanya sesuatu..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button class="btn text-primary p-0" onclick="sendMessage()"><i class="bi bi-send-fill fs-5"></i></button>
            </div>
        </div>
    </div>
    <button class="btn btn-primary rounded-circle shadow-lg p-0" style="width: 70px; height: 70px;" onclick="toggleChat()">
        <i class="bi bi-chat-dots-fill fs-2"></i>
    </button>
</div>

<script>
    function toggleChat() {
        const win = document.getElementById('chat-window');
        win.style.display = win.style.display === 'flex' ? 'none' : 'flex';
    }

    async function sendMessage() {
        const input = document.getElementById('userInput');
        const body = document.getElementById('chat-body');
        const text = input.value.trim();
        if(!text) return;

        body.innerHTML += `<div class="msg user-msg">${text}</div>`;
        input.value = '';
        body.scrollTop = body.scrollHeight;

        try {
            const resp = await fetch(`http://127.0.0.1:8000/chat?message=${encodeURIComponent(text)}`);
            const res = await resp.json();

            let botHTML = `
                <div class="msg bot-msg shadow-sm">
                    <small class="text-muted d-block mb-1" style="font-size:0.7rem;">[THOUGHT]: ${res.thought}</small>
                    <div class="mb-2">${res.reply}</div>`;
            
            if(res.data && res.data.length > 0) {
                res.data.forEach(p => {
                    // Gambar otomatis mengambil dari database (Link Wireframe)
                    const imgUrl = p.gambar_url;
                    botHTML += `
                    <div class="chat-wf-item">
                        <img src="${imgUrl}" alt="Wireframe">
                        <div class="fw-bold" style="font-size:0.8rem;">${p.nama_produk}</div>
                        <div class="text-primary small fw-bold">Rp${p.harga.toLocaleString('id-ID')}</div>
                    </div>`;
                });
            }
            botHTML += `</div>`;
            body.innerHTML += botHTML;
        } catch (err) {
            body.innerHTML += `<div class="msg bot-msg text-danger small">Koneksi Error. Pastikan Python Aktif!</div>`;
        }
        body.scrollTop = body.scrollHeight;
    }
</script>

</body>
</html>