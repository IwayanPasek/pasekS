import mysql.connector
import json
from openai import AzureOpenAI
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware

# --- KONFIGURASI AZURE ---
client = AzureOpenAI(
    azure_endpoint="https://iwaya-mm78gp1o-eastus2.cognitiveservices.azure.com/openai/deployments/tanyawayan-ai/chat/completions?api-version=2025-01-01-preview", 
    api_key="",  
    api_version="2024-02-15-preview"
)
DEPLOYMENT_NAME = "gpt-4o-mini"

app = FastAPI()
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

db_config = {"host": "localhost", "user": "root", "password": "", "database": "db_gadget"}
chat_sessions = {}

# --- SYSTEM PROMPT DENGAN SENTIMEN & COMPARISON ---
SYSTEM_PROMPT = """
Kamu adalah 'Pasek Tech Consultant'. 
DATA: produk_gadget (id_produk, nama_produk, merk, harga, ram_gb, chipset, stok, spesifikasi)

TUGAS:
1. SENTIMEN: Analisis apakah input user 'Positif', 'Netral', atau 'Negatif'.
2. COMPARISON: Jika user minta bandingkan (misal: 'A vs B'), buat SQL untuk mengambil kedua produk tersebut.
   Gunakan LIKE atau IN (misal: SELECT * FROM produk_gadget WHERE nama_produk LIKE '%A%' OR nama_produk LIKE '%B%').
3. PROBING: Jika info kurang, tanya balik.

FORMAT JSON WAJIB:
{
  "thought": "Analisis mendalam kamu",
  "sentimen": "Positif/Netral/Negatif",
  "action": "CHAT" atau "SQL",
  "content": "Pesan/SQL Query",
  "reply": "Kalimat pengantar"
}
"""

@app.get("/chat")
async def chat_engine(message: str, session_id: str = "user_1"):
    if session_id not in chat_sessions:
        chat_sessions[session_id] = [{"role": "system", "content": SYSTEM_PROMPT}]
    
    chat_sessions[session_id].append({"role": "user", "content": message})
    conn = None

    try:
        response = client.chat.completions.create(
            model=DEPLOYMENT_NAME,
            messages=chat_sessions[session_id],
            response_format={ "type": "json_object" }
        )
        
        res = json.loads(response.choices[0].message.content)
        data_result = []
        final_reply = res.get('content') if res['action'] == "CHAT" else res.get('reply')

        if res['action'] == "SQL":
            conn = mysql.connector.connect(**db_config)
            cursor = conn.cursor(dictionary=True)
            cursor.execute(res['content'])
            data_result = cursor.fetchall()
            
            # SIMPAN KE LOG DENGAN SENTIMEN
            cursor.execute(
                "INSERT INTO log_chat (user_query, ai_thought, sql_generated, sentimen) VALUES (%s, %s, %s, %s)",
                (message, res['thought'], res['content'], res['sentimen'])
            )
            conn.commit()

        chat_sessions[session_id].append({"role": "assistant", "content": final_reply})
        return {"thought": res['thought'], "sentimen": res['sentimen'], "reply": final_reply, "data": data_result}

    except Exception as e:
        return {"reply": f"Error: {str(e)}", "data": []}
    finally:
        if conn: conn.close()

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8000)