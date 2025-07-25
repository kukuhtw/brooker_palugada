Berikut adalah isi file `README.md` lengkap untuk GitHub proyek **Brooker AI / Calo AI Palugada**, dalam format Markdown:

````markdown
# 🤖 Brooker AI / Calo AI Palugada  
**"Apa elo mau? Gw ada." – Platform AI untuk mencocokkan keinginan pembeli dengan penjual**

Aplikasi ini menggunakan teknologi **LLM (ChatGPT/OpenAI)** dan **vector search Pinecone**
 untuk melakukan pencarian berbasis makna (semantic search).
Cocok untuk pencocokan properti, barang, kendaraan, atau jasa.

---

## 🚀 Fitur Utama

- ✍️ Input data penjual bebas, diproses jadi data terstruktur
- 🧠 Dibantu LLM untuk ekstraksi metadata
- 📦 Disimpan dalam bentuk embedding (1536 dimensi)
- 🔍 Query pembeli dicocokkan secara semantik
- ✅ Menampilkan hasil paling relevan otomatis

---

## 🛠️ Teknologi yang Digunakan

- PHP 7/8 (Native)
- MySQL (data backend)
- OpenAI API (text-embedding-ada-002)
- Pinecone Vector Database
- Bootstrap 5 (UI admin panel)

---

## ⚙️ Arsitektur Alur

```plaintext
[Penjual Input Data] 
    ↓
[Refine LLM + Metadata] 
    ↓
[Embedding ke Pinecone (1536 dimensi)] 
    ↓                      ↑
[Pembeli Query] → [LLM Embedding] 
    ↓
[Vector Search] → [Matching Result]
````

---

## 📂 Struktur Folder

```
/public/         → Halaman publik & admin
/src/            → Class utama: DataInventory, TransaksiQuery
/logs/           → File log sistem
/sql/             → File mysql script  
/bootstrap.php   → Inisialisasi koneksi & logger
```

---

## ⚡ Cara Instalasi

```bash
git clone https://github.com/kukuhtw/brooker_paluugada.git

Windows
bikin folder di c:/xampp/htdocs/palugada

Linux
bikin folder di var/www/html/palugada




---

## 📞 Kontak Developer

* 👨‍💻 Kukuh TW
* 📧 Email: [kukuhtw@gmail.com](mailto:kukuhtw@gmail.com)
* 📱 WhatsApp: [https://wa.me/628129893706](https://wa.me/628129893706)
* 🌐 Website: [https://kumpulproperti.com](https://kumpulproperti.com)

---
📄 Lisensi
GNU General Public License v3.0

Anda bebas:

Menggunakan dan menjalankan aplikasi

Mempelajari dan memodifikasi kode

Mendistribusikan ulang, dengan syarat tetap open-source

https://www.gnu.org/licenses/gpl-3.0.html
---

## ❤️ Suka Proyek Ini?

Silakan ⭐ repo ini dan forking untuk dikembangkan jadi usaha brooker / mediator anda sendiri

```


