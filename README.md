
## Deskripsi Project

Proyek ini merupakan pengembangan sistem monitoring stok bahan baku dalam silo berbasis Internet of Things (IoT) yang terintegrasi dengan fitur prediksi inventori dan Digital Twin. Sistem menggunakan sensor Load Cell dan ESP32 untuk mengukur serta mengirimkan data berat bahan baku secara real-time ke dashboard web. Selain itu, Digital Twin berbasis Roblox Studio digunakan untuk menampilkan simulasi kondisi silo dan pabrik dalam lingkungan 3D. Sistem ini bertujuan membantu proses pemantauan stok bahan baku, mendukung pengambilan keputusan, dan meningkatkan efisiensi operasional sesuai konsep Industri 4.0. 

## Pengembangan Solusi

Solusi yang dikembangkan difokuskan pada sinkronisasi antara perangkat IoT fisik dengan ekosistem digital. Kami membangun dashboard berbasis web sebagai antarmuka utama pemantauan stok secara real-time. Lebih lanjut, kami mengimplementasikan konsep Digital Twin melalui simulasi di platform Roblox. Pengembangan logika dalam simulasi ini dibuat sangat dinamis dengan menyematkan parameter operasional yang kompleks, seperti pengurangan stok otomatis saat mesin beroperasi, perhitungan yield and usage secara presisi, hingga simulasi skenario kegagalan sistem seperti kerusakan pipa atau malfungsi alat. Integrasi data antara database pusat dengan simulasi dilakukan secara seamless menggunakan API, sehingga setiap perubahan berat pada silo fisik akan langsung direfleksikan secara visual di dalam simulasi pabrik virtual.





## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
