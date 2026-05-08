IM Mobile App

IM Mobile App ini buatkan aplikasi php vanilla, yang terhubung ke database ospos, username ospos, password ospos.
bentuk database ospos ini ada di file ospos.sql

web php ini akan dibuka di HP, jadi pastikan tampilannya mobile first. 
Tolong sarankan tech stack yg paling minimal dan paling sederhana utk membuat ini.
buat menggunakan php vanilla dan library javascript tertentu yang sederhana. 
buat semua library css dan font semua ada di local. tidak mengambil ke CDN

Yang paling awal ditampilkan adalah halaman login. jika berhasil login, baru masuk ke tampilan dashboard.
Ada 4 tampilan di web ini setelah login yaitu Dashboard, Cek Transfer, Cek Stok, dan Cek Kasir

User bisa berpindah dari satu tampilan ke tampilan lain dengan klik icon pada navbar di bagian bawah.

1. Login
bentuk tampilan ada di direktori stitch_login
login menggunakan username dan password yang ada di database ospos

2. Dashboard
bentuk tampilan ada di direktori stitch_dashboard

ini tampilan laporan transaksi ikhwanmart. Tanggal transaksi bisa dipilih. yang ditampilkan ialah 
a. jumlah transaksi
b. nilai transaksi
c. grafik bar chart yg sumbu Y nya menunjukkan nilai penjualan, sumbu X nya menunjukkan jam, mulai jam 6.00 s.d 21:00. 
   barchart jam 6 menunjukkan transaksi s.d jam 7. jam 7 menunjukkan nilai transaksi jam 7 s.d jam 8. dan  seterusnya. 
d. Tabel penjualan tiap jenis pembayaran (tunai debit transfer qris) tiap jam nya

3. Cek Transfer
bentuk tampilan ada di direktori stitch_transfer dan direktori stitch_lihat_nota

ini gunanya untuk mengeluarkan data perincian penjualan dengan pembayaran transfer pada tanggal tertentu. 
tanggalnya bisa dipilih, 
Tampilan hasilnya dalam bentuk tabel, yang isinya ialah jam transaksi, Nama kasir, Nama Pelanggan, nilainya transaksi

Selain ini, nama pelanggan dalam daftar ini bisa di klik,  ketika di klik akan ditampilkan nota penjualan atas nama dia (sales receipt dari ID penjualan tsb)
No note nya ialah sales ID nya

Di bagian bawah nota ini ada button Kirim ke WhatsApp (dgn warna dan logo whasapp) dan Button Copy Nota (dgn warna biru spt tulisan IkhwanMart diatas)


4. Cek Stok
bentuk tampilan ada di direktori stitch_cek_stok

ini gunanya menampilkan stok barang yg rendah, berdasarkan kategori barang yang dipilih.
maksud stok rendah itu dibawah stok minimum utk tiap barang. 
kategori awal yang dipilih di dropdown ialah "Belum Dipilih".
data barang dan kategori barang diambil dari database

Di bagian paling bawah list ini ada 
1. Tombol Kirim ke WhatsApp (warna hijau WhatsApp dan ada icon WhatsApp), untuk mengirimkan list stok rendah yang ditampilkan ini ke WhatsApp 
2. Tombol Copy List (warna biru spt ikhwanMart) untuk mengcopy list stok rendah ini utk di paste ke app lain

5. Cek Kasir
bentuk tampilan ada di direktori stitch_cek_kasir

ini gunanya utk mencatat uang cash yg ada di kasir minimarket pada jam tertentu, untuk disimpan ke database. 
jadi pada hari dan jam tertentu, user akan datang ke kasir , mengambil uang cash yg besar (satuan 100rb dan 50rb rupiah) 
dan menghitung masing2 berapa lembar yg ada di kasir, uang 20rb, 10rb, 5rb, 2rb, dan 1000 rupiah. bentuk tampilannya 
seperti di direktori stitch_cashier_cek_kasir